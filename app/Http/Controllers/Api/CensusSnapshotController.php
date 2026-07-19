<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCensusSnapshotRequest;
use App\Http\Resources\CensusSnapshotResource;
use App\Models\CensusSnapshot;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `census_snapshot`.
 */
class CensusSnapshotController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = CensusSnapshot::query()->paginate($perPage);

        return response()->json([
            'data' => CensusSnapshotResource::collection($rows->items()),
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
        $censusSnapshot = CensusSnapshot::findOrFail($id);

        return response()->json(['data' => new CensusSnapshotResource($censusSnapshot)]);
    }

    public function store(StoreCensusSnapshotRequest $request): JsonResponse
    {
        $censusSnapshot = CensusSnapshot::create($request->validated());

        AuditLogger::record('create', 'census_snapshot', (string) $censusSnapshot->getKey());

        return response()->json(
            ['data' => new CensusSnapshotResource($censusSnapshot)], 201
        );
    }

    public function update(StoreCensusSnapshotRequest $request, string $id): JsonResponse
    {
        $censusSnapshot = CensusSnapshot::findOrFail($id);
        $censusSnapshot->update($request->validated());

        AuditLogger::record('update', 'census_snapshot', $id);

        return response()->json(['data' => new CensusSnapshotResource($censusSnapshot)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $censusSnapshot = CensusSnapshot::findOrFail($id);

        $censusSnapshot->delete();

        AuditLogger::record('delete', 'census_snapshot', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
