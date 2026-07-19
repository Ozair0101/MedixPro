<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBloodTransfusionRequest;
use App\Http\Resources\BloodTransfusionResource;
use App\Models\BloodTransfusion;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `blood_transfusion`.
 */
class BloodTransfusionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = BloodTransfusion::query()->paginate($perPage);

        return response()->json([
            'data' => BloodTransfusionResource::collection($rows->items()),
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
        $bloodTransfusion = BloodTransfusion::findOrFail($id);

        return response()->json(['data' => new BloodTransfusionResource($bloodTransfusion)]);
    }

    public function store(StoreBloodTransfusionRequest $request): JsonResponse
    {
        $bloodTransfusion = BloodTransfusion::create($request->validated());

        AuditLogger::record('create', 'blood_transfusion', (string) $bloodTransfusion->getKey());

        return response()->json(
            ['data' => new BloodTransfusionResource($bloodTransfusion)], 201
        );
    }

    public function update(StoreBloodTransfusionRequest $request, string $id): JsonResponse
    {
        $bloodTransfusion = BloodTransfusion::findOrFail($id);
        $bloodTransfusion->update($request->validated());

        AuditLogger::record('update', 'blood_transfusion', $id);

        return response()->json(['data' => new BloodTransfusionResource($bloodTransfusion)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $bloodTransfusion = BloodTransfusion::findOrFail($id);

        $bloodTransfusion->delete();

        AuditLogger::record('delete', 'blood_transfusion', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
