<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAgingBucketRequest;
use App\Http\Resources\AgingBucketResource;
use App\Models\AgingBucket;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `aging_bucket`.
 */
class AgingBucketController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = AgingBucket::query()->paginate($perPage);

        return response()->json([
            'data' => AgingBucketResource::collection($rows->items()),
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
        $agingBucket = AgingBucket::findOrFail($id);

        return response()->json(['data' => new AgingBucketResource($agingBucket)]);
    }

    public function store(StoreAgingBucketRequest $request): JsonResponse
    {
        $agingBucket = AgingBucket::create($request->validated());

        AuditLogger::record('create', 'aging_bucket', (string) $agingBucket->getKey());

        return response()->json(
            ['data' => new AgingBucketResource($agingBucket)], 201
        );
    }

    public function update(StoreAgingBucketRequest $request, string $id): JsonResponse
    {
        $agingBucket = AgingBucket::findOrFail($id);
        $agingBucket->update($request->validated());

        AuditLogger::record('update', 'aging_bucket', $id);

        return response()->json(['data' => new AgingBucketResource($agingBucket)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $agingBucket = AgingBucket::findOrFail($id);

        $agingBucket->delete();

        AuditLogger::record('delete', 'aging_bucket', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
