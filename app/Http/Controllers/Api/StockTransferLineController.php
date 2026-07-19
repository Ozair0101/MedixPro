<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStockTransferLineRequest;
use App\Http\Resources\StockTransferLineResource;
use App\Models\StockTransferLine;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `stock_transfer_line`.
 */
class StockTransferLineController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = StockTransferLine::query()->paginate($perPage);

        return response()->json([
            'data' => StockTransferLineResource::collection($rows->items()),
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
        $stockTransferLine = StockTransferLine::findOrFail($id);

        return response()->json(['data' => new StockTransferLineResource($stockTransferLine)]);
    }

    public function store(StoreStockTransferLineRequest $request): JsonResponse
    {
        $stockTransferLine = StockTransferLine::create($request->validated());

        AuditLogger::record('create', 'stock_transfer_line', (string) $stockTransferLine->getKey());

        return response()->json(
            ['data' => new StockTransferLineResource($stockTransferLine)], 201
        );
    }

    public function update(StoreStockTransferLineRequest $request, string $id): JsonResponse
    {
        $stockTransferLine = StockTransferLine::findOrFail($id);
        $stockTransferLine->update($request->validated());

        AuditLogger::record('update', 'stock_transfer_line', $id);

        return response()->json(['data' => new StockTransferLineResource($stockTransferLine)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $stockTransferLine = StockTransferLine::findOrFail($id);

        $stockTransferLine->delete();

        AuditLogger::record('delete', 'stock_transfer_line', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
