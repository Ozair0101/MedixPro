<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVitalRequest;
use App\Http\Resources\VitalResource;
use App\Models\Vital;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `vitals`.
 */
class VitalController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = Vital::query()->paginate($perPage);

        return response()->json([
            'data' => VitalResource::collection($rows->items()),
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
        $vital = Vital::findOrFail($id);

        return response()->json(['data' => new VitalResource($vital)]);
    }

    public function store(StoreVitalRequest $request): JsonResponse
    {
        $vital = Vital::create($request->validated());

        AuditLogger::record('create', 'vitals', (string) $vital->getKey());

        return response()->json(
            ['data' => new VitalResource($vital)], 201
        );
    }

    public function update(StoreVitalRequest $request, string $id): JsonResponse
    {
        $vital = Vital::findOrFail($id);
        $vital->update($request->validated());

        AuditLogger::record('update', 'vitals', $id);

        return response()->json(['data' => new VitalResource($vital)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $vital = Vital::findOrFail($id);

        $vital->delete();

        AuditLogger::record('delete', 'vitals', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
