<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGeoVillageSuggestionRequest;
use App\Http\Resources\GeoVillageSuggestionResource;
use App\Models\GeoVillageSuggestion;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `geo_village_suggestion`.
 */
class GeoVillageSuggestionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = GeoVillageSuggestion::query()->paginate($perPage);

        return response()->json([
            'data' => GeoVillageSuggestionResource::collection($rows->items()),
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
        $geoVillageSuggestion = GeoVillageSuggestion::findOrFail($id);

        return response()->json(['data' => new GeoVillageSuggestionResource($geoVillageSuggestion)]);
    }

    public function store(StoreGeoVillageSuggestionRequest $request): JsonResponse
    {
        $geoVillageSuggestion = GeoVillageSuggestion::create($request->validated());

        AuditLogger::record('create', 'geo_village_suggestion', (string) $geoVillageSuggestion->getKey());

        return response()->json(
            ['data' => new GeoVillageSuggestionResource($geoVillageSuggestion)], 201
        );
    }

    public function update(StoreGeoVillageSuggestionRequest $request, string $id): JsonResponse
    {
        $geoVillageSuggestion = GeoVillageSuggestion::findOrFail($id);
        $geoVillageSuggestion->update($request->validated());

        AuditLogger::record('update', 'geo_village_suggestion', $id);

        return response()->json(['data' => new GeoVillageSuggestionResource($geoVillageSuggestion)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $geoVillageSuggestion = GeoVillageSuggestion::findOrFail($id);

        $geoVillageSuggestion->delete();

        AuditLogger::record('delete', 'geo_village_suggestion', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
