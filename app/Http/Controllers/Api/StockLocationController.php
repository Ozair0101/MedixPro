<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStockLocationRequest;
use App\Http\Resources\StockLocationResource;
use App\Models\StockLocation;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `stock_location`.
 */
class StockLocationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = StockLocation::query()->paginate($perPage);

        return response()->json([
            'data' => StockLocationResource::collection($rows->items()),
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
        $stockLocation = StockLocation::findOrFail($id);

        return response()->json(['data' => new StockLocationResource($stockLocation)]);
    }

    public function store(StoreStockLocationRequest $request): JsonResponse
    {
        $stockLocation = StockLocation::create($request->validated());

        AuditLogger::record('create', 'stock_location', (string) $stockLocation->getKey());

        return response()->json(
            ['data' => new StockLocationResource($stockLocation)], 201
        );
    }

    public function update(StoreStockLocationRequest $request, string $id): JsonResponse
    {
        $stockLocation = StockLocation::findOrFail($id);
        $stockLocation->update($request->validated());

        AuditLogger::record('update', 'stock_location', $id);

        return response()->json(['data' => new StockLocationResource($stockLocation)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $stockLocation = StockLocation::findOrFail($id);

        // Deactivate rather than delete: clinical and financial records
        // must stay resolvable for anything that already references them.
        $stockLocation->update(['is_active' => false]);

        AuditLogger::record('delete', 'stock_location', $id);

        return response()->json(['message' => 'Deactivated.']);
    }
}
