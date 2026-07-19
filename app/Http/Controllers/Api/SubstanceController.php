<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSubstanceRequest;
use App\Http\Resources\SubstanceResource;
use App\Models\Substance;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `substance`.
 */
class SubstanceController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = Substance::query()->paginate($perPage);

        return response()->json([
            'data' => SubstanceResource::collection($rows->items()),
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
        $substance = Substance::findOrFail($id);

        return response()->json(['data' => new SubstanceResource($substance)]);
    }

    public function store(StoreSubstanceRequest $request): JsonResponse
    {
        $substance = Substance::create($request->validated());

        AuditLogger::record('create', 'substance', (string) $substance->getKey());

        return response()->json(
            ['data' => new SubstanceResource($substance)], 201
        );
    }

    public function update(StoreSubstanceRequest $request, string $id): JsonResponse
    {
        $substance = Substance::findOrFail($id);
        $substance->update($request->validated());

        AuditLogger::record('update', 'substance', $id);

        return response()->json(['data' => new SubstanceResource($substance)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $substance = Substance::findOrFail($id);

        $substance->delete();

        AuditLogger::record('delete', 'substance', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
