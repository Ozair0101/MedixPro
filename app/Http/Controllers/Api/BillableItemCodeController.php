<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBillableItemCodeRequest;
use App\Http\Resources\BillableItemCodeResource;
use App\Models\BillableItemCode;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `billable_item_code`.
 */
class BillableItemCodeController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = BillableItemCode::query()->paginate($perPage);

        return response()->json([
            'data' => BillableItemCodeResource::collection($rows->items()),
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
        $billableItemCode = BillableItemCode::findOrFail($id);

        return response()->json(['data' => new BillableItemCodeResource($billableItemCode)]);
    }

    public function store(StoreBillableItemCodeRequest $request): JsonResponse
    {
        $billableItemCode = BillableItemCode::create($request->validated());

        AuditLogger::record('create', 'billable_item_code', (string) $billableItemCode->getKey());

        return response()->json(
            ['data' => new BillableItemCodeResource($billableItemCode)], 201
        );
    }

    public function update(StoreBillableItemCodeRequest $request, string $id): JsonResponse
    {
        $billableItemCode = BillableItemCode::findOrFail($id);
        $billableItemCode->update($request->validated());

        AuditLogger::record('update', 'billable_item_code', $id);

        return response()->json(['data' => new BillableItemCodeResource($billableItemCode)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $billableItemCode = BillableItemCode::findOrFail($id);

        $billableItemCode->delete();

        AuditLogger::record('delete', 'billable_item_code', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
