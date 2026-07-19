<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBillableItemRequest;
use App\Http\Resources\BillableItemResource;
use App\Models\BillableItem;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `billable_item`.
 */
class BillableItemController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = BillableItem::query()->paginate($perPage);

        return response()->json([
            'data' => BillableItemResource::collection($rows->items()),
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
        $billableItem = BillableItem::findOrFail($id);

        return response()->json(['data' => new BillableItemResource($billableItem)]);
    }

    public function store(StoreBillableItemRequest $request): JsonResponse
    {
        $billableItem = BillableItem::create($request->validated());

        AuditLogger::record('create', 'billable_item', (string) $billableItem->getKey());

        return response()->json(
            ['data' => new BillableItemResource($billableItem)], 201
        );
    }

    public function update(StoreBillableItemRequest $request, string $id): JsonResponse
    {
        $billableItem = BillableItem::findOrFail($id);
        $billableItem->update($request->validated());

        AuditLogger::record('update', 'billable_item', $id);

        return response()->json(['data' => new BillableItemResource($billableItem)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $billableItem = BillableItem::findOrFail($id);

        // Deactivate rather than delete: clinical and financial records
        // must stay resolvable for anything that already references them.
        $billableItem->update(['is_active' => false]);

        AuditLogger::record('delete', 'billable_item', $id);

        return response()->json(['message' => 'Deactivated.']);
    }
}
