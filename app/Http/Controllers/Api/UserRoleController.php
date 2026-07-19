<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRoleRequest;
use App\Http\Resources\UserRoleResource;
use App\Models\UserRole;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `user_role`.
 */
class UserRoleController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = UserRole::query()->paginate($perPage);

        return response()->json([
            'data' => UserRoleResource::collection($rows->items()),
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
        $userRole = UserRole::findOrFail($id);

        return response()->json(['data' => new UserRoleResource($userRole)]);
    }

    public function store(StoreUserRoleRequest $request): JsonResponse
    {
        $userRole = UserRole::create($request->validated());

        AuditLogger::record('create', 'user_role', (string) $userRole->getKey());

        return response()->json(
            ['data' => new UserRoleResource($userRole)], 201
        );
    }

    public function update(StoreUserRoleRequest $request, string $id): JsonResponse
    {
        $userRole = UserRole::findOrFail($id);
        $userRole->update($request->validated());

        AuditLogger::record('update', 'user_role', $id);

        return response()->json(['data' => new UserRoleResource($userRole)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $userRole = UserRole::findOrFail($id);

        $userRole->delete();

        AuditLogger::record('delete', 'user_role', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
