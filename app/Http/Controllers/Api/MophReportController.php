<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMophReportRequest;
use App\Http\Resources\MophReportResource;
use App\Models\MophReport;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `moph_report`.
 */
class MophReportController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = MophReport::query()->paginate($perPage);

        return response()->json([
            'data' => MophReportResource::collection($rows->items()),
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
        $mophReport = MophReport::findOrFail($id);

        return response()->json(['data' => new MophReportResource($mophReport)]);
    }

    public function store(StoreMophReportRequest $request): JsonResponse
    {
        $mophReport = MophReport::create($request->validated());

        AuditLogger::record('create', 'moph_report', (string) $mophReport->getKey());

        return response()->json(
            ['data' => new MophReportResource($mophReport)], 201
        );
    }

    public function update(StoreMophReportRequest $request, string $id): JsonResponse
    {
        $mophReport = MophReport::findOrFail($id);
        $mophReport->update($request->validated());

        AuditLogger::record('update', 'moph_report', $id);

        return response()->json(['data' => new MophReportResource($mophReport)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $mophReport = MophReport::findOrFail($id);

        $mophReport->delete();

        AuditLogger::record('delete', 'moph_report', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
