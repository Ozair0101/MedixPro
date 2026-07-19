<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBedOccupancyRequest;
use App\Http\Resources\BedOccupancyResource;
use App\Models\BedOccupancy;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `bed_occupancy`.
 */
class BedOccupancyController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = BedOccupancy::query()->paginate($perPage);

        return response()->json([
            'data' => BedOccupancyResource::collection($rows->items()),
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
        $bedOccupancy = BedOccupancy::findOrFail($id);

        return response()->json(['data' => new BedOccupancyResource($bedOccupancy)]);
    }

    public function store(StoreBedOccupancyRequest $request): JsonResponse
    {
        $bedOccupancy = BedOccupancy::create($request->validated());

        AuditLogger::record('create', 'bed_occupancy', (string) $bedOccupancy->getKey());

        return response()->json(
            ['data' => new BedOccupancyResource($bedOccupancy)], 201
        );
    }

    public function update(StoreBedOccupancyRequest $request, string $id): JsonResponse
    {
        $bedOccupancy = BedOccupancy::findOrFail($id);
        $bedOccupancy->update($request->validated());

        AuditLogger::record('update', 'bed_occupancy', $id);

        return response()->json(['data' => new BedOccupancyResource($bedOccupancy)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $bedOccupancy = BedOccupancy::findOrFail($id);

        $bedOccupancy->delete();

        AuditLogger::record('delete', 'bed_occupancy', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
