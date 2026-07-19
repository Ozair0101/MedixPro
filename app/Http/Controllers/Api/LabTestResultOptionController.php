<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLabTestResultOptionRequest;
use App\Http\Resources\LabTestResultOptionResource;
use App\Models\LabTestResultOption;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `lab_test_result_option`.
 */
class LabTestResultOptionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = LabTestResultOption::query()->paginate($perPage);

        return response()->json([
            'data' => LabTestResultOptionResource::collection($rows->items()),
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
        $labTestResultOption = LabTestResultOption::findOrFail($id);

        return response()->json(['data' => new LabTestResultOptionResource($labTestResultOption)]);
    }

    public function store(StoreLabTestResultOptionRequest $request): JsonResponse
    {
        $labTestResultOption = LabTestResultOption::create($request->validated());

        AuditLogger::record('create', 'lab_test_result_option', (string) $labTestResultOption->getKey());

        return response()->json(
            ['data' => new LabTestResultOptionResource($labTestResultOption)], 201
        );
    }

    public function update(StoreLabTestResultOptionRequest $request, string $id): JsonResponse
    {
        $labTestResultOption = LabTestResultOption::findOrFail($id);
        $labTestResultOption->update($request->validated());

        AuditLogger::record('update', 'lab_test_result_option', $id);

        return response()->json(['data' => new LabTestResultOptionResource($labTestResultOption)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $labTestResultOption = LabTestResultOption::findOrFail($id);

        $labTestResultOption->delete();

        AuditLogger::record('delete', 'lab_test_result_option', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
