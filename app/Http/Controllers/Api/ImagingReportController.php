<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreImagingReportRequest;
use App\Http\Resources\ImagingReportResource;
use App\Models\ImagingReport;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `imaging_report`.
 */
class ImagingReportController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = ImagingReport::query()->paginate($perPage);

        return response()->json([
            'data' => ImagingReportResource::collection($rows->items()),
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
        $imagingReport = ImagingReport::findOrFail($id);

        return response()->json(['data' => new ImagingReportResource($imagingReport)]);
    }

    public function store(StoreImagingReportRequest $request): JsonResponse
    {
        $imagingReport = ImagingReport::create($request->validated());

        AuditLogger::record('create', 'imaging_report', (string) $imagingReport->getKey());

        return response()->json(
            ['data' => new ImagingReportResource($imagingReport)], 201
        );
    }

    public function update(StoreImagingReportRequest $request, string $id): JsonResponse
    {
        $imagingReport = ImagingReport::findOrFail($id);
        $imagingReport->update($request->validated());

        AuditLogger::record('update', 'imaging_report', $id);

        return response()->json(['data' => new ImagingReportResource($imagingReport)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $imagingReport = ImagingReport::findOrFail($id);

        $imagingReport->delete();

        AuditLogger::record('delete', 'imaging_report', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
