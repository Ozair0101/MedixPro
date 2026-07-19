<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePatientIdentifierRequest;
use App\Http\Resources\PatientIdentifierResource;
use App\Models\PatientIdentifier;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `patient_identifier`.
 */
class PatientIdentifierController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = PatientIdentifier::query()->paginate($perPage);

        return response()->json([
            'data' => PatientIdentifierResource::collection($rows->items()),
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
        $patientIdentifier = PatientIdentifier::findOrFail($id);

        return response()->json(['data' => new PatientIdentifierResource($patientIdentifier)]);
    }

    public function store(StorePatientIdentifierRequest $request): JsonResponse
    {
        $patientIdentifier = PatientIdentifier::create($request->validated());

        AuditLogger::record('create', 'patient_identifier', (string) $patientIdentifier->getKey());

        return response()->json(
            ['data' => new PatientIdentifierResource($patientIdentifier)], 201
        );
    }

    public function update(StorePatientIdentifierRequest $request, string $id): JsonResponse
    {
        $patientIdentifier = PatientIdentifier::findOrFail($id);
        $patientIdentifier->update($request->validated());

        AuditLogger::record('update', 'patient_identifier', $id);

        return response()->json(['data' => new PatientIdentifierResource($patientIdentifier)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $patientIdentifier = PatientIdentifier::findOrFail($id);

        $patientIdentifier->delete();

        AuditLogger::record('delete', 'patient_identifier', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
