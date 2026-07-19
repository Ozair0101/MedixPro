<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreHousekeepingTaskRequest;
use App\Http\Resources\HousekeepingTaskResource;
use App\Models\HousekeepingTask;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `housekeeping_task`.
 */
class HousekeepingTaskController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = HousekeepingTask::query()->paginate($perPage);

        return response()->json([
            'data' => HousekeepingTaskResource::collection($rows->items()),
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
        $housekeepingTask = HousekeepingTask::findOrFail($id);

        return response()->json(['data' => new HousekeepingTaskResource($housekeepingTask)]);
    }

    public function store(StoreHousekeepingTaskRequest $request): JsonResponse
    {
        $housekeepingTask = HousekeepingTask::create($request->validated());

        AuditLogger::record('create', 'housekeeping_task', (string) $housekeepingTask->getKey());

        return response()->json(
            ['data' => new HousekeepingTaskResource($housekeepingTask)], 201
        );
    }

    public function update(StoreHousekeepingTaskRequest $request, string $id): JsonResponse
    {
        $housekeepingTask = HousekeepingTask::findOrFail($id);
        $housekeepingTask->update($request->validated());

        AuditLogger::record('update', 'housekeeping_task', $id);

        return response()->json(['data' => new HousekeepingTaskResource($housekeepingTask)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $housekeepingTask = HousekeepingTask::findOrFail($id);

        $housekeepingTask->delete();

        AuditLogger::record('delete', 'housekeeping_task', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
