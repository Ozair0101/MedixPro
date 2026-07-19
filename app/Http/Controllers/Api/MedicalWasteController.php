<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMedicalWasteRequest;
use App\Http\Resources\MedicalWasteResource;
use App\Models\MedicalWaste;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `medical_waste`.
 */
class MedicalWasteController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = MedicalWaste::query()->paginate($perPage);

        return response()->json([
            'data' => MedicalWasteResource::collection($rows->items()),
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
        $medicalWaste = MedicalWaste::findOrFail($id);

        return response()->json(['data' => new MedicalWasteResource($medicalWaste)]);
    }

    public function store(StoreMedicalWasteRequest $request): JsonResponse
    {
        $medicalWaste = MedicalWaste::create($request->validated());

        AuditLogger::record('create', 'medical_waste', (string) $medicalWaste->getKey());

        return response()->json(
            ['data' => new MedicalWasteResource($medicalWaste)], 201
        );
    }

    public function update(StoreMedicalWasteRequest $request, string $id): JsonResponse
    {
        $medicalWaste = MedicalWaste::findOrFail($id);
        $medicalWaste->update($request->validated());

        AuditLogger::record('update', 'medical_waste', $id);

        return response()->json(['data' => new MedicalWasteResource($medicalWaste)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $medicalWaste = MedicalWaste::findOrFail($id);

        $medicalWaste->delete();

        AuditLogger::record('delete', 'medical_waste', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
