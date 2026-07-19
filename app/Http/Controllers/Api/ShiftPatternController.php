<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreShiftPatternRequest;
use App\Http\Resources\ShiftPatternResource;
use App\Models\ShiftPattern;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `shift_pattern`.
 */
class ShiftPatternController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = ShiftPattern::query()->paginate($perPage);

        return response()->json([
            'data' => ShiftPatternResource::collection($rows->items()),
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
        $shiftPattern = ShiftPattern::findOrFail($id);

        return response()->json(['data' => new ShiftPatternResource($shiftPattern)]);
    }

    public function store(StoreShiftPatternRequest $request): JsonResponse
    {
        $shiftPattern = ShiftPattern::create($request->validated());

        AuditLogger::record('create', 'shift_pattern', (string) $shiftPattern->getKey());

        return response()->json(
            ['data' => new ShiftPatternResource($shiftPattern)], 201
        );
    }

    public function update(StoreShiftPatternRequest $request, string $id): JsonResponse
    {
        $shiftPattern = ShiftPattern::findOrFail($id);
        $shiftPattern->update($request->validated());

        AuditLogger::record('update', 'shift_pattern', $id);

        return response()->json(['data' => new ShiftPatternResource($shiftPattern)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $shiftPattern = ShiftPattern::findOrFail($id);

        $shiftPattern->delete();

        AuditLogger::record('delete', 'shift_pattern', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
