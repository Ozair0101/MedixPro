<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTriageRequest;
use App\Http\Resources\TriageResource;
use App\Models\Triage;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `triage`.
 */
class TriageController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = Triage::query()->paginate($perPage);

        return response()->json([
            'data' => TriageResource::collection($rows->items()),
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
        $triage = Triage::findOrFail($id);

        return response()->json(['data' => new TriageResource($triage)]);
    }

    public function store(StoreTriageRequest $request): JsonResponse
    {
        $triage = Triage::create($request->validated());

        AuditLogger::record('create', 'triage', (string) $triage->getKey());

        return response()->json(
            ['data' => new TriageResource($triage)], 201
        );
    }

    public function update(StoreTriageRequest $request, string $id): JsonResponse
    {
        $triage = Triage::findOrFail($id);
        $triage->update($request->validated());

        AuditLogger::record('update', 'triage', $id);

        return response()->json(['data' => new TriageResource($triage)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $triage = Triage::findOrFail($id);

        $triage->delete();

        AuditLogger::record('delete', 'triage', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
