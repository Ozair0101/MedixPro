<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreConceptReferenceMapRequest;
use App\Http\Resources\ConceptReferenceMapResource;
use App\Models\ConceptReferenceMap;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `concept_reference_map`.
 */
class ConceptReferenceMapController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = ConceptReferenceMap::query()->paginate($perPage);

        return response()->json([
            'data' => ConceptReferenceMapResource::collection($rows->items()),
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
        $conceptReferenceMap = ConceptReferenceMap::findOrFail($id);

        return response()->json(['data' => new ConceptReferenceMapResource($conceptReferenceMap)]);
    }

    public function store(StoreConceptReferenceMapRequest $request): JsonResponse
    {
        $conceptReferenceMap = ConceptReferenceMap::create($request->validated());

        AuditLogger::record('create', 'concept_reference_map', (string) $conceptReferenceMap->getKey());

        return response()->json(
            ['data' => new ConceptReferenceMapResource($conceptReferenceMap)], 201
        );
    }

    public function update(StoreConceptReferenceMapRequest $request, string $id): JsonResponse
    {
        $conceptReferenceMap = ConceptReferenceMap::findOrFail($id);
        $conceptReferenceMap->update($request->validated());

        AuditLogger::record('update', 'concept_reference_map', $id);

        return response()->json(['data' => new ConceptReferenceMapResource($conceptReferenceMap)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $conceptReferenceMap = ConceptReferenceMap::findOrFail($id);

        $conceptReferenceMap->delete();

        AuditLogger::record('delete', 'concept_reference_map', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
