<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCashierShiftRequest;
use App\Http\Resources\CashierShiftResource;
use App\Models\CashierShift;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `cashier_shift`.
 */
class CashierShiftController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = CashierShift::query()->paginate($perPage);

        return response()->json([
            'data' => CashierShiftResource::collection($rows->items()),
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
        $cashierShift = CashierShift::findOrFail($id);

        return response()->json(['data' => new CashierShiftResource($cashierShift)]);
    }

    public function store(StoreCashierShiftRequest $request): JsonResponse
    {
        $cashierShift = CashierShift::create($request->validated());

        AuditLogger::record('create', 'cashier_shift', (string) $cashierShift->getKey());

        return response()->json(
            ['data' => new CashierShiftResource($cashierShift)], 201
        );
    }

    public function update(StoreCashierShiftRequest $request, string $id): JsonResponse
    {
        $cashierShift = CashierShift::findOrFail($id);
        $cashierShift->update($request->validated());

        AuditLogger::record('update', 'cashier_shift', $id);

        return response()->json(['data' => new CashierShiftResource($cashierShift)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $cashierShift = CashierShift::findOrFail($id);

        $cashierShift->delete();

        AuditLogger::record('delete', 'cashier_shift', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
