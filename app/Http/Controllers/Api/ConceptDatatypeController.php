<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreConceptDatatypeRequest;
use App\Http\Resources\ConceptDatatypeResource;
use App\Models\ConceptDatatype;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `concept_datatype`.
 */
class ConceptDatatypeController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = ConceptDatatype::query()->paginate($perPage);

        return response()->json([
            'data' => ConceptDatatypeResource::collection($rows->items()),
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
        $conceptDatatype = ConceptDatatype::findOrFail($id);

        return response()->json(['data' => new ConceptDatatypeResource($conceptDatatype)]);
    }

    public function store(StoreConceptDatatypeRequest $request): JsonResponse
    {
        $conceptDatatype = ConceptDatatype::create($request->validated());

        AuditLogger::record('create', 'concept_datatype', (string) $conceptDatatype->getKey());

        return response()->json(
            ['data' => new ConceptDatatypeResource($conceptDatatype)], 201
        );
    }

    public function update(StoreConceptDatatypeRequest $request, string $id): JsonResponse
    {
        $conceptDatatype = ConceptDatatype::findOrFail($id);
        $conceptDatatype->update($request->validated());

        AuditLogger::record('update', 'concept_datatype', $id);

        return response()->json(['data' => new ConceptDatatypeResource($conceptDatatype)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $conceptDatatype = ConceptDatatype::findOrFail($id);

        $conceptDatatype->delete();

        AuditLogger::record('delete', 'concept_datatype', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
