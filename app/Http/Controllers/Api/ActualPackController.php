<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreActualPackRequest;
use App\Http\Resources\ActualPackResource;
use App\Models\ActualPack;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `actual_pack`.
 */
class ActualPackController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = ActualPack::query()->paginate($perPage);

        return response()->json([
            'data' => ActualPackResource::collection($rows->items()),
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
        $actualPack = ActualPack::findOrFail($id);

        return response()->json(['data' => new ActualPackResource($actualPack)]);
    }

    public function store(StoreActualPackRequest $request): JsonResponse
    {
        $actualPack = ActualPack::create($request->validated());

        AuditLogger::record('create', 'actual_pack', (string) $actualPack->getKey());

        return response()->json(
            ['data' => new ActualPackResource($actualPack)], 201
        );
    }

    public function update(StoreActualPackRequest $request, string $id): JsonResponse
    {
        $actualPack = ActualPack::findOrFail($id);
        $actualPack->update($request->validated());

        AuditLogger::record('update', 'actual_pack', $id);

        return response()->json(['data' => new ActualPackResource($actualPack)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $actualPack = ActualPack::findOrFail($id);

        $actualPack->delete();

        AuditLogger::record('delete', 'actual_pack', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
