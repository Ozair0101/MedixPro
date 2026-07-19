<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMortuaryRecordRequest;
use App\Http\Resources\MortuaryRecordResource;
use App\Models\MortuaryRecord;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `mortuary_record`.
 */
class MortuaryRecordController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = MortuaryRecord::query()->paginate($perPage);

        return response()->json([
            'data' => MortuaryRecordResource::collection($rows->items()),
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
        $mortuaryRecord = MortuaryRecord::findOrFail($id);

        return response()->json(['data' => new MortuaryRecordResource($mortuaryRecord)]);
    }

    public function store(StoreMortuaryRecordRequest $request): JsonResponse
    {
        $mortuaryRecord = MortuaryRecord::create($request->validated());

        AuditLogger::record('create', 'mortuary_record', (string) $mortuaryRecord->getKey());

        return response()->json(
            ['data' => new MortuaryRecordResource($mortuaryRecord)], 201
        );
    }

    public function update(StoreMortuaryRecordRequest $request, string $id): JsonResponse
    {
        $mortuaryRecord = MortuaryRecord::findOrFail($id);
        $mortuaryRecord->update($request->validated());

        AuditLogger::record('update', 'mortuary_record', $id);

        return response()->json(['data' => new MortuaryRecordResource($mortuaryRecord)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $mortuaryRecord = MortuaryRecord::findOrFail($id);

        $mortuaryRecord->delete();

        AuditLogger::record('delete', 'mortuary_record', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
