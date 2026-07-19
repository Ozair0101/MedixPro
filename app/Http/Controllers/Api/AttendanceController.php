<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAttendanceRequest;
use App\Http\Resources\AttendanceResource;
use App\Models\Attendance;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `attendance`.
 */
class AttendanceController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = Attendance::query()->paginate($perPage);

        return response()->json([
            'data' => AttendanceResource::collection($rows->items()),
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
        $attendance = Attendance::findOrFail($id);

        return response()->json(['data' => new AttendanceResource($attendance)]);
    }

    public function store(StoreAttendanceRequest $request): JsonResponse
    {
        $attendance = Attendance::create($request->validated());

        AuditLogger::record('create', 'attendance', (string) $attendance->getKey());

        return response()->json(
            ['data' => new AttendanceResource($attendance)], 201
        );
    }

    public function update(StoreAttendanceRequest $request, string $id): JsonResponse
    {
        $attendance = Attendance::findOrFail($id);
        $attendance->update($request->validated());

        AuditLogger::record('update', 'attendance', $id);

        return response()->json(['data' => new AttendanceResource($attendance)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $attendance = Attendance::findOrFail($id);

        $attendance->delete();

        AuditLogger::record('delete', 'attendance', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
