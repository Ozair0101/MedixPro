<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAdjustmentReasonRequest;
use App\Http\Resources\AdjustmentReasonResource;
use App\Models\AdjustmentReason;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `adjustment_reason`.
 */
class AdjustmentReasonController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = AdjustmentReason::query()->paginate($perPage);

        return response()->json([
            'data' => AdjustmentReasonResource::collection($rows->items()),
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
        $adjustmentReason = AdjustmentReason::findOrFail($id);

        return response()->json(['data' => new AdjustmentReasonResource($adjustmentReason)]);
    }

    public function store(StoreAdjustmentReasonRequest $request): JsonResponse
    {
        $adjustmentReason = AdjustmentReason::create($request->validated());

        AuditLogger::record('create', 'adjustment_reason', (string) $adjustmentReason->getKey());

        return response()->json(
            ['data' => new AdjustmentReasonResource($adjustmentReason)], 201
        );
    }

    public function update(StoreAdjustmentReasonRequest $request, string $id): JsonResponse
    {
        $adjustmentReason = AdjustmentReason::findOrFail($id);
        $adjustmentReason->update($request->validated());

        AuditLogger::record('update', 'adjustment_reason', $id);

        return response()->json(['data' => new AdjustmentReasonResource($adjustmentReason)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $adjustmentReason = AdjustmentReason::findOrFail($id);

        $adjustmentReason->delete();

        AuditLogger::record('delete', 'adjustment_reason', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
