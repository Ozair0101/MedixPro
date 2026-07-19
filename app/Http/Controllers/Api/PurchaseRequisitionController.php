<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePurchaseRequisitionRequest;
use App\Http\Resources\PurchaseRequisitionResource;
use App\Models\PurchaseRequisition;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `purchase_requisition`.
 */
class PurchaseRequisitionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = PurchaseRequisition::query()->paginate($perPage);

        return response()->json([
            'data' => PurchaseRequisitionResource::collection($rows->items()),
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
        $purchaseRequisition = PurchaseRequisition::findOrFail($id);

        return response()->json(['data' => new PurchaseRequisitionResource($purchaseRequisition)]);
    }

    public function store(StorePurchaseRequisitionRequest $request): JsonResponse
    {
        $purchaseRequisition = PurchaseRequisition::create($request->validated());

        AuditLogger::record('create', 'purchase_requisition', (string) $purchaseRequisition->getKey());

        return response()->json(
            ['data' => new PurchaseRequisitionResource($purchaseRequisition)], 201
        );
    }

    public function update(StorePurchaseRequisitionRequest $request, string $id): JsonResponse
    {
        $purchaseRequisition = PurchaseRequisition::findOrFail($id);
        $purchaseRequisition->update($request->validated());

        AuditLogger::record('update', 'purchase_requisition', $id);

        return response()->json(['data' => new PurchaseRequisitionResource($purchaseRequisition)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $purchaseRequisition = PurchaseRequisition::findOrFail($id);

        $purchaseRequisition->delete();

        AuditLogger::record('delete', 'purchase_requisition', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
