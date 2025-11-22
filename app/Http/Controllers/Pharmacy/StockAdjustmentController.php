<?php

namespace App\Http\Controllers\Pharmacy;

use App\Http\Controllers\Controller;
use App\Models\MedicationBatch;
use App\Models\StockAdjustment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockAdjustmentController extends Controller
{
    public function store(Request $request)
    {
        $hospitalId = (int) $request->header('X-Hospital-Id', 1);

        $validated = $request->validate([
            'medication_id' => ['required', 'integer'],
            'batch_id' => ['nullable', 'integer'],
            'type' => ['required', 'in:increase,decrease,expiry,damage'],
            'quantity' => ['required', 'integer', 'min:1'],
            'reason' => ['nullable', 'string', 'max:255'],
        ]);

        $validated['hospital_id'] = $hospitalId;
        $validated['created_by'] = optional($request->user())->id;

        return DB::transaction(function () use ($validated, $hospitalId) {
            $batchQuery = MedicationBatch::query()
                ->where('hospital_id', $hospitalId)
                ->where('medication_id', $validated['medication_id']);

            if (! empty($validated['batch_id'])) {
                $batchQuery->where('id', $validated['batch_id']);
            }

            /** @var MedicationBatch|null $batch */
            $batch = $batchQuery->lockForUpdate()->first();

            if (! $batch) {
                abort(422, 'Batch not found for adjustment.');
            }

            $qty = $validated['quantity'];

            if (in_array($validated['type'], ['decrease', 'expiry', 'damage'], true)) {
                if ($batch->quantity < $qty) {
                    abort(422, 'Cannot decrease more than available quantity.');
                }

                $batch->quantity -= $qty;
            } else {
                $batch->quantity += $qty;
            }

            $batch->save();

            $adjustment = StockAdjustment::create($validated);

            return response()->json($adjustment, 201);
        });
    }
}
