<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAmbulanceTripRequest;
use App\Http\Resources\AmbulanceTripResource;
use App\Models\AmbulanceTrip;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `ambulance_trip`.
 */
class AmbulanceTripController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = AmbulanceTrip::query()->paginate($perPage);

        return response()->json([
            'data' => AmbulanceTripResource::collection($rows->items()),
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
        $ambulanceTrip = AmbulanceTrip::findOrFail($id);

        return response()->json(['data' => new AmbulanceTripResource($ambulanceTrip)]);
    }

    public function store(StoreAmbulanceTripRequest $request): JsonResponse
    {
        $ambulanceTrip = AmbulanceTrip::create($request->validated());

        AuditLogger::record('create', 'ambulance_trip', (string) $ambulanceTrip->getKey());

        return response()->json(
            ['data' => new AmbulanceTripResource($ambulanceTrip)], 201
        );
    }

    public function update(StoreAmbulanceTripRequest $request, string $id): JsonResponse
    {
        $ambulanceTrip = AmbulanceTrip::findOrFail($id);
        $ambulanceTrip->update($request->validated());

        AuditLogger::record('update', 'ambulance_trip', $id);

        return response()->json(['data' => new AmbulanceTripResource($ambulanceTrip)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $ambulanceTrip = AmbulanceTrip::findOrFail($id);

        $ambulanceTrip->delete();

        AuditLogger::record('delete', 'ambulance_trip', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
