<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreClinicalNoteRequest;
use App\Http\Resources\ClinicalNoteResource;
use App\Models\ClinicalNote;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `clinical_note`.
 */
class ClinicalNoteController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = ClinicalNote::query()->paginate($perPage);

        return response()->json([
            'data' => ClinicalNoteResource::collection($rows->items()),
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
        $clinicalNote = ClinicalNote::findOrFail($id);

        return response()->json(['data' => new ClinicalNoteResource($clinicalNote)]);
    }

    public function store(StoreClinicalNoteRequest $request): JsonResponse
    {
        $clinicalNote = ClinicalNote::create($request->validated());

        AuditLogger::record('create', 'clinical_note', (string) $clinicalNote->getKey());

        return response()->json(
            ['data' => new ClinicalNoteResource($clinicalNote)], 201
        );
    }

    public function update(StoreClinicalNoteRequest $request, string $id): JsonResponse
    {
        $clinicalNote = ClinicalNote::findOrFail($id);
        $clinicalNote->update($request->validated());

        AuditLogger::record('update', 'clinical_note', $id);

        return response()->json(['data' => new ClinicalNoteResource($clinicalNote)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $clinicalNote = ClinicalNote::findOrFail($id);

        $clinicalNote->delete();

        AuditLogger::record('delete', 'clinical_note', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
