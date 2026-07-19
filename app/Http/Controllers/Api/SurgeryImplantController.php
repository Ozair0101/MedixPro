<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSurgeryImplantRequest;
use App\Http\Resources\SurgeryImplantResource;
use App\Models\SurgeryImplant;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `surgery_implant`.
 */
class SurgeryImplantController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = SurgeryImplant::query()->paginate($perPage);

        return response()->json([
            'data' => SurgeryImplantResource::collection($rows->items()),
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
        $surgeryImplant = SurgeryImplant::findOrFail($id);

        return response()->json(['data' => new SurgeryImplantResource($surgeryImplant)]);
    }

    public function store(StoreSurgeryImplantRequest $request): JsonResponse
    {
        $surgeryImplant = SurgeryImplant::create($request->validated());

        AuditLogger::record('create', 'surgery_implant', (string) $surgeryImplant->getKey());

        return response()->json(
            ['data' => new SurgeryImplantResource($surgeryImplant)], 201
        );
    }

    public function update(StoreSurgeryImplantRequest $request, string $id): JsonResponse
    {
        $surgeryImplant = SurgeryImplant::findOrFail($id);
        $surgeryImplant->update($request->validated());

        AuditLogger::record('update', 'surgery_implant', $id);

        return response()->json(['data' => new SurgeryImplantResource($surgeryImplant)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $surgeryImplant = SurgeryImplant::findOrFail($id);

        $surgeryImplant->delete();

        AuditLogger::record('delete', 'surgery_implant', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
