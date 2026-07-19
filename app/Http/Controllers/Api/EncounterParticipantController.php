<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEncounterParticipantRequest;
use App\Http\Resources\EncounterParticipantResource;
use App\Models\EncounterParticipant;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `encounter_participant`.
 */
class EncounterParticipantController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = EncounterParticipant::query()->paginate($perPage);

        return response()->json([
            'data' => EncounterParticipantResource::collection($rows->items()),
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
        $encounterParticipant = EncounterParticipant::findOrFail($id);

        return response()->json(['data' => new EncounterParticipantResource($encounterParticipant)]);
    }

    public function store(StoreEncounterParticipantRequest $request): JsonResponse
    {
        $encounterParticipant = EncounterParticipant::create($request->validated());

        AuditLogger::record('create', 'encounter_participant', (string) $encounterParticipant->getKey());

        return response()->json(
            ['data' => new EncounterParticipantResource($encounterParticipant)], 201
        );
    }

    public function update(StoreEncounterParticipantRequest $request, string $id): JsonResponse
    {
        $encounterParticipant = EncounterParticipant::findOrFail($id);
        $encounterParticipant->update($request->validated());

        AuditLogger::record('update', 'encounter_participant', $id);

        return response()->json(['data' => new EncounterParticipantResource($encounterParticipant)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $encounterParticipant = EncounterParticipant::findOrFail($id);

        $encounterParticipant->delete();

        AuditLogger::record('delete', 'encounter_participant', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
