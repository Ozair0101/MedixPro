<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePatientAccountRequest;
use App\Http\Resources\PatientAccountResource;
use App\Models\PatientAccount;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `patient_account`.
 */
class PatientAccountController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = PatientAccount::query()->paginate($perPage);

        return response()->json([
            'data' => PatientAccountResource::collection($rows->items()),
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
        $patientAccount = PatientAccount::findOrFail($id);

        return response()->json(['data' => new PatientAccountResource($patientAccount)]);
    }

    public function store(StorePatientAccountRequest $request): JsonResponse
    {
        $patientAccount = PatientAccount::create($request->validated());

        AuditLogger::record('create', 'patient_account', (string) $patientAccount->getKey());

        return response()->json(
            ['data' => new PatientAccountResource($patientAccount)], 201
        );
    }

    public function update(StorePatientAccountRequest $request, string $id): JsonResponse
    {
        $patientAccount = PatientAccount::findOrFail($id);
        $patientAccount->update($request->validated());

        AuditLogger::record('update', 'patient_account', $id);

        return response()->json(['data' => new PatientAccountResource($patientAccount)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $patientAccount = PatientAccount::findOrFail($id);

        $patientAccount->delete();

        AuditLogger::record('delete', 'patient_account', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
