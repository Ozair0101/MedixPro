<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStockAllocationRequest;
use App\Http\Resources\StockAllocationResource;
use App\Models\StockAllocation;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `stock_allocation`.
 */
class StockAllocationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = StockAllocation::query()->paginate($perPage);

        return response()->json([
            'data' => StockAllocationResource::collection($rows->items()),
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
        $stockAllocation = StockAllocation::findOrFail($id);

        return response()->json(['data' => new StockAllocationResource($stockAllocation)]);
    }

    public function store(StoreStockAllocationRequest $request): JsonResponse
    {
        $stockAllocation = StockAllocation::create($request->validated());

        AuditLogger::record('create', 'stock_allocation', (string) $stockAllocation->getKey());

        return response()->json(
            ['data' => new StockAllocationResource($stockAllocation)], 201
        );
    }

    public function update(StoreStockAllocationRequest $request, string $id): JsonResponse
    {
        $stockAllocation = StockAllocation::findOrFail($id);
        $stockAllocation->update($request->validated());

        AuditLogger::record('update', 'stock_allocation', $id);

        return response()->json(['data' => new StockAllocationResource($stockAllocation)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $stockAllocation = StockAllocation::findOrFail($id);

        $stockAllocation->delete();

        AuditLogger::record('delete', 'stock_allocation', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
