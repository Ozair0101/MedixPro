<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAssetRequest;
use App\Http\Resources\AssetResource;
use App\Models\Asset;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `asset`.
 */
class AssetController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = Asset::query()->paginate($perPage);

        return response()->json([
            'data' => AssetResource::collection($rows->items()),
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
        $asset = Asset::findOrFail($id);

        return response()->json(['data' => new AssetResource($asset)]);
    }

    public function store(StoreAssetRequest $request): JsonResponse
    {
        $asset = Asset::create($request->validated());

        AuditLogger::record('create', 'asset', (string) $asset->getKey());

        return response()->json(
            ['data' => new AssetResource($asset)], 201
        );
    }

    public function update(StoreAssetRequest $request, string $id): JsonResponse
    {
        $asset = Asset::findOrFail($id);
        $asset->update($request->validated());

        AuditLogger::record('update', 'asset', $id);

        return response()->json(['data' => new AssetResource($asset)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $asset = Asset::findOrFail($id);

        $asset->delete();

        AuditLogger::record('delete', 'asset', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
