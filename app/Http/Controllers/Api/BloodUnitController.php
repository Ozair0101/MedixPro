<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBloodUnitRequest;
use App\Http\Resources\BloodUnitResource;
use App\Models\BloodUnit;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `blood_unit`.
 */
class BloodUnitController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = BloodUnit::query()->paginate($perPage);

        return response()->json([
            'data' => BloodUnitResource::collection($rows->items()),
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
        $bloodUnit = BloodUnit::findOrFail($id);

        return response()->json(['data' => new BloodUnitResource($bloodUnit)]);
    }

    public function store(StoreBloodUnitRequest $request): JsonResponse
    {
        $bloodUnit = BloodUnit::create($request->validated());

        AuditLogger::record('create', 'blood_unit', (string) $bloodUnit->getKey());

        return response()->json(
            ['data' => new BloodUnitResource($bloodUnit)], 201
        );
    }

    public function update(StoreBloodUnitRequest $request, string $id): JsonResponse
    {
        $bloodUnit = BloodUnit::findOrFail($id);
        $bloodUnit->update($request->validated());

        AuditLogger::record('update', 'blood_unit', $id);

        return response()->json(['data' => new BloodUnitResource($bloodUnit)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $bloodUnit = BloodUnit::findOrFail($id);

        $bloodUnit->delete();

        AuditLogger::record('delete', 'blood_unit', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
