<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePurchaseOrderRequest;
use App\Http\Resources\PurchaseOrderResource;
use App\Models\PurchaseOrder;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `purchase_order`.
 */
class PurchaseOrderController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = PurchaseOrder::query()->paginate($perPage);

        return response()->json([
            'data' => PurchaseOrderResource::collection($rows->items()),
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
        $purchaseOrder = PurchaseOrder::findOrFail($id);

        return response()->json(['data' => new PurchaseOrderResource($purchaseOrder)]);
    }

    public function store(StorePurchaseOrderRequest $request): JsonResponse
    {
        $purchaseOrder = PurchaseOrder::create($request->validated());

        AuditLogger::record('create', 'purchase_order', (string) $purchaseOrder->getKey());

        return response()->json(
            ['data' => new PurchaseOrderResource($purchaseOrder)], 201
        );
    }

    public function update(StorePurchaseOrderRequest $request, string $id): JsonResponse
    {
        $purchaseOrder = PurchaseOrder::findOrFail($id);
        $purchaseOrder->update($request->validated());

        AuditLogger::record('update', 'purchase_order', $id);

        return response()->json(['data' => new PurchaseOrderResource($purchaseOrder)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $purchaseOrder = PurchaseOrder::findOrFail($id);

        $purchaseOrder->delete();

        AuditLogger::record('delete', 'purchase_order', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
