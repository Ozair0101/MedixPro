<?php

namespace App\Http\Controllers\Pharmacy;

use App\Http\Controllers\Controller;
use App\Models\Medication;
use App\Models\MedicationBatch;
use App\Models\Prescription;
use App\Models\PrescriptionItem;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $hospitalId = (int) $request->header('X-Hospital-Id', 1);
        $today = Carbon::today();

        // Scope prescriptions to hospital and today
        $baseQuery = Prescription::query()->where('hospital_id', $hospitalId);

        $todayQuery = (clone $baseQuery)->whereDate('created_at', $today);

        $totalPrescriptionsToday = (clone $todayQuery)->count();
        $pendingCount = (clone $todayQuery)->where('status', 'Pending')->count();
        $completedCount = (clone $todayQuery)->where('status', 'Dispensed')->count();
        $partialCount = (clone $todayQuery)->where('status', 'Partial')->count();
        $rejectedCount = (clone $todayQuery)->where('status', 'Rejected')->count();

        // Expiring batches: within next 30 days, quantity > 0
        $expiringThreshold = Carbon::today()->addDays(30);
        $expiringBatchesCount = MedicationBatch::query()
            ->where('hospital_id', $hospitalId)
            ->whereDate('expiry_date', '<=', $expiringThreshold)
            ->whereDate('expiry_date', '>=', $today)
            ->where('quantity', '>', 0)
            ->count();

        // Low stock medications: sum of batch quantities < min_stock
        $lowStockCount = Medication::query()
            ->where('hospital_id', $hospitalId)
            ->where('min_stock', '>', 0)
            ->whereHas('batches', function ($q) use ($hospitalId) {
                $q->where('hospital_id', $hospitalId);
            })
            ->get()
            ->filter(function (Medication $medication) use ($hospitalId) {
                $totalQty = $medication->batches()
                    ->where('hospital_id', $hospitalId)
                    ->sum('quantity');

                return $totalQty < $medication->min_stock;
            })
            ->count();

        // Controlled substances alerts: any active prescription items for controlled meds
        $controlledMedicationIds = Medication::query()
            ->where('hospital_id', $hospitalId)
            ->where('controlled', true)
            ->pluck('id');

        $controlledAlertsCount = PrescriptionItem::query()
            ->where('hospital_id', $hospitalId)
            ->whereIn('medication_id', $controlledMedicationIds)
            ->whereIn('status', ['Pending', 'Partial'])
            ->count();

        return response()->json([
            'totalPrescriptionsToday' => $totalPrescriptionsToday,
            'pendingCount' => $pendingCount,
            'completedCount' => $completedCount,
            'partialCount' => $partialCount,
            'rejectedCount' => $rejectedCount,
            'expiringBatchesCount' => $expiringBatchesCount,
            'lowStockCount' => $lowStockCount,
            'controlledAlertsCount' => $controlledAlertsCount,
        ]);
    }
}
