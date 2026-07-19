<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVirtualProductIngredientRequest;
use App\Http\Resources\VirtualProductIngredientResource;
use App\Models\VirtualProductIngredient;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `virtual_product_ingredient`.
 */
class VirtualProductIngredientController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = VirtualProductIngredient::query()->paginate($perPage);

        return response()->json([
            'data' => VirtualProductIngredientResource::collection($rows->items()),
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
        $virtualProductIngredient = VirtualProductIngredient::findOrFail($id);

        return response()->json(['data' => new VirtualProductIngredientResource($virtualProductIngredient)]);
    }

    public function store(StoreVirtualProductIngredientRequest $request): JsonResponse
    {
        $virtualProductIngredient = VirtualProductIngredient::create($request->validated());

        AuditLogger::record('create', 'virtual_product_ingredient', (string) $virtualProductIngredient->getKey());

        return response()->json(
            ['data' => new VirtualProductIngredientResource($virtualProductIngredient)], 201
        );
    }

    public function update(StoreVirtualProductIngredientRequest $request, string $id): JsonResponse
    {
        $virtualProductIngredient = VirtualProductIngredient::findOrFail($id);
        $virtualProductIngredient->update($request->validated());

        AuditLogger::record('update', 'virtual_product_ingredient', $id);

        return response()->json(['data' => new VirtualProductIngredientResource($virtualProductIngredient)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $virtualProductIngredient = VirtualProductIngredient::findOrFail($id);

        $virtualProductIngredient->delete();

        AuditLogger::record('delete', 'virtual_product_ingredient', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
