<?php

namespace App\Http\Controllers\Pharmacy;

use App\Http\Controllers\Controller;
use App\Models\Prescription;
use Illuminate\Http\Request;

class PrescriptionController extends Controller
{
    public function index(Request $request)
    {
        $hospitalId = (int) $request->header('X-Hospital-Id', 1);

        $query = Prescription::query()->where('hospital_id', $hospitalId);

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        if ($opIp = $request->query('type')) {
            $query->where('op_ip_type', $opIp);
        }

        if ($search = $request->query('search')) {
            $s = '%'.$search.'%';
            $query->where(function ($q) use ($s) {
                $q->where('id', 'like', $s)
                    ->orWhere('patient_id', 'like', $s)
                    ->orWhere('doctor_id', 'like', $s);
            });
        }

        $sortBy = $request->query('sort_by', 'created_at');
        $sortDir = $request->query('sort_dir', 'desc');

        $query->orderBy($sortBy, $sortDir);

        $perPage = (int) $request->query('per_page', 20);

        $paginator = $query->with('items')->paginate($perPage);

        return response()->json([
            'data' => $paginator->items(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
        ]);
    }

    public function show(Request $request, int $id)
    {
        $hospitalId = (int) $request->header('X-Hospital-Id', 1);

        $prescription = Prescription::query()
            ->where('hospital_id', $hospitalId)
            ->with(['items.medication'])
            ->findOrFail($id);

        return response()->json($prescription);
    }
}
