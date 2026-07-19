<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGeoDistrictRequest;
use App\Http\Resources\GeoDistrictResource;
use App\Models\GeoDistrict;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `geo_district`.
 */
class GeoDistrictController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = GeoDistrict::query()->paginate($perPage);

        return response()->json([
            'data' => GeoDistrictResource::collection($rows->items()),
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
        $geoDistrict = GeoDistrict::findOrFail($id);

        return response()->json(['data' => new GeoDistrictResource($geoDistrict)]);
    }

    public function store(StoreGeoDistrictRequest $request): JsonResponse
    {
        $geoDistrict = GeoDistrict::create($request->validated());

        AuditLogger::record('create', 'geo_district', (string) $geoDistrict->getKey());

        return response()->json(
            ['data' => new GeoDistrictResource($geoDistrict)], 201
        );
    }

    public function update(StoreGeoDistrictRequest $request, string $id): JsonResponse
    {
        $geoDistrict = GeoDistrict::findOrFail($id);
        $geoDistrict->update($request->validated());

        AuditLogger::record('update', 'geo_district', $id);

        return response()->json(['data' => new GeoDistrictResource($geoDistrict)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $geoDistrict = GeoDistrict::findOrFail($id);

        $geoDistrict->delete();

        AuditLogger::record('delete', 'geo_district', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
