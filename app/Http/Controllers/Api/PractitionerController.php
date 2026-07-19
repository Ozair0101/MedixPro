<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePractitionerRequest;
use App\Http\Resources\PractitionerResource;
use App\Models\Practitioner;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `practitioner`.
 */
class PractitionerController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = Practitioner::query()->paginate($perPage);

        return response()->json([
            'data' => PractitionerResource::collection($rows->items()),
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
        $practitioner = Practitioner::findOrFail($id);

        return response()->json(['data' => new PractitionerResource($practitioner)]);
    }

    public function store(StorePractitionerRequest $request): JsonResponse
    {
        $practitioner = Practitioner::create($request->validated());

        AuditLogger::record('create', 'practitioner', (string) $practitioner->getKey());

        return response()->json(
            ['data' => new PractitionerResource($practitioner)], 201
        );
    }

    public function update(StorePractitionerRequest $request, string $id): JsonResponse
    {
        $practitioner = Practitioner::findOrFail($id);
        $practitioner->update($request->validated());

        AuditLogger::record('update', 'practitioner', $id);

        return response()->json(['data' => new PractitionerResource($practitioner)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $practitioner = Practitioner::findOrFail($id);

        // Deactivate rather than delete: clinical and financial records
        // must stay resolvable for anything that already references them.
        $practitioner->update(['is_active' => false]);

        AuditLogger::record('delete', 'practitioner', $id);

        return response()->json(['message' => 'Deactivated.']);
    }
}
