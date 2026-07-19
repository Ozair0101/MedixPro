<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTherapeuticMoietyRequest;
use App\Http\Resources\TherapeuticMoietyResource;
use App\Models\TherapeuticMoiety;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `therapeutic_moiety`.
 */
class TherapeuticMoietyController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = TherapeuticMoiety::query()->paginate($perPage);

        return response()->json([
            'data' => TherapeuticMoietyResource::collection($rows->items()),
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
        $therapeuticMoiety = TherapeuticMoiety::findOrFail($id);

        return response()->json(['data' => new TherapeuticMoietyResource($therapeuticMoiety)]);
    }

    public function store(StoreTherapeuticMoietyRequest $request): JsonResponse
    {
        $therapeuticMoiety = TherapeuticMoiety::create($request->validated());

        AuditLogger::record('create', 'therapeutic_moiety', (string) $therapeuticMoiety->getKey());

        return response()->json(
            ['data' => new TherapeuticMoietyResource($therapeuticMoiety)], 201
        );
    }

    public function update(StoreTherapeuticMoietyRequest $request, string $id): JsonResponse
    {
        $therapeuticMoiety = TherapeuticMoiety::findOrFail($id);
        $therapeuticMoiety->update($request->validated());

        AuditLogger::record('update', 'therapeutic_moiety', $id);

        return response()->json(['data' => new TherapeuticMoietyResource($therapeuticMoiety)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $therapeuticMoiety = TherapeuticMoiety::findOrFail($id);

        $therapeuticMoiety->delete();

        AuditLogger::record('delete', 'therapeutic_moiety', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
