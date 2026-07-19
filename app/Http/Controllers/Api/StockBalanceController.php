<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStockBalanceRequest;
use App\Http\Resources\StockBalanceResource;
use App\Models\StockBalance;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `stock_balance`.
 */
class StockBalanceController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = StockBalance::query()->paginate($perPage);

        return response()->json([
            'data' => StockBalanceResource::collection($rows->items()),
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
        $stockBalance = StockBalance::findOrFail($id);

        return response()->json(['data' => new StockBalanceResource($stockBalance)]);
    }

    public function store(StoreStockBalanceRequest $request): JsonResponse
    {
        $stockBalance = StockBalance::create($request->validated());

        AuditLogger::record('create', 'stock_balance', (string) $stockBalance->getKey());

        return response()->json(
            ['data' => new StockBalanceResource($stockBalance)], 201
        );
    }

    public function update(StoreStockBalanceRequest $request, string $id): JsonResponse
    {
        $stockBalance = StockBalance::findOrFail($id);
        $stockBalance->update($request->validated());

        AuditLogger::record('update', 'stock_balance', $id);

        return response()->json(['data' => new StockBalanceResource($stockBalance)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $stockBalance = StockBalance::findOrFail($id);

        $stockBalance->delete();

        AuditLogger::record('delete', 'stock_balance', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
