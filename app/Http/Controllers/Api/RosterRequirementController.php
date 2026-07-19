<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRosterRequirementRequest;
use App\Http\Resources\RosterRequirementResource;
use App\Models\RosterRequirement;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `roster_requirement`.
 */
class RosterRequirementController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = RosterRequirement::query()->paginate($perPage);

        return response()->json([
            'data' => RosterRequirementResource::collection($rows->items()),
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
        $rosterRequirement = RosterRequirement::findOrFail($id);

        return response()->json(['data' => new RosterRequirementResource($rosterRequirement)]);
    }

    public function store(StoreRosterRequirementRequest $request): JsonResponse
    {
        $rosterRequirement = RosterRequirement::create($request->validated());

        AuditLogger::record('create', 'roster_requirement', (string) $rosterRequirement->getKey());

        return response()->json(
            ['data' => new RosterRequirementResource($rosterRequirement)], 201
        );
    }

    public function update(StoreRosterRequirementRequest $request, string $id): JsonResponse
    {
        $rosterRequirement = RosterRequirement::findOrFail($id);
        $rosterRequirement->update($request->validated());

        AuditLogger::record('update', 'roster_requirement', $id);

        return response()->json(['data' => new RosterRequirementResource($rosterRequirement)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $rosterRequirement = RosterRequirement::findOrFail($id);

        $rosterRequirement->delete();

        AuditLogger::record('delete', 'roster_requirement', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
