<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreIncidentRequest;
use App\Http\Resources\IncidentResource;
use App\Models\Incident;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `incident`.
 */
class IncidentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = Incident::query()->paginate($perPage);

        return response()->json([
            'data' => IncidentResource::collection($rows->items()),
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
        $incident = Incident::findOrFail($id);

        return response()->json(['data' => new IncidentResource($incident)]);
    }

    public function store(StoreIncidentRequest $request): JsonResponse
    {
        $incident = Incident::create($request->validated());

        AuditLogger::record('create', 'incident', (string) $incident->getKey());

        return response()->json(
            ['data' => new IncidentResource($incident)], 201
        );
    }

    public function update(StoreIncidentRequest $request, string $id): JsonResponse
    {
        $incident = Incident::findOrFail($id);
        $incident->update($request->validated());

        AuditLogger::record('update', 'incident', $id);

        return response()->json(['data' => new IncidentResource($incident)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $incident = Incident::findOrFail($id);

        $incident->delete();

        AuditLogger::record('delete', 'incident', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
