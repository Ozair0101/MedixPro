<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTransfusionReactionRequest;
use App\Http\Resources\TransfusionReactionResource;
use App\Models\TransfusionReaction;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `transfusion_reaction`.
 */
class TransfusionReactionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = TransfusionReaction::query()->paginate($perPage);

        return response()->json([
            'data' => TransfusionReactionResource::collection($rows->items()),
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
        $transfusionReaction = TransfusionReaction::findOrFail($id);

        return response()->json(['data' => new TransfusionReactionResource($transfusionReaction)]);
    }

    public function store(StoreTransfusionReactionRequest $request): JsonResponse
    {
        $transfusionReaction = TransfusionReaction::create($request->validated());

        AuditLogger::record('create', 'transfusion_reaction', (string) $transfusionReaction->getKey());

        return response()->json(
            ['data' => new TransfusionReactionResource($transfusionReaction)], 201
        );
    }

    public function update(StoreTransfusionReactionRequest $request, string $id): JsonResponse
    {
        $transfusionReaction = TransfusionReaction::findOrFail($id);
        $transfusionReaction->update($request->validated());

        AuditLogger::record('update', 'transfusion_reaction', $id);

        return response()->json(['data' => new TransfusionReactionResource($transfusionReaction)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $transfusionReaction = TransfusionReaction::findOrFail($id);

        $transfusionReaction->delete();

        AuditLogger::record('delete', 'transfusion_reaction', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
