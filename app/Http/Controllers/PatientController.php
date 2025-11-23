<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Http\Requests\PatientRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class PatientController extends Controller
{
    public function index()
    {
        $patients = Patient::query()
            ->when(request('search'), function($query, $search) {
                $query->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('patient_code', 'like', "%{$search}%");
            })
            ->paginate(request('per_page', 15));

        return response()->json($patients);
    }

    public function store(PatientRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            
            $patient = Patient::create($request->validated());
            
            DB::commit();
            
            return response()->json([
                'message' => 'Patient created successfully',
                'data' => $patient
            ], Response::HTTP_CREATED);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'message' => 'Failed to create patient',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(Patient $patient): JsonResponse
    {
        return response()->json([
            'data' => $patient->loadMissing('visits')
        ]);
    }

    public function update(PatientRequest $request, Patient $patient): JsonResponse
    {
        try {
            DB::beginTransaction();
            
            $patient->update($request->validated());
            
            DB::commit();
            
            return response()->json([
                'message' => 'Patient updated successfully',
                'data' => $patient->fresh()
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'message' => 'Failed to update patient',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(Patient $patient): JsonResponse
    {
        try {
            DB::beginTransaction();
            
            $patient->delete();
            
            DB::commit();
            
            return response()->json([
                'message' => 'Patient deleted successfully'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'message' => 'Failed to delete patient',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
