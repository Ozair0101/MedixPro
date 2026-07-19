<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreQueueTokenRequest;
use App\Http\Resources\QueueTokenResource;
use App\Models\QueueToken;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `queue_token`.
 */
class QueueTokenController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = QueueToken::query()->paginate($perPage);

        return response()->json([
            'data' => QueueTokenResource::collection($rows->items()),
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
        $queueToken = QueueToken::findOrFail($id);

        return response()->json(['data' => new QueueTokenResource($queueToken)]);
    }

    public function store(StoreQueueTokenRequest $request): JsonResponse
    {
        $queueToken = QueueToken::create($request->validated());

        AuditLogger::record('create', 'queue_token', (string) $queueToken->getKey());

        return response()->json(
            ['data' => new QueueTokenResource($queueToken)], 201
        );
    }

    public function update(StoreQueueTokenRequest $request, string $id): JsonResponse
    {
        $queueToken = QueueToken::findOrFail($id);
        $queueToken->update($request->validated());

        AuditLogger::record('update', 'queue_token', $id);

        return response()->json(['data' => new QueueTokenResource($queueToken)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $queueToken = QueueToken::findOrFail($id);

        $queueToken->delete();

        AuditLogger::record('delete', 'queue_token', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
