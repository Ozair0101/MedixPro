<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePractitionerSpecialtyRequest;
use App\Http\Resources\PractitionerSpecialtyResource;
use App\Models\PractitionerSpecialty;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `practitioner_specialty`.
 */
class PractitionerSpecialtyController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = PractitionerSpecialty::query()->paginate($perPage);

        return response()->json([
            'data' => PractitionerSpecialtyResource::collection($rows->items()),
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
        $practitionerSpecialty = PractitionerSpecialty::findOrFail($id);

        return response()->json(['data' => new PractitionerSpecialtyResource($practitionerSpecialty)]);
    }

    public function store(StorePractitionerSpecialtyRequest $request): JsonResponse
    {
        $practitionerSpecialty = PractitionerSpecialty::create($request->validated());

        AuditLogger::record('create', 'practitioner_specialty', (string) $practitionerSpecialty->getKey());

        return response()->json(
            ['data' => new PractitionerSpecialtyResource($practitionerSpecialty)], 201
        );
    }

    public function update(StorePractitionerSpecialtyRequest $request, string $id): JsonResponse
    {
        $practitionerSpecialty = PractitionerSpecialty::findOrFail($id);
        $practitionerSpecialty->update($request->validated());

        AuditLogger::record('update', 'practitioner_specialty', $id);

        return response()->json(['data' => new PractitionerSpecialtyResource($practitionerSpecialty)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $practitionerSpecialty = PractitionerSpecialty::findOrFail($id);

        $practitionerSpecialty->delete();

        AuditLogger::record('delete', 'practitioner_specialty', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
