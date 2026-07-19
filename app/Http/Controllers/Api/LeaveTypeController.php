<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLeaveTypeRequest;
use App\Http\Resources\LeaveTypeResource;
use App\Models\LeaveType;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `leave_type`.
 */
class LeaveTypeController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = LeaveType::query()->paginate($perPage);

        return response()->json([
            'data' => LeaveTypeResource::collection($rows->items()),
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
        $leaveType = LeaveType::findOrFail($id);

        return response()->json(['data' => new LeaveTypeResource($leaveType)]);
    }

    public function store(StoreLeaveTypeRequest $request): JsonResponse
    {
        $leaveType = LeaveType::create($request->validated());

        AuditLogger::record('create', 'leave_type', (string) $leaveType->getKey());

        return response()->json(
            ['data' => new LeaveTypeResource($leaveType)], 201
        );
    }

    public function update(StoreLeaveTypeRequest $request, string $id): JsonResponse
    {
        $leaveType = LeaveType::findOrFail($id);
        $leaveType->update($request->validated());

        AuditLogger::record('update', 'leave_type', $id);

        return response()->json(['data' => new LeaveTypeResource($leaveType)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $leaveType = LeaveType::findOrFail($id);

        $leaveType->delete();

        AuditLogger::record('delete', 'leave_type', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
