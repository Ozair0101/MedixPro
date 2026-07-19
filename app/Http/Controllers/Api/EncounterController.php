<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEncounterRequest;
use App\Http\Resources\EncounterResource;
use App\Models\Encounter;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `encounter`.
 */
class EncounterController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = Encounter::query()->paginate($perPage);

        return response()->json([
            'data' => EncounterResource::collection($rows->items()),
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
        $encounter = Encounter::findOrFail($id);

        return response()->json(['data' => new EncounterResource($encounter)]);
    }

    public function store(StoreEncounterRequest $request): JsonResponse
    {
        $encounter = Encounter::create($request->validated());

        AuditLogger::record('create', 'encounter', (string) $encounter->getKey());

        return response()->json(
            ['data' => new EncounterResource($encounter)], 201
        );
    }

    public function update(StoreEncounterRequest $request, string $id): JsonResponse
    {
        $encounter = Encounter::findOrFail($id);
        $encounter->update($request->validated());

        AuditLogger::record('update', 'encounter', $id);

        return response()->json(['data' => new EncounterResource($encounter)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $encounter = Encounter::findOrFail($id);

        $encounter->delete();

        AuditLogger::record('delete', 'encounter', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
