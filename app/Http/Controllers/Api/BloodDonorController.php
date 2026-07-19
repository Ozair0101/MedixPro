<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBloodDonorRequest;
use App\Http\Resources\BloodDonorResource;
use App\Models\BloodDonor;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `blood_donor`.
 */
class BloodDonorController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = BloodDonor::query()->paginate($perPage);

        return response()->json([
            'data' => BloodDonorResource::collection($rows->items()),
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
        $bloodDonor = BloodDonor::findOrFail($id);

        return response()->json(['data' => new BloodDonorResource($bloodDonor)]);
    }

    public function store(StoreBloodDonorRequest $request): JsonResponse
    {
        $bloodDonor = BloodDonor::create($request->validated());

        AuditLogger::record('create', 'blood_donor', (string) $bloodDonor->getKey());

        return response()->json(
            ['data' => new BloodDonorResource($bloodDonor)], 201
        );
    }

    public function update(StoreBloodDonorRequest $request, string $id): JsonResponse
    {
        $bloodDonor = BloodDonor::findOrFail($id);
        $bloodDonor->update($request->validated());

        AuditLogger::record('update', 'blood_donor', $id);

        return response()->json(['data' => new BloodDonorResource($bloodDonor)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $bloodDonor = BloodDonor::findOrFail($id);

        $bloodDonor->delete();

        AuditLogger::record('delete', 'blood_donor', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
