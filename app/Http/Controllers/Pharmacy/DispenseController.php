<?php

namespace App\Http\Controllers\Pharmacy;

use App\Http\Controllers\Controller;
use App\Models\Dispense;
use App\Models\MedicationBatch;
use App\Models\PrescriptionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DispenseController extends Controller
{
    public function store(Request $request, int $id)
    {
        $hospitalId = (int) $request->header('X-Hospital-Id', 1);

        $validated = $request->validate([
            'items' => ['required', 'array'],
            'items.*.prescription_item_id' => ['required', 'integer'],
            'items.*.batch_id' => ['required', 'integer'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ]);

        $userId = optional($request->user())->id;

        return DB::transaction(function () use ($validated, $hospitalId, $userId) {
            $dispenses = [];

            foreach ($validated['items'] as $row) {
                /** @var PrescriptionItem $item */
                $item = PrescriptionItem::query()
                    ->where('hospital_id', $hospitalId)
                    ->findOrFail($row['prescription_item_id']);

                /** @var MedicationBatch $batch */
                $batch = MedicationBatch::query()
                    ->where('hospital_id', $hospitalId)
                    ->where('medication_id', $item->medication_id)
                    ->lockForUpdate()
                    ->findOrFail($row['batch_id']);

                $remaining = $item->quantity_prescribed - $item->quantity_dispensed;
                $qty = $row['quantity'];

                if ($qty > $remaining) {
                    abort(422, 'Dispense quantity exceeds remaining prescription quantity.');
                }

                if ($qty > $batch->quantity) {
                    abort(422, 'Dispense quantity exceeds available stock in batch.');
                }

                $item->quantity_dispensed += $qty;
                $item->status = $item->quantity_dispensed >= $item->quantity_prescribed ? 'Dispensed' : 'Partial';
                $item->save();

                $batch->quantity -= $qty;
                $batch->save();

                $dispenses[] = Dispense::create([
                    'prescription_item_id' => $item->id,
                    'medication_id' => $item->medication_id,
                    'batch_id' => $batch->id,
                    'quantity' => $qty,
                    'pharmacist_id' => $userId,
                    'hospital_id' => $hospitalId,
                ]);
            }

            return response()->json($dispenses, 201);
        });
    }
}
