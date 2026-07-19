<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLabTestRequest;
use App\Http\Resources\LabTestResource;
use App\Models\LabTest;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `lab_test`.
 */
class LabTestController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = LabTest::query()->paginate($perPage);

        return response()->json([
            'data' => LabTestResource::collection($rows->items()),
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
        $labTest = LabTest::findOrFail($id);

        return response()->json(['data' => new LabTestResource($labTest)]);
    }

    public function store(StoreLabTestRequest $request): JsonResponse
    {
        $labTest = LabTest::create($request->validated());

        AuditLogger::record('create', 'lab_test', (string) $labTest->getKey());

        return response()->json(
            ['data' => new LabTestResource($labTest)], 201
        );
    }

    public function update(StoreLabTestRequest $request, string $id): JsonResponse
    {
        $labTest = LabTest::findOrFail($id);
        $labTest->update($request->validated());

        AuditLogger::record('update', 'lab_test', $id);

        return response()->json(['data' => new LabTestResource($labTest)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $labTest = LabTest::findOrFail($id);

        $labTest->delete();

        AuditLogger::record('delete', 'lab_test', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
