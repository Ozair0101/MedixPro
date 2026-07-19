<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStockItemRequest;
use App\Http\Resources\StockItemResource;
use App\Models\StockItem;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `stock_item`.
 */
class StockItemController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = StockItem::query()->paginate($perPage);

        return response()->json([
            'data' => StockItemResource::collection($rows->items()),
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
        $stockItem = StockItem::findOrFail($id);

        return response()->json(['data' => new StockItemResource($stockItem)]);
    }

    public function store(StoreStockItemRequest $request): JsonResponse
    {
        $stockItem = StockItem::create($request->validated());

        AuditLogger::record('create', 'stock_item', (string) $stockItem->getKey());

        return response()->json(
            ['data' => new StockItemResource($stockItem)], 201
        );
    }

    public function update(StoreStockItemRequest $request, string $id): JsonResponse
    {
        $stockItem = StockItem::findOrFail($id);
        $stockItem->update($request->validated());

        AuditLogger::record('update', 'stock_item', $id);

        return response()->json(['data' => new StockItemResource($stockItem)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $stockItem = StockItem::findOrFail($id);

        // Deactivate rather than delete: clinical and financial records
        // must stay resolvable for anything that already references them.
        $stockItem->update(['is_active' => false]);

        AuditLogger::record('delete', 'stock_item', $id);

        return response()->json(['message' => 'Deactivated.']);
    }
}
