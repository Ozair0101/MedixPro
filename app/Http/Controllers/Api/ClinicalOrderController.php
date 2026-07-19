<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreClinicalOrderRequest;
use App\Http\Resources\ClinicalOrderResource;
use App\Models\ClinicalOrder;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `clinical_order`.
 */
class ClinicalOrderController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = ClinicalOrder::query()->paginate($perPage);

        return response()->json([
            'data' => ClinicalOrderResource::collection($rows->items()),
            'meta' => [
                'current_page' => $rows->currentPage(),
                'last_page' => $rows->lastPage(),
                'per_page' => $rows->perPage(),
                'total' => $rows->total(),
            ],
        ]);
    }

    public function show(string $id): JsonResponse
    {
        $clinicalOrder = ClinicalOrder::findOrFail($id);

        return response()->json(['data' => new ClinicalOrderResource($clinicalOrder)]);
    }

    public function store(StoreClinicalOrderRequest $request): JsonResponse
    {
        $clinicalOrder = ClinicalOrder::create($request->validated());

        AuditLogger::record('create', 'clinical_order', (string) $clinicalOrder->getKey());

        return response()->json(
            ['data' => new ClinicalOrderResource($clinicalOrder)], 201
        );
    }

    public function update(StoreClinicalOrderRequest $request, string $id): JsonResponse
    {
        $clinicalOrder = ClinicalOrder::findOrFail($id);
        $clinicalOrder->update($request->validated());

        AuditLogger::record('update', 'clinical_order', $id);

        return response()->json(['data' => new ClinicalOrderResource($clinicalOrder)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $clinicalOrder = ClinicalOrder::findOrFail($id);

        $clinicalOrder->delete();

        AuditLogger::record('delete', 'clinical_order', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
