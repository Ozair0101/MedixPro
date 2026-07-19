<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGeoProvinceRequest;
use App\Http\Resources\GeoProvinceResource;
use App\Models\GeoProvince;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `geo_province`.
 */
class GeoProvinceController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = GeoProvince::query()->paginate($perPage);

        return response()->json([
            'data' => GeoProvinceResource::collection($rows->items()),
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
        $geoProvince = GeoProvince::findOrFail($id);

        return response()->json(['data' => new GeoProvinceResource($geoProvince)]);
    }

    public function store(StoreGeoProvinceRequest $request): JsonResponse
    {
        $geoProvince = GeoProvince::create($request->validated());

        AuditLogger::record('create', 'geo_province', (string) $geoProvince->getKey());

        return response()->json(
            ['data' => new GeoProvinceResource($geoProvince)], 201
        );
    }

    public function update(StoreGeoProvinceRequest $request, string $id): JsonResponse
    {
        $geoProvince = GeoProvince::findOrFail($id);
        $geoProvince->update($request->validated());

        AuditLogger::record('update', 'geo_province', $id);

        return response()->json(['data' => new GeoProvinceResource($geoProvince)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $geoProvince = GeoProvince::findOrFail($id);

        $geoProvince->delete();

        AuditLogger::record('delete', 'geo_province', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
