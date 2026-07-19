<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreConceptReferenceTermRequest;
use App\Http\Resources\ConceptReferenceTermResource;
use App\Models\ConceptReferenceTerm;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `concept_reference_term`.
 */
class ConceptReferenceTermController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = ConceptReferenceTerm::query()->paginate($perPage);

        return response()->json([
            'data' => ConceptReferenceTermResource::collection($rows->items()),
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
        $conceptReferenceTerm = ConceptReferenceTerm::findOrFail($id);

        return response()->json(['data' => new ConceptReferenceTermResource($conceptReferenceTerm)]);
    }

    public function store(StoreConceptReferenceTermRequest $request): JsonResponse
    {
        $conceptReferenceTerm = ConceptReferenceTerm::create($request->validated());

        AuditLogger::record('create', 'concept_reference_term', (string) $conceptReferenceTerm->getKey());

        return response()->json(
            ['data' => new ConceptReferenceTermResource($conceptReferenceTerm)], 201
        );
    }

    public function update(StoreConceptReferenceTermRequest $request, string $id): JsonResponse
    {
        $conceptReferenceTerm = ConceptReferenceTerm::findOrFail($id);
        $conceptReferenceTerm->update($request->validated());

        AuditLogger::record('update', 'concept_reference_term', $id);

        return response()->json(['data' => new ConceptReferenceTermResource($conceptReferenceTerm)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $conceptReferenceTerm = ConceptReferenceTerm::findOrFail($id);

        $conceptReferenceTerm->delete();

        AuditLogger::record('delete', 'concept_reference_term', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
