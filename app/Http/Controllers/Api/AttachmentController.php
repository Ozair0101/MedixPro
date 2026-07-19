<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAttachmentRequest;
use App\Http\Resources\AttachmentResource;
use App\Models\Attachment;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `attachment`.
 */
class AttachmentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = Attachment::query()->paginate($perPage);

        return response()->json([
            'data' => AttachmentResource::collection($rows->items()),
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
        $attachment = Attachment::findOrFail($id);

        return response()->json(['data' => new AttachmentResource($attachment)]);
    }

    public function store(StoreAttachmentRequest $request): JsonResponse
    {
        $attachment = Attachment::create($request->validated());

        AuditLogger::record('create', 'attachment', (string) $attachment->getKey());

        return response()->json(
            ['data' => new AttachmentResource($attachment)], 201
        );
    }

    public function update(StoreAttachmentRequest $request, string $id): JsonResponse
    {
        $attachment = Attachment::findOrFail($id);
        $attachment->update($request->validated());

        AuditLogger::record('update', 'attachment', $id);

        return response()->json(['data' => new AttachmentResource($attachment)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $attachment = Attachment::findOrFail($id);

        $attachment->delete();

        AuditLogger::record('delete', 'attachment', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
