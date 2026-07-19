<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStockRequisitionLineRequest;
use App\Http\Resources\StockRequisitionLineResource;
use App\Models\StockRequisitionLine;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `stock_requisition_line`.
 */
class StockRequisitionLineController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = StockRequisitionLine::query()->paginate($perPage);

        return response()->json([
            'data' => StockRequisitionLineResource::collection($rows->items()),
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
        $stockRequisitionLine = StockRequisitionLine::findOrFail($id);

        return response()->json(['data' => new StockRequisitionLineResource($stockRequisitionLine)]);
    }

    public function store(StoreStockRequisitionLineRequest $request): JsonResponse
    {
        $stockRequisitionLine = StockRequisitionLine::create($request->validated());

        AuditLogger::record('create', 'stock_requisition_line', (string) $stockRequisitionLine->getKey());

        return response()->json(
            ['data' => new StockRequisitionLineResource($stockRequisitionLine)], 201
        );
    }

    public function update(StoreStockRequisitionLineRequest $request, string $id): JsonResponse
    {
        $stockRequisitionLine = StockRequisitionLine::findOrFail($id);
        $stockRequisitionLine->update($request->validated());

        AuditLogger::record('update', 'stock_requisition_line', $id);

        return response()->json(['data' => new StockRequisitionLineResource($stockRequisitionLine)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $stockRequisitionLine = StockRequisitionLine::findOrFail($id);

        $stockRequisitionLine->delete();

        AuditLogger::record('delete', 'stock_requisition_line', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
