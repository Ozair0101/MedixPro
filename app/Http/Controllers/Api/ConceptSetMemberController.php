<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreConceptSetMemberRequest;
use App\Http\Resources\ConceptSetMemberResource;
use App\Models\ConceptSetMember;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `concept_set_member`.
 */
class ConceptSetMemberController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = ConceptSetMember::query()->paginate($perPage);

        return response()->json([
            'data' => ConceptSetMemberResource::collection($rows->items()),
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
        $conceptSetMember = ConceptSetMember::findOrFail($id);

        return response()->json(['data' => new ConceptSetMemberResource($conceptSetMember)]);
    }

    public function store(StoreConceptSetMemberRequest $request): JsonResponse
    {
        $conceptSetMember = ConceptSetMember::create($request->validated());

        AuditLogger::record('create', 'concept_set_member', (string) $conceptSetMember->getKey());

        return response()->json(
            ['data' => new ConceptSetMemberResource($conceptSetMember)], 201
        );
    }

    public function update(StoreConceptSetMemberRequest $request, string $id): JsonResponse
    {
        $conceptSetMember = ConceptSetMember::findOrFail($id);
        $conceptSetMember->update($request->validated());

        AuditLogger::record('update', 'concept_set_member', $id);

        return response()->json(['data' => new ConceptSetMemberResource($conceptSetMember)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $conceptSetMember = ConceptSetMember::findOrFail($id);

        $conceptSetMember->delete();

        AuditLogger::record('delete', 'concept_set_member', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
