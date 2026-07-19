<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEncounterDiagnosiRequest;
use App\Http\Resources\EncounterDiagnosiResource;
use App\Models\EncounterDiagnosi;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `encounter_diagnosis`.
 */
class EncounterDiagnosiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = EncounterDiagnosi::query()->paginate($perPage);

        return response()->json([
            'data' => EncounterDiagnosiResource::collection($rows->items()),
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
        $encounterDiagnosi = EncounterDiagnosi::findOrFail($id);

        return response()->json(['data' => new EncounterDiagnosiResource($encounterDiagnosi)]);
    }

    public function store(StoreEncounterDiagnosiRequest $request): JsonResponse
    {
        $encounterDiagnosi = EncounterDiagnosi::create($request->validated());

        AuditLogger::record('create', 'encounter_diagnosis', (string) $encounterDiagnosi->getKey());

        return response()->json(
            ['data' => new EncounterDiagnosiResource($encounterDiagnosi)], 201
        );
    }

    public function update(StoreEncounterDiagnosiRequest $request, string $id): JsonResponse
    {
        $encounterDiagnosi = EncounterDiagnosi::findOrFail($id);
        $encounterDiagnosi->update($request->validated());

        AuditLogger::record('update', 'encounter_diagnosis', $id);

        return response()->json(['data' => new EncounterDiagnosiResource($encounterDiagnosi)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $encounterDiagnosi = EncounterDiagnosi::findOrFail($id);

        $encounterDiagnosi->delete();

        AuditLogger::record('delete', 'encounter_diagnosis', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
