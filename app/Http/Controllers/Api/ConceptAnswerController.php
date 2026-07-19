<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreConceptAnswerRequest;
use App\Http\Resources\ConceptAnswerResource;
use App\Models\ConceptAnswer;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `concept_answer`.
 */
class ConceptAnswerController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = ConceptAnswer::query()->paginate($perPage);

        return response()->json([
            'data' => ConceptAnswerResource::collection($rows->items()),
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
        $conceptAnswer = ConceptAnswer::findOrFail($id);

        return response()->json(['data' => new ConceptAnswerResource($conceptAnswer)]);
    }

    public function store(StoreConceptAnswerRequest $request): JsonResponse
    {
        $conceptAnswer = ConceptAnswer::create($request->validated());

        AuditLogger::record('create', 'concept_answer', (string) $conceptAnswer->getKey());

        return response()->json(
            ['data' => new ConceptAnswerResource($conceptAnswer)], 201
        );
    }

    public function update(StoreConceptAnswerRequest $request, string $id): JsonResponse
    {
        $conceptAnswer = ConceptAnswer::findOrFail($id);
        $conceptAnswer->update($request->validated());

        AuditLogger::record('update', 'concept_answer', $id);

        return response()->json(['data' => new ConceptAnswerResource($conceptAnswer)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $conceptAnswer = ConceptAnswer::findOrFail($id);

        $conceptAnswer->delete();

        AuditLogger::record('delete', 'concept_answer', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
