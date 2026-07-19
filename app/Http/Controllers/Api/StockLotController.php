<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStockLotRequest;
use App\Http\Resources\StockLotResource;
use App\Models\StockLot;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `stock_lot`.
 */
class StockLotController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = StockLot::query()->paginate($perPage);

        return response()->json([
            'data' => StockLotResource::collection($rows->items()),
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
        $stockLot = StockLot::findOrFail($id);

        return response()->json(['data' => new StockLotResource($stockLot)]);
    }

    public function store(StoreStockLotRequest $request): JsonResponse
    {
        $stockLot = StockLot::create($request->validated());

        AuditLogger::record('create', 'stock_lot', (string) $stockLot->getKey());

        return response()->json(
            ['data' => new StockLotResource($stockLot)], 201
        );
    }

    public function update(StoreStockLotRequest $request, string $id): JsonResponse
    {
        $stockLot = StockLot::findOrFail($id);
        $stockLot->update($request->validated());

        AuditLogger::record('update', 'stock_lot', $id);

        return response()->json(['data' => new StockLotResource($stockLot)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $stockLot = StockLot::findOrFail($id);

        $stockLot->delete();

        AuditLogger::record('delete', 'stock_lot', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
