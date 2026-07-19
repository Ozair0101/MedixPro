<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSterilizationCycleRequest;
use App\Http\Resources\SterilizationCycleResource;
use App\Models\SterilizationCycle;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `sterilization_cycle`.
 */
class SterilizationCycleController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = SterilizationCycle::query()->paginate($perPage);

        return response()->json([
            'data' => SterilizationCycleResource::collection($rows->items()),
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
        $sterilizationCycle = SterilizationCycle::findOrFail($id);

        return response()->json(['data' => new SterilizationCycleResource($sterilizationCycle)]);
    }

    public function store(StoreSterilizationCycleRequest $request): JsonResponse
    {
        $sterilizationCycle = SterilizationCycle::create($request->validated());

        AuditLogger::record('create', 'sterilization_cycle', (string) $sterilizationCycle->getKey());

        return response()->json(
            ['data' => new SterilizationCycleResource($sterilizationCycle)], 201
        );
    }

    public function update(StoreSterilizationCycleRequest $request, string $id): JsonResponse
    {
        $sterilizationCycle = SterilizationCycle::findOrFail($id);
        $sterilizationCycle->update($request->validated());

        AuditLogger::record('update', 'sterilization_cycle', $id);

        return response()->json(['data' => new SterilizationCycleResource($sterilizationCycle)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $sterilizationCycle = SterilizationCycle::findOrFail($id);

        $sterilizationCycle->delete();

        AuditLogger::record('delete', 'sterilization_cycle', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
