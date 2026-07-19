<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLabSampleRequest;
use App\Http\Resources\LabSampleResource;
use App\Models\LabSample;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `lab_sample`.
 */
class LabSampleController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = LabSample::query()->paginate($perPage);

        return response()->json([
            'data' => LabSampleResource::collection($rows->items()),
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
        $labSample = LabSample::findOrFail($id);

        return response()->json(['data' => new LabSampleResource($labSample)]);
    }

    public function store(StoreLabSampleRequest $request): JsonResponse
    {
        $labSample = LabSample::create($request->validated());

        AuditLogger::record('create', 'lab_sample', (string) $labSample->getKey());

        return response()->json(
            ['data' => new LabSampleResource($labSample)], 201
        );
    }

    public function update(StoreLabSampleRequest $request, string $id): JsonResponse
    {
        $labSample = LabSample::findOrFail($id);
        $labSample->update($request->validated());

        AuditLogger::record('update', 'lab_sample', $id);

        return response()->json(['data' => new LabSampleResource($labSample)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $labSample = LabSample::findOrFail($id);

        $labSample->delete();

        AuditLogger::record('delete', 'lab_sample', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
