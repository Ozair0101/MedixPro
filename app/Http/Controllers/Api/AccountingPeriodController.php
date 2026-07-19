<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAccountingPeriodRequest;
use App\Http\Resources\AccountingPeriodResource;
use App\Models\AccountingPeriod;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `accounting_period`.
 */
class AccountingPeriodController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = AccountingPeriod::query()->paginate($perPage);

        return response()->json([
            'data' => AccountingPeriodResource::collection($rows->items()),
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
        $accountingPeriod = AccountingPeriod::findOrFail($id);

        return response()->json(['data' => new AccountingPeriodResource($accountingPeriod)]);
    }

    public function store(StoreAccountingPeriodRequest $request): JsonResponse
    {
        $accountingPeriod = AccountingPeriod::create($request->validated());

        AuditLogger::record('create', 'accounting_period', (string) $accountingPeriod->getKey());

        return response()->json(
            ['data' => new AccountingPeriodResource($accountingPeriod)], 201
        );
    }

    public function update(StoreAccountingPeriodRequest $request, string $id): JsonResponse
    {
        $accountingPeriod = AccountingPeriod::findOrFail($id);
        $accountingPeriod->update($request->validated());

        AuditLogger::record('update', 'accounting_period', $id);

        return response()->json(['data' => new AccountingPeriodResource($accountingPeriod)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $accountingPeriod = AccountingPeriod::findOrFail($id);

        $accountingPeriod->delete();

        AuditLogger::record('delete', 'accounting_period', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
