<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreConceptReferenceRangeRequest;
use App\Http\Resources\ConceptReferenceRangeResource;
use App\Models\ConceptReferenceRange;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `concept_reference_range`.
 */
class ConceptReferenceRangeController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = ConceptReferenceRange::query()->paginate($perPage);

        return response()->json([
            'data' => ConceptReferenceRangeResource::collection($rows->items()),
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
        $conceptReferenceRange = ConceptReferenceRange::findOrFail($id);

        return response()->json(['data' => new ConceptReferenceRangeResource($conceptReferenceRange)]);
    }

    public function store(StoreConceptReferenceRangeRequest $request): JsonResponse
    {
        $conceptReferenceRange = ConceptReferenceRange::create($request->validated());

        AuditLogger::record('create', 'concept_reference_range', (string) $conceptReferenceRange->getKey());

        return response()->json(
            ['data' => new ConceptReferenceRangeResource($conceptReferenceRange)], 201
        );
    }

    public function update(StoreConceptReferenceRangeRequest $request, string $id): JsonResponse
    {
        $conceptReferenceRange = ConceptReferenceRange::findOrFail($id);
        $conceptReferenceRange->update($request->validated());

        AuditLogger::record('update', 'concept_reference_range', $id);

        return response()->json(['data' => new ConceptReferenceRangeResource($conceptReferenceRange)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $conceptReferenceRange = ConceptReferenceRange::findOrFail($id);

        $conceptReferenceRange->delete();

        AuditLogger::record('delete', 'concept_reference_range', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
