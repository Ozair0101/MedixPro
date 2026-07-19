<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreConsentRequest;
use App\Http\Resources\ConsentResource;
use App\Models\Consent;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `consent`.
 */
class ConsentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = Consent::query()->paginate($perPage);

        return response()->json([
            'data' => ConsentResource::collection($rows->items()),
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
        $consent = Consent::findOrFail($id);

        return response()->json(['data' => new ConsentResource($consent)]);
    }

    public function store(StoreConsentRequest $request): JsonResponse
    {
        $consent = Consent::create($request->validated());

        AuditLogger::record('create', 'consent', (string) $consent->getKey());

        return response()->json(
            ['data' => new ConsentResource($consent)], 201
        );
    }

    public function update(StoreConsentRequest $request, string $id): JsonResponse
    {
        $consent = Consent::findOrFail($id);
        $consent->update($request->validated());

        AuditLogger::record('update', 'consent', $id);

        return response()->json(['data' => new ConsentResource($consent)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $consent = Consent::findOrFail($id);

        $consent->delete();

        AuditLogger::record('delete', 'consent', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
