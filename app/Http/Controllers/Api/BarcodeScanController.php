<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBarcodeScanRequest;
use App\Http\Resources\BarcodeScanResource;
use App\Models\BarcodeScan;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `barcode_scan`.
 */
class BarcodeScanController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = BarcodeScan::query()->paginate($perPage);

        return response()->json([
            'data' => BarcodeScanResource::collection($rows->items()),
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
        $barcodeScan = BarcodeScan::findOrFail($id);

        return response()->json(['data' => new BarcodeScanResource($barcodeScan)]);
    }

    public function store(StoreBarcodeScanRequest $request): JsonResponse
    {
        $barcodeScan = BarcodeScan::create($request->validated());

        AuditLogger::record('create', 'barcode_scan', (string) $barcodeScan->getKey());

        return response()->json(
            ['data' => new BarcodeScanResource($barcodeScan)], 201
        );
    }

    public function update(StoreBarcodeScanRequest $request, string $id): JsonResponse
    {
        $barcodeScan = BarcodeScan::findOrFail($id);
        $barcodeScan->update($request->validated());

        AuditLogger::record('update', 'barcode_scan', $id);

        return response()->json(['data' => new BarcodeScanResource($barcodeScan)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $barcodeScan = BarcodeScan::findOrFail($id);

        $barcodeScan->delete();

        AuditLogger::record('delete', 'barcode_scan', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
