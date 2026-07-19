<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStockTransferRequest;
use App\Http\Resources\StockTransferResource;
use App\Models\StockTransfer;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `stock_transfer`.
 */
class StockTransferController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = StockTransfer::query()->paginate($perPage);

        return response()->json([
            'data' => StockTransferResource::collection($rows->items()),
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
        $stockTransfer = StockTransfer::findOrFail($id);

        return response()->json(['data' => new StockTransferResource($stockTransfer)]);
    }

    public function store(StoreStockTransferRequest $request): JsonResponse
    {
        $stockTransfer = StockTransfer::create($request->validated());

        AuditLogger::record('create', 'stock_transfer', (string) $stockTransfer->getKey());

        return response()->json(
            ['data' => new StockTransferResource($stockTransfer)], 201
        );
    }

    public function update(StoreStockTransferRequest $request, string $id): JsonResponse
    {
        $stockTransfer = StockTransfer::findOrFail($id);
        $stockTransfer->update($request->validated());

        AuditLogger::record('update', 'stock_transfer', $id);

        return response()->json(['data' => new StockTransferResource($stockTransfer)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $stockTransfer = StockTransfer::findOrFail($id);

        $stockTransfer->delete();

        AuditLogger::record('delete', 'stock_transfer', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
