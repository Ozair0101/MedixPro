<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLabSectionRequest;
use App\Http\Resources\LabSectionResource;
use App\Models\LabSection;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `lab_section`.
 */
class LabSectionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = LabSection::query()->paginate($perPage);

        return response()->json([
            'data' => LabSectionResource::collection($rows->items()),
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
        $labSection = LabSection::findOrFail($id);

        return response()->json(['data' => new LabSectionResource($labSection)]);
    }

    public function store(StoreLabSectionRequest $request): JsonResponse
    {
        $labSection = LabSection::create($request->validated());

        AuditLogger::record('create', 'lab_section', (string) $labSection->getKey());

        return response()->json(
            ['data' => new LabSectionResource($labSection)], 201
        );
    }

    public function update(StoreLabSectionRequest $request, string $id): JsonResponse
    {
        $labSection = LabSection::findOrFail($id);
        $labSection->update($request->validated());

        AuditLogger::record('update', 'lab_section', $id);

        return response()->json(['data' => new LabSectionResource($labSection)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $labSection = LabSection::findOrFail($id);

        $labSection->delete();

        AuditLogger::record('delete', 'lab_section', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
