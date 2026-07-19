<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderTypeRequest;
use App\Http\Resources\OrderTypeResource;
use App\Models\OrderType;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `order_type`.
 */
class OrderTypeController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = OrderType::query()->paginate($perPage);

        return response()->json([
            'data' => OrderTypeResource::collection($rows->items()),
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
        $orderType = OrderType::findOrFail($id);

        return response()->json(['data' => new OrderTypeResource($orderType)]);
    }

    public function store(StoreOrderTypeRequest $request): JsonResponse
    {
        $orderType = OrderType::create($request->validated());

        AuditLogger::record('create', 'order_type', (string) $orderType->getKey());

        return response()->json(
            ['data' => new OrderTypeResource($orderType)], 201
        );
    }

    public function update(StoreOrderTypeRequest $request, string $id): JsonResponse
    {
        $orderType = OrderType::findOrFail($id);
        $orderType->update($request->validated());

        AuditLogger::record('update', 'order_type', $id);

        return response()->json(['data' => new OrderTypeResource($orderType)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $orderType = OrderType::findOrFail($id);

        $orderType->delete();

        AuditLogger::record('delete', 'order_type', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
