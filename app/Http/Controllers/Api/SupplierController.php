<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSupplierRequest;
use App\Http\Resources\SupplierResource;
use App\Models\Supplier;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `supplier`.
 */
class SupplierController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = Supplier::query()->paginate($perPage);

        return response()->json([
            'data' => SupplierResource::collection($rows->items()),
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
        $supplier = Supplier::findOrFail($id);

        return response()->json(['data' => new SupplierResource($supplier)]);
    }

    public function store(StoreSupplierRequest $request): JsonResponse
    {
        $supplier = Supplier::create($request->validated());

        AuditLogger::record('create', 'supplier', (string) $supplier->getKey());

        return response()->json(
            ['data' => new SupplierResource($supplier)], 201
        );
    }

    public function update(StoreSupplierRequest $request, string $id): JsonResponse
    {
        $supplier = Supplier::findOrFail($id);
        $supplier->update($request->validated());

        AuditLogger::record('update', 'supplier', $id);

        return response()->json(['data' => new SupplierResource($supplier)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $supplier = Supplier::findOrFail($id);

        // Deactivate rather than delete: clinical and financial records
        // must stay resolvable for anything that already references them.
        $supplier->update(['is_active' => false]);

        AuditLogger::record('delete', 'supplier', $id);

        return response()->json(['message' => 'Deactivated.']);
    }
}
