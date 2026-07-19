<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGoodsReceiptRequest;
use App\Http\Resources\GoodsReceiptResource;
use App\Models\GoodsReceipt;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `goods_receipt`.
 */
class GoodsReceiptController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = GoodsReceipt::query()->paginate($perPage);

        return response()->json([
            'data' => GoodsReceiptResource::collection($rows->items()),
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
        $goodsReceipt = GoodsReceipt::findOrFail($id);

        return response()->json(['data' => new GoodsReceiptResource($goodsReceipt)]);
    }

    public function store(StoreGoodsReceiptRequest $request): JsonResponse
    {
        $goodsReceipt = GoodsReceipt::create($request->validated());

        AuditLogger::record('create', 'goods_receipt', (string) $goodsReceipt->getKey());

        return response()->json(
            ['data' => new GoodsReceiptResource($goodsReceipt)], 201
        );
    }

    public function update(StoreGoodsReceiptRequest $request, string $id): JsonResponse
    {
        $goodsReceipt = GoodsReceipt::findOrFail($id);
        $goodsReceipt->update($request->validated());

        AuditLogger::record('update', 'goods_receipt', $id);

        return response()->json(['data' => new GoodsReceiptResource($goodsReceipt)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $goodsReceipt = GoodsReceipt::findOrFail($id);

        $goodsReceipt->delete();

        AuditLogger::record('delete', 'goods_receipt', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
