<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\Purchase;
use App\Models\PurchaseItem;

class InventorySeeder extends Seeder
{
    public function run(): void
    {
        $hospitalId = 1;

        // 100 categories
        $categories = collect(range(1, 100))->map(function (int $i) use ($hospitalId) {
            return Category::create([
                'name'        => "Category {$i}",
                'code'        => "CAT{$i}",
                'hospital_id' => $hospitalId,
            ]);
        });

        // 100 suppliers
        $suppliers = collect(range(1, 100))->map(function (int $i) use ($hospitalId) {
            return Supplier::create([
                'name'        => "Supplier {$i}",
                'phone'       => '07' . str_pad((string) $i, 8, '0', STR_PAD_LEFT),
                'active'      => $i % 5 !== 0,
                'hospital_id' => $hospitalId,
            ]);
        });

        // 100 purchases (draft) with 1–3 items each
        foreach (range(1, 100) as $i) {
            $supplier = $suppliers->random();
            $category = $categories->random();

            $purchase = Purchase::create([
                'supplier_id'   => $supplier->id,
                'reference_no'  => sprintf('PO-%03d', $i),
                'status'        => 'draft',
                'order_date'    => now()->subDays(rand(0, 30))->toDateString(),
                'expected_date' => now()->addDays(rand(1, 14))->toDateString(),
                'total_amount'  => 0,
                'currency'      => 'AFN',
                'hospital_id'   => $hospitalId,
            ]);

            $lineTotal = 0;

            foreach (range(1, rand(1, 3)) as $row) {
                $qty   = rand(10, 50);
                $price = rand(5, 20);
                $line  = $qty * $price;

                PurchaseItem::create([
                    'purchase_id'       => $purchase->id,
                    'medication_id'     => 1,
                    'category_id'       => $category->id,
                    'batch_no'          => null,
                    'expiry_date'       => null,
                    'unit_cost'         => $price,
                    'quantity_ordered'  => $qty,
                    'quantity_received' => 0,
                    'tax'               => 0,
                    'discount'          => 0,
                    'line_total'        => $line,
                    'hospital_id'       => $hospitalId,
                ]);

                $lineTotal += $line;
            }

            $purchase->update(['total_amount' => $lineTotal]);
        }
    }
}
