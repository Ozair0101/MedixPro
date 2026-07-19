<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreClinicScheduleRequest;
use App\Http\Resources\ClinicScheduleResource;
use App\Models\ClinicSchedule;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `clinic_schedule`.
 */
class ClinicScheduleController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = ClinicSchedule::query()->paginate($perPage);

        return response()->json([
            'data' => ClinicScheduleResource::collection($rows->items()),
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
        $clinicSchedule = ClinicSchedule::findOrFail($id);

        return response()->json(['data' => new ClinicScheduleResource($clinicSchedule)]);
    }

    public function store(StoreClinicScheduleRequest $request): JsonResponse
    {
        $clinicSchedule = ClinicSchedule::create($request->validated());

        AuditLogger::record('create', 'clinic_schedule', (string) $clinicSchedule->getKey());

        return response()->json(
            ['data' => new ClinicScheduleResource($clinicSchedule)], 201
        );
    }

    public function update(StoreClinicScheduleRequest $request, string $id): JsonResponse
    {
        $clinicSchedule = ClinicSchedule::findOrFail($id);
        $clinicSchedule->update($request->validated());

        AuditLogger::record('update', 'clinic_schedule', $id);

        return response()->json(['data' => new ClinicScheduleResource($clinicSchedule)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $clinicSchedule = ClinicSchedule::findOrFail($id);

        $clinicSchedule->delete();

        AuditLogger::record('delete', 'clinic_schedule', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
