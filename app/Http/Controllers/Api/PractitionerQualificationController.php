<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePractitionerQualificationRequest;
use App\Http\Resources\PractitionerQualificationResource;
use App\Models\PractitionerQualification;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `practitioner_qualification`.
 */
class PractitionerQualificationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = PractitionerQualification::query()->paginate($perPage);

        return response()->json([
            'data' => PractitionerQualificationResource::collection($rows->items()),
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
        $practitionerQualification = PractitionerQualification::findOrFail($id);

        return response()->json(['data' => new PractitionerQualificationResource($practitionerQualification)]);
    }

    public function store(StorePractitionerQualificationRequest $request): JsonResponse
    {
        $practitionerQualification = PractitionerQualification::create($request->validated());

        AuditLogger::record('create', 'practitioner_qualification', (string) $practitionerQualification->getKey());

        return response()->json(
            ['data' => new PractitionerQualificationResource($practitionerQualification)], 201
        );
    }

    public function update(StorePractitionerQualificationRequest $request, string $id): JsonResponse
    {
        $practitionerQualification = PractitionerQualification::findOrFail($id);
        $practitionerQualification->update($request->validated());

        AuditLogger::record('update', 'practitioner_qualification', $id);

        return response()->json(['data' => new PractitionerQualificationResource($practitionerQualification)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $practitionerQualification = PractitionerQualification::findOrFail($id);

        $practitionerQualification->delete();

        AuditLogger::record('delete', 'practitioner_qualification', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
