<?php

namespace App\Http\Controllers;

use App\Models\Dispense;
use App\Services\DispenseService;
use App\Services\StockLedgerService;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

/**
 * Dispensing and stock queries.
 *
 * Replaces the pre-migration pharmacy controllers, which decremented a batch
 * quantity column directly. Every movement now goes through the append-only
 * ledger.
 */
class DispenseController extends Controller
{
    public function __construct(
        private readonly DispenseService $dispensing,
        private readonly StockLedgerService $ledger,
    ) {}

    /** Step one: reserve stock FEFO. Nothing has left the shelf yet. */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'drug_order_id' => ['required', 'uuid', 'exists:drug_order,id'],
            'stock_item_id' => ['required', 'uuid', 'exists:stock_item,id'],
            'location_id' => ['required', 'uuid', 'exists:stock_location,id'],
            'unit_id' => ['required', 'uuid', 'exists:unit,id'],
            'quantity' => ['required', 'numeric', 'gt:0'],
            'dispense_type' => ['nullable', Rule::in([
                'outpatient', 'inpatient_unit_dose', 'ward_stock', 'discharge',
                'over_the_counter',
            ])],
            'screened_interactions' => ['nullable', 'boolean'],
            'screened_allergy' => ['nullable', 'boolean'],
            'screening_overridden' => ['nullable', 'boolean'],
            // Mandatory when an interaction or allergy alert was overridden:
            // an override with no recorded justification is indistinguishable
            // from a mistake.
            'override_reason' => ['nullable', 'required_if:screening_overridden,true',
                'string', 'min:5', 'max:500'],
        ]);

        $dispense = $this->dispensing->prepare($validated);

        return response()->json([
            'data' => $this->present($dispense->load('items')),
            'message' => 'Stock reserved. Complete the dispense to hand it over.',
        ], 201);
    }

    /** Step two: consume the reservations and record the hand-over. */
    public function complete(Request $request, string $id): JsonResponse
    {
        $validated = $request->validate([
            'base_unit' => ['required', 'uuid', 'exists:unit,id'],
            'counselled' => ['nullable', 'boolean'],
            // Controlled substances require an independent witness; the
            // database enforces witness <> performer.
            'witnessed_by' => ['nullable', 'uuid', 'exists:practitioner,id'],
        ]);

        $dispense = Dispense::findOrFail($id);

        if (! empty($validated['witnessed_by'])) {
            $dispense->update(['witnessed_by' => $validated['witnessed_by']]);
        }

        $completed = $this->dispensing->complete($dispense, $validated['base_unit']);

        return response()->json(['data' => $this->present($completed->load('items'))]);
    }

    public function cancel(Request $request, string $id): JsonResponse
    {
        $validated = $request->validate([
            'reason' => ['required', 'string', 'min:5', 'max:500'],
        ]);

        $dispense = $this->dispensing->cancel(Dispense::findOrFail($id), $validated['reason']);

        return response()->json([
            'data' => $this->present($dispense),
            'message' => 'Dispense cancelled and reserved stock released.',
        ]);
    }

    public function show(string $id): JsonResponse
    {
        $dispense = Dispense::with('items')->findOrFail($id);

        AuditLogger::recordRead('dispense', $dispense->id, $dispense->patient_id);

        return response()->json(['data' => $this->present($dispense)]);
    }

    /** On-hand and available stock by item and location. */
    public function stockBalance(Request $request): JsonResponse
    {
        $rows = DB::table('stock_balance as b')
            ->join('stock_item as i', 'i.id', '=', 'b.stock_item_id')
            ->join('stock_location as l', 'l.id', '=', 'b.location_id')
            ->leftJoin('stock_lot as lot', 'lot.id', '=', 'b.stock_lot_id')
            ->when($request->query('location_id'),
                fn ($q, $v) => $q->where('b.location_id', $v))
            ->when($request->query('item_id'),
                fn ($q, $v) => $q->where('b.stock_item_id', $v))
            ->where('b.qty_on_hand', '>', 0)
            ->orderBy('i.name_latin')
            ->limit(500)
            ->get([
                'i.item_code', 'i.name_local', 'i.name_latin', 'l.name_latin as location',
                'lot.lot_number', 'lot.expiry_date', 'lot.removal_date',
                'b.qty_on_hand', 'b.qty_allocated', 'b.qty_available',
            ]);

        return response()->json(['data' => $rows]);
    }

    /**
     * Lots approaching expiry.
     *
     * Reported against removal_date, not expiry_date. Stock is withdrawn a
     * safety window before it actually expires, because dispensing something
     * that expires in the patient's hands is a failure even though the label
     * was technically in date.
     */
    public function expiring(Request $request): JsonResponse
    {
        $days = min((int) $request->query('days', 90), 365);

        $rows = DB::table('stock_lot as lot')
            ->join('stock_item as i', 'i.id', '=', 'lot.stock_item_id')
            ->leftJoin('stock_balance as b', 'b.stock_lot_id', '=', 'lot.id')
            ->where('lot.lot_status', 'active')
            ->whereNotNull('lot.expiry_date')
            ->whereRaw('COALESCE(lot.removal_date, lot.expiry_date) <= CURRENT_DATE + ?::int',
                [$days])
            ->where('b.qty_on_hand', '>', 0)
            ->orderBy('lot.expiry_date')
            ->limit(500)
            ->get([
                'i.item_code', 'i.name_local', 'i.name_latin',
                'lot.lot_number', 'lot.expiry_date', 'lot.removal_date',
                'b.qty_on_hand',
            ]);

        return response()->json(['data' => $rows]);
    }

    /**
     * Ledger-versus-balance drift.
     *
     * Should always be empty. A non-empty result means a write path bypassed
     * the ledger service and the shelf no longer matches the screen — it is a
     * bug to investigate, never a number to overwrite.
     */
    public function reconciliation(): JsonResponse
    {
        $drift = $this->ledger->reconcile();

        return response()->json([
            'data' => $drift,
            'is_balanced' => $drift === [],
        ]);
    }

    private function present(Dispense $dispense): array
    {
        return [
            'id' => $dispense->id,
            'dispense_number' => $dispense->dispense_number,
            'patient_id' => $dispense->patient_id,
            'drug_order_id' => $dispense->drug_order_id,
            'status' => $dispense->status,
            'dispense_type' => $dispense->dispense_type,
            'dispensed_at' => $dispense->dispensed_at?->toIso8601String(),
            'screening_overridden' => (bool) $dispense->screening_overridden,
            'override_reason' => $dispense->override_reason,
            'items' => $dispense->relationLoaded('items')
                ? $dispense->items->map(fn ($i) => [
                    'stock_item_id' => $i->stock_item_id,
                    // The lot is on the line, not the header: FEFO routinely
                    // spans several lots, and a recall must be answerable.
                    'stock_lot_id' => $i->stock_lot_id,
                    'quantity' => (float) $i->quantity,
                ])
                : null,
        ];
    }
}
