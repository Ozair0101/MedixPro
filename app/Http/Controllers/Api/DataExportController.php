<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDataExportRequest;
use App\Http\Resources\DataExportResource;
use App\Models\DataExport;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `data_export`.
 */
class DataExportController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = DataExport::query()->paginate($perPage);

        return response()->json([
            'data' => DataExportResource::collection($rows->items()),
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
        $dataExport = DataExport::findOrFail($id);

        return response()->json(['data' => new DataExportResource($dataExport)]);
    }

    public function store(StoreDataExportRequest $request): JsonResponse
    {
        $dataExport = DataExport::create($request->validated());

        AuditLogger::record('create', 'data_export', (string) $dataExport->getKey());

        return response()->json(
            ['data' => new DataExportResource($dataExport)], 201
        );
    }

    public function update(StoreDataExportRequest $request, string $id): JsonResponse
    {
        $dataExport = DataExport::findOrFail($id);
        $dataExport->update($request->validated());

        AuditLogger::record('update', 'data_export', $id);

        return response()->json(['data' => new DataExportResource($dataExport)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $dataExport = DataExport::findOrFail($id);

        $dataExport->delete();

        AuditLogger::record('delete', 'data_export', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
