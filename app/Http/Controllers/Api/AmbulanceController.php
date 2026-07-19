<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAmbulanceRequest;
use App\Http\Resources\AmbulanceResource;
use App\Models\Ambulance;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `ambulance`.
 */
class AmbulanceController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = Ambulance::query()->paginate($perPage);

        return response()->json([
            'data' => AmbulanceResource::collection($rows->items()),
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
        $ambulance = Ambulance::findOrFail($id);

        return response()->json(['data' => new AmbulanceResource($ambulance)]);
    }

    public function store(StoreAmbulanceRequest $request): JsonResponse
    {
        $ambulance = Ambulance::create($request->validated());

        AuditLogger::record('create', 'ambulance', (string) $ambulance->getKey());

        return response()->json(
            ['data' => new AmbulanceResource($ambulance)], 201
        );
    }

    public function update(StoreAmbulanceRequest $request, string $id): JsonResponse
    {
        $ambulance = Ambulance::findOrFail($id);
        $ambulance->update($request->validated());

        AuditLogger::record('update', 'ambulance', $id);

        return response()->json(['data' => new AmbulanceResource($ambulance)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $ambulance = Ambulance::findOrFail($id);

        $ambulance->delete();

        AuditLogger::record('delete', 'ambulance', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
