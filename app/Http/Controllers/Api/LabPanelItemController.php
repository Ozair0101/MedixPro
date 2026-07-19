<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLabPanelItemRequest;
use App\Http\Resources\LabPanelItemResource;
use App\Models\LabPanelItem;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `lab_panel_item`.
 */
class LabPanelItemController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = LabPanelItem::query()->paginate($perPage);

        return response()->json([
            'data' => LabPanelItemResource::collection($rows->items()),
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
        $labPanelItem = LabPanelItem::findOrFail($id);

        return response()->json(['data' => new LabPanelItemResource($labPanelItem)]);
    }

    public function store(StoreLabPanelItemRequest $request): JsonResponse
    {
        $labPanelItem = LabPanelItem::create($request->validated());

        AuditLogger::record('create', 'lab_panel_item', (string) $labPanelItem->getKey());

        return response()->json(
            ['data' => new LabPanelItemResource($labPanelItem)], 201
        );
    }

    public function update(StoreLabPanelItemRequest $request, string $id): JsonResponse
    {
        $labPanelItem = LabPanelItem::findOrFail($id);
        $labPanelItem->update($request->validated());

        AuditLogger::record('update', 'lab_panel_item', $id);

        return response()->json(['data' => new LabPanelItemResource($labPanelItem)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $labPanelItem = LabPanelItem::findOrFail($id);

        $labPanelItem->delete();

        AuditLogger::record('delete', 'lab_panel_item', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
