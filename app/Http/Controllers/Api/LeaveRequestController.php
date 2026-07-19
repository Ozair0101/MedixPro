<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLeaveRequestRequest;
use App\Http\Resources\LeaveRequestResource;
use App\Models\LeaveRequest;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `leave_request`.
 */
class LeaveRequestController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = LeaveRequest::query()->paginate($perPage);

        return response()->json([
            'data' => LeaveRequestResource::collection($rows->items()),
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
        $leaveRequest = LeaveRequest::findOrFail($id);

        return response()->json(['data' => new LeaveRequestResource($leaveRequest)]);
    }

    public function store(StoreLeaveRequestRequest $request): JsonResponse
    {
        $leaveRequest = LeaveRequest::create($request->validated());

        AuditLogger::record('create', 'leave_request', (string) $leaveRequest->getKey());

        return response()->json(
            ['data' => new LeaveRequestResource($leaveRequest)], 201
        );
    }

    public function update(StoreLeaveRequestRequest $request, string $id): JsonResponse
    {
        $leaveRequest = LeaveRequest::findOrFail($id);
        $leaveRequest->update($request->validated());

        AuditLogger::record('update', 'leave_request', $id);

        return response()->json(['data' => new LeaveRequestResource($leaveRequest)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $leaveRequest = LeaveRequest::findOrFail($id);

        $leaveRequest->delete();

        AuditLogger::record('delete', 'leave_request', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
