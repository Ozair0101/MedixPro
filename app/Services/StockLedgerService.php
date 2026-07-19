<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

/**
 * The stock ledger — the only sanctioned way to move stock.
 *
 * INVARIANTS THIS CLASS EXISTS TO PROTECT (ADR-013)
 * -------------------------------------------------
 *  1. The ledger holds DELTAS ONLY. No running balance column, so a backdated
 *     entry is one INSERT plus one `qty = qty + delta` — order-independent,
 *     because addition commutes. Systems that store a running balance turn a
 *     backdated receipt into a full recomputation job.
 *  2. `stock_balance` is a rebuildable CACHE, never the truth. It exists for
 *     O(1) reads and for row locking. Nightly reconciliation compares it to the
 *     ledger; any drift is a bug to investigate, not a number to overwrite.
 *  3. A cycle count posts a `count_adjustment` DELTA. Never a direct UPDATE of
 *     the balance — that breaks the reconciliation identity and makes drift
 *     undetectable.
 *  4. Picking is FEFO by `removal_date` (expiry minus a safety window), not by
 *     raw expiry. Picking to raw expiry guarantees dispensing stock that
 *     expires in the patient's hands.
 *
 * CONCURRENCY
 * -----------
 * Layered, in order: the `qty_on_hand >= 0` CHECK is the backstop; row locks on
 * the lot-level balance are the primary control; `stock_allocation` holds cover
 * multi-step workflows where a lock cannot be held (scan → pharmacist verify →
 * dispense, minutes apart); idempotency keys make every write safely retryable.
 *
 * Lots are locked in a deterministic order and deadlocks are retried, because
 * two pickers taking the same lots in different orders will otherwise deadlock.
 */
class StockLedgerService
{
    private const DEADLOCK_RETRIES = 3;

    /**
     * Record a stock movement and update the cached balance atomically.
     *
     * @param  array<string, mixed>  $movement
     * @return int  the ledger row id
     */
    public function post(array $movement): int
    {
        $idempotencyKey = $movement['idempotency_key'] ?? null;

        // Scanners retry and mobile clients reconnect. Replaying a dispense
        // must not decrement stock twice.
        if ($idempotencyKey !== null) {
            $existing = DB::table('stock_ledger')
                ->where('idempotency_key', $idempotencyKey)
                ->value('id');

            if ($existing !== null) {
                return (int) $existing;
            }
        }

        return $this->withDeadlockRetry(function () use ($movement, $idempotencyKey) {
            return DB::transaction(function () use ($movement, $idempotencyKey) {
                $itemId = $movement['stock_item_id'];
                $locationId = $movement['location_id'];
                $lotId = $movement['stock_lot_id'] ?? null;
                $delta = (float) $movement['qty_delta'];

                // FOR NO KEY UPDATE rather than FOR UPDATE: it does not block
                // FOR KEY SHARE, which is what foreign-key checks from child
                // tables take, so incidental blocking is avoided.
                $balance = DB::selectOne(
                    'SELECT qty_on_hand, qty_allocated, moving_avg_cost
                       FROM stock_balance
                      WHERE stock_item_id = ? AND location_id = ?
                        AND stock_lot_id IS NOT DISTINCT FROM ?
                      FOR NO KEY UPDATE',
                    [$itemId, $locationId, $lotId]
                );

                $onHand = (float) ($balance->qty_on_hand ?? 0);

                if ($onHand + $delta < 0) {
                    throw new RuntimeException(sprintf(
                        'Insufficient stock: %s on hand, %s requested.',
                        rtrim(rtrim(number_format($onHand, 6, '.', ''), '0'), '.'),
                        rtrim(rtrim(number_format(abs($delta), 6, '.', ''), '0'), '.')
                    ));
                }

                $ledgerId = DB::table('stock_ledger')->insertGetId([
                    'facility_id' => $this->facilityId(),
                    'stock_item_id' => $itemId,
                    'location_id' => $locationId,
                    'stock_lot_id' => $lotId,
                    'qty_delta' => $delta,
                    'entered_qty' => $movement['entered_qty'] ?? null,
                    'entered_unit' => $movement['entered_unit'] ?? null,
                    'base_unit' => $movement['base_unit'],
                    'unit_cost' => $movement['unit_cost'] ?? null,
                    'txn_type' => $movement['txn_type'],
                    'posting_date' => $movement['posting_date'] ?? now()->toDateString(),
                    'voucher_type' => $movement['voucher_type'] ?? null,
                    'voucher_id' => $movement['voucher_id'] ?? null,
                    'idempotency_key' => $idempotencyKey,
                    'performed_by' => Auth::id(),
                ], 'id');

                $this->applyToBalance(
                    $itemId, $locationId, $lotId, $delta,
                    $balance, $movement['unit_cost'] ?? null
                );

                return (int) $ledgerId;
            });
        });
    }

    /**
     * Allocate stock FEFO and return the holds.
     *
     * Allocation is separate from consumption because the real workflow spans
     * minutes: a nurse scans, a pharmacist verifies, the dispense completes
     * later. Holding a row lock across that is unacceptable, so the quantity is
     * reserved instead and swept if abandoned.
     *
     * @return array<int, array{stock_lot_id: string, qty: float}>
     */
    public function allocateFefo(
        string $itemId,
        string $locationId,
        float $quantity,
        string $referenceType,
        string $referenceId,
        ?string $idempotencyKey = null,
    ): array {
        if ($quantity <= 0) {
            throw new RuntimeException('Allocation quantity must be positive.');
        }

        return $this->withDeadlockRetry(function () use (
            $itemId, $locationId, $quantity, $referenceType, $referenceId, $idempotencyKey
        ) {
            return DB::transaction(function () use (
                $itemId, $locationId, $quantity, $referenceType, $referenceId, $idempotencyKey
            ) {
                // FEFO by removal_date, expired lots excluded, and lots LOCKED
                // in a stable order so concurrent pickers cannot deadlock.
                $candidates = DB::select(
                    'SELECT b.stock_lot_id, b.qty_available
                       FROM stock_balance b
                       JOIN stock_lot l ON l.id = b.stock_lot_id
                      WHERE b.stock_item_id = ?
                        AND b.location_id = ?
                        AND b.qty_available > 0
                        AND l.lot_status = \'active\'
                        AND COALESCE(l.removal_date, l.expiry_date, \'infinity\'::date) > CURRENT_DATE
                      ORDER BY COALESCE(l.removal_date, l.expiry_date) NULLS LAST,
                               l.id
                      FOR NO KEY UPDATE OF b',
                    [$itemId, $locationId]
                );

                $available = array_sum(array_map(
                    fn ($row) => (float) $row->qty_available, $candidates
                ));

                if ($available < $quantity) {
                    throw new RuntimeException(sprintf(
                        'Insufficient unexpired stock: %s available, %s required.',
                        $available, $quantity
                    ));
                }

                $remaining = $quantity;
                $allocations = [];

                foreach ($candidates as $candidate) {
                    if ($remaining <= 0) {
                        break;
                    }

                    $take = min((float) $candidate->qty_available, $remaining);
                    $remaining -= $take;

                    DB::table('stock_allocation')->insert([
                        'id' => (string) Str::uuid(),
                        'facility_id' => $this->facilityId(),
                        'stock_item_id' => $itemId,
                        'location_id' => $locationId,
                        'stock_lot_id' => $candidate->stock_lot_id,
                        'qty' => $take,
                        'reference_type' => $referenceType,
                        'reference_id' => $referenceId,
                        'status' => 'held',
                        'idempotency_key' => $idempotencyKey
                            ? $idempotencyKey.':'.$candidate->stock_lot_id
                            : null,
                    ]);

                    DB::update(
                        'UPDATE stock_balance
                            SET qty_allocated = qty_allocated + ?, version = version + 1,
                                updated_at = now()
                          WHERE stock_item_id = ? AND location_id = ?
                            AND stock_lot_id IS NOT DISTINCT FROM ?',
                        [$take, $itemId, $locationId, $candidate->stock_lot_id]
                    );

                    $allocations[] = [
                        'stock_lot_id' => $candidate->stock_lot_id,
                        'qty' => $take,
                    ];
                }

                return $allocations;
            });
        });
    }

    /**
     * Consume held allocations, turning reservations into ledger movements.
     */
    public function consumeAllocations(
        string $referenceType,
        string $referenceId,
        string $txnType,
        string $baseUnit,
    ): int {
        return DB::transaction(function () use ($referenceType, $referenceId, $txnType, $baseUnit) {
            $holds = DB::table('stock_allocation')
                ->where('reference_type', $referenceType)
                ->where('reference_id', $referenceId)
                ->where('status', 'held')
                ->lockForUpdate()
                ->get();

            if ($holds->isEmpty()) {
                throw new RuntimeException('No held allocation found to consume.');
            }

            $count = 0;

            foreach ($holds as $hold) {
                // Release the reservation and post the movement in one step, so
                // the quantity is never counted as both allocated and consumed.
                DB::update(
                    'UPDATE stock_balance
                        SET qty_allocated = qty_allocated - ?, version = version + 1
                      WHERE stock_item_id = ? AND location_id = ?
                        AND stock_lot_id IS NOT DISTINCT FROM ?',
                    [$hold->qty, $hold->stock_item_id, $hold->location_id, $hold->stock_lot_id]
                );

                $this->post([
                    'stock_item_id' => $hold->stock_item_id,
                    'location_id' => $hold->location_id,
                    'stock_lot_id' => $hold->stock_lot_id,
                    'qty_delta' => -1 * (float) $hold->qty,
                    'base_unit' => $baseUnit,
                    'txn_type' => $txnType,
                    'voucher_type' => $referenceType,
                    'voucher_id' => $referenceId,
                    'idempotency_key' => 'consume:'.$hold->id,
                ]);

                DB::table('stock_allocation')
                    ->where('id', $hold->id)
                    ->update(['status' => 'consumed']);

                $count++;
            }

            return $count;
        });
    }

    /** Release holds that were never consumed. */
    public function releaseAllocations(string $referenceType, string $referenceId): int
    {
        return DB::transaction(function () use ($referenceType, $referenceId) {
            $holds = DB::table('stock_allocation')
                ->where('reference_type', $referenceType)
                ->where('reference_id', $referenceId)
                ->where('status', 'held')
                ->lockForUpdate()
                ->get();

            foreach ($holds as $hold) {
                DB::update(
                    'UPDATE stock_balance
                        SET qty_allocated = qty_allocated - ?, version = version + 1
                      WHERE stock_item_id = ? AND location_id = ?
                        AND stock_lot_id IS NOT DISTINCT FROM ?',
                    [$hold->qty, $hold->stock_item_id, $hold->location_id, $hold->stock_lot_id]
                );

                DB::table('stock_allocation')
                    ->where('id', $hold->id)
                    ->update(['status' => 'released']);
            }

            return $holds->count();
        });
    }

    /**
     * Sweep expired holds.
     *
     * Without this, an abandoned dispense leaves stock permanently reserved and
     * invisible — the shelf has it, the system says it does not.
     */
    public function sweepExpiredAllocations(): int
    {
        return DB::transaction(function () {
            $expired = DB::table('stock_allocation')
                ->where('status', 'held')
                ->where('expires_at', '<', now())
                ->lockForUpdate()
                ->get();

            foreach ($expired as $hold) {
                DB::update(
                    'UPDATE stock_balance
                        SET qty_allocated = qty_allocated - ?, version = version + 1
                      WHERE stock_item_id = ? AND location_id = ?
                        AND stock_lot_id IS NOT DISTINCT FROM ?',
                    [$hold->qty, $hold->stock_item_id, $hold->location_id, $hold->stock_lot_id]
                );

                DB::table('stock_allocation')
                    ->where('id', $hold->id)->update(['status' => 'expired']);
            }

            return $expired->count();
        });
    }

    /**
     * Post a cycle-count adjustment.
     *
     * A DELTA, never a direct balance UPDATE. Overwriting the balance would
     * break `balance == sum(ledger)` and make future drift undetectable.
     */
    public function postCountAdjustment(
        string $itemId,
        string $locationId,
        ?string $lotId,
        float $countedQty,
        string $baseUnit,
        ?string $reason = null,
    ): int {
        $current = (float) (DB::table('stock_balance')
            ->where('stock_item_id', $itemId)
            ->where('location_id', $locationId)
            ->when($lotId === null,
                fn ($q) => $q->whereNull('stock_lot_id'),
                fn ($q) => $q->where('stock_lot_id', $lotId))
            ->value('qty_on_hand') ?? 0);

        $delta = $countedQty - $current;

        if (abs($delta) < 0.000001) {
            return 0;   // nothing to post; the count agreed
        }

        return $this->post([
            'stock_item_id' => $itemId,
            'location_id' => $locationId,
            'stock_lot_id' => $lotId,
            'qty_delta' => $delta,
            'base_unit' => $baseUnit,
            'txn_type' => 'count_adjustment',
            'voucher_type' => 'stock_count',
            'idempotency_key' => null,
        ]);
    }

    /**
     * Rows where the cached balance disagrees with the ledger.
     *
     * Should always be empty. Anything here means a write path bypassed this
     * service, and the shelf and the screen no longer agree.
     *
     * @return array<int, object>
     */
    public function reconcile(): array
    {
        return DB::select('SELECT * FROM stock_reconciliation');
    }

    /**
     * Upsert the cached balance and roll the moving-average cost.
     *
     * Costing is moving average while PICKING is FEFO — the two are
     * independent, and conflating them is a common error. Moving average keeps
     * one number per item-location instead of a layer stack that must be
     * reposted whenever an entry is backdated.
     */
    private function applyToBalance(
        string $itemId,
        string $locationId,
        ?string $lotId,
        float $delta,
        ?object $existing,
        mixed $unitCost,
    ): void {
        if ($existing === null) {
            DB::table('stock_balance')->insert([
                'facility_id' => $this->facilityId(),
                'stock_item_id' => $itemId,
                'location_id' => $locationId,
                'stock_lot_id' => $lotId,
                'qty_on_hand' => $delta,
                'qty_allocated' => 0,
                'moving_avg_cost' => $unitCost ?? 0,
            ]);

            return;
        }

        $newCost = $existing->moving_avg_cost;

        // Only receipts move the average; issues consume at the current rate.
        if ($delta > 0 && $unitCost !== null) {
            $currentQty = (float) $existing->qty_on_hand;
            $currentValue = $currentQty * (float) $existing->moving_avg_cost;
            $incomingValue = $delta * (float) $unitCost;
            $totalQty = $currentQty + $delta;

            $newCost = $totalQty > 0
                ? ($currentValue + $incomingValue) / $totalQty
                : $unitCost;
        }

        DB::update(
            'UPDATE stock_balance
                SET qty_on_hand = qty_on_hand + ?, moving_avg_cost = ?,
                    version = version + 1, updated_at = now()
              WHERE stock_item_id = ? AND location_id = ?
                AND stock_lot_id IS NOT DISTINCT FROM ?',
            [$delta, $newCost, $itemId, $locationId, $lotId]
        );
    }

    /**
     * Retry on deadlock (SQLSTATE 40P01).
     *
     * Two pickers taking overlapping lots can deadlock even with deterministic
     * ordering, because Postgres may choose different plans. Retrying is
     * correct and cheap; failing a dispense because of a transient lock cycle
     * is not.
     */
    private function withDeadlockRetry(callable $operation): mixed
    {
        $attempt = 0;

        while (true) {
            try {
                return $operation();
            } catch (Throwable $e) {
                $isDeadlock = str_contains($e->getMessage(), '40P01')
                    || str_contains(strtolower($e->getMessage()), 'deadlock');

                if (! $isDeadlock || ++$attempt >= self::DEADLOCK_RETRIES) {
                    throw $e;
                }

                usleep(random_int(10_000, 50_000) * $attempt);
            }
        }
    }

    private function facilityId(): ?string
    {
        return DB::selectOne(
            "SELECT nullif(current_setting('app.facility_id', true), '') AS f"
        )?->f;
    }
}
