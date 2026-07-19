<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStockCountLineRequest;
use App\Http\Resources\StockCountLineResource;
use App\Models\StockCountLine;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `stock_count_line`.
 */
class StockCountLineController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = StockCountLine::query()->paginate($perPage);

        return response()->json([
            'data' => StockCountLineResource::collection($rows->items()),
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
        $stockCountLine = StockCountLine::findOrFail($id);

        return response()->json(['data' => new StockCountLineResource($stockCountLine)]);
    }

    public function store(StoreStockCountLineRequest $request): JsonResponse
    {
        $stockCountLine = StockCountLine::create($request->validated());

        AuditLogger::record('create', 'stock_count_line', (string) $stockCountLine->getKey());

        return response()->json(
            ['data' => new StockCountLineResource($stockCountLine)], 201
        );
    }

    public function update(StoreStockCountLineRequest $request, string $id): JsonResponse
    {
        $stockCountLine = StockCountLine::findOrFail($id);
        $stockCountLine->update($request->validated());

        AuditLogger::record('update', 'stock_count_line', $id);

        return response()->json(['data' => new StockCountLineResource($stockCountLine)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $stockCountLine = StockCountLine::findOrFail($id);

        $stockCountLine->delete();

        AuditLogger::record('delete', 'stock_count_line', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
