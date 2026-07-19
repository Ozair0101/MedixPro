<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAppointmentRequest;
use App\Http\Resources\AppointmentResource;
use App\Models\Appointment;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CRUD for `appointment`.
 */
class AppointmentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);

        // RLS scopes this to the caller's facility; no WHERE needed here.
        $rows = Appointment::query()->paginate($perPage);

        return response()->json([
            'data' => AppointmentResource::collection($rows->items()),
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
        $appointment = Appointment::findOrFail($id);

        return response()->json(['data' => new AppointmentResource($appointment)]);
    }

    public function store(StoreAppointmentRequest $request): JsonResponse
    {
        $appointment = Appointment::create($request->validated());

        AuditLogger::record('create', 'appointment', (string) $appointment->getKey());

        return response()->json(
            ['data' => new AppointmentResource($appointment)], 201
        );
    }

    public function update(StoreAppointmentRequest $request, string $id): JsonResponse
    {
        $appointment = Appointment::findOrFail($id);
        $appointment->update($request->validated());

        AuditLogger::record('update', 'appointment', $id);

        return response()->json(['data' => new AppointmentResource($appointment)]);
    }

    public function destroy(string $id): JsonResponse
    {
        $appointment = Appointment::findOrFail($id);

        $appointment->delete();

        AuditLogger::record('delete', 'appointment', $id);

        return response()->json(['message' => 'Deleted.']);
    }
}
