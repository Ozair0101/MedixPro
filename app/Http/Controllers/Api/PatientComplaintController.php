<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePatientComplaintRequest;
use App\Http\Resources\PatientComplaintResource;
use App\Models\PatientComplaint;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `patient_complaint`.
 */
class PatientComplaintController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = PatientComplaint::query()->paginate($perPage);

        return response()->json([
            'data' => PatientComplaintResource::collection($rows->items()),
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
        $patientComplaint = PatientComplaint::findOrFail($id);

        return response()->json(['data' => new PatientComplaintResource($patientComplaint)]);
    }

    public function store(StorePatientComplaintRequest $request): JsonResponse
    {
        $patientComplaint = PatientComplaint::create($request->validated());

        AuditLogger::record('create', 'patient_complaint', (string) $patientComplaint->getKey());

        return response()->json(
            ['data' => new PatientComplaintResource($patientComplaint)], 201
        );
    }

    public function update(StorePatientComplaintRequest $request, string $id): JsonResponse
    {
        $patientComplaint = PatientComplaint::findOrFail($id);
        $patientComplaint->update($request->validated());

        AuditLogger::record('update', 'patient_complaint', $id);

        return response()->json(['data' => new PatientComplaintResource($patientComplaint)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $patientComplaint = PatientComplaint::findOrFail($id);

        $patientComplaint->delete();

        AuditLogger::record('delete', 'patient_complaint', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
