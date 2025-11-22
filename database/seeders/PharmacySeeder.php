<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Medication;
use App\Models\MedicationBatch;
use App\Models\Prescription;
use App\Models\PrescriptionItem;

class PharmacySeeder extends Seeder
{
    public function run(): void
    {
        $hospitalId = 1;

        // 100 medications
        $medications = collect(range(1, 100))->map(function (int $i) use ($hospitalId) {
            return Medication::create([
                'name'        => "Demo Medication {$i}",
                'form'        => 'Tablet',
                'strength'    => '500mg',
                'min_stock'   => rand(50, 200),
                'controlled'  => false,
                'hospital_id' => $hospitalId,
            ]);
        });

        // 1 batch per medication
        foreach ($medications as $med) {
            MedicationBatch::create([
                'medication_id' => $med->id,
                'batch_no'      => 'B-' . $med->id . '-01',
                'expiry_date'   => now()->addMonths(rand(3, 18)),
                'quantity'      => rand(100, 300),
                'cost_price'    => rand(5, 20),
                'selling_price' => rand(10, 40),
                'hospital_id'   => $hospitalId,
            ]);
        }

        // 100 prescriptions with 1–3 items each
        foreach (range(1, 100) as $pIndex) {
            $prescription = Prescription::create([
                'patient_id'  => $pIndex,
                'doctor_id'   => 1,
                'op_ip_type'  => 'OPD',
                'status'      => 'Pending',
                'hospital_id' => $hospitalId,
            ]);

            foreach ($medications->random(rand(1, 3)) as $med) {
                PrescriptionItem::create([
                    'prescription_id'     => $prescription->id,
                    'medication_id'       => $med->id,
                    'dose'                => '1 tab',
                    'frequency'           => 'TID',
                    'duration'            => rand(3, 7) . ' days',
                    'quantity_prescribed' => 15,
                    'quantity_dispensed'  => 0,
                    'status'              => 'Pending',
                    'hospital_id'         => $hospitalId,
                ]);
            }
        }
    }
}
