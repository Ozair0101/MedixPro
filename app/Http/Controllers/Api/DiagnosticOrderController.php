<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDiagnosticOrderRequest;
use App\Http\Resources\DiagnosticOrderResource;
use App\Models\DiagnosticOrder;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `diagnostic_order`.
 */
class DiagnosticOrderController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = DiagnosticOrder::query()->paginate($perPage);

        return response()->json([
            'data' => DiagnosticOrderResource::collection($rows->items()),
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
        $diagnosticOrder = DiagnosticOrder::findOrFail($id);

        return response()->json(['data' => new DiagnosticOrderResource($diagnosticOrder)]);
    }

    public function store(StoreDiagnosticOrderRequest $request): JsonResponse
    {
        $diagnosticOrder = DiagnosticOrder::create($request->validated());

        AuditLogger::record('create', 'diagnostic_order', (string) $diagnosticOrder->getKey());

        return response()->json(
            ['data' => new DiagnosticOrderResource($diagnosticOrder)], 201
        );
    }

    public function update(StoreDiagnosticOrderRequest $request, string $id): JsonResponse
    {
        $diagnosticOrder = DiagnosticOrder::findOrFail($id);
        $diagnosticOrder->update($request->validated());

        AuditLogger::record('update', 'diagnostic_order', $id);

        return response()->json(['data' => new DiagnosticOrderResource($diagnosticOrder)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $diagnosticOrder = DiagnosticOrder::findOrFail($id);

        $diagnosticOrder->delete();

        AuditLogger::record('delete', 'diagnostic_order', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
