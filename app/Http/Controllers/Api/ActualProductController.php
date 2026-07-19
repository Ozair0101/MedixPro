<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreActualProductRequest;
use App\Http\Resources\ActualProductResource;
use App\Models\ActualProduct;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `actual_product`.
 */
class ActualProductController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = ActualProduct::query()->paginate($perPage);

        return response()->json([
            'data' => ActualProductResource::collection($rows->items()),
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
        $actualProduct = ActualProduct::findOrFail($id);

        return response()->json(['data' => new ActualProductResource($actualProduct)]);
    }

    public function store(StoreActualProductRequest $request): JsonResponse
    {
        $actualProduct = ActualProduct::create($request->validated());

        AuditLogger::record('create', 'actual_product', (string) $actualProduct->getKey());

        return response()->json(
            ['data' => new ActualProductResource($actualProduct)], 201
        );
    }

    public function update(StoreActualProductRequest $request, string $id): JsonResponse
    {
        $actualProduct = ActualProduct::findOrFail($id);
        $actualProduct->update($request->validated());

        AuditLogger::record('update', 'actual_product', $id);

        return response()->json(['data' => new ActualProductResource($actualProduct)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $actualProduct = ActualProduct::findOrFail($id);

        $actualProduct->delete();

        AuditLogger::record('delete', 'actual_product', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
