<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMophCaseRequest;
use App\Http\Resources\MophCaseResource;
use App\Models\MophCase;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `moph_case`.
 */
class MophCaseController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = MophCase::query()->paginate($perPage);

        return response()->json([
            'data' => MophCaseResource::collection($rows->items()),
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
        $mophCase = MophCase::findOrFail($id);

        return response()->json(['data' => new MophCaseResource($mophCase)]);
    }

    public function store(StoreMophCaseRequest $request): JsonResponse
    {
        $mophCase = MophCase::create($request->validated());

        AuditLogger::record('create', 'moph_case', (string) $mophCase->getKey());

        return response()->json(
            ['data' => new MophCaseResource($mophCase)], 201
        );
    }

    public function update(StoreMophCaseRequest $request, string $id): JsonResponse
    {
        $mophCase = MophCase::findOrFail($id);
        $mophCase->update($request->validated());

        AuditLogger::record('update', 'moph_case', $id);

        return response()->json(['data' => new MophCaseResource($mophCase)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $mophCase = MophCase::findOrFail($id);

        $mophCase->delete();

        AuditLogger::record('delete', 'moph_case', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
