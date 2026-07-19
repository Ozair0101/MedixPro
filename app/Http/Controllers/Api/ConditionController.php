<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreConditionRequest;
use App\Http\Resources\ConditionResource;
use App\Models\Condition;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `condition`.
 */
class ConditionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = Condition::query()->paginate($perPage);

        return response()->json([
            'data' => ConditionResource::collection($rows->items()),
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
        $condition = Condition::findOrFail($id);

        return response()->json(['data' => new ConditionResource($condition)]);
    }

    public function store(StoreConditionRequest $request): JsonResponse
    {
        $condition = Condition::create($request->validated());

        AuditLogger::record('create', 'condition', (string) $condition->getKey());

        return response()->json(
            ['data' => new ConditionResource($condition)], 201
        );
    }

    public function update(StoreConditionRequest $request, string $id): JsonResponse
    {
        $condition = Condition::findOrFail($id);
        $condition->update($request->validated());

        AuditLogger::record('update', 'condition', $id);

        return response()->json(['data' => new ConditionResource($condition)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $condition = Condition::findOrFail($id);

        $condition->delete();

        AuditLogger::record('delete', 'condition', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
