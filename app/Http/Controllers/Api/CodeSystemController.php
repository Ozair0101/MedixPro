<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCodeSystemRequest;
use App\Http\Resources\CodeSystemResource;
use App\Models\CodeSystem;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `code_system`.
 */
class CodeSystemController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = CodeSystem::query()->paginate($perPage);

        return response()->json([
            'data' => CodeSystemResource::collection($rows->items()),
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
        $codeSystem = CodeSystem::findOrFail($id);

        return response()->json(['data' => new CodeSystemResource($codeSystem)]);
    }

    public function store(StoreCodeSystemRequest $request): JsonResponse
    {
        $codeSystem = CodeSystem::create($request->validated());

        AuditLogger::record('create', 'code_system', (string) $codeSystem->getKey());

        return response()->json(
            ['data' => new CodeSystemResource($codeSystem)], 201
        );
    }

    public function update(StoreCodeSystemRequest $request, string $id): JsonResponse
    {
        $codeSystem = CodeSystem::findOrFail($id);
        $codeSystem->update($request->validated());

        AuditLogger::record('update', 'code_system', $id);

        return response()->json(['data' => new CodeSystemResource($codeSystem)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $codeSystem = CodeSystem::findOrFail($id);

        $codeSystem->delete();

        AuditLogger::record('delete', 'code_system', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
