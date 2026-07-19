<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSterilizationUsageRequest;
use App\Http\Resources\SterilizationUsageResource;
use App\Models\SterilizationUsage;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `sterilization_usage`.
 */
class SterilizationUsageController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = SterilizationUsage::query()->paginate($perPage);

        return response()->json([
            'data' => SterilizationUsageResource::collection($rows->items()),
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
        $sterilizationUsage = SterilizationUsage::findOrFail($id);

        return response()->json(['data' => new SterilizationUsageResource($sterilizationUsage)]);
    }

    public function store(StoreSterilizationUsageRequest $request): JsonResponse
    {
        $sterilizationUsage = SterilizationUsage::create($request->validated());

        AuditLogger::record('create', 'sterilization_usage', (string) $sterilizationUsage->getKey());

        return response()->json(
            ['data' => new SterilizationUsageResource($sterilizationUsage)], 201
        );
    }

    public function update(StoreSterilizationUsageRequest $request, string $id): JsonResponse
    {
        $sterilizationUsage = SterilizationUsage::findOrFail($id);
        $sterilizationUsage->update($request->validated());

        AuditLogger::record('update', 'sterilization_usage', $id);

        return response()->json(['data' => new SterilizationUsageResource($sterilizationUsage)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $sterilizationUsage = SterilizationUsage::findOrFail($id);

        $sterilizationUsage->delete();

        AuditLogger::record('delete', 'sterilization_usage', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
