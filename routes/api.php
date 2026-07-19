<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DispenseController;
use App\Http\Controllers\EncounterController;
use App\Http\Controllers\PatientController;
use Illuminate\Support\Facades\Route;

// Authentication (public)
Route::post('auth/login', [AuthController::class, 'login']);
Route::get('sanctum/csrf-cookie', fn () => response()->json(['message' => 'CSRF cookie set']));

Route::middleware('auth:sanctum')->group(function () {
    Route::post('auth/logout', [AuthController::class, 'logout']);
    Route::get('auth/me', [AuthController::class, 'me']);
    Route::post('auth/refresh', [AuthController::class, 'refresh']);
});

/*
|--------------------------------------------------------------------------
| v1
|--------------------------------------------------------------------------
| Every route below requires authentication AND the facility middleware, which
| sets the PostgreSQL session variable that Row-Level Security reads. Without
| it RLS fails closed and queries return nothing — which is the intended
| behaviour for an unattributed request (ADR-002).
*/
Route::prefix('v1')->middleware(['auth:sanctum', 'facility'])->group(function () {

    // ---- Patients --------------------------------------------------------
    //
    // ORDER MATTERS: literal paths must precede apiResource, or the resource's
    // `show` route (`patients/{patient}`) swallows them.
    Route::get('patients/search/{term}', [PatientController::class, 'search']);
    Route::post('patients/check-duplicates', [PatientController::class, 'checkDuplicates']);
    Route::get('patients/duplicates', [PatientController::class, 'duplicateQueue']);
    Route::post('patients/merge', [PatientController::class, 'mergePatients']);
    Route::post('patients/unmerge', [PatientController::class, 'unmergePatients']);
    Route::post('patients/duplicates/{candidate}/reject',
        [PatientController::class, 'rejectDuplicate']);
    Route::apiResource('patients', PatientController::class);

    // ---- Encounters ------------------------------------------------------
    Route::post('encounters', [EncounterController::class, 'store']);
    Route::get('encounters/{encounter}', [EncounterController::class, 'show']);
    Route::post('encounters/{encounter}/close', [EncounterController::class, 'close']);
    Route::get('patients/{patient}/encounters', [EncounterController::class, 'forPatient']);

    // Vitals hang off an encounter: a measurement with no clinical context
    // cannot be interpreted later.
    Route::post('encounters/{encounter}/vitals', [EncounterController::class, 'recordVitals']);

    // ---- Dispensing ------------------------------------------------------
    //
    // Two steps by design. Reservation and hand-over are minutes apart in
    // practice, and a database lock cannot be held across a human
    // conversation — so stock is held, then consumed.
    Route::post('dispenses', [DispenseController::class, 'store']);
    Route::post('dispenses/{dispense}/complete', [DispenseController::class, 'complete']);
    Route::post('dispenses/{dispense}/cancel', [DispenseController::class, 'cancel']);
    Route::get('dispenses/{dispense}', [DispenseController::class, 'show']);

    // ---- Stock -----------------------------------------------------------
    Route::get('stock/balance', [DispenseController::class, 'stockBalance']);
    Route::get('stock/expiring', [DispenseController::class, 'expiring']);
    // Should always return an empty list. Anything here means a write path
    // bypassed the ledger service and the shelf no longer matches the screen.
    Route::get('stock/reconciliation', [DispenseController::class, 'reconciliation']);
});

/*
| REMOVED IN PHASE 2
|--------------------------------------------------------------------------
| The previous `pharmacy/*` and `v1/{categories,suppliers,purchases}` routes
| were deleted along with their controllers and models. They targeted the
| pre-migration MySQL tables (medications, medication_batches, prescriptions,
| categories, suppliers, purchases), none of which exist in the PostgreSQL
| schema — so every one of them errored at runtime.
|
| Their replacements are the dispensing and stock routes above, built on the
| append-only stock ledger. Procurement (purchase requisition → order → goods
| receipt) is scheduled for the remainder of Phase 2; the schema for it is
| already in database/schema/60-pharmacy-supply.sql.
*/
