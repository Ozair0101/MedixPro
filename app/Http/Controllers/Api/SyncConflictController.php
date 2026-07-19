<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSyncConflictRequest;
use App\Http\Resources\SyncConflictResource;
use App\Models\SyncConflict;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `sync_conflict`.
 */
class SyncConflictController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = SyncConflict::query()->paginate($perPage);

        return response()->json([
            'data' => SyncConflictResource::collection($rows->items()),
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
        $syncConflict = SyncConflict::findOrFail($id);

        return response()->json(['data' => new SyncConflictResource($syncConflict)]);
    }

    public function store(StoreSyncConflictRequest $request): JsonResponse
    {
        $syncConflict = SyncConflict::create($request->validated());

        AuditLogger::record('create', 'sync_conflict', (string) $syncConflict->getKey());

        return response()->json(
            ['data' => new SyncConflictResource($syncConflict)], 201
        );
    }

    public function update(StoreSyncConflictRequest $request, string $id): JsonResponse
    {
        $syncConflict = SyncConflict::findOrFail($id);
        $syncConflict->update($request->validated());

        AuditLogger::record('update', 'sync_conflict', $id);

        return response()->json(['data' => new SyncConflictResource($syncConflict)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $syncConflict = SyncConflict::findOrFail($id);

        $syncConflict->delete();

        AuditLogger::record('delete', 'sync_conflict', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
