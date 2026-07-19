<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreArAgingSnapshotRequest;
use App\Http\Resources\ArAgingSnapshotResource;
use App\Models\ArAgingSnapshot;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `ar_aging_snapshot`.
 */
class ArAgingSnapshotController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = ArAgingSnapshot::query()->paginate($perPage);

        return response()->json([
            'data' => ArAgingSnapshotResource::collection($rows->items()),
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
        $arAgingSnapshot = ArAgingSnapshot::findOrFail($id);

        return response()->json(['data' => new ArAgingSnapshotResource($arAgingSnapshot)]);
    }

    public function store(StoreArAgingSnapshotRequest $request): JsonResponse
    {
        $arAgingSnapshot = ArAgingSnapshot::create($request->validated());

        AuditLogger::record('create', 'ar_aging_snapshot', (string) $arAgingSnapshot->getKey());

        return response()->json(
            ['data' => new ArAgingSnapshotResource($arAgingSnapshot)], 201
        );
    }

    public function update(StoreArAgingSnapshotRequest $request, string $id): JsonResponse
    {
        $arAgingSnapshot = ArAgingSnapshot::findOrFail($id);
        $arAgingSnapshot->update($request->validated());

        AuditLogger::record('update', 'ar_aging_snapshot', $id);

        return response()->json(['data' => new ArAgingSnapshotResource($arAgingSnapshot)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $arAgingSnapshot = ArAgingSnapshot::findOrFail($id);

        $arAgingSnapshot->delete();

        AuditLogger::record('delete', 'ar_aging_snapshot', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
