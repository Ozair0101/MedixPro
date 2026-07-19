<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLabPanelRequest;
use App\Http\Resources\LabPanelResource;
use App\Models\LabPanel;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `lab_panel`.
 */
class LabPanelController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = LabPanel::query()->paginate($perPage);

        return response()->json([
            'data' => LabPanelResource::collection($rows->items()),
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
        $labPanel = LabPanel::findOrFail($id);

        return response()->json(['data' => new LabPanelResource($labPanel)]);
    }

    public function store(StoreLabPanelRequest $request): JsonResponse
    {
        $labPanel = LabPanel::create($request->validated());

        AuditLogger::record('create', 'lab_panel', (string) $labPanel->getKey());

        return response()->json(
            ['data' => new LabPanelResource($labPanel)], 201
        );
    }

    public function update(StoreLabPanelRequest $request, string $id): JsonResponse
    {
        $labPanel = LabPanel::findOrFail($id);
        $labPanel->update($request->validated());

        AuditLogger::record('update', 'lab_panel', $id);

        return response()->json(['data' => new LabPanelResource($labPanel)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $labPanel = LabPanel::findOrFail($id);

        $labPanel->delete();

        AuditLogger::record('delete', 'lab_panel', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
