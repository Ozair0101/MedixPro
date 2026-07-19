<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRolePermissionRequest;
use App\Http\Resources\RolePermissionResource;
use App\Models\RolePermission;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `role_permission`.
 */
class RolePermissionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = RolePermission::query()->paginate($perPage);

        return response()->json([
            'data' => RolePermissionResource::collection($rows->items()),
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
        $rolePermission = RolePermission::findOrFail($id);

        return response()->json(['data' => new RolePermissionResource($rolePermission)]);
    }

    public function store(StoreRolePermissionRequest $request): JsonResponse
    {
        $rolePermission = RolePermission::create($request->validated());

        AuditLogger::record('create', 'role_permission', (string) $rolePermission->getKey());

        return response()->json(
            ['data' => new RolePermissionResource($rolePermission)], 201
        );
    }

    public function update(StoreRolePermissionRequest $request, string $id): JsonResponse
    {
        $rolePermission = RolePermission::findOrFail($id);
        $rolePermission->update($request->validated());

        AuditLogger::record('update', 'role_permission', $id);

        return response()->json(['data' => new RolePermissionResource($rolePermission)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $rolePermission = RolePermission::findOrFail($id);

        $rolePermission->delete();

        AuditLogger::record('delete', 'role_permission', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
