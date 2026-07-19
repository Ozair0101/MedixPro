<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMophConditionMapRequest;
use App\Http\Resources\MophConditionMapResource;
use App\Models\MophConditionMap;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `moph_condition_map`.
 */
class MophConditionMapController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = MophConditionMap::query()->paginate($perPage);

        return response()->json([
            'data' => MophConditionMapResource::collection($rows->items()),
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
        $mophConditionMap = MophConditionMap::findOrFail($id);

        return response()->json(['data' => new MophConditionMapResource($mophConditionMap)]);
    }

    public function store(StoreMophConditionMapRequest $request): JsonResponse
    {
        $mophConditionMap = MophConditionMap::create($request->validated());

        AuditLogger::record('create', 'moph_condition_map', (string) $mophConditionMap->getKey());

        return response()->json(
            ['data' => new MophConditionMapResource($mophConditionMap)], 201
        );
    }

    public function update(StoreMophConditionMapRequest $request, string $id): JsonResponse
    {
        $mophConditionMap = MophConditionMap::findOrFail($id);
        $mophConditionMap->update($request->validated());

        AuditLogger::record('update', 'moph_condition_map', $id);

        return response()->json(['data' => new MophConditionMapResource($mophConditionMap)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $mophConditionMap = MophConditionMap::findOrFail($id);

        $mophConditionMap->delete();

        AuditLogger::record('delete', 'moph_condition_map', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
