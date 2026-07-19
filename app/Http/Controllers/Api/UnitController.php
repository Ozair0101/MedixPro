<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUnitRequest;
use App\Http\Resources\UnitResource;
use App\Models\Unit;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `unit`.
 */
class UnitController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = Unit::query()->paginate($perPage);

        return response()->json([
            'data' => UnitResource::collection($rows->items()),
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
        $unit = Unit::findOrFail($id);

        return response()->json(['data' => new UnitResource($unit)]);
    }

    public function store(StoreUnitRequest $request): JsonResponse
    {
        $unit = Unit::create($request->validated());

        AuditLogger::record('create', 'unit', (string) $unit->getKey());

        return response()->json(
            ['data' => new UnitResource($unit)], 201
        );
    }

    public function update(StoreUnitRequest $request, string $id): JsonResponse
    {
        $unit = Unit::findOrFail($id);
        $unit->update($request->validated());

        AuditLogger::record('update', 'unit', $id);

        return response()->json(['data' => new UnitResource($unit)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $unit = Unit::findOrFail($id);

        $unit->delete();

        AuditLogger::record('delete', 'unit', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
