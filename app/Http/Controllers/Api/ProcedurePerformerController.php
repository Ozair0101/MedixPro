<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProcedurePerformerRequest;
use App\Http\Resources\ProcedurePerformerResource;
use App\Models\ProcedurePerformer;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `procedure_performer`.
 */
class ProcedurePerformerController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = ProcedurePerformer::query()->paginate($perPage);

        return response()->json([
            'data' => ProcedurePerformerResource::collection($rows->items()),
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
        $procedurePerformer = ProcedurePerformer::findOrFail($id);

        return response()->json(['data' => new ProcedurePerformerResource($procedurePerformer)]);
    }

    public function store(StoreProcedurePerformerRequest $request): JsonResponse
    {
        $procedurePerformer = ProcedurePerformer::create($request->validated());

        AuditLogger::record('create', 'procedure_performer', (string) $procedurePerformer->getKey());

        return response()->json(
            ['data' => new ProcedurePerformerResource($procedurePerformer)], 201
        );
    }

    public function update(StoreProcedurePerformerRequest $request, string $id): JsonResponse
    {
        $procedurePerformer = ProcedurePerformer::findOrFail($id);
        $procedurePerformer->update($request->validated());

        AuditLogger::record('update', 'procedure_performer', $id);

        return response()->json(['data' => new ProcedurePerformerResource($procedurePerformer)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $procedurePerformer = ProcedurePerformer::findOrFail($id);

        $procedurePerformer->delete();

        AuditLogger::record('delete', 'procedure_performer', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
