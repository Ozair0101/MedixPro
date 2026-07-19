<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBedUnavailabilityRequest;
use App\Http\Resources\BedUnavailabilityResource;
use App\Models\BedUnavailability;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `bed_unavailability`.
 */
class BedUnavailabilityController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = BedUnavailability::query()->paginate($perPage);

        return response()->json([
            'data' => BedUnavailabilityResource::collection($rows->items()),
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
        $bedUnavailability = BedUnavailability::findOrFail($id);

        return response()->json(['data' => new BedUnavailabilityResource($bedUnavailability)]);
    }

    public function store(StoreBedUnavailabilityRequest $request): JsonResponse
    {
        $bedUnavailability = BedUnavailability::create($request->validated());

        AuditLogger::record('create', 'bed_unavailability', (string) $bedUnavailability->getKey());

        return response()->json(
            ['data' => new BedUnavailabilityResource($bedUnavailability)], 201
        );
    }

    public function update(StoreBedUnavailabilityRequest $request, string $id): JsonResponse
    {
        $bedUnavailability = BedUnavailability::findOrFail($id);
        $bedUnavailability->update($request->validated());

        AuditLogger::record('update', 'bed_unavailability', $id);

        return response()->json(['data' => new BedUnavailabilityResource($bedUnavailability)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $bedUnavailability = BedUnavailability::findOrFail($id);

        $bedUnavailability->delete();

        AuditLogger::record('delete', 'bed_unavailability', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
