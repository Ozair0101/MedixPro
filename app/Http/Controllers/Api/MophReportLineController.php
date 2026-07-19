<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMophReportLineRequest;
use App\Http\Resources\MophReportLineResource;
use App\Models\MophReportLine;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `moph_report_line`.
 */
class MophReportLineController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = MophReportLine::query()->paginate($perPage);

        return response()->json([
            'data' => MophReportLineResource::collection($rows->items()),
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
        $mophReportLine = MophReportLine::findOrFail($id);

        return response()->json(['data' => new MophReportLineResource($mophReportLine)]);
    }

    public function store(StoreMophReportLineRequest $request): JsonResponse
    {
        $mophReportLine = MophReportLine::create($request->validated());

        AuditLogger::record('create', 'moph_report_line', (string) $mophReportLine->getKey());

        return response()->json(
            ['data' => new MophReportLineResource($mophReportLine)], 201
        );
    }

    public function update(StoreMophReportLineRequest $request, string $id): JsonResponse
    {
        $mophReportLine = MophReportLine::findOrFail($id);
        $mophReportLine->update($request->validated());

        AuditLogger::record('update', 'moph_report_line', $id);

        return response()->json(['data' => new MophReportLineResource($mophReportLine)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $mophReportLine = MophReportLine::findOrFail($id);

        $mophReportLine->delete();

        AuditLogger::record('delete', 'moph_report_line', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
