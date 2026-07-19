<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDispenseItemRequest;
use App\Http\Resources\DispenseItemResource;
use App\Models\DispenseItem;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `dispense_item`.
 */
class DispenseItemController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = DispenseItem::query()->paginate($perPage);

        return response()->json([
            'data' => DispenseItemResource::collection($rows->items()),
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
        $dispenseItem = DispenseItem::findOrFail($id);

        return response()->json(['data' => new DispenseItemResource($dispenseItem)]);
    }

    public function store(StoreDispenseItemRequest $request): JsonResponse
    {
        $dispenseItem = DispenseItem::create($request->validated());

        AuditLogger::record('create', 'dispense_item', (string) $dispenseItem->getKey());

        return response()->json(
            ['data' => new DispenseItemResource($dispenseItem)], 201
        );
    }

    public function update(StoreDispenseItemRequest $request, string $id): JsonResponse
    {
        $dispenseItem = DispenseItem::findOrFail($id);
        $dispenseItem->update($request->validated());

        AuditLogger::record('update', 'dispense_item', $id);

        return response()->json(['data' => new DispenseItemResource($dispenseItem)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $dispenseItem = DispenseItem::findOrFail($id);

        $dispenseItem->delete();

        AuditLogger::record('delete', 'dispense_item', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
