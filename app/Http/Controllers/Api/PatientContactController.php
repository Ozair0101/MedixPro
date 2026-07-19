<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePatientContactRequest;
use App\Http\Resources\PatientContactResource;
use App\Models\PatientContact;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `patient_contact`.
 */
class PatientContactController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = PatientContact::query()->paginate($perPage);

        return response()->json([
            'data' => PatientContactResource::collection($rows->items()),
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
        $patientContact = PatientContact::findOrFail($id);

        return response()->json(['data' => new PatientContactResource($patientContact)]);
    }

    public function store(StorePatientContactRequest $request): JsonResponse
    {
        $patientContact = PatientContact::create($request->validated());

        AuditLogger::record('create', 'patient_contact', (string) $patientContact->getKey());

        return response()->json(
            ['data' => new PatientContactResource($patientContact)], 201
        );
    }

    public function update(StorePatientContactRequest $request, string $id): JsonResponse
    {
        $patientContact = PatientContact::findOrFail($id);
        $patientContact->update($request->validated());

        AuditLogger::record('update', 'patient_contact', $id);

        return response()->json(['data' => new PatientContactResource($patientContact)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $patientContact = PatientContact::findOrFail($id);

        $patientContact->delete();

        AuditLogger::record('delete', 'patient_contact', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
