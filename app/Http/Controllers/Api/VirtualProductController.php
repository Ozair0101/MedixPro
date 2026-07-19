<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVirtualProductRequest;
use App\Http\Resources\VirtualProductResource;
use App\Models\VirtualProduct;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `virtual_product`.
 */
class VirtualProductController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = VirtualProduct::query()->paginate($perPage);

        return response()->json([
            'data' => VirtualProductResource::collection($rows->items()),
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
        $virtualProduct = VirtualProduct::findOrFail($id);

        return response()->json(['data' => new VirtualProductResource($virtualProduct)]);
    }

    public function store(StoreVirtualProductRequest $request): JsonResponse
    {
        $virtualProduct = VirtualProduct::create($request->validated());

        AuditLogger::record('create', 'virtual_product', (string) $virtualProduct->getKey());

        return response()->json(
            ['data' => new VirtualProductResource($virtualProduct)], 201
        );
    }

    public function update(StoreVirtualProductRequest $request, string $id): JsonResponse
    {
        $virtualProduct = VirtualProduct::findOrFail($id);
        $virtualProduct->update($request->validated());

        AuditLogger::record('update', 'virtual_product', $id);

        return response()->json(['data' => new VirtualProductResource($virtualProduct)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $virtualProduct = VirtualProduct::findOrFail($id);

        $virtualProduct->delete();

        AuditLogger::record('delete', 'virtual_product', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
