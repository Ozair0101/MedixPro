<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePayrollPeriodRequest;
use App\Http\Resources\PayrollPeriodResource;
use App\Models\PayrollPeriod;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `payroll_period`.
 */
class PayrollPeriodController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = PayrollPeriod::query()->paginate($perPage);

        return response()->json([
            'data' => PayrollPeriodResource::collection($rows->items()),
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
        $payrollPeriod = PayrollPeriod::findOrFail($id);

        return response()->json(['data' => new PayrollPeriodResource($payrollPeriod)]);
    }

    public function store(StorePayrollPeriodRequest $request): JsonResponse
    {
        $payrollPeriod = PayrollPeriod::create($request->validated());

        AuditLogger::record('create', 'payroll_period', (string) $payrollPeriod->getKey());

        return response()->json(
            ['data' => new PayrollPeriodResource($payrollPeriod)], 201
        );
    }

    public function update(StorePayrollPeriodRequest $request, string $id): JsonResponse
    {
        $payrollPeriod = PayrollPeriod::findOrFail($id);
        $payrollPeriod->update($request->validated());

        AuditLogger::record('update', 'payroll_period', $id);

        return response()->json(['data' => new PayrollPeriodResource($payrollPeriod)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $payrollPeriod = PayrollPeriod::findOrFail($id);

        $payrollPeriod->delete();

        AuditLogger::record('delete', 'payroll_period', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
