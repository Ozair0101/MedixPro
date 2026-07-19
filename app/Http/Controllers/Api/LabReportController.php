<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLabReportRequest;
use App\Http\Resources\LabReportResource;
use App\Models\LabReport;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `lab_report`.
 */
class LabReportController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = LabReport::query()->paginate($perPage);

        return response()->json([
            'data' => LabReportResource::collection($rows->items()),
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
        $labReport = LabReport::findOrFail($id);

        return response()->json(['data' => new LabReportResource($labReport)]);
    }

    public function store(StoreLabReportRequest $request): JsonResponse
    {
        $labReport = LabReport::create($request->validated());

        AuditLogger::record('create', 'lab_report', (string) $labReport->getKey());

        return response()->json(
            ['data' => new LabReportResource($labReport)], 201
        );
    }

    public function update(StoreLabReportRequest $request, string $id): JsonResponse
    {
        $labReport = LabReport::findOrFail($id);
        $labReport->update($request->validated());

        AuditLogger::record('update', 'lab_report', $id);

        return response()->json(['data' => new LabReportResource($labReport)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $labReport = LabReport::findOrFail($id);

        $labReport->delete();

        AuditLogger::record('delete', 'lab_report', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
