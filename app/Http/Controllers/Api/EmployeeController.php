<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Resources\EmployeeResource;
use App\Models\Employee;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `employee`.
 */
class EmployeeController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = Employee::query()->paginate($perPage);

        return response()->json([
            'data' => EmployeeResource::collection($rows->items()),
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
        $employee = Employee::findOrFail($id);

        return response()->json(['data' => new EmployeeResource($employee)]);
    }

    public function store(StoreEmployeeRequest $request): JsonResponse
    {
        $employee = Employee::create($request->validated());

        AuditLogger::record('create', 'employee', (string) $employee->getKey());

        return response()->json(
            ['data' => new EmployeeResource($employee)], 201
        );
    }

    public function update(StoreEmployeeRequest $request, string $id): JsonResponse
    {
        $employee = Employee::findOrFail($id);
        $employee->update($request->validated());

        AuditLogger::record('update', 'employee', $id);

        return response()->json(['data' => new EmployeeResource($employee)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $employee = Employee::findOrFail($id);

        // Deactivate rather than delete: clinical and financial records
        // must stay resolvable for anything that already references them.
        $employee->update(['is_active' => false]);

        AuditLogger::record('delete', 'employee', $id);

        return response()->json(['message' => 'Deactivated.']);
    }
}
