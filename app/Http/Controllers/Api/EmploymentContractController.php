<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEmploymentContractRequest;
use App\Http\Resources\EmploymentContractResource;
use App\Models\EmploymentContract;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `employment_contract`.
 */
class EmploymentContractController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = EmploymentContract::query()->paginate($perPage);

        return response()->json([
            'data' => EmploymentContractResource::collection($rows->items()),
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
        $employmentContract = EmploymentContract::findOrFail($id);

        return response()->json(['data' => new EmploymentContractResource($employmentContract)]);
    }

    public function store(StoreEmploymentContractRequest $request): JsonResponse
    {
        $employmentContract = EmploymentContract::create($request->validated());

        AuditLogger::record('create', 'employment_contract', (string) $employmentContract->getKey());

        return response()->json(
            ['data' => new EmploymentContractResource($employmentContract)], 201
        );
    }

    public function update(StoreEmploymentContractRequest $request, string $id): JsonResponse
    {
        $employmentContract = EmploymentContract::findOrFail($id);
        $employmentContract->update($request->validated());

        AuditLogger::record('update', 'employment_contract', $id);

        return response()->json(['data' => new EmploymentContractResource($employmentContract)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $employmentContract = EmploymentContract::findOrFail($id);

        $employmentContract->delete();

        AuditLogger::record('delete', 'employment_contract', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
