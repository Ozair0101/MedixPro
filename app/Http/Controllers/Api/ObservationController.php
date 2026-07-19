<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreObservationRequest;
use App\Http\Resources\ObservationResource;
use App\Models\Observation;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `observation`.
 */
class ObservationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = Observation::query()->paginate($perPage);

        return response()->json([
            'data' => ObservationResource::collection($rows->items()),
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
        $observation = Observation::findOrFail($id);

        return response()->json(['data' => new ObservationResource($observation)]);
    }

    public function store(StoreObservationRequest $request): JsonResponse
    {
        $observation = Observation::create($request->validated());

        AuditLogger::record('create', 'observation', (string) $observation->getKey());

        return response()->json(
            ['data' => new ObservationResource($observation)], 201
        );
    }

    public function update(StoreObservationRequest $request, string $id): JsonResponse
    {
        $observation = Observation::findOrFail($id);
        $observation->update($request->validated());

        AuditLogger::record('update', 'observation', $id);

        return response()->json(['data' => new ObservationResource($observation)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $observation = Observation::findOrFail($id);

        $observation->delete();

        AuditLogger::record('delete', 'observation', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
