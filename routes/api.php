<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\Pharmacy\BatchController;
use App\Http\Controllers\Pharmacy\DashboardController;
use App\Http\Controllers\Pharmacy\DispenseController;
use App\Http\Controllers\Pharmacy\MedicationController;
use App\Http\Controllers\Pharmacy\PrescriptionController;
use App\Http\Controllers\Pharmacy\ReportController;
use App\Http\Controllers\Pharmacy\StockAdjustmentController;
use App\Models\Category;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

// Authentication routes (public)
Route::post('auth/login', [AuthController::class, 'login']);
Route::get('sanctum/csrf-cookie', function () {
    return response()->json(['message' => 'CSRF cookie set']);
});

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('auth/logout', [AuthController::class, 'logout']);
    Route::get('auth/me', [AuthController::class, 'me']);
    Route::post('auth/refresh', [AuthController::class, 'refresh']);
});

// SECURITY: this group was previously unauthenticated. Anyone who could reach
// the API could read prescriptions, adjust stock and record dispenses.
Route::prefix('pharmacy')
    ->name('pharmacy.')
    ->middleware(['auth:sanctum', 'facility'])
    ->group(function () {
        // Dashboard
        Route::get('dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');

        // Prescriptions
        Route::get('prescriptions', [PrescriptionController::class, 'index'])
            ->name('prescriptions.index');
        Route::get('prescriptions/{id}', [PrescriptionController::class, 'show'])
            ->name('prescriptions.show');
        Route::post('prescriptions/{id}/dispense', [DispenseController::class, 'store'])
            ->name('prescriptions.dispense');

        // Medications
        Route::get('medications', [MedicationController::class, 'index'])
            ->name('medications.index');
        Route::post('medications', [MedicationController::class, 'store'])
            ->name('medications.store');
        Route::get('medications/{id}', [MedicationController::class, 'show'])
            ->name('medications.show');
        Route::put('medications/{id}', [MedicationController::class, 'update'])
            ->name('medications.update');
        Route::delete('medications/{id}', [MedicationController::class, 'destroy'])
            ->name('medications.destroy');

        // Batches
        Route::get('batches', [BatchController::class, 'index'])
            ->name('batches.index');
        Route::post('batches', [BatchController::class, 'store'])
            ->name('batches.store');
        Route::put('batches/{id}', [BatchController::class, 'update'])
            ->name('batches.update');

        // Stock adjustments
        Route::post('stock/adjust', [StockAdjustmentController::class, 'store'])
            ->name('stock.adjust');

        // Reports
        Route::get('reports/expiry', [ReportController::class, 'expiry'])
            ->name('reports.expiry');
        Route::get('reports/stock', [ReportController::class, 'stock'])
            ->name('reports.stock');
        Route::get('reports/dispensing', [ReportController::class, 'dispensing'])
            ->name('reports.dispensing');
    });

// Temporary inventory routes (simple implementations) so frontend can work.
//
// TODO(Phase 2): these closures are scheduled for extraction into controllers
// and services. The purchase-order creation in particular writes across two
// tables in a transaction and belongs in a service, not a route file.
Route::prefix('v1')->middleware(['auth:sanctum', 'facility'])->group(function () {
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
            'supplier_id' => ['required', 'integer'],
            'order_date' => ['required', 'date'],
            'expected_date' => ['nullable', 'date'],
            'currency' => ['required', 'string'],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.medication_id' => ['required', 'integer'],
            'items.*.quantity_ordered' => ['required', 'integer', 'min:1'],
            'items.*.unit_cost' => ['required', 'numeric', 'min:0'],
            'items.*.tax' => ['nullable', 'numeric', 'min:0'],
            'items.*.discount' => ['nullable', 'numeric', 'min:0'],
        ]);

        return DB::transaction(function () use ($validated, $hospitalId, $request) {
            $purchase = Purchase::create([
                'supplier_id' => $validated['supplier_id'],
                'reference_no' => 'PO-'.now()->format('Ymd-His'),
                'status' => 'draft',
                'order_date' => $validated['order_date'],
                'expected_date' => $validated['expected_date'] ?? null,
                'total_amount' => 0,
                'currency' => $validated['currency'],
                'hospital_id' => $hospitalId,
                'notes' => $validated['notes'] ?? null,
                'created_by' => optional($request->user())->id,
            ]);

            $total = 0;
            foreach ($validated['items'] as $item) {
                $line = $item['quantity_ordered'] * $item['unit_cost']
                    + ($item['tax'] ?? 0)
                    - ($item['discount'] ?? 0);

                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'medication_id' => $item['medication_id'],
                    'category_id' => null,
                    'batch_no' => null,
                    'expiry_date' => null,
                    'unit_cost' => $item['unit_cost'],
                    'quantity_ordered' => $item['quantity_ordered'],
                    'quantity_received' => 0,
                    'tax' => $item['tax'] ?? 0,
                    'discount' => $item['discount'] ?? 0,
                    'line_total' => $line,
                    'hospital_id' => $hospitalId,
                ]);

                $total += $line;
            }

            $purchase->update(['total_amount' => $total]);

            return response()->json($purchase->fresh(), 201);
        });
    });

    // Patient Management Routes
    //
    // ORDER MATTERS: literal paths must be registered BEFORE apiResource,
    // otherwise the resource's `show` route (`patients/{patient}`) matches
    // first and every literal below becomes unreachable.
    Route::get('patients/search/{term}', [PatientController::class, 'search']);

    // Search-before-create support: score a prospective registration without
    // writing anything, so the UI can warn before the clerk commits.
    Route::post('patients/check-duplicates', [PatientController::class, 'checkDuplicates']);

    // Duplicate review queue. Merging is never automatic — a false positive
    // fuses two people's medical histories.
    Route::get('patients/duplicates', [PatientController::class, 'duplicateQueue']);
    Route::post('patients/merge', [PatientController::class, 'mergePatients']);
    Route::post('patients/unmerge', [PatientController::class, 'unmergePatients']);
    Route::post('patients/duplicates/{candidate}/reject',
        [PatientController::class, 'rejectDuplicate']);

    Route::apiResource('patients', PatientController::class);
});
