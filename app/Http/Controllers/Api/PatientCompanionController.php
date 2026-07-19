<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePatientCompanionRequest;
use App\Http\Resources\PatientCompanionResource;
use App\Models\PatientCompanion;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `patient_companion`.
 */
class PatientCompanionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = PatientCompanion::query()->paginate($perPage);

        return response()->json([
            'data' => PatientCompanionResource::collection($rows->items()),
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
        $patientCompanion = PatientCompanion::findOrFail($id);

        return response()->json(['data' => new PatientCompanionResource($patientCompanion)]);
    }

    public function store(StorePatientCompanionRequest $request): JsonResponse
    {
        $patientCompanion = PatientCompanion::create($request->validated());

        AuditLogger::record('create', 'patient_companion', (string) $patientCompanion->getKey());

        return response()->json(
            ['data' => new PatientCompanionResource($patientCompanion)], 201
        );
    }

    public function update(StorePatientCompanionRequest $request, string $id): JsonResponse
    {
        $patientCompanion = PatientCompanion::findOrFail($id);
        $patientCompanion->update($request->validated());

        AuditLogger::record('update', 'patient_companion', $id);

        return response()->json(['data' => new PatientCompanionResource($patientCompanion)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $patientCompanion = PatientCompanion::findOrFail($id);

        // Deactivate rather than delete: clinical and financial records
        // must stay resolvable for anything that already references them.
        $patientCompanion->update(['is_active' => false]);

        AuditLogger::record('delete', 'patient_companion', $id);

        return response()->json(['message' => 'Deactivated.']);
    }
}
