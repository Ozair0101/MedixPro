<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCoverageRequest;
use App\Http\Resources\CoverageResource;
use App\Models\Coverage;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `coverage`.
 */
class CoverageController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = Coverage::query()->paginate($perPage);

        return response()->json([
            'data' => CoverageResource::collection($rows->items()),
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
        $coverage = Coverage::findOrFail($id);

        return response()->json(['data' => new CoverageResource($coverage)]);
    }

    public function store(StoreCoverageRequest $request): JsonResponse
    {
        $coverage = Coverage::create($request->validated());

        AuditLogger::record('create', 'coverage', (string) $coverage->getKey());

        return response()->json(
            ['data' => new CoverageResource($coverage)], 201
        );
    }

    public function update(StoreCoverageRequest $request, string $id): JsonResponse
    {
        $coverage = Coverage::findOrFail($id);
        $coverage->update($request->validated());

        AuditLogger::record('update', 'coverage', $id);

        return response()->json(['data' => new CoverageResource($coverage)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $coverage = Coverage::findOrFail($id);

        $coverage->delete();

        AuditLogger::record('delete', 'coverage', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
