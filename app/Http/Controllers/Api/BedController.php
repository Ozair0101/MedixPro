<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBedRequest;
use App\Http\Resources\BedResource;
use App\Models\Bed;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `bed`.
 */
class BedController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = Bed::query()->paginate($perPage);

        return response()->json([
            'data' => BedResource::collection($rows->items()),
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
        $bed = Bed::findOrFail($id);

        return response()->json(['data' => new BedResource($bed)]);
    }

    public function store(StoreBedRequest $request): JsonResponse
    {
        $bed = Bed::create($request->validated());

        AuditLogger::record('create', 'bed', (string) $bed->getKey());

        return response()->json(
            ['data' => new BedResource($bed)], 201
        );
    }

    public function update(StoreBedRequest $request, string $id): JsonResponse
    {
        $bed = Bed::findOrFail($id);
        $bed->update($request->validated());

        AuditLogger::record('update', 'bed', $id);

        return response()->json(['data' => new BedResource($bed)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $bed = Bed::findOrFail($id);

        $bed->delete();

        AuditLogger::record('delete', 'bed', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
