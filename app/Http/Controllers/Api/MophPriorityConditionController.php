<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMophPriorityConditionRequest;
use App\Http\Resources\MophPriorityConditionResource;
use App\Models\MophPriorityCondition;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `moph_priority_condition`.
 */
class MophPriorityConditionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = MophPriorityCondition::query()->paginate($perPage);

        return response()->json([
            'data' => MophPriorityConditionResource::collection($rows->items()),
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
        $mophPriorityCondition = MophPriorityCondition::findOrFail($id);

        return response()->json(['data' => new MophPriorityConditionResource($mophPriorityCondition)]);
    }

    public function store(StoreMophPriorityConditionRequest $request): JsonResponse
    {
        $mophPriorityCondition = MophPriorityCondition::create($request->validated());

        AuditLogger::record('create', 'moph_priority_condition', (string) $mophPriorityCondition->getKey());

        return response()->json(
            ['data' => new MophPriorityConditionResource($mophPriorityCondition)], 201
        );
    }

    public function update(StoreMophPriorityConditionRequest $request, string $id): JsonResponse
    {
        $mophPriorityCondition = MophPriorityCondition::findOrFail($id);
        $mophPriorityCondition->update($request->validated());

        AuditLogger::record('update', 'moph_priority_condition', $id);

        return response()->json(['data' => new MophPriorityConditionResource($mophPriorityCondition)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $mophPriorityCondition = MophPriorityCondition::findOrFail($id);

        $mophPriorityCondition->delete();

        AuditLogger::record('delete', 'moph_priority_condition', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
