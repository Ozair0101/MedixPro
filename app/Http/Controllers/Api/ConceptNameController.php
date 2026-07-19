<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreConceptNameRequest;
use App\Http\Resources\ConceptNameResource;
use App\Models\ConceptName;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `concept_name`.
 */
class ConceptNameController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = ConceptName::query()->paginate($perPage);

        return response()->json([
            'data' => ConceptNameResource::collection($rows->items()),
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
        $conceptName = ConceptName::findOrFail($id);

        return response()->json(['data' => new ConceptNameResource($conceptName)]);
    }

    public function store(StoreConceptNameRequest $request): JsonResponse
    {
        $conceptName = ConceptName::create($request->validated());

        AuditLogger::record('create', 'concept_name', (string) $conceptName->getKey());

        return response()->json(
            ['data' => new ConceptNameResource($conceptName)], 201
        );
    }

    public function update(StoreConceptNameRequest $request, string $id): JsonResponse
    {
        $conceptName = ConceptName::findOrFail($id);
        $conceptName->update($request->validated());

        AuditLogger::record('update', 'concept_name', $id);

        return response()->json(['data' => new ConceptNameResource($conceptName)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $conceptName = ConceptName::findOrFail($id);

        $conceptName->delete();

        AuditLogger::record('delete', 'concept_name', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
