<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAllergyManifestationRequest;
use App\Http\Resources\AllergyManifestationResource;
use App\Models\AllergyManifestation;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `allergy_manifestation`.
 */
class AllergyManifestationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = AllergyManifestation::query()->paginate($perPage);

        return response()->json([
            'data' => AllergyManifestationResource::collection($rows->items()),
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
        $allergyManifestation = AllergyManifestation::findOrFail($id);

        return response()->json(['data' => new AllergyManifestationResource($allergyManifestation)]);
    }

    public function store(StoreAllergyManifestationRequest $request): JsonResponse
    {
        $allergyManifestation = AllergyManifestation::create($request->validated());

        AuditLogger::record('create', 'allergy_manifestation', (string) $allergyManifestation->getKey());

        return response()->json(
            ['data' => new AllergyManifestationResource($allergyManifestation)], 201
        );
    }

    public function update(StoreAllergyManifestationRequest $request, string $id): JsonResponse
    {
        $allergyManifestation = AllergyManifestation::findOrFail($id);
        $allergyManifestation->update($request->validated());

        AuditLogger::record('update', 'allergy_manifestation', $id);

        return response()->json(['data' => new AllergyManifestationResource($allergyManifestation)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $allergyManifestation = AllergyManifestation::findOrFail($id);

        $allergyManifestation->delete();

        AuditLogger::record('delete', 'allergy_manifestation', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
