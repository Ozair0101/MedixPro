<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBedTypeRequest;
use App\Http\Resources\BedTypeResource;
use App\Models\BedType;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `bed_type`.
 */
class BedTypeController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = BedType::query()->paginate($perPage);

        return response()->json([
            'data' => BedTypeResource::collection($rows->items()),
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
        $bedType = BedType::findOrFail($id);

        return response()->json(['data' => new BedTypeResource($bedType)]);
    }

    public function store(StoreBedTypeRequest $request): JsonResponse
    {
        $bedType = BedType::create($request->validated());

        AuditLogger::record('create', 'bed_type', (string) $bedType->getKey());

        return response()->json(
            ['data' => new BedTypeResource($bedType)], 201
        );
    }

    public function update(StoreBedTypeRequest $request, string $id): JsonResponse
    {
        $bedType = BedType::findOrFail($id);
        $bedType->update($request->validated());

        AuditLogger::record('update', 'bed_type', $id);

        return response()->json(['data' => new BedTypeResource($bedType)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $bedType = BedType::findOrFail($id);

        $bedType->delete();

        AuditLogger::record('delete', 'bed_type', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
