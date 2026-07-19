<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDewsNotificationRequest;
use App\Http\Resources\DewsNotificationResource;
use App\Models\DewsNotification;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `dews_notification`.
 */
class DewsNotificationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = DewsNotification::query()->paginate($perPage);

        return response()->json([
            'data' => DewsNotificationResource::collection($rows->items()),
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
        $dewsNotification = DewsNotification::findOrFail($id);

        return response()->json(['data' => new DewsNotificationResource($dewsNotification)]);
    }

    public function store(StoreDewsNotificationRequest $request): JsonResponse
    {
        $dewsNotification = DewsNotification::create($request->validated());

        AuditLogger::record('create', 'dews_notification', (string) $dewsNotification->getKey());

        return response()->json(
            ['data' => new DewsNotificationResource($dewsNotification)], 201
        );
    }

    public function update(StoreDewsNotificationRequest $request, string $id): JsonResponse
    {
        $dewsNotification = DewsNotification::findOrFail($id);
        $dewsNotification->update($request->validated());

        AuditLogger::record('update', 'dews_notification', $id);

        return response()->json(['data' => new DewsNotificationResource($dewsNotification)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $dewsNotification = DewsNotification::findOrFail($id);

        $dewsNotification->delete();

        AuditLogger::record('delete', 'dews_notification', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
