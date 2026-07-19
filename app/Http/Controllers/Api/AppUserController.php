<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAppUserRequest;
use App\Http\Resources\AppUserResource;
use App\Models\AppUser;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `app_user`.
 */
class AppUserController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = AppUser::query()->paginate($perPage);

        return response()->json([
            'data' => AppUserResource::collection($rows->items()),
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
        $appUser = AppUser::findOrFail($id);

        return response()->json(['data' => new AppUserResource($appUser)]);
    }

    public function store(StoreAppUserRequest $request): JsonResponse
    {
        $appUser = AppUser::create($request->validated());

        AuditLogger::record('create', 'app_user', (string) $appUser->getKey());

        return response()->json(
            ['data' => new AppUserResource($appUser)], 201
        );
    }

    public function update(StoreAppUserRequest $request, string $id): JsonResponse
    {
        $appUser = AppUser::findOrFail($id);
        $appUser->update($request->validated());

        AuditLogger::record('update', 'app_user', $id);

        return response()->json(['data' => new AppUserResource($appUser)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $appUser = AppUser::findOrFail($id);

        // Deactivate rather than delete: clinical and financial records
        // must stay resolvable for anything that already references them.
        $appUser->update(['is_active' => false]);

        AuditLogger::record('delete', 'app_user', $id);

        return response()->json(['message' => 'Deactivated.']);
    }
}
