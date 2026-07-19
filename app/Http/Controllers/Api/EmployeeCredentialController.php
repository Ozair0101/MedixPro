<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEmployeeCredentialRequest;
use App\Http\Resources\EmployeeCredentialResource;
use App\Models\EmployeeCredential;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `employee_credential`.
 */
class EmployeeCredentialController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = EmployeeCredential::query()->paginate($perPage);

        return response()->json([
            'data' => EmployeeCredentialResource::collection($rows->items()),
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
        $employeeCredential = EmployeeCredential::findOrFail($id);

        return response()->json(['data' => new EmployeeCredentialResource($employeeCredential)]);
    }

    public function store(StoreEmployeeCredentialRequest $request): JsonResponse
    {
        $employeeCredential = EmployeeCredential::create($request->validated());

        AuditLogger::record('create', 'employee_credential', (string) $employeeCredential->getKey());

        return response()->json(
            ['data' => new EmployeeCredentialResource($employeeCredential)], 201
        );
    }

    public function update(StoreEmployeeCredentialRequest $request, string $id): JsonResponse
    {
        $employeeCredential = EmployeeCredential::findOrFail($id);
        $employeeCredential->update($request->validated());

        AuditLogger::record('update', 'employee_credential', $id);

        return response()->json(['data' => new EmployeeCredentialResource($employeeCredential)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $employeeCredential = EmployeeCredential::findOrFail($id);

        $employeeCredential->delete();

        AuditLogger::record('delete', 'employee_credential', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
