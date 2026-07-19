<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGlAccountRequest;
use App\Http\Resources\GlAccountResource;
use App\Models\GlAccount;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `gl_account`.
 */
class GlAccountController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = GlAccount::query()->paginate($perPage);

        return response()->json([
            'data' => GlAccountResource::collection($rows->items()),
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
        $glAccount = GlAccount::findOrFail($id);

        return response()->json(['data' => new GlAccountResource($glAccount)]);
    }

    public function store(StoreGlAccountRequest $request): JsonResponse
    {
        $glAccount = GlAccount::create($request->validated());

        AuditLogger::record('create', 'gl_account', (string) $glAccount->getKey());

        return response()->json(
            ['data' => new GlAccountResource($glAccount)], 201
        );
    }

    public function update(StoreGlAccountRequest $request, string $id): JsonResponse
    {
        $glAccount = GlAccount::findOrFail($id);
        $glAccount->update($request->validated());

        AuditLogger::record('update', 'gl_account', $id);

        return response()->json(['data' => new GlAccountResource($glAccount)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $glAccount = GlAccount::findOrFail($id);

        // Deactivate rather than delete: clinical and financial records
        // must stay resolvable for anything that already references them.
        $glAccount->update(['is_active' => false]);

        AuditLogger::record('delete', 'gl_account', $id);

        return response()->json(['message' => 'Deactivated.']);
    }
}
