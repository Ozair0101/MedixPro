<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDispenseRequest;
use App\Http\Resources\DispenseResource;
use App\Models\Dispense;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `dispense`.
 */
class DispenseController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = Dispense::query()->paginate($perPage);

        return response()->json([
            'data' => DispenseResource::collection($rows->items()),
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
        $dispense = Dispense::findOrFail($id);

        return response()->json(['data' => new DispenseResource($dispense)]);
    }

    public function store(StoreDispenseRequest $request): JsonResponse
    {
        $dispense = Dispense::create($request->validated());

        AuditLogger::record('create', 'dispense', (string) $dispense->getKey());

        return response()->json(
            ['data' => new DispenseResource($dispense)], 201
        );
    }

    public function update(StoreDispenseRequest $request, string $id): JsonResponse
    {
        $dispense = Dispense::findOrFail($id);
        $dispense->update($request->validated());

        AuditLogger::record('update', 'dispense', $id);

        return response()->json(['data' => new DispenseResource($dispense)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $dispense = Dispense::findOrFail($id);

        $dispense->delete();

        AuditLogger::record('delete', 'dispense', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
