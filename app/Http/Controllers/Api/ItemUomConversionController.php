<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreItemUomConversionRequest;
use App\Http\Resources\ItemUomConversionResource;
use App\Models\ItemUomConversion;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `item_uom_conversion`.
 */
class ItemUomConversionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = ItemUomConversion::query()->paginate($perPage);

        return response()->json([
            'data' => ItemUomConversionResource::collection($rows->items()),
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
        $itemUomConversion = ItemUomConversion::findOrFail($id);

        return response()->json(['data' => new ItemUomConversionResource($itemUomConversion)]);
    }

    public function store(StoreItemUomConversionRequest $request): JsonResponse
    {
        $itemUomConversion = ItemUomConversion::create($request->validated());

        AuditLogger::record('create', 'item_uom_conversion', (string) $itemUomConversion->getKey());

        return response()->json(
            ['data' => new ItemUomConversionResource($itemUomConversion)], 201
        );
    }

    public function update(StoreItemUomConversionRequest $request, string $id): JsonResponse
    {
        $itemUomConversion = ItemUomConversion::findOrFail($id);
        $itemUomConversion->update($request->validated());

        AuditLogger::record('update', 'item_uom_conversion', $id);

        return response()->json(['data' => new ItemUomConversionResource($itemUomConversion)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $itemUomConversion = ItemUomConversion::findOrFail($id);

        $itemUomConversion->delete();

        AuditLogger::record('delete', 'item_uom_conversion', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
