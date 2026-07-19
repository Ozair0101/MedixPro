<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePatientLinkRequest;
use App\Http\Resources\PatientLinkResource;
use App\Models\PatientLink;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `patient_link`.
 */
class PatientLinkController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = PatientLink::query()->paginate($perPage);

        return response()->json([
            'data' => PatientLinkResource::collection($rows->items()),
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
        $patientLink = PatientLink::findOrFail($id);

        return response()->json(['data' => new PatientLinkResource($patientLink)]);
    }

    public function store(StorePatientLinkRequest $request): JsonResponse
    {
        $patientLink = PatientLink::create($request->validated());

        AuditLogger::record('create', 'patient_link', (string) $patientLink->getKey());

        return response()->json(
            ['data' => new PatientLinkResource($patientLink)], 201
        );
    }

    public function update(StorePatientLinkRequest $request, string $id): JsonResponse
    {
        $patientLink = PatientLink::findOrFail($id);
        $patientLink->update($request->validated());

        AuditLogger::record('update', 'patient_link', $id);

        return response()->json(['data' => new PatientLinkResource($patientLink)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $patientLink = PatientLink::findOrFail($id);

        $patientLink->delete();

        AuditLogger::record('delete', 'patient_link', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
