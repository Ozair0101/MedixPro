<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInfectionSurveillanceRequest;
use App\Http\Resources\InfectionSurveillanceResource;
use App\Models\InfectionSurveillance;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `infection_surveillance`.
 */
class InfectionSurveillanceController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = InfectionSurveillance::query()->paginate($perPage);

        return response()->json([
            'data' => InfectionSurveillanceResource::collection($rows->items()),
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
        $infectionSurveillance = InfectionSurveillance::findOrFail($id);

        return response()->json(['data' => new InfectionSurveillanceResource($infectionSurveillance)]);
    }

    public function store(StoreInfectionSurveillanceRequest $request): JsonResponse
    {
        $infectionSurveillance = InfectionSurveillance::create($request->validated());

        AuditLogger::record('create', 'infection_surveillance', (string) $infectionSurveillance->getKey());

        return response()->json(
            ['data' => new InfectionSurveillanceResource($infectionSurveillance)], 201
        );
    }

    public function update(StoreInfectionSurveillanceRequest $request, string $id): JsonResponse
    {
        $infectionSurveillance = InfectionSurveillance::findOrFail($id);
        $infectionSurveillance->update($request->validated());

        AuditLogger::record('update', 'infection_surveillance', $id);

        return response()->json(['data' => new InfectionSurveillanceResource($infectionSurveillance)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $infectionSurveillance = InfectionSurveillance::findOrFail($id);

        $infectionSurveillance->delete();

        AuditLogger::record('delete', 'infection_surveillance', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
