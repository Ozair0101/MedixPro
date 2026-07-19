<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePayrollRunRequest;
use App\Http\Resources\PayrollRunResource;
use App\Models\PayrollRun;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `payroll_run`.
 */
class PayrollRunController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = PayrollRun::query()->paginate($perPage);

        return response()->json([
            'data' => PayrollRunResource::collection($rows->items()),
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
        $payrollRun = PayrollRun::findOrFail($id);

        return response()->json(['data' => new PayrollRunResource($payrollRun)]);
    }

    public function store(StorePayrollRunRequest $request): JsonResponse
    {
        $payrollRun = PayrollRun::create($request->validated());

        AuditLogger::record('create', 'payroll_run', (string) $payrollRun->getKey());

        return response()->json(
            ['data' => new PayrollRunResource($payrollRun)], 201
        );
    }

    public function update(StorePayrollRunRequest $request, string $id): JsonResponse
    {
        $payrollRun = PayrollRun::findOrFail($id);
        $payrollRun->update($request->validated());

        AuditLogger::record('update', 'payroll_run', $id);

        return response()->json(['data' => new PayrollRunResource($payrollRun)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $payrollRun = PayrollRun::findOrFail($id);

        $payrollRun->delete();

        AuditLogger::record('delete', 'payroll_run', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
