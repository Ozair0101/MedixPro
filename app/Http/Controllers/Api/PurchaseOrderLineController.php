<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePurchaseOrderLineRequest;
use App\Http\Resources\PurchaseOrderLineResource;
use App\Models\PurchaseOrderLine;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `purchase_order_line`.
 */
class PurchaseOrderLineController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = PurchaseOrderLine::query()->paginate($perPage);

        return response()->json([
            'data' => PurchaseOrderLineResource::collection($rows->items()),
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
        $purchaseOrderLine = PurchaseOrderLine::findOrFail($id);

        return response()->json(['data' => new PurchaseOrderLineResource($purchaseOrderLine)]);
    }

    public function store(StorePurchaseOrderLineRequest $request): JsonResponse
    {
        $purchaseOrderLine = PurchaseOrderLine::create($request->validated());

        AuditLogger::record('create', 'purchase_order_line', (string) $purchaseOrderLine->getKey());

        return response()->json(
            ['data' => new PurchaseOrderLineResource($purchaseOrderLine)], 201
        );
    }

    public function update(StorePurchaseOrderLineRequest $request, string $id): JsonResponse
    {
        $purchaseOrderLine = PurchaseOrderLine::findOrFail($id);
        $purchaseOrderLine->update($request->validated());

        AuditLogger::record('update', 'purchase_order_line', $id);

        return response()->json(['data' => new PurchaseOrderLineResource($purchaseOrderLine)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $purchaseOrderLine = PurchaseOrderLine::findOrFail($id);

        $purchaseOrderLine->delete();

        AuditLogger::record('delete', 'purchase_order_line', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
