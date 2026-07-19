<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBloodCrossmatchRequest;
use App\Http\Resources\BloodCrossmatchResource;
use App\Models\BloodCrossmatch;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `blood_crossmatch`.
 */
class BloodCrossmatchController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = BloodCrossmatch::query()->paginate($perPage);

        return response()->json([
            'data' => BloodCrossmatchResource::collection($rows->items()),
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
        $bloodCrossmatch = BloodCrossmatch::findOrFail($id);

        return response()->json(['data' => new BloodCrossmatchResource($bloodCrossmatch)]);
    }

    public function store(StoreBloodCrossmatchRequest $request): JsonResponse
    {
        $bloodCrossmatch = BloodCrossmatch::create($request->validated());

        AuditLogger::record('create', 'blood_crossmatch', (string) $bloodCrossmatch->getKey());

        return response()->json(
            ['data' => new BloodCrossmatchResource($bloodCrossmatch)], 201
        );
    }

    public function update(StoreBloodCrossmatchRequest $request, string $id): JsonResponse
    {
        $bloodCrossmatch = BloodCrossmatch::findOrFail($id);
        $bloodCrossmatch->update($request->validated());

        AuditLogger::record('update', 'blood_crossmatch', $id);

        return response()->json(['data' => new BloodCrossmatchResource($bloodCrossmatch)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $bloodCrossmatch = BloodCrossmatch::findOrFail($id);

        $bloodCrossmatch->delete();

        AuditLogger::record('delete', 'blood_crossmatch', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
