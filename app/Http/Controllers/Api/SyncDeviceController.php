<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSyncDeviceRequest;
use App\Http\Resources\SyncDeviceResource;
use App\Models\SyncDevice;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `sync_device`.
 */
class SyncDeviceController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = SyncDevice::query()->paginate($perPage);

        return response()->json([
            'data' => SyncDeviceResource::collection($rows->items()),
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
        $syncDevice = SyncDevice::findOrFail($id);

        return response()->json(['data' => new SyncDeviceResource($syncDevice)]);
    }

    public function store(StoreSyncDeviceRequest $request): JsonResponse
    {
        $syncDevice = SyncDevice::create($request->validated());

        AuditLogger::record('create', 'sync_device', (string) $syncDevice->getKey());

        return response()->json(
            ['data' => new SyncDeviceResource($syncDevice)], 201
        );
    }

    public function update(StoreSyncDeviceRequest $request, string $id): JsonResponse
    {
        $syncDevice = SyncDevice::findOrFail($id);
        $syncDevice->update($request->validated());

        AuditLogger::record('update', 'sync_device', $id);

        return response()->json(['data' => new SyncDeviceResource($syncDevice)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $syncDevice = SyncDevice::findOrFail($id);

        // Deactivate rather than delete: clinical and financial records
        // must stay resolvable for anything that already references them.
        $syncDevice->update(['is_active' => false]);

        AuditLogger::record('delete', 'sync_device', $id);

        return response()->json(['message' => 'Deactivated.']);
    }
}
