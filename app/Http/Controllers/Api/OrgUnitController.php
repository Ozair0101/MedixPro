<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrgUnitRequest;
use App\Http\Resources\OrgUnitResource;
use App\Models\OrgUnit;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `org_unit`.
 */
class OrgUnitController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = OrgUnit::query()->paginate($perPage);

        return response()->json([
            'data' => OrgUnitResource::collection($rows->items()),
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
        $orgUnit = OrgUnit::findOrFail($id);

        return response()->json(['data' => new OrgUnitResource($orgUnit)]);
    }

    public function store(StoreOrgUnitRequest $request): JsonResponse
    {
        $orgUnit = OrgUnit::create($request->validated());

        AuditLogger::record('create', 'org_unit', (string) $orgUnit->getKey());

        return response()->json(
            ['data' => new OrgUnitResource($orgUnit)], 201
        );
    }

    public function update(StoreOrgUnitRequest $request, string $id): JsonResponse
    {
        $orgUnit = OrgUnit::findOrFail($id);
        $orgUnit->update($request->validated());

        AuditLogger::record('update', 'org_unit', $id);

        return response()->json(['data' => new OrgUnitResource($orgUnit)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $orgUnit = OrgUnit::findOrFail($id);

        // Deactivate rather than delete: clinical and financial records
        // must stay resolvable for anything that already references them.
        $orgUnit->update(['is_active' => false]);

        AuditLogger::record('delete', 'org_unit', $id);

        return response()->json(['message' => 'Deactivated.']);
    }
}
