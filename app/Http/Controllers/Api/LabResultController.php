<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLabResultRequest;
use App\Http\Resources\LabResultResource;
use App\Models\LabResult;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `lab_result`.
 */
class LabResultController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = LabResult::query()->paginate($perPage);

        return response()->json([
            'data' => LabResultResource::collection($rows->items()),
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
        $labResult = LabResult::findOrFail($id);

        return response()->json(['data' => new LabResultResource($labResult)]);
    }

    public function store(StoreLabResultRequest $request): JsonResponse
    {
        $labResult = LabResult::create($request->validated());

        AuditLogger::record('create', 'lab_result', (string) $labResult->getKey());

        return response()->json(
            ['data' => new LabResultResource($labResult)], 201
        );
    }

    public function update(StoreLabResultRequest $request, string $id): JsonResponse
    {
        $labResult = LabResult::findOrFail($id);
        $labResult->update($request->validated());

        AuditLogger::record('update', 'lab_result', $id);

        return response()->json(['data' => new LabResultResource($labResult)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $labResult = LabResult::findOrFail($id);

        $labResult->delete();

        AuditLogger::record('delete', 'lab_result', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
