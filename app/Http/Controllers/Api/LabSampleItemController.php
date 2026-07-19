<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLabSampleItemRequest;
use App\Http\Resources\LabSampleItemResource;
use App\Models\LabSampleItem;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `lab_sample_item`.
 */
class LabSampleItemController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = LabSampleItem::query()->paginate($perPage);

        return response()->json([
            'data' => LabSampleItemResource::collection($rows->items()),
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
        $labSampleItem = LabSampleItem::findOrFail($id);

        return response()->json(['data' => new LabSampleItemResource($labSampleItem)]);
    }

    public function store(StoreLabSampleItemRequest $request): JsonResponse
    {
        $labSampleItem = LabSampleItem::create($request->validated());

        AuditLogger::record('create', 'lab_sample_item', (string) $labSampleItem->getKey());

        return response()->json(
            ['data' => new LabSampleItemResource($labSampleItem)], 201
        );
    }

    public function update(StoreLabSampleItemRequest $request, string $id): JsonResponse
    {
        $labSampleItem = LabSampleItem::findOrFail($id);
        $labSampleItem->update($request->validated());

        AuditLogger::record('update', 'lab_sample_item', $id);

        return response()->json(['data' => new LabSampleItemResource($labSampleItem)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $labSampleItem = LabSampleItem::findOrFail($id);

        $labSampleItem->delete();

        AuditLogger::record('delete', 'lab_sample_item', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
