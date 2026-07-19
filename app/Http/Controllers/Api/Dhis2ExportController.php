<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDhis2ExportRequest;
use App\Http\Resources\Dhis2ExportResource;
use App\Models\Dhis2Export;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `dhis2_export`.
 */
class Dhis2ExportController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = Dhis2Export::query()->paginate($perPage);

        return response()->json([
            'data' => Dhis2ExportResource::collection($rows->items()),
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
        $dhis2Export = Dhis2Export::findOrFail($id);

        return response()->json(['data' => new Dhis2ExportResource($dhis2Export)]);
    }

    public function store(StoreDhis2ExportRequest $request): JsonResponse
    {
        $dhis2Export = Dhis2Export::create($request->validated());

        AuditLogger::record('create', 'dhis2_export', (string) $dhis2Export->getKey());

        return response()->json(
            ['data' => new Dhis2ExportResource($dhis2Export)], 201
        );
    }

    public function update(StoreDhis2ExportRequest $request, string $id): JsonResponse
    {
        $dhis2Export = Dhis2Export::findOrFail($id);
        $dhis2Export->update($request->validated());

        AuditLogger::record('update', 'dhis2_export', $id);

        return response()->json(['data' => new Dhis2ExportResource($dhis2Export)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $dhis2Export = Dhis2Export::findOrFail($id);

        $dhis2Export->delete();

        AuditLogger::record('delete', 'dhis2_export', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
