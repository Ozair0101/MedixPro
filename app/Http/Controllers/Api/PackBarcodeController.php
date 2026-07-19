<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePackBarcodeRequest;
use App\Http\Resources\PackBarcodeResource;
use App\Models\PackBarcode;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `pack_barcode`.
 */
class PackBarcodeController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = PackBarcode::query()->paginate($perPage);

        return response()->json([
            'data' => PackBarcodeResource::collection($rows->items()),
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
        $packBarcode = PackBarcode::findOrFail($id);

        return response()->json(['data' => new PackBarcodeResource($packBarcode)]);
    }

    public function store(StorePackBarcodeRequest $request): JsonResponse
    {
        $packBarcode = PackBarcode::create($request->validated());

        AuditLogger::record('create', 'pack_barcode', (string) $packBarcode->getKey());

        return response()->json(
            ['data' => new PackBarcodeResource($packBarcode)], 201
        );
    }

    public function update(StorePackBarcodeRequest $request, string $id): JsonResponse
    {
        $packBarcode = PackBarcode::findOrFail($id);
        $packBarcode->update($request->validated());

        AuditLogger::record('update', 'pack_barcode', $id);

        return response()->json(['data' => new PackBarcodeResource($packBarcode)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $packBarcode = PackBarcode::findOrFail($id);

        $packBarcode->delete();

        AuditLogger::record('delete', 'pack_barcode', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
