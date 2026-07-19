<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePatientRelationRequest;
use App\Http\Resources\PatientRelationResource;
use App\Models\PatientRelation;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `patient_relation`.
 */
class PatientRelationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = PatientRelation::query()->paginate($perPage);

        return response()->json([
            'data' => PatientRelationResource::collection($rows->items()),
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
        $patientRelation = PatientRelation::findOrFail($id);

        return response()->json(['data' => new PatientRelationResource($patientRelation)]);
    }

    public function store(StorePatientRelationRequest $request): JsonResponse
    {
        $patientRelation = PatientRelation::create($request->validated());

        AuditLogger::record('create', 'patient_relation', (string) $patientRelation->getKey());

        return response()->json(
            ['data' => new PatientRelationResource($patientRelation)], 201
        );
    }

    public function update(StorePatientRelationRequest $request, string $id): JsonResponse
    {
        $patientRelation = PatientRelation::findOrFail($id);
        $patientRelation->update($request->validated());

        AuditLogger::record('update', 'patient_relation', $id);

        return response()->json(['data' => new PatientRelationResource($patientRelation)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $patientRelation = PatientRelation::findOrFail($id);

        $patientRelation->delete();

        AuditLogger::record('delete', 'patient_relation', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
