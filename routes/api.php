<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\Purchase;
use App\Models\PurchaseItem;

Route::prefix('pharmacy')
    ->name('pharmacy.')
    ->group(function () {
        // Dashboard
        Route::get('dashboard', [\App\Http\Controllers\Pharmacy\DashboardController::class, 'index'])
            ->name('dashboard');

        // Prescriptions
        Route::get('prescriptions', [\App\Http\Controllers\Pharmacy\PrescriptionController::class, 'index'])
            ->name('prescriptions.index');
        Route::get('prescriptions/{id}', [\App\Http\Controllers\Pharmacy\PrescriptionController::class, 'show'])
            ->name('prescriptions.show');
        Route::post('prescriptions/{id}/dispense', [\App\Http\Controllers\Pharmacy\DispenseController::class, 'store'])
            ->name('prescriptions.dispense');

        // Medications
        Route::get('medications', [\App\Http\Controllers\Pharmacy\MedicationController::class, 'index'])
            ->name('medications.index');
        Route::post('medications', [\App\Http\Controllers\Pharmacy\MedicationController::class, 'store'])
            ->name('medications.store');
        Route::get('medications/{id}', [\App\Http\Controllers\Pharmacy\MedicationController::class, 'show'])
            ->name('medications.show');
        Route::put('medications/{id}', [\App\Http\Controllers\Pharmacy\MedicationController::class, 'update'])
            ->name('medications.update');
        Route::delete('medications/{id}', [\App\Http\Controllers\Pharmacy\MedicationController::class, 'destroy'])
            ->name('medications.destroy');

        // Batches
        Route::get('batches', [\App\Http\Controllers\Pharmacy\BatchController::class, 'index'])
            ->name('batches.index');
        Route::post('batches', [\App\Http\Controllers\Pharmacy\BatchController::class, 'store'])
            ->name('batches.store');
        Route::put('batches/{id}', [\App\Http\Controllers\Pharmacy\BatchController::class, 'update'])
            ->name('batches.update');

        // Stock adjustments
        Route::post('stock/adjust', [\App\Http\Controllers\Pharmacy\StockAdjustmentController::class, 'store'])
            ->name('stock.adjust');

        // Reports
        Route::get('reports/expiry', [\App\Http\Controllers\Pharmacy\ReportController::class, 'expiry'])
            ->name('reports.expiry');
        Route::get('reports/stock', [\App\Http\Controllers\Pharmacy\ReportController::class, 'stock'])
            ->name('reports.stock');
        Route::get('reports/dispensing', [\App\Http\Controllers\Pharmacy\ReportController::class, 'dispensing'])
            ->name('reports.dispensing');
    });

// Temporary inventory routes (simple implementations) so frontend can work
Route::prefix('v1')->group(function () {
    // Categories: basic index + store using Category model
    Route::get('categories', function (Request $request) {
        $perPage = (int) $request->query('per_page', 15);
        $search = (string) $request->query('search', '');
        $hospitalId = (int) $request->header('X-Hospital-Id', 1);

        $query = Category::query()->where('hospital_id', $hospitalId);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $paginator = $query->orderBy('name')->paginate($perPage);

        return response()->json([
            'data' => $paginator->items(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
        ]);
    });

    Route::post('categories', function (Request $request) {
        $hospitalId = (int) $request->header('X-Hospital-Id', 1);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:191'],
            'code' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $category = Category::create([
            'name' => $validated['name'],
            'code' => $validated['code'] ?? null,
            'description' => $validated['description'] ?? null,
            'hospital_id' => $hospitalId,
            'created_by' => optional($request->user())->id,
        ]);

        return response()->json($category, 201);
    });

    // Suppliers: basic index + store using Supplier model
    Route::get('suppliers', function (Request $request) {
        $perPage = (int) $request->query('per_page', 15);
        $search = (string) $request->query('search', '');
        $hospitalId = (int) $request->header('X-Hospital-Id', 1);

        $query = Supplier::query()->where('hospital_id', $hospitalId);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('contact_person', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $paginator = $query->orderBy('name')->paginate($perPage);

        return response()->json([
            'data' => $paginator->items(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
        ]);
    });

    Route::post('suppliers', function (Request $request) {
        $hospitalId = (int) $request->header('X-Hospital-Id', 1);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:191'],
            'contact_person' => ['nullable', 'string', 'max:191'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:191'],
            'address' => ['nullable', 'string', 'max:255'],
            'tax_id' => ['nullable', 'string', 'max:100'],
            'payment_terms' => ['nullable', 'string', 'max:191'],
            'active' => ['nullable', 'boolean'],
        ]);

        $supplier = Supplier::create([
            'name' => $validated['name'],
            'contact_person' => $validated['contact_person'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'email' => $validated['email'] ?? null,
            'address' => $validated['address'] ?? null,
            'tax_id' => $validated['tax_id'] ?? null,
            'payment_terms' => $validated['payment_terms'] ?? null,
            'active' => $validated['active'] ?? true,
            'hospital_id' => $hospitalId,
            'created_by' => optional($request->user())->id,
        ]);

        return response()->json($supplier, 201);
    });

    // Purchases: index
    Route::get('purchases', function (Request $request) {
        $perPage = (int) $request->query('per_page', 15);
        $status = (string) $request->query('status', '');
        $hospitalId = (int) $request->header('X-Hospital-Id', 1);

        $query = Purchase::query()->where('hospital_id', $hospitalId);

        if ($status !== '') {
            $query->where('status', $status);
        }

        $paginator = $query->orderByDesc('order_date')->paginate($perPage);

        return response()->json([
            'data' => $paginator->items(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->PerPage(),
                'total' => $paginator->total(),
            ],
        ]);
    });

    // Purchases: create draft
    Route::post('purchases', function (Request $request) {
        $hospitalId = (int) $request->header('X-Hospital-Id', 1);

        $validated = $request->validate([
            'supplier_id'               => ['required', 'integer'],
            'order_date'                => ['required', 'date'],
            'expected_date'             => ['nullable', 'date'],
            'currency'                  => ['required', 'string'],
            'notes'                     => ['nullable', 'string'],
            'items'                     => ['required', 'array', 'min:1'],
            'items.*.medication_id'    => ['required', 'integer'],
            'items.*.quantity_ordered' => ['required', 'integer', 'min:1'],
            'items.*.unit_cost'        => ['required', 'numeric', 'min:0'],
            'items.*.tax'              => ['nullable', 'numeric', 'min:0'],
            'items.*.discount'         => ['nullable', 'numeric', 'min:0'],
        ]);

        return \DB::transaction(function () use ($validated, $hospitalId, $request) {
            $purchase = Purchase::create([
                'supplier_id'  => $validated['supplier_id'],
                'reference_no' => 'PO-' . now()->format('Ymd-His'),
                'status'       => 'draft',
                'order_date'   => $validated['order_date'],
                'expected_date'=> $validated['expected_date'] ?? null,
                'total_amount' => 0,
                'currency'     => $validated['currency'],
                'hospital_id'  => $hospitalId,
                'notes'        => $validated['notes'] ?? null,
                'created_by'   => optional($request->user())->id,
            ]);

            $total = 0;
            foreach ($validated['items'] as $item) {
                $line = $item['quantity_ordered'] * $item['unit_cost']
                    + ($item['tax'] ?? 0)
                    - ($item['discount'] ?? 0);

                PurchaseItem::create([
                    'purchase_id'       => $purchase->id,
                    'medication_id'     => $item['medication_id'],
                    'category_id'       => null,
                    'batch_no'          => null,
                    'expiry_date'       => null,
                    'unit_cost'         => $item['unit_cost'],
                    'quantity_ordered'  => $item['quantity_ordered'],
                    'quantity_received' => 0,
                    'tax'               => $item['tax'] ?? 0,
                    'discount'          => $item['discount'] ?? 0,
                    'line_total'        => $line,
                    'hospital_id'       => $hospitalId,
                ]);

                $total += $line;
            }

            $purchase->update(['total_amount' => $total]);

            return response()->json($purchase->fresh(), 201);
        });
    });
});
