<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreChargeItemRequest;
use App\Http\Resources\ChargeItemResource;
use App\Models\ChargeItem;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `charge_item`.
 */
class ChargeItemController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = ChargeItem::query()->paginate($perPage);

        return response()->json([
            'data' => ChargeItemResource::collection($rows->items()),
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
        $chargeItem = ChargeItem::findOrFail($id);

        return response()->json(['data' => new ChargeItemResource($chargeItem)]);
    }

    public function store(StoreChargeItemRequest $request): JsonResponse
    {
        $chargeItem = ChargeItem::create($request->validated());

        AuditLogger::record('create', 'charge_item', (string) $chargeItem->getKey());

        return response()->json(
            ['data' => new ChargeItemResource($chargeItem)], 201
        );
    }

    public function update(StoreChargeItemRequest $request, string $id): JsonResponse
    {
        $chargeItem = ChargeItem::findOrFail($id);
        $chargeItem->update($request->validated());

        AuditLogger::record('update', 'charge_item', $id);

        return response()->json(['data' => new ChargeItemResource($chargeItem)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $chargeItem = ChargeItem::findOrFail($id);

        $chargeItem->delete();

        AuditLogger::record('delete', 'charge_item', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
