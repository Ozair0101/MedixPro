<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePayrollLineRequest;
use App\Http\Resources\PayrollLineResource;
use App\Models\PayrollLine;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `payroll_line`.
 */
class PayrollLineController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = PayrollLine::query()->paginate($perPage);

        return response()->json([
            'data' => PayrollLineResource::collection($rows->items()),
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
        $payrollLine = PayrollLine::findOrFail($id);

        return response()->json(['data' => new PayrollLineResource($payrollLine)]);
    }

    public function store(StorePayrollLineRequest $request): JsonResponse
    {
        $payrollLine = PayrollLine::create($request->validated());

        AuditLogger::record('create', 'payroll_line', (string) $payrollLine->getKey());

        return response()->json(
            ['data' => new PayrollLineResource($payrollLine)], 201
        );
    }

    public function update(StorePayrollLineRequest $request, string $id): JsonResponse
    {
        $payrollLine = PayrollLine::findOrFail($id);
        $payrollLine->update($request->validated());

        AuditLogger::record('update', 'payroll_line', $id);

        return response()->json(['data' => new PayrollLineResource($payrollLine)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $payrollLine = PayrollLine::findOrFail($id);

        $payrollLine->delete();

        AuditLogger::record('delete', 'payroll_line', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
