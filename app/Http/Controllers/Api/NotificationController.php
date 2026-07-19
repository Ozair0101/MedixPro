<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreNotificationRequest;
use App\Http\Resources\NotificationResource;
use App\Models\Notification;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `notification`.
 */
class NotificationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = Notification::query()->paginate($perPage);

        return response()->json([
            'data' => NotificationResource::collection($rows->items()),
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
        $notification = Notification::findOrFail($id);

        return response()->json(['data' => new NotificationResource($notification)]);
    }

    public function store(StoreNotificationRequest $request): JsonResponse
    {
        $notification = Notification::create($request->validated());

        AuditLogger::record('create', 'notification', (string) $notification->getKey());

        return response()->json(
            ['data' => new NotificationResource($notification)], 201
        );
    }

    public function update(StoreNotificationRequest $request, string $id): JsonResponse
    {
        $notification = Notification::findOrFail($id);
        $notification->update($request->validated());

        AuditLogger::record('update', 'notification', $id);

        return response()->json(['data' => new NotificationResource($notification)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $notification = Notification::findOrFail($id);

        $notification->delete();

        AuditLogger::record('delete', 'notification', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
