<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAdjustmentRequest;
use App\Http\Resources\AdjustmentResource;
use App\Models\Adjustment;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `adjustment`.
 */
class AdjustmentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = Adjustment::query()->paginate($perPage);

        return response()->json([
            'data' => AdjustmentResource::collection($rows->items()),
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
        $adjustment = Adjustment::findOrFail($id);

        return response()->json(['data' => new AdjustmentResource($adjustment)]);
    }

    public function store(StoreAdjustmentRequest $request): JsonResponse
    {
        $adjustment = Adjustment::create($request->validated());

        AuditLogger::record('create', 'adjustment', (string) $adjustment->getKey());

        return response()->json(
            ['data' => new AdjustmentResource($adjustment)], 201
        );
    }

    public function update(StoreAdjustmentRequest $request, string $id): JsonResponse
    {
        $adjustment = Adjustment::findOrFail($id);
        $adjustment->update($request->validated());

        AuditLogger::record('update', 'adjustment', $id);

        return response()->json(['data' => new AdjustmentResource($adjustment)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $adjustment = Adjustment::findOrFail($id);

        $adjustment->delete();

        AuditLogger::record('delete', 'adjustment', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
