<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Resources\RoleResource;
use App\Models\Role;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `role`.
 */
class RoleController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = Role::query()->paginate($perPage);

        return response()->json([
            'data' => RoleResource::collection($rows->items()),
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
        $role = Role::findOrFail($id);

        return response()->json(['data' => new RoleResource($role)]);
    }

    public function store(StoreRoleRequest $request): JsonResponse
    {
        $role = Role::create($request->validated());

        AuditLogger::record('create', 'role', (string) $role->getKey());

        return response()->json(
            ['data' => new RoleResource($role)], 201
        );
    }

    public function update(StoreRoleRequest $request, string $id): JsonResponse
    {
        $role = Role::findOrFail($id);
        $role->update($request->validated());

        AuditLogger::record('update', 'role', $id);

        return response()->json(['data' => new RoleResource($role)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $role = Role::findOrFail($id);

        $role->delete();

        AuditLogger::record('delete', 'role', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
