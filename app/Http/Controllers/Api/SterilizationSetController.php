<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSterilizationSetRequest;
use App\Http\Resources\SterilizationSetResource;
use App\Models\SterilizationSet;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `sterilization_set`.
 */
class SterilizationSetController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = SterilizationSet::query()->paginate($perPage);

        return response()->json([
            'data' => SterilizationSetResource::collection($rows->items()),
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
        $sterilizationSet = SterilizationSet::findOrFail($id);

        return response()->json(['data' => new SterilizationSetResource($sterilizationSet)]);
    }

    public function store(StoreSterilizationSetRequest $request): JsonResponse
    {
        $sterilizationSet = SterilizationSet::create($request->validated());

        AuditLogger::record('create', 'sterilization_set', (string) $sterilizationSet->getKey());

        return response()->json(
            ['data' => new SterilizationSetResource($sterilizationSet)], 201
        );
    }

    public function update(StoreSterilizationSetRequest $request, string $id): JsonResponse
    {
        $sterilizationSet = SterilizationSet::findOrFail($id);
        $sterilizationSet->update($request->validated());

        AuditLogger::record('update', 'sterilization_set', $id);

        return response()->json(['data' => new SterilizationSetResource($sterilizationSet)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $sterilizationSet = SterilizationSet::findOrFail($id);

        $sterilizationSet->delete();

        AuditLogger::record('delete', 'sterilization_set', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
