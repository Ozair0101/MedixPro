<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreConceptNumericRequest;
use App\Http\Resources\ConceptNumericResource;
use App\Models\ConceptNumeric;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `concept_numeric`.
 */
class ConceptNumericController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = ConceptNumeric::query()->paginate($perPage);

        return response()->json([
            'data' => ConceptNumericResource::collection($rows->items()),
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
        $conceptNumeric = ConceptNumeric::findOrFail($id);

        return response()->json(['data' => new ConceptNumericResource($conceptNumeric)]);
    }

    public function store(StoreConceptNumericRequest $request): JsonResponse
    {
        $conceptNumeric = ConceptNumeric::create($request->validated());

        AuditLogger::record('create', 'concept_numeric', (string) $conceptNumeric->getKey());

        return response()->json(
            ['data' => new ConceptNumericResource($conceptNumeric)], 201
        );
    }

    public function update(StoreConceptNumericRequest $request, string $id): JsonResponse
    {
        $conceptNumeric = ConceptNumeric::findOrFail($id);
        $conceptNumeric->update($request->validated());

        AuditLogger::record('update', 'concept_numeric', $id);

        return response()->json(['data' => new ConceptNumericResource($conceptNumeric)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $conceptNumeric = ConceptNumeric::findOrFail($id);

        $conceptNumeric->delete();

        AuditLogger::record('delete', 'concept_numeric', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
