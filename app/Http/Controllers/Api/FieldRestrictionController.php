<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFieldRestrictionRequest;
use App\Http\Resources\FieldRestrictionResource;
use App\Models\FieldRestriction;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `field_restriction`.
 */
class FieldRestrictionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = FieldRestriction::query()->paginate($perPage);

        return response()->json([
            'data' => FieldRestrictionResource::collection($rows->items()),
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
        $fieldRestriction = FieldRestriction::findOrFail($id);

        return response()->json(['data' => new FieldRestrictionResource($fieldRestriction)]);
    }

    public function store(StoreFieldRestrictionRequest $request): JsonResponse
    {
        $fieldRestriction = FieldRestriction::create($request->validated());

        AuditLogger::record('create', 'field_restriction', (string) $fieldRestriction->getKey());

        return response()->json(
            ['data' => new FieldRestrictionResource($fieldRestriction)], 201
        );
    }

    public function update(StoreFieldRestrictionRequest $request, string $id): JsonResponse
    {
        $fieldRestriction = FieldRestriction::findOrFail($id);
        $fieldRestriction->update($request->validated());

        AuditLogger::record('update', 'field_restriction', $id);

        return response()->json(['data' => new FieldRestrictionResource($fieldRestriction)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $fieldRestriction = FieldRestriction::findOrFail($id);

        $fieldRestriction->delete();

        AuditLogger::record('delete', 'field_restriction', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
