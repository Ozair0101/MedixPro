<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStockRequisitionRequest;
use App\Http\Resources\StockRequisitionResource;
use App\Models\StockRequisition;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `stock_requisition`.
 */
class StockRequisitionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = StockRequisition::query()->paginate($perPage);

        return response()->json([
            'data' => StockRequisitionResource::collection($rows->items()),
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
        $stockRequisition = StockRequisition::findOrFail($id);

        return response()->json(['data' => new StockRequisitionResource($stockRequisition)]);
    }

    public function store(StoreStockRequisitionRequest $request): JsonResponse
    {
        $stockRequisition = StockRequisition::create($request->validated());

        AuditLogger::record('create', 'stock_requisition', (string) $stockRequisition->getKey());

        return response()->json(
            ['data' => new StockRequisitionResource($stockRequisition)], 201
        );
    }

    public function update(StoreStockRequisitionRequest $request, string $id): JsonResponse
    {
        $stockRequisition = StockRequisition::findOrFail($id);
        $stockRequisition->update($request->validated());

        AuditLogger::record('update', 'stock_requisition', $id);

        return response()->json(['data' => new StockRequisitionResource($stockRequisition)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $stockRequisition = StockRequisition::findOrFail($id);

        $stockRequisition->delete();

        AuditLogger::record('delete', 'stock_requisition', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
