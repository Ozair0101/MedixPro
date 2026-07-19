<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDrugAdministrationRequest;
use App\Http\Resources\DrugAdministrationResource;
use App\Models\DrugAdministration;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `drug_administration`.
 */
class DrugAdministrationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = DrugAdministration::query()->paginate($perPage);

        return response()->json([
            'data' => DrugAdministrationResource::collection($rows->items()),
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
        $drugAdministration = DrugAdministration::findOrFail($id);

        return response()->json(['data' => new DrugAdministrationResource($drugAdministration)]);
    }

    public function store(StoreDrugAdministrationRequest $request): JsonResponse
    {
        $drugAdministration = DrugAdministration::create($request->validated());

        AuditLogger::record('create', 'drug_administration', (string) $drugAdministration->getKey());

        return response()->json(
            ['data' => new DrugAdministrationResource($drugAdministration)], 201
        );
    }

    public function update(StoreDrugAdministrationRequest $request, string $id): JsonResponse
    {
        $drugAdministration = DrugAdministration::findOrFail($id);
        $drugAdministration->update($request->validated());

        AuditLogger::record('update', 'drug_administration', $id);

        return response()->json(['data' => new DrugAdministrationResource($drugAdministration)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $drugAdministration = DrugAdministration::findOrFail($id);

        $drugAdministration->delete();

        AuditLogger::record('delete', 'drug_administration', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
