<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreImagingModalityRequest;
use App\Http\Resources\ImagingModalityResource;
use App\Models\ImagingModality;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `imaging_modality`.
 */
class ImagingModalityController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = ImagingModality::query()->paginate($perPage);

        return response()->json([
            'data' => ImagingModalityResource::collection($rows->items()),
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
        $imagingModality = ImagingModality::findOrFail($id);

        return response()->json(['data' => new ImagingModalityResource($imagingModality)]);
    }

    public function store(StoreImagingModalityRequest $request): JsonResponse
    {
        $imagingModality = ImagingModality::create($request->validated());

        AuditLogger::record('create', 'imaging_modality', (string) $imagingModality->getKey());

        return response()->json(
            ['data' => new ImagingModalityResource($imagingModality)], 201
        );
    }

    public function update(StoreImagingModalityRequest $request, string $id): JsonResponse
    {
        $imagingModality = ImagingModality::findOrFail($id);
        $imagingModality->update($request->validated());

        AuditLogger::record('update', 'imaging_modality', $id);

        return response()->json(['data' => new ImagingModalityResource($imagingModality)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $imagingModality = ImagingModality::findOrFail($id);

        $imagingModality->delete();

        AuditLogger::record('delete', 'imaging_modality', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
