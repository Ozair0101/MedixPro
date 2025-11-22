<?php

namespace App\Http\Controllers\Pharmacy;

use App\Http\Controllers\Controller;
use App\Models\MedicationBatch;
use Illuminate\Http\Request;

class BatchController extends Controller
{
    public function index(Request $request)
    {
        $hospitalId = (int) $request->header('X-Hospital-Id', 1);

        $query = MedicationBatch::query()->where('hospital_id', $hospitalId)->with('medication');

        if ($medicationId = $request->query('medication_id')) {
            $query->where('medication_id', $medicationId);
        }

        if ($request->boolean('only_active')) {
            $query->where('quantity', '>', 0);
        }

        $perPage = (int) $request->query('per_page', 50);

        $paginator = $query->orderBy('expiry_date')->paginate($perPage);

        return response()->json([
            'data' => $paginator->items(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'medication_id' => ['required', 'integer'],
            'batch_no' => ['required', 'string', 'max:100'],
            'expiry_date' => ['required', 'date'],
            'quantity' => ['required', 'integer', 'min:0'],
            'cost_price' => ['required', 'numeric', 'min:0'],
            'selling_price' => ['required', 'numeric', 'min:0'],
        ]);

        $validated['hospital_id'] = (int) $request->header('X-Hospital-Id', 1);

        $batch = MedicationBatch::create($validated);

        return response()->json($batch, 201);
    }

    public function update(Request $request, int $id)
    {
        $hospitalId = (int) $request->header('X-Hospital-Id', 1);

        $batch = MedicationBatch::query()
            ->where('hospital_id', $hospitalId)
            ->findOrFail($id);

        $validated = $request->validate([
            'expiry_date' => ['sometimes', 'date'],
            'quantity' => ['sometimes', 'integer', 'min:0'],
            'cost_price' => ['sometimes', 'numeric', 'min:0'],
            'selling_price' => ['sometimes', 'numeric', 'min:0'],
        ]);

        $batch->update($validated);

        return response()->json($batch);
    }
}
