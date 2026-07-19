<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDrugOrderRequest;
use App\Http\Resources\DrugOrderResource;
use App\Models\DrugOrder;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `drug_order`.
 */
class DrugOrderController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = DrugOrder::query()->paginate($perPage);

        return response()->json([
            'data' => DrugOrderResource::collection($rows->items()),
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
        $drugOrder = DrugOrder::findOrFail($id);

        return response()->json(['data' => new DrugOrderResource($drugOrder)]);
    }

    public function store(StoreDrugOrderRequest $request): JsonResponse
    {
        $drugOrder = DrugOrder::create($request->validated());

        AuditLogger::record('create', 'drug_order', (string) $drugOrder->getKey());

        return response()->json(
            ['data' => new DrugOrderResource($drugOrder)], 201
        );
    }

    public function update(StoreDrugOrderRequest $request, string $id): JsonResponse
    {
        $drugOrder = DrugOrder::findOrFail($id);
        $drugOrder->update($request->validated());

        AuditLogger::record('update', 'drug_order', $id);

        return response()->json(['data' => new DrugOrderResource($drugOrder)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $drugOrder = DrugOrder::findOrFail($id);

        $drugOrder->delete();

        AuditLogger::record('delete', 'drug_order', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
