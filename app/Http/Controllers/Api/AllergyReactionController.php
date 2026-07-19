<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAllergyReactionRequest;
use App\Http\Resources\AllergyReactionResource;
use App\Models\AllergyReaction;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `allergy_reaction`.
 */
class AllergyReactionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = AllergyReaction::query()->paginate($perPage);

        return response()->json([
            'data' => AllergyReactionResource::collection($rows->items()),
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
        $allergyReaction = AllergyReaction::findOrFail($id);

        return response()->json(['data' => new AllergyReactionResource($allergyReaction)]);
    }

    public function store(StoreAllergyReactionRequest $request): JsonResponse
    {
        $allergyReaction = AllergyReaction::create($request->validated());

        AuditLogger::record('create', 'allergy_reaction', (string) $allergyReaction->getKey());

        return response()->json(
            ['data' => new AllergyReactionResource($allergyReaction)], 201
        );
    }

    public function update(StoreAllergyReactionRequest $request, string $id): JsonResponse
    {
        $allergyReaction = AllergyReaction::findOrFail($id);
        $allergyReaction->update($request->validated());

        AuditLogger::record('update', 'allergy_reaction', $id);

        return response()->json(['data' => new AllergyReactionResource($allergyReaction)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $allergyReaction = AllergyReaction::findOrFail($id);

        $allergyReaction->delete();

        AuditLogger::record('delete', 'allergy_reaction', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
