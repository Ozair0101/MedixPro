<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLabCriticalNotificationRequest;
use App\Http\Resources\LabCriticalNotificationResource;
use App\Models\LabCriticalNotification;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `lab_critical_notification`.
 */
class LabCriticalNotificationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = LabCriticalNotification::query()->paginate($perPage);

        return response()->json([
            'data' => LabCriticalNotificationResource::collection($rows->items()),
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
        $labCriticalNotification = LabCriticalNotification::findOrFail($id);

        return response()->json(['data' => new LabCriticalNotificationResource($labCriticalNotification)]);
    }

    public function store(StoreLabCriticalNotificationRequest $request): JsonResponse
    {
        $labCriticalNotification = LabCriticalNotification::create($request->validated());

        AuditLogger::record('create', 'lab_critical_notification', (string) $labCriticalNotification->getKey());

        return response()->json(
            ['data' => new LabCriticalNotificationResource($labCriticalNotification)], 201
        );
    }

    public function update(StoreLabCriticalNotificationRequest $request, string $id): JsonResponse
    {
        $labCriticalNotification = LabCriticalNotification::findOrFail($id);
        $labCriticalNotification->update($request->validated());

        AuditLogger::record('update', 'lab_critical_notification', $id);

        return response()->json(['data' => new LabCriticalNotificationResource($labCriticalNotification)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $labCriticalNotification = LabCriticalNotification::findOrFail($id);

        $labCriticalNotification->delete();

        AuditLogger::record('delete', 'lab_critical_notification', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
