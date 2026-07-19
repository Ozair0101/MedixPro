<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePartyRequest;
use App\Http\Resources\PartyResource;
use App\Models\Party;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `party`.
 */
class PartyController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = Party::query()->paginate($perPage);

        return response()->json([
            'data' => PartyResource::collection($rows->items()),
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
        $party = Party::findOrFail($id);

        return response()->json(['data' => new PartyResource($party)]);
    }

    public function store(StorePartyRequest $request): JsonResponse
    {
        $party = Party::create($request->validated());

        AuditLogger::record('create', 'party', (string) $party->getKey());

        return response()->json(
            ['data' => new PartyResource($party)], 201
        );
    }

    public function update(StorePartyRequest $request, string $id): JsonResponse
    {
        $party = Party::findOrFail($id);
        $party->update($request->validated());

        AuditLogger::record('update', 'party', $id);

        return response()->json(['data' => new PartyResource($party)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $party = Party::findOrFail($id);

        $party->delete();

        AuditLogger::record('delete', 'party', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
