<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLabAnalysiRequest;
use App\Http\Resources\LabAnalysiResource;
use App\Models\LabAnalysi;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `lab_analysis`.
 */
class LabAnalysiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = LabAnalysi::query()->paginate($perPage);

        return response()->json([
            'data' => LabAnalysiResource::collection($rows->items()),
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
        $labAnalysi = LabAnalysi::findOrFail($id);

        return response()->json(['data' => new LabAnalysiResource($labAnalysi)]);
    }

    public function store(StoreLabAnalysiRequest $request): JsonResponse
    {
        $labAnalysi = LabAnalysi::create($request->validated());

        AuditLogger::record('create', 'lab_analysis', (string) $labAnalysi->getKey());

        return response()->json(
            ['data' => new LabAnalysiResource($labAnalysi)], 201
        );
    }

    public function update(StoreLabAnalysiRequest $request, string $id): JsonResponse
    {
        $labAnalysi = LabAnalysi::findOrFail($id);
        $labAnalysi->update($request->validated());

        AuditLogger::record('update', 'lab_analysis', $id);

        return response()->json(['data' => new LabAnalysiResource($labAnalysi)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $labAnalysi = LabAnalysi::findOrFail($id);

        $labAnalysi->delete();

        AuditLogger::record('delete', 'lab_analysis', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
