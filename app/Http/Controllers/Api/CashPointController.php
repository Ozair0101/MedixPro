<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCashPointRequest;
use App\Http\Resources\CashPointResource;
use App\Models\CashPoint;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `cash_point`.
 */
class CashPointController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = CashPoint::query()->paginate($perPage);

        return response()->json([
            'data' => CashPointResource::collection($rows->items()),
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
        $cashPoint = CashPoint::findOrFail($id);

        return response()->json(['data' => new CashPointResource($cashPoint)]);
    }

    public function store(StoreCashPointRequest $request): JsonResponse
    {
        $cashPoint = CashPoint::create($request->validated());

        AuditLogger::record('create', 'cash_point', (string) $cashPoint->getKey());

        return response()->json(
            ['data' => new CashPointResource($cashPoint)], 201
        );
    }

    public function update(StoreCashPointRequest $request, string $id): JsonResponse
    {
        $cashPoint = CashPoint::findOrFail($id);
        $cashPoint->update($request->validated());

        AuditLogger::record('update', 'cash_point', $id);

        return response()->json(['data' => new CashPointResource($cashPoint)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $cashPoint = CashPoint::findOrFail($id);

        // Deactivate rather than delete: clinical and financial records
        // must stay resolvable for anything that already references them.
        $cashPoint->update(['is_active' => false]);

        AuditLogger::record('delete', 'cash_point', $id);

        return response()->json(['message' => 'Deactivated.']);
    }
}
