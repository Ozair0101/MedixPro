<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLabQcRunRequest;
use App\Http\Resources\LabQcRunResource;
use App\Models\LabQcRun;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `lab_qc_run`.
 */
class LabQcRunController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = LabQcRun::query()->paginate($perPage);

        return response()->json([
            'data' => LabQcRunResource::collection($rows->items()),
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
        $labQcRun = LabQcRun::findOrFail($id);

        return response()->json(['data' => new LabQcRunResource($labQcRun)]);
    }

    public function store(StoreLabQcRunRequest $request): JsonResponse
    {
        $labQcRun = LabQcRun::create($request->validated());

        AuditLogger::record('create', 'lab_qc_run', (string) $labQcRun->getKey());

        return response()->json(
            ['data' => new LabQcRunResource($labQcRun)], 201
        );
    }

    public function update(StoreLabQcRunRequest $request, string $id): JsonResponse
    {
        $labQcRun = LabQcRun::findOrFail($id);
        $labQcRun->update($request->validated());

        AuditLogger::record('update', 'lab_qc_run', $id);

        return response()->json(['data' => new LabQcRunResource($labQcRun)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $labQcRun = LabQcRun::findOrFail($id);

        $labQcRun->delete();

        AuditLogger::record('delete', 'lab_qc_run', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
