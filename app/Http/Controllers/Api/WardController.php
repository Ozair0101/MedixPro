<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreWardRequest;
use App\Http\Resources\WardResource;
use App\Models\Ward;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `ward`.
 */
class WardController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = Ward::query()->paginate($perPage);

        return response()->json([
            'data' => WardResource::collection($rows->items()),
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
        $ward = Ward::findOrFail($id);

        return response()->json(['data' => new WardResource($ward)]);
    }

    public function store(StoreWardRequest $request): JsonResponse
    {
        $ward = Ward::create($request->validated());

        AuditLogger::record('create', 'ward', (string) $ward->getKey());

        return response()->json(
            ['data' => new WardResource($ward)], 201
        );
    }

    public function update(StoreWardRequest $request, string $id): JsonResponse
    {
        $ward = Ward::findOrFail($id);
        $ward->update($request->validated());

        AuditLogger::record('update', 'ward', $id);

        return response()->json(['data' => new WardResource($ward)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $ward = Ward::findOrFail($id);

        $ward->delete();

        AuditLogger::record('delete', 'ward', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
