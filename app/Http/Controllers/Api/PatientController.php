<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePatientRequest;
use App\Http\Resources\PatientResource;
use App\Models\Patient;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `patient`.
 */
class PatientController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = Patient::query()->paginate($perPage);

        return response()->json([
            'data' => PatientResource::collection($rows->items()),
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
        $patient = Patient::findOrFail($id);

        return response()->json(['data' => new PatientResource($patient)]);
    }

    public function store(StorePatientRequest $request): JsonResponse
    {
        $patient = Patient::create($request->validated());

        AuditLogger::record('create', 'patient', (string) $patient->getKey());

        return response()->json(
            ['data' => new PatientResource($patient)], 201
        );
    }

    public function update(StorePatientRequest $request, string $id): JsonResponse
    {
        $patient = Patient::findOrFail($id);
        $patient->update($request->validated());

        AuditLogger::record('update', 'patient', $id);

        return response()->json(['data' => new PatientResource($patient)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $patient = Patient::findOrFail($id);

        // Deactivate rather than delete: clinical and financial records
        // must stay resolvable for anything that already references them.
        $patient->update(['is_active' => false]);

        AuditLogger::record('delete', 'patient', $id);

        return response()->json(['message' => 'Deactivated.']);
    }
}
