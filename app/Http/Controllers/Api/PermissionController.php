<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePermissionRequest;
use App\Http\Resources\PermissionResource;
use App\Models\Permission;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `permission`.
 */
class PermissionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = Permission::query()->paginate($perPage);

        return response()->json([
            'data' => PermissionResource::collection($rows->items()),
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
        $permission = Permission::findOrFail($id);

        return response()->json(['data' => new PermissionResource($permission)]);
    }

    public function store(StorePermissionRequest $request): JsonResponse
    {
        $permission = Permission::create($request->validated());

        AuditLogger::record('create', 'permission', (string) $permission->getKey());

        return response()->json(
            ['data' => new PermissionResource($permission)], 201
        );
    }

    public function update(StorePermissionRequest $request, string $id): JsonResponse
    {
        $permission = Permission::findOrFail($id);
        $permission->update($request->validated());

        AuditLogger::record('update', 'permission', $id);

        return response()->json(['data' => new PermissionResource($permission)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $permission = Permission::findOrFail($id);

        $permission->delete();

        AuditLogger::record('delete', 'permission', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
