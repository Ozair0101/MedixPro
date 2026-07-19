<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreHandHygieneAuditRequest;
use App\Http\Resources\HandHygieneAuditResource;
use App\Models\HandHygieneAudit;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `hand_hygiene_audit`.
 */
class HandHygieneAuditController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = HandHygieneAudit::query()->paginate($perPage);

        return response()->json([
            'data' => HandHygieneAuditResource::collection($rows->items()),
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
        $handHygieneAudit = HandHygieneAudit::findOrFail($id);

        return response()->json(['data' => new HandHygieneAuditResource($handHygieneAudit)]);
    }

    public function store(StoreHandHygieneAuditRequest $request): JsonResponse
    {
        $handHygieneAudit = HandHygieneAudit::create($request->validated());

        AuditLogger::record('create', 'hand_hygiene_audit', (string) $handHygieneAudit->getKey());

        return response()->json(
            ['data' => new HandHygieneAuditResource($handHygieneAudit)], 201
        );
    }

    public function update(StoreHandHygieneAuditRequest $request, string $id): JsonResponse
    {
        $handHygieneAudit = HandHygieneAudit::findOrFail($id);
        $handHygieneAudit->update($request->validated());

        AuditLogger::record('update', 'hand_hygiene_audit', $id);

        return response()->json(['data' => new HandHygieneAuditResource($handHygieneAudit)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $handHygieneAudit = HandHygieneAudit::findOrFail($id);

        $handHygieneAudit->delete();

        AuditLogger::record('delete', 'hand_hygiene_audit', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
