<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAllergyRequest;
use App\Http\Resources\AllergyResource;
use App\Models\Allergy;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `allergy`.
 */
class AllergyController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = Allergy::query()->paginate($perPage);

        return response()->json([
            'data' => AllergyResource::collection($rows->items()),
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
        $allergy = Allergy::findOrFail($id);

        return response()->json(['data' => new AllergyResource($allergy)]);
    }

    public function store(StoreAllergyRequest $request): JsonResponse
    {
        $allergy = Allergy::create($request->validated());

        AuditLogger::record('create', 'allergy', (string) $allergy->getKey());

        return response()->json(
            ['data' => new AllergyResource($allergy)], 201
        );
    }

    public function update(StoreAllergyRequest $request, string $id): JsonResponse
    {
        $allergy = Allergy::findOrFail($id);
        $allergy->update($request->validated());

        AuditLogger::record('update', 'allergy', $id);

        return response()->json(['data' => new AllergyResource($allergy)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $allergy = Allergy::findOrFail($id);

        $allergy->delete();

        AuditLogger::record('delete', 'allergy', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
