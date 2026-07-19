<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGeoAliasRequest;
use App\Http\Resources\GeoAliasResource;
use App\Models\GeoAlias;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `geo_alias`.
 */
class GeoAliasController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = GeoAlias::query()->paginate($perPage);

        return response()->json([
            'data' => GeoAliasResource::collection($rows->items()),
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
        $geoAlias = GeoAlias::findOrFail($id);

        return response()->json(['data' => new GeoAliasResource($geoAlias)]);
    }

    public function store(StoreGeoAliasRequest $request): JsonResponse
    {
        $geoAlias = GeoAlias::create($request->validated());

        AuditLogger::record('create', 'geo_alias', (string) $geoAlias->getKey());

        return response()->json(
            ['data' => new GeoAliasResource($geoAlias)], 201
        );
    }

    public function update(StoreGeoAliasRequest $request, string $id): JsonResponse
    {
        $geoAlias = GeoAlias::findOrFail($id);
        $geoAlias->update($request->validated());

        AuditLogger::record('update', 'geo_alias', $id);

        return response()->json(['data' => new GeoAliasResource($geoAlias)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $geoAlias = GeoAlias::findOrFail($id);

        $geoAlias->delete();

        AuditLogger::record('delete', 'geo_alias', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
