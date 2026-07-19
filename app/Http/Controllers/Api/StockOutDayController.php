<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStockOutDayRequest;
use App\Http\Resources\StockOutDayResource;
use App\Models\StockOutDay;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `stock_out_day`.
 */
class StockOutDayController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = StockOutDay::query()->paginate($perPage);

        return response()->json([
            'data' => StockOutDayResource::collection($rows->items()),
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
        $stockOutDay = StockOutDay::findOrFail($id);

        return response()->json(['data' => new StockOutDayResource($stockOutDay)]);
    }

    public function store(StoreStockOutDayRequest $request): JsonResponse
    {
        $stockOutDay = StockOutDay::create($request->validated());

        AuditLogger::record('create', 'stock_out_day', (string) $stockOutDay->getKey());

        return response()->json(
            ['data' => new StockOutDayResource($stockOutDay)], 201
        );
    }

    public function update(StoreStockOutDayRequest $request, string $id): JsonResponse
    {
        $stockOutDay = StockOutDay::findOrFail($id);
        $stockOutDay->update($request->validated());

        AuditLogger::record('update', 'stock_out_day', $id);

        return response()->json(['data' => new StockOutDayResource($stockOutDay)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $stockOutDay = StockOutDay::findOrFail($id);

        $stockOutDay->delete();

        AuditLogger::record('delete', 'stock_out_day', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
