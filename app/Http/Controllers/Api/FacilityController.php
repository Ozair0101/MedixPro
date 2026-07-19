<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFacilityRequest;
use App\Http\Resources\FacilityResource;
use App\Models\Facility;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `facility`.
 */
class FacilityController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = Facility::query()->paginate($perPage);

        return response()->json([
            'data' => FacilityResource::collection($rows->items()),
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
        $facility = Facility::findOrFail($id);

        return response()->json(['data' => new FacilityResource($facility)]);
    }

    public function store(StoreFacilityRequest $request): JsonResponse
    {
        $facility = Facility::create($request->validated());

        AuditLogger::record('create', 'facility', (string) $facility->getKey());

        return response()->json(
            ['data' => new FacilityResource($facility)], 201
        );
    }

    public function update(StoreFacilityRequest $request, string $id): JsonResponse
    {
        $facility = Facility::findOrFail($id);
        $facility->update($request->validated());

        AuditLogger::record('update', 'facility', $id);

        return response()->json(['data' => new FacilityResource($facility)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $facility = Facility::findOrFail($id);

        // Deactivate rather than delete: clinical and financial records
        // must stay resolvable for anything that already references them.
        $facility->update(['is_active' => false]);

        AuditLogger::record('delete', 'facility', $id);

        return response()->json(['message' => 'Deactivated.']);
    }
}
