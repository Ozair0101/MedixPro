<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSurgeryRequest;
use App\Http\Resources\SurgeryResource;
use App\Models\Surgery;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `surgery`.
 */
class SurgeryController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = Surgery::query()->paginate($perPage);

        return response()->json([
            'data' => SurgeryResource::collection($rows->items()),
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
        $surgery = Surgery::findOrFail($id);

        return response()->json(['data' => new SurgeryResource($surgery)]);
    }

    public function store(StoreSurgeryRequest $request): JsonResponse
    {
        $surgery = Surgery::create($request->validated());

        AuditLogger::record('create', 'surgery', (string) $surgery->getKey());

        return response()->json(
            ['data' => new SurgeryResource($surgery)], 201
        );
    }

    public function update(StoreSurgeryRequest $request, string $id): JsonResponse
    {
        $surgery = Surgery::findOrFail($id);
        $surgery->update($request->validated());

        AuditLogger::record('update', 'surgery', $id);

        return response()->json(['data' => new SurgeryResource($surgery)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $surgery = Surgery::findOrFail($id);

        $surgery->delete();

        AuditLogger::record('delete', 'surgery', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
