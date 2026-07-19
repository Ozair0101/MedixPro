<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreConceptClassRequest;
use App\Http\Resources\ConceptClassResource;
use App\Models\ConceptClass;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `concept_class`.
 */
class ConceptClassController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = ConceptClass::query()->paginate($perPage);

        return response()->json([
            'data' => ConceptClassResource::collection($rows->items()),
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
        $conceptClass = ConceptClass::findOrFail($id);

        return response()->json(['data' => new ConceptClassResource($conceptClass)]);
    }

    public function store(StoreConceptClassRequest $request): JsonResponse
    {
        $conceptClass = ConceptClass::create($request->validated());

        AuditLogger::record('create', 'concept_class', (string) $conceptClass->getKey());

        return response()->json(
            ['data' => new ConceptClassResource($conceptClass)], 201
        );
    }

    public function update(StoreConceptClassRequest $request, string $id): JsonResponse
    {
        $conceptClass = ConceptClass::findOrFail($id);
        $conceptClass->update($request->validated());

        AuditLogger::record('update', 'concept_class', $id);

        return response()->json(['data' => new ConceptClassResource($conceptClass)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $conceptClass = ConceptClass::findOrFail($id);

        $conceptClass->delete();

        AuditLogger::record('delete', 'concept_class', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
