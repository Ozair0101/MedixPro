<?php

namespace App\Http\Controllers\Pharmacy;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pharmacy\StoreMedicationRequest;
use App\Http\Requests\Pharmacy\UpdateMedicationRequest;
use App\Http\Resources\Pharmacy\MedicationResource;
use App\Models\Medication;
use Illuminate\Http\Request;

class MedicationController extends Controller
{
    public function index(Request $request)
    {
        $hospitalId = (int) $request->header('X-Hospital-Id', 1);

        $query = Medication::query()->forHospital($hospitalId);

        if ($search = $request->string('search')->toString()) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('generic_name', 'like', "%{$search}%");
            });
        }

        if (! is_null($request->input('controlled'))) {
            $query->where('controlled', filter_var($request->input('controlled'), FILTER_VALIDATE_BOOLEAN));
        }

        $sortBy = $request->get('sort_by', 'name');
        $sortDir = $request->get('sort_dir', 'asc');

        $query->orderBy($sortBy, $sortDir);

        $perPage = (int) $request->get('per_page', 20);

        return MedicationResource::collection($query->paginate($perPage));
    }

    public function store(StoreMedicationRequest $request)
    {
        $data = $request->validated();
        $data['hospital_id'] = (int) $request->header('X-Hospital-Id', 1);
        $data['created_by'] = optional($request->user())->id;

        $medication = Medication::create($data);

        return new MedicationResource($medication);
    }

    public function show(Request $request, int $id)
    {
        $hospitalId = (int) $request->header('X-Hospital-Id', 1);

        $medication = Medication::forHospital($hospitalId)->findOrFail($id);

        return new MedicationResource($medication);
    }

    public function update(UpdateMedicationRequest $request, int $id)
    {
        $hospitalId = (int) $request->header('X-Hospital-Id', 1);

        $medication = Medication::forHospital($hospitalId)->findOrFail($id);

        $medication->update($request->validated());

        return new MedicationResource($medication);
    }

    public function destroy(Request $request, int $id)
    {
        $hospitalId = (int) $request->header('X-Hospital-Id', 1);

        $medication = Medication::forHospital($hospitalId)->findOrFail($id);
        $medication->delete();

        return response()->json(['message' => 'Medication deleted']);
    }
}
