<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AdtEventResource;
use App\Models\AdtEvent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `adt_event`.
 *
 * READ-ONLY. This table is append-only at the database level; the write
 * endpoints are omitted rather than offered and rejected.
 */
class AdtEventController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = AdtEvent::query()->paginate($perPage);

        return response()->json([
            'data' => AdtEventResource::collection($rows->items()),
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
        $adtEvent = AdtEvent::findOrFail($id);

        return response()->json(['data' => new AdtEventResource($adtEvent)]);
    }
}
