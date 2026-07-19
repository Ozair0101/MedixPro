<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUomCategoryRequest;
use App\Http\Resources\UomCategoryResource;
use App\Models\UomCategory;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `uom_category`.
 */
class UomCategoryController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = UomCategory::query()->paginate($perPage);

        return response()->json([
            'data' => UomCategoryResource::collection($rows->items()),
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
        $uomCategory = UomCategory::findOrFail($id);

        return response()->json(['data' => new UomCategoryResource($uomCategory)]);
    }

    public function store(StoreUomCategoryRequest $request): JsonResponse
    {
        $uomCategory = UomCategory::create($request->validated());

        AuditLogger::record('create', 'uom_category', (string) $uomCategory->getKey());

        return response()->json(
            ['data' => new UomCategoryResource($uomCategory)], 201
        );
    }

    public function update(StoreUomCategoryRequest $request, string $id): JsonResponse
    {
        $uomCategory = UomCategory::findOrFail($id);
        $uomCategory->update($request->validated());

        AuditLogger::record('update', 'uom_category', $id);

        return response()->json(['data' => new UomCategoryResource($uomCategory)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $uomCategory = UomCategory::findOrFail($id);

        $uomCategory->delete();

        AuditLogger::record('delete', 'uom_category', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
