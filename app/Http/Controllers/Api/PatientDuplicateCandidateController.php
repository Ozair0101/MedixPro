<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePatientDuplicateCandidateRequest;
use App\Http\Resources\PatientDuplicateCandidateResource;
use App\Models\PatientDuplicateCandidate;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `patient_duplicate_candidate`.
 */
class PatientDuplicateCandidateController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = PatientDuplicateCandidate::query()->paginate($perPage);

        return response()->json([
            'data' => PatientDuplicateCandidateResource::collection($rows->items()),
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
        $patientDuplicateCandidate = PatientDuplicateCandidate::findOrFail($id);

        return response()->json(['data' => new PatientDuplicateCandidateResource($patientDuplicateCandidate)]);
    }

    public function store(StorePatientDuplicateCandidateRequest $request): JsonResponse
    {
        $patientDuplicateCandidate = PatientDuplicateCandidate::create($request->validated());

        AuditLogger::record('create', 'patient_duplicate_candidate', (string) $patientDuplicateCandidate->getKey());

        return response()->json(
            ['data' => new PatientDuplicateCandidateResource($patientDuplicateCandidate)], 201
        );
    }

    public function update(StorePatientDuplicateCandidateRequest $request, string $id): JsonResponse
    {
        $patientDuplicateCandidate = PatientDuplicateCandidate::findOrFail($id);
        $patientDuplicateCandidate->update($request->validated());

        AuditLogger::record('update', 'patient_duplicate_candidate', $id);

        return response()->json(['data' => new PatientDuplicateCandidateResource($patientDuplicateCandidate)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $patientDuplicateCandidate = PatientDuplicateCandidate::findOrFail($id);

        $patientDuplicateCandidate->delete();

        AuditLogger::record('delete', 'patient_duplicate_candidate', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
