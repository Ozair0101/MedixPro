<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGoodsReceiptLineRequest;
use App\Http\Resources\GoodsReceiptLineResource;
use App\Models\GoodsReceiptLine;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `goods_receipt_line`.
 */
class GoodsReceiptLineController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = GoodsReceiptLine::query()->paginate($perPage);

        return response()->json([
            'data' => GoodsReceiptLineResource::collection($rows->items()),
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
        $goodsReceiptLine = GoodsReceiptLine::findOrFail($id);

        return response()->json(['data' => new GoodsReceiptLineResource($goodsReceiptLine)]);
    }

    public function store(StoreGoodsReceiptLineRequest $request): JsonResponse
    {
        $goodsReceiptLine = GoodsReceiptLine::create($request->validated());

        AuditLogger::record('create', 'goods_receipt_line', (string) $goodsReceiptLine->getKey());

        return response()->json(
            ['data' => new GoodsReceiptLineResource($goodsReceiptLine)], 201
        );
    }

    public function update(StoreGoodsReceiptLineRequest $request, string $id): JsonResponse
    {
        $goodsReceiptLine = GoodsReceiptLine::findOrFail($id);
        $goodsReceiptLine->update($request->validated());

        AuditLogger::record('update', 'goods_receipt_line', $id);

        return response()->json(['data' => new GoodsReceiptLineResource($goodsReceiptLine)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $goodsReceiptLine = GoodsReceiptLine::findOrFail($id);

        $goodsReceiptLine->delete();

        AuditLogger::record('delete', 'goods_receipt_line', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
