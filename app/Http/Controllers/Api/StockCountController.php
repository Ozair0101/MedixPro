<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStockCountRequest;
use App\Http\Resources\StockCountResource;
use App\Models\StockCount;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `stock_count`.
 */
class StockCountController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = StockCount::query()->paginate($perPage);

        return response()->json([
            'data' => StockCountResource::collection($rows->items()),
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
        $stockCount = StockCount::findOrFail($id);

        return response()->json(['data' => new StockCountResource($stockCount)]);
    }

    public function store(StoreStockCountRequest $request): JsonResponse
    {
        $stockCount = StockCount::create($request->validated());

        AuditLogger::record('create', 'stock_count', (string) $stockCount->getKey());

        return response()->json(
            ['data' => new StockCountResource($stockCount)], 201
        );
    }

    public function update(StoreStockCountRequest $request, string $id): JsonResponse
    {
        $stockCount = StockCount::findOrFail($id);
        $stockCount->update($request->validated());

        AuditLogger::record('update', 'stock_count', $id);

        return response()->json(['data' => new StockCountResource($stockCount)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $stockCount = StockCount::findOrFail($id);

        $stockCount->delete();

        AuditLogger::record('delete', 'stock_count', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
