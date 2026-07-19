<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMophCaseDefinitionRequest;
use App\Http\Resources\MophCaseDefinitionResource;
use App\Models\MophCaseDefinition;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `moph_case_definition`.
 */
class MophCaseDefinitionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = MophCaseDefinition::query()->paginate($perPage);

        return response()->json([
            'data' => MophCaseDefinitionResource::collection($rows->items()),
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
        $mophCaseDefinition = MophCaseDefinition::findOrFail($id);

        return response()->json(['data' => new MophCaseDefinitionResource($mophCaseDefinition)]);
    }

    public function store(StoreMophCaseDefinitionRequest $request): JsonResponse
    {
        $mophCaseDefinition = MophCaseDefinition::create($request->validated());

        AuditLogger::record('create', 'moph_case_definition', (string) $mophCaseDefinition->getKey());

        return response()->json(
            ['data' => new MophCaseDefinitionResource($mophCaseDefinition)], 201
        );
    }

    public function update(StoreMophCaseDefinitionRequest $request, string $id): JsonResponse
    {
        $mophCaseDefinition = MophCaseDefinition::findOrFail($id);
        $mophCaseDefinition->update($request->validated());

        AuditLogger::record('update', 'moph_case_definition', $id);

        return response()->json(['data' => new MophCaseDefinitionResource($mophCaseDefinition)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $mophCaseDefinition = MophCaseDefinition::findOrFail($id);

        $mophCaseDefinition->delete();

        AuditLogger::record('delete', 'moph_case_definition', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
