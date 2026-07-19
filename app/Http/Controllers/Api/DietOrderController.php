<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDietOrderRequest;
use App\Http\Resources\DietOrderResource;
use App\Models\DietOrder;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `diet_order`.
 */
class DietOrderController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = DietOrder::query()->paginate($perPage);

        return response()->json([
            'data' => DietOrderResource::collection($rows->items()),
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
        $dietOrder = DietOrder::findOrFail($id);

        return response()->json(['data' => new DietOrderResource($dietOrder)]);
    }

    public function store(StoreDietOrderRequest $request): JsonResponse
    {
        $dietOrder = DietOrder::create($request->validated());

        AuditLogger::record('create', 'diet_order', (string) $dietOrder->getKey());

        return response()->json(
            ['data' => new DietOrderResource($dietOrder)], 201
        );
    }

    public function update(StoreDietOrderRequest $request, string $id): JsonResponse
    {
        $dietOrder = DietOrder::findOrFail($id);
        $dietOrder->update($request->validated());

        AuditLogger::record('update', 'diet_order', $id);

        return response()->json(['data' => new DietOrderResource($dietOrder)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $dietOrder = DietOrder::findOrFail($id);

        $dietOrder->delete();

        AuditLogger::record('delete', 'diet_order', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
