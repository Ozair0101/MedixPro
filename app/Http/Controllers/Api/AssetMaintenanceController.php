<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAssetMaintenanceRequest;
use App\Http\Resources\AssetMaintenanceResource;
use App\Models\AssetMaintenance;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `asset_maintenance`.
 */
class AssetMaintenanceController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = AssetMaintenance::query()->paginate($perPage);

        return response()->json([
            'data' => AssetMaintenanceResource::collection($rows->items()),
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
        $assetMaintenance = AssetMaintenance::findOrFail($id);

        return response()->json(['data' => new AssetMaintenanceResource($assetMaintenance)]);
    }

    public function store(StoreAssetMaintenanceRequest $request): JsonResponse
    {
        $assetMaintenance = AssetMaintenance::create($request->validated());

        AuditLogger::record('create', 'asset_maintenance', (string) $assetMaintenance->getKey());

        return response()->json(
            ['data' => new AssetMaintenanceResource($assetMaintenance)], 201
        );
    }

    public function update(StoreAssetMaintenanceRequest $request, string $id): JsonResponse
    {
        $assetMaintenance = AssetMaintenance::findOrFail($id);
        $assetMaintenance->update($request->validated());

        AuditLogger::record('update', 'asset_maintenance', $id);

        return response()->json(['data' => new AssetMaintenanceResource($assetMaintenance)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $assetMaintenance = AssetMaintenance::findOrFail($id);

        $assetMaintenance->delete();

        AuditLogger::record('delete', 'asset_maintenance', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
