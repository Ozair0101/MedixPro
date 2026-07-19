<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreClinicalProcedureRequest;
use App\Http\Resources\ClinicalProcedureResource;
use App\Models\ClinicalProcedure;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `clinical_procedure`.
 */
class ClinicalProcedureController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = ClinicalProcedure::query()->paginate($perPage);

        return response()->json([
            'data' => ClinicalProcedureResource::collection($rows->items()),
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
        $clinicalProcedure = ClinicalProcedure::findOrFail($id);

        return response()->json(['data' => new ClinicalProcedureResource($clinicalProcedure)]);
    }

    public function store(StoreClinicalProcedureRequest $request): JsonResponse
    {
        $clinicalProcedure = ClinicalProcedure::create($request->validated());

        AuditLogger::record('create', 'clinical_procedure', (string) $clinicalProcedure->getKey());

        return response()->json(
            ['data' => new ClinicalProcedureResource($clinicalProcedure)], 201
        );
    }

    public function update(StoreClinicalProcedureRequest $request, string $id): JsonResponse
    {
        $clinicalProcedure = ClinicalProcedure::findOrFail($id);
        $clinicalProcedure->update($request->validated());

        AuditLogger::record('update', 'clinical_procedure', $id);

        return response()->json(['data' => new ClinicalProcedureResource($clinicalProcedure)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $clinicalProcedure = ClinicalProcedure::findOrFail($id);

        $clinicalProcedure->delete();

        AuditLogger::record('delete', 'clinical_procedure', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
