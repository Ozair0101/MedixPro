<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRosterAssignmentRequest;
use App\Http\Resources\RosterAssignmentResource;
use App\Models\RosterAssignment;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `roster_assignment`.
 */
class RosterAssignmentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = RosterAssignment::query()->paginate($perPage);

        return response()->json([
            'data' => RosterAssignmentResource::collection($rows->items()),
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
        $rosterAssignment = RosterAssignment::findOrFail($id);

        return response()->json(['data' => new RosterAssignmentResource($rosterAssignment)]);
    }

    public function store(StoreRosterAssignmentRequest $request): JsonResponse
    {
        $rosterAssignment = RosterAssignment::create($request->validated());

        AuditLogger::record('create', 'roster_assignment', (string) $rosterAssignment->getKey());

        return response()->json(
            ['data' => new RosterAssignmentResource($rosterAssignment)], 201
        );
    }

    public function update(StoreRosterAssignmentRequest $request, string $id): JsonResponse
    {
        $rosterAssignment = RosterAssignment::findOrFail($id);
        $rosterAssignment->update($request->validated());

        AuditLogger::record('update', 'roster_assignment', $id);

        return response()->json(['data' => new RosterAssignmentResource($rosterAssignment)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $rosterAssignment = RosterAssignment::findOrFail($id);

        $rosterAssignment->delete();

        AuditLogger::record('delete', 'roster_assignment', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
