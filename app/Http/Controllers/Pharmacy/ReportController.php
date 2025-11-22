<?php

namespace App\Http\Controllers\Pharmacy;

use App\Http\Controllers\Controller;
use App\Models\Dispense;
use App\Models\Medication;
use App\Models\MedicationBatch;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function expiry(Request $request)
    {
        $hospitalId = (int) $request->header('X-Hospital-Id', 1);
        $days = (int) $request->query('days', 60);

        $today = Carbon::today();
        $threshold = Carbon::today()->addDays($days);

        $batches = MedicationBatch::query()
            ->where('hospital_id', $hospitalId)
            ->whereBetween('expiry_date', [$today, $threshold])
            ->where('quantity', '>', 0)
            ->with('medication')
            ->orderBy('expiry_date')
            ->get();

        $data = $batches->map(function (MedicationBatch $batch) {
            return [
                'batch' => $batch,
                'medication' => $batch->medication,
                'daysToExpiry' => Carbon::today()->diffInDays(Carbon::parse($batch->expiry_date), false),
            ];
        });

        return response()->json($data);
    }

    public function stock(Request $request)
    {
        $hospitalId = (int) $request->header('X-Hospital-Id', 1);

        $medications = Medication::query()
            ->where('hospital_id', $hospitalId)
            ->with(['batches' => function ($q) use ($hospitalId) {
                $q->where('hospital_id', $hospitalId);
            }])
            ->get();

        $data = $medications->map(function (Medication $medication) {
            $totalQty = $medication->batches->sum('quantity');

            return [
                'medication' => $medication,
                'totalQuantity' => $totalQty,
                'isLowStock' => $totalQty < $medication->min_stock,
            ];
        });

        return response()->json($data);
    }

    public function dispensing(Request $request)
    {
        $hospitalId = (int) $request->header('X-Hospital-Id', 1);
        $from = Carbon::parse($request->query('from', Carbon::today()->toDateString()));
        $to = Carbon::parse($request->query('to', Carbon::today()->toDateString()))->endOfDay();

        $rows = Dispense::query()
            ->where('hospital_id', $hospitalId)
            ->whereBetween('dispense_time', [$from, $to])
            ->with('medication')
            ->get()
            ->groupBy('medication_id');

        $data = $rows->map(function ($group) {
            /** @var \Illuminate\Support\Collection $group */
            /** @var Dispense $first */
            $first = $group->first();

            $totalDispensed = $group->sum('quantity');
            $medication = $first->medication;

            return [
                'medication' => $medication,
                'totalDispensed' => $totalDispensed,
                'isControlled' => (bool) optional($medication)->controlled,
            ];
        })->values();

        return response()->json($data);
    }
}
