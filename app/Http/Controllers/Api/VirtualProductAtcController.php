<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVirtualProductAtcRequest;
use App\Http\Resources\VirtualProductAtcResource;
use App\Models\VirtualProductAtc;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `virtual_product_atc`.
 */
class VirtualProductAtcController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = VirtualProductAtc::query()->paginate($perPage);

        return response()->json([
            'data' => VirtualProductAtcResource::collection($rows->items()),
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
        $virtualProductAtc = VirtualProductAtc::findOrFail($id);

        return response()->json(['data' => new VirtualProductAtcResource($virtualProductAtc)]);
    }

    public function store(StoreVirtualProductAtcRequest $request): JsonResponse
    {
        $virtualProductAtc = VirtualProductAtc::create($request->validated());

        AuditLogger::record('create', 'virtual_product_atc', (string) $virtualProductAtc->getKey());

        return response()->json(
            ['data' => new VirtualProductAtcResource($virtualProductAtc)], 201
        );
    }

    public function update(StoreVirtualProductAtcRequest $request, string $id): JsonResponse
    {
        $virtualProductAtc = VirtualProductAtc::findOrFail($id);
        $virtualProductAtc->update($request->validated());

        AuditLogger::record('update', 'virtual_product_atc', $id);

        return response()->json(['data' => new VirtualProductAtcResource($virtualProductAtc)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $virtualProductAtc = VirtualProductAtc::findOrFail($id);

        $virtualProductAtc->delete();

        AuditLogger::record('delete', 'virtual_product_atc', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
