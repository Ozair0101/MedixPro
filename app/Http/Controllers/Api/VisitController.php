<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVisitRequest;
use App\Http\Resources\VisitResource;
use App\Models\Visit;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `visit`.
 */
class VisitController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = Visit::query()->paginate($perPage);

        return response()->json([
            'data' => VisitResource::collection($rows->items()),
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
        $visit = Visit::findOrFail($id);

        return response()->json(['data' => new VisitResource($visit)]);
    }

    public function store(StoreVisitRequest $request): JsonResponse
    {
        $visit = Visit::create($request->validated());

        AuditLogger::record('create', 'visit', (string) $visit->getKey());

        return response()->json(
            ['data' => new VisitResource($visit)], 201
        );
    }

    public function update(StoreVisitRequest $request, string $id): JsonResponse
    {
        $visit = Visit::findOrFail($id);
        $visit->update($request->validated());

        AuditLogger::record('update', 'visit', $id);

        return response()->json(['data' => new VisitResource($visit)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $visit = Visit::findOrFail($id);

        $visit->delete();

        AuditLogger::record('delete', 'visit', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
