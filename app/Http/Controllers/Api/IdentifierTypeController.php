<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreIdentifierTypeRequest;
use App\Http\Resources\IdentifierTypeResource;
use App\Models\IdentifierType;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `identifier_type`.
 */
class IdentifierTypeController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = IdentifierType::query()->paginate($perPage);

        return response()->json([
            'data' => IdentifierTypeResource::collection($rows->items()),
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
        $identifierType = IdentifierType::findOrFail($id);

        return response()->json(['data' => new IdentifierTypeResource($identifierType)]);
    }

    public function store(StoreIdentifierTypeRequest $request): JsonResponse
    {
        $identifierType = IdentifierType::create($request->validated());

        AuditLogger::record('create', 'identifier_type', (string) $identifierType->getKey());

        return response()->json(
            ['data' => new IdentifierTypeResource($identifierType)], 201
        );
    }

    public function update(StoreIdentifierTypeRequest $request, string $id): JsonResponse
    {
        $identifierType = IdentifierType::findOrFail($id);
        $identifierType->update($request->validated());

        AuditLogger::record('update', 'identifier_type', $id);

        return response()->json(['data' => new IdentifierTypeResource($identifierType)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $identifierType = IdentifierType::findOrFail($id);

        $identifierType->delete();

        AuditLogger::record('delete', 'identifier_type', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
