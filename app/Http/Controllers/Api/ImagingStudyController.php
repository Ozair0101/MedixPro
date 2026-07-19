<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreImagingStudyRequest;
use App\Http\Resources\ImagingStudyResource;
use App\Models\ImagingStudy;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `imaging_study`.
 */
class ImagingStudyController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = ImagingStudy::query()->paginate($perPage);

        return response()->json([
            'data' => ImagingStudyResource::collection($rows->items()),
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
        $imagingStudy = ImagingStudy::findOrFail($id);

        return response()->json(['data' => new ImagingStudyResource($imagingStudy)]);
    }

    public function store(StoreImagingStudyRequest $request): JsonResponse
    {
        $imagingStudy = ImagingStudy::create($request->validated());

        AuditLogger::record('create', 'imaging_study', (string) $imagingStudy->getKey());

        return response()->json(
            ['data' => new ImagingStudyResource($imagingStudy)], 201
        );
    }

    public function update(StoreImagingStudyRequest $request, string $id): JsonResponse
    {
        $imagingStudy = ImagingStudy::findOrFail($id);
        $imagingStudy->update($request->validated());

        AuditLogger::record('update', 'imaging_study', $id);

        return response()->json(['data' => new ImagingStudyResource($imagingStudy)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $imagingStudy = ImagingStudy::findOrFail($id);

        $imagingStudy->delete();

        AuditLogger::record('delete', 'imaging_study', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
