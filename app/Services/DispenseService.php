<?php

namespace App\Services;

use App\Models\Dispense;
use App\Models\DrugOrder;
use App\Support\AuditLogger;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Dispensing, rebuilt on the stock ledger.
 *
 * The previous implementation decremented a `quantity` column on a batch row
 * directly. That is the pattern this whole design exists to replace: the column
 * was the only record of stock, so a mistake had no audit trail, a backdated
 * correction was impossible, and the shelf and the screen could drift apart
 * with nothing to detect it.
 *
 * Now every movement is an append-only ledger row, the balance is a cache that
 * is reconciled nightly, and picking is FEFO with a safety window.
 */
class DispenseService
{
    public function __construct(private readonly StockLedgerService $ledger) {}

    /**
     * Prepare a dispense: reserve stock without consuming it.
     *
     * Reservation and consumption are separate because the real workflow spans
     * minutes — pick, verify, hand over. Reserving prevents two pharmacists
     * promising the same last packet without holding a database lock across a
     * human conversation.
     *
     * @param  array<string, mixed>  $data
     */
    public function prepare(array $data): Dispense
    {
        return DB::transaction(function () use ($data) {
            $drugOrder = DrugOrder::with('order')->findOrFail($data['drug_order_id']);
            $order = $drugOrder->order;

            if ($order->status !== 'active') {
                throw new RuntimeException(
                    "Order {$order->order_number} is {$order->status} and cannot be dispensed."
                );
            }

            // A verbal order is a valid instruction but a provisional record.
            // Dispensing against one that was never countersigned leaves no
            // signed authority for the medicine that was handed over.
            if ($order->awaitsCountersignature()) {
                throw new RuntimeException(
                    "Order {$order->order_number} is a verbal order awaiting "
                    .'countersignature by the prescriber.'
                );
            }

            $requested = (float) $data['quantity'];
            $remaining = $drugOrder->quantityRemaining();

            if ($requested > $remaining + 0.000001) {
                throw new RuntimeException(sprintf(
                    'Only %s remains on this prescription; %s was requested.',
                    $remaining, $requested
                ));
            }

            $dispense = Dispense::create([
                'dispense_number' => DB::selectOne(
                    'SELECT next_number(?, ?) AS n',
                    [$order->facility_id, 'DISPENSE']
                )->n,
                'patient_id' => $order->patient_id,
                'encounter_id' => $order->encounter_id,
                'drug_order_id' => $drugOrder->id,
                'location_id' => $data['location_id'],
                'dispense_type' => $data['dispense_type'] ?? 'outpatient',
                'status' => 'preparation',
                'screened_interactions' => $data['screened_interactions'] ?? false,
                'screened_allergy' => $data['screened_allergy'] ?? false,
                'screening_overridden' => $data['screening_overridden'] ?? false,
                'override_reason' => $data['override_reason'] ?? null,
            ]);

            // FEFO reservation across however many lots are needed.
            $allocations = $this->ledger->allocateFefo(
                itemId: $data['stock_item_id'],
                locationId: $data['location_id'],
                quantity: $requested,
                referenceType: 'dispense',
                referenceId: $dispense->id,
                idempotencyKey: 'alloc:'.$dispense->id,
            );

            foreach ($allocations as $allocation) {
                DB::table('dispense_item')->insert([
                    'id' => (string) Str::uuid(),
                    'facility_id' => $order->facility_id,
                    'dispense_id' => $dispense->id,
                    'stock_item_id' => $data['stock_item_id'],
                    'stock_lot_id' => $allocation['stock_lot_id'],
                    'quantity' => $allocation['qty'],
                    'unit_id' => $data['unit_id'],
                    'base_quantity' => $allocation['qty'],
                ]);
            }

            return $dispense->fresh();
        });
    }

    /**
     * Complete a dispense: consume the reservations and hand the medicine over.
     */
    public function complete(Dispense $dispense, string $baseUnit): Dispense
    {
        return DB::transaction(function () use ($dispense, $baseUnit) {
            if ($dispense->status === 'completed') {
                return $dispense;   // idempotent: a retried click must not double-issue
            }

            if ($dispense->status === 'cancelled') {
                throw new RuntimeException('This dispense was cancelled.');
            }

            $this->ledger->consumeAllocations(
                referenceType: 'dispense',
                referenceId: $dispense->id,
                txnType: 'dispense',
                baseUnit: $baseUnit,
            );

            $dispense->update([
                'status' => 'completed',
                'dispensed_at' => now(),
                'dispensed_by' => $this->currentPractitionerId(),
            ]);

            $this->recordControlledDrugIfNeeded($dispense);
            $this->closeOrderIfFullyDispensed($dispense);

            AuditLogger::record(
                action: 'update',
                table: 'dispense',
                recordId: $dispense->id,
                patientId: $dispense->patient_id,
                newValues: ['status' => 'completed', 'number' => $dispense->dispense_number],
            );

            return $dispense->fresh('items');
        });
    }

    /** Cancel a prepared dispense and put the reserved stock back. */
    public function cancel(Dispense $dispense, string $reason): Dispense
    {
        return DB::transaction(function () use ($dispense, $reason) {
            if ($dispense->status === 'completed') {
                throw new RuntimeException(
                    'A completed dispense cannot be cancelled — record a return instead.'
                );
            }

            $this->ledger->releaseAllocations('dispense', $dispense->id);

            $dispense->update(['status' => 'cancelled']);

            AuditLogger::record(
                action: 'update',
                table: 'dispense',
                recordId: $dispense->id,
                patientId: $dispense->patient_id,
                newValues: ['status' => 'cancelled', 'reason' => $reason],
            );

            return $dispense->fresh();
        });
    }

    /**
     * Controlled substances get a witnessed running register, separate from the
     * general ledger, because the legal artefact is a distinct document.
     */
    private function recordControlledDrugIfNeeded(Dispense $dispense): void
    {
        $items = DB::table('dispense_item as di')
            ->join('stock_item as si', 'si.id', '=', 'di.stock_item_id')
            ->where('di.dispense_id', $dispense->id)
            ->where('si.is_controlled', true)
            ->get(['di.stock_item_id', 'di.base_quantity', 'di.stock_lot_id']);

        foreach ($items as $item) {
            $balance = (float) (DB::table('stock_balance')
                ->where('stock_item_id', $item->stock_item_id)
                ->where('location_id', $dispense->location_id)
                ->sum('qty_on_hand') ?? 0);

            DB::table('controlled_drug_register')->insert([
                'id' => (string) Str::uuid(),
                'facility_id' => $dispense->facility_id,
                'stock_item_id' => $item->stock_item_id,
                'location_id' => $dispense->location_id,
                'entry_type' => 'issue',
                'quantity' => $item->base_quantity,
                'balance_after' => $balance,
                'patient_id' => $dispense->patient_id,
                'performed_by' => $dispense->dispensed_by,
                // The schema enforces witness <> performer, so an unwitnessed
                // controlled issue fails loudly rather than being recorded
                // as self-witnessed.
                'witnessed_by' => $dispense->witnessed_by
                    ?? throw new RuntimeException(
                        'A controlled substance requires an independent witness.'
                    ),
            ]);
        }
    }

    /** Mark the order complete once nothing remains to dispense. */
    private function closeOrderIfFullyDispensed(Dispense $dispense): void
    {
        $drugOrder = DrugOrder::find($dispense->drug_order_id);

        if ($drugOrder && $drugOrder->quantityRemaining() <= 0.000001) {
            DB::table('clinical_order')
                ->where('id', $drugOrder->id)
                ->update(['status' => 'completed', 'fulfiller_status' => 'completed']);
        }
    }

    private function currentPractitionerId(): ?string
    {
        return DB::table('practitioner')
            ->where('user_id', Auth::id())
            ->value('id');
    }
}
