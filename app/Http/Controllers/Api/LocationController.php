<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLocationRequest;
use App\Http\Resources\LocationResource;
use App\Models\Location;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `location`.
 */
class LocationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = Location::query()->paginate($perPage);

        return response()->json([
            'data' => LocationResource::collection($rows->items()),
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
        $location = Location::findOrFail($id);

        return response()->json(['data' => new LocationResource($location)]);
    }

    public function store(StoreLocationRequest $request): JsonResponse
    {
        $location = Location::create($request->validated());

        AuditLogger::record('create', 'location', (string) $location->getKey());

        return response()->json(
            ['data' => new LocationResource($location)], 201
        );
    }

    public function update(StoreLocationRequest $request, string $id): JsonResponse
    {
        $location = Location::findOrFail($id);
        $location->update($request->validated());

        AuditLogger::record('update', 'location', $id);

        return response()->json(['data' => new LocationResource($location)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $location = Location::findOrFail($id);

        $location->delete();

        AuditLogger::record('delete', 'location', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
