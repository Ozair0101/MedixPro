<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDhis2MappingRequest;
use App\Http\Resources\Dhis2MappingResource;
use App\Models\Dhis2Mapping;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `dhis2_mapping`.
 */
class Dhis2MappingController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = Dhis2Mapping::query()->paginate($perPage);

        return response()->json([
            'data' => Dhis2MappingResource::collection($rows->items()),
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
        $dhis2Mapping = Dhis2Mapping::findOrFail($id);

        return response()->json(['data' => new Dhis2MappingResource($dhis2Mapping)]);
    }

    public function store(StoreDhis2MappingRequest $request): JsonResponse
    {
        $dhis2Mapping = Dhis2Mapping::create($request->validated());

        AuditLogger::record('create', 'dhis2_mapping', (string) $dhis2Mapping->getKey());

        return response()->json(
            ['data' => new Dhis2MappingResource($dhis2Mapping)], 201
        );
    }

    public function update(StoreDhis2MappingRequest $request, string $id): JsonResponse
    {
        $dhis2Mapping = Dhis2Mapping::findOrFail($id);
        $dhis2Mapping->update($request->validated());

        AuditLogger::record('update', 'dhis2_mapping', $id);

        return response()->json(['data' => new Dhis2MappingResource($dhis2Mapping)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $dhis2Mapping = Dhis2Mapping::findOrFail($id);

        $dhis2Mapping->delete();

        AuditLogger::record('delete', 'dhis2_mapping', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
