<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAdmissionRequest;
use App\Http\Resources\AdmissionResource;
use App\Models\Admission;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `admission`.
 */
class AdmissionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = Admission::query()->paginate($perPage);

        return response()->json([
            'data' => AdmissionResource::collection($rows->items()),
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
        $admission = Admission::findOrFail($id);

        return response()->json(['data' => new AdmissionResource($admission)]);
    }

    public function store(StoreAdmissionRequest $request): JsonResponse
    {
        $admission = Admission::create($request->validated());

        AuditLogger::record('create', 'admission', (string) $admission->getKey());

        return response()->json(
            ['data' => new AdmissionResource($admission)], 201
        );
    }

    public function update(StoreAdmissionRequest $request, string $id): JsonResponse
    {
        $admission = Admission::findOrFail($id);
        $admission->update($request->validated());

        AuditLogger::record('update', 'admission', $id);

        return response()->json(['data' => new AdmissionResource($admission)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $admission = Admission::findOrFail($id);

        $admission->delete();

        AuditLogger::record('delete', 'admission', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
