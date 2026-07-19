<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreManufacturerRequest;
use App\Http\Resources\ManufacturerResource;
use App\Models\Manufacturer;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `manufacturer`.
 */
class ManufacturerController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = Manufacturer::query()->paginate($perPage);

        return response()->json([
            'data' => ManufacturerResource::collection($rows->items()),
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
        $manufacturer = Manufacturer::findOrFail($id);

        return response()->json(['data' => new ManufacturerResource($manufacturer)]);
    }

    public function store(StoreManufacturerRequest $request): JsonResponse
    {
        $manufacturer = Manufacturer::create($request->validated());

        AuditLogger::record('create', 'manufacturer', (string) $manufacturer->getKey());

        return response()->json(
            ['data' => new ManufacturerResource($manufacturer)], 201
        );
    }

    public function update(StoreManufacturerRequest $request, string $id): JsonResponse
    {
        $manufacturer = Manufacturer::findOrFail($id);
        $manufacturer->update($request->validated());

        AuditLogger::record('update', 'manufacturer', $id);

        return response()->json(['data' => new ManufacturerResource($manufacturer)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $manufacturer = Manufacturer::findOrFail($id);

        $manufacturer->delete();

        AuditLogger::record('delete', 'manufacturer', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
