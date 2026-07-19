<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMarSlotRequest;
use App\Http\Resources\MarSlotResource;
use App\Models\MarSlot;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `mar_slot`.
 */
class MarSlotController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = MarSlot::query()->paginate($perPage);

        return response()->json([
            'data' => MarSlotResource::collection($rows->items()),
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
        $marSlot = MarSlot::findOrFail($id);

        return response()->json(['data' => new MarSlotResource($marSlot)]);
    }

    public function store(StoreMarSlotRequest $request): JsonResponse
    {
        $marSlot = MarSlot::create($request->validated());

        AuditLogger::record('create', 'mar_slot', (string) $marSlot->getKey());

        return response()->json(
            ['data' => new MarSlotResource($marSlot)], 201
        );
    }

    public function update(StoreMarSlotRequest $request, string $id): JsonResponse
    {
        $marSlot = MarSlot::findOrFail($id);
        $marSlot->update($request->validated());

        AuditLogger::record('update', 'mar_slot', $id);

        return response()->json(['data' => new MarSlotResource($marSlot)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $marSlot = MarSlot::findOrFail($id);

        $marSlot->delete();

        AuditLogger::record('delete', 'mar_slot', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
