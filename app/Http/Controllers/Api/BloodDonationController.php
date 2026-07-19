<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBloodDonationRequest;
use App\Http\Resources\BloodDonationResource;
use App\Models\BloodDonation;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `blood_donation`.
 */
class BloodDonationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = BloodDonation::query()->paginate($perPage);

        return response()->json([
            'data' => BloodDonationResource::collection($rows->items()),
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
        $bloodDonation = BloodDonation::findOrFail($id);

        return response()->json(['data' => new BloodDonationResource($bloodDonation)]);
    }

    public function store(StoreBloodDonationRequest $request): JsonResponse
    {
        $bloodDonation = BloodDonation::create($request->validated());

        AuditLogger::record('create', 'blood_donation', (string) $bloodDonation->getKey());

        return response()->json(
            ['data' => new BloodDonationResource($bloodDonation)], 201
        );
    }

    public function update(StoreBloodDonationRequest $request, string $id): JsonResponse
    {
        $bloodDonation = BloodDonation::findOrFail($id);
        $bloodDonation->update($request->validated());

        AuditLogger::record('update', 'blood_donation', $id);

        return response()->json(['data' => new BloodDonationResource($bloodDonation)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $bloodDonation = BloodDonation::findOrFail($id);

        $bloodDonation->delete();

        AuditLogger::record('delete', 'blood_donation', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
