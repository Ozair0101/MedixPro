<?php

/*
|--------------------------------------------------------------------------
| Generated API routes — DO NOT EDIT
|--------------------------------------------------------------------------
|
| Regenerate with: php artisan hmis:scaffold-routes
|
| Hand-written endpoints live in routes/api.php and are excluded here, so
| regeneration never overwrites domain logic with plain CRUD.
|
| Every route below already sits inside the v1 group in api.php, which
| applies auth:sanctum and the facility middleware. The facility middleware
| sets the PostgreSQL session variable that Row-Level Security reads —
| without it queries return nothing, which is the correct behaviour for an
| unattributed request.
|
| Generated 2026-07-19, 199 resources.
*/

use Illuminate\Support\Facades\Route;

Route::apiResource('account-coverages', \App\Http\Controllers\Api\AccountCoverageController::class);
Route::apiResource('accounting-periods', \App\Http\Controllers\Api\AccountingPeriodController::class);
Route::apiResource('actual-packs', \App\Http\Controllers\Api\ActualPackController::class);
Route::apiResource('actual-products', \App\Http\Controllers\Api\ActualProductController::class);
Route::apiResource('adjustments', \App\Http\Controllers\Api\AdjustmentController::class);
Route::apiResource('adjustment-reasons', \App\Http\Controllers\Api\AdjustmentReasonController::class);
Route::apiResource('admissions', \App\Http\Controllers\Api\AdmissionController::class);
// AdtEvent: append-only at the database level.
Route::get('adt-events', [\App\Http\Controllers\Api\AdtEventController::class, 'index']);
Route::get('adt-events/{id}', [\App\Http\Controllers\Api\AdtEventController::class, 'show']);

Route::apiResource('aging-buckets', \App\Http\Controllers\Api\AgingBucketController::class);
Route::apiResource('allergies', \App\Http\Controllers\Api\AllergyController::class);
Route::apiResource('allergy-manifestations', \App\Http\Controllers\Api\AllergyManifestationController::class);
Route::apiResource('allergy-reactions', \App\Http\Controllers\Api\AllergyReactionController::class);
Route::apiResource('ambulances', \App\Http\Controllers\Api\AmbulanceController::class);
Route::apiResource('ambulance-trips', \App\Http\Controllers\Api\AmbulanceTripController::class);
Route::apiResource('app-settings', \App\Http\Controllers\Api\AppSettingController::class);
Route::apiResource('app-users', \App\Http\Controllers\Api\AppUserController::class);
Route::apiResource('appointments', \App\Http\Controllers\Api\AppointmentController::class);
Route::apiResource('ar-aging-snapshots', \App\Http\Controllers\Api\ArAgingSnapshotController::class);
Route::apiResource('assets', \App\Http\Controllers\Api\AssetController::class);
Route::apiResource('asset-maintenances', \App\Http\Controllers\Api\AssetMaintenanceController::class);
Route::apiResource('attachments', \App\Http\Controllers\Api\AttachmentController::class);
Route::apiResource('attendances', \App\Http\Controllers\Api\AttendanceController::class);
Route::apiResource('barcode-scans', \App\Http\Controllers\Api\BarcodeScanController::class);
Route::apiResource('beds', \App\Http\Controllers\Api\BedController::class);
Route::apiResource('bed-occupancies', \App\Http\Controllers\Api\BedOccupancyController::class);
Route::apiResource('bed-types', \App\Http\Controllers\Api\BedTypeController::class);
Route::apiResource('bed-unavailabilities', \App\Http\Controllers\Api\BedUnavailabilityController::class);
Route::apiResource('billable-items', \App\Http\Controllers\Api\BillableItemController::class);
Route::apiResource('billable-item-codes', \App\Http\Controllers\Api\BillableItemCodeController::class);
Route::apiResource('blood-crossmatches', \App\Http\Controllers\Api\BloodCrossmatchController::class);
Route::apiResource('blood-donations', \App\Http\Controllers\Api\BloodDonationController::class);
Route::apiResource('blood-donors', \App\Http\Controllers\Api\BloodDonorController::class);
Route::apiResource('blood-transfusions', \App\Http\Controllers\Api\BloodTransfusionController::class);
Route::apiResource('blood-units', \App\Http\Controllers\Api\BloodUnitController::class);
Route::apiResource('cash-points', \App\Http\Controllers\Api\CashPointController::class);
Route::apiResource('cashier-shifts', \App\Http\Controllers\Api\CashierShiftController::class);
Route::apiResource('census-snapshots', \App\Http\Controllers\Api\CensusSnapshotController::class);
Route::apiResource('charge-items', \App\Http\Controllers\Api\ChargeItemController::class);
Route::apiResource('clinic-schedules', \App\Http\Controllers\Api\ClinicScheduleController::class);
Route::apiResource('clinical-notes', \App\Http\Controllers\Api\ClinicalNoteController::class);
Route::apiResource('clinical-orders', \App\Http\Controllers\Api\ClinicalOrderController::class);
Route::apiResource('clinical-procedures', \App\Http\Controllers\Api\ClinicalProcedureController::class);
Route::apiResource('code-systems', \App\Http\Controllers\Api\CodeSystemController::class);
Route::apiResource('concepts', \App\Http\Controllers\Api\ConceptController::class);
Route::apiResource('concept-answers', \App\Http\Controllers\Api\ConceptAnswerController::class);
Route::apiResource('concept-classes', \App\Http\Controllers\Api\ConceptClassController::class);
Route::apiResource('concept-datatypes', \App\Http\Controllers\Api\ConceptDatatypeController::class);
Route::apiResource('concept-names', \App\Http\Controllers\Api\ConceptNameController::class);
Route::apiResource('concept-numerics', \App\Http\Controllers\Api\ConceptNumericController::class);
Route::apiResource('concept-reference-maps', \App\Http\Controllers\Api\ConceptReferenceMapController::class);
Route::apiResource('concept-reference-ranges', \App\Http\Controllers\Api\ConceptReferenceRangeController::class);
Route::apiResource('concept-reference-terms', \App\Http\Controllers\Api\ConceptReferenceTermController::class);
Route::apiResource('concept-set-members', \App\Http\Controllers\Api\ConceptSetMemberController::class);
Route::apiResource('conditions', \App\Http\Controllers\Api\ConditionController::class);
Route::apiResource('consents', \App\Http\Controllers\Api\ConsentController::class);
Route::apiResource('coverages', \App\Http\Controllers\Api\CoverageController::class);
Route::apiResource('credit-notes', \App\Http\Controllers\Api\CreditNoteController::class);
Route::apiResource('data-exports', \App\Http\Controllers\Api\DataExportController::class);
Route::apiResource('dews-notifications', \App\Http\Controllers\Api\DewsNotificationController::class);
Route::apiResource('dhis2-exports', \App\Http\Controllers\Api\Dhis2ExportController::class);
Route::apiResource('dhis2-mappings', \App\Http\Controllers\Api\Dhis2MappingController::class);
Route::apiResource('diagnostic-orders', \App\Http\Controllers\Api\DiagnosticOrderController::class);
Route::apiResource('diet-orders', \App\Http\Controllers\Api\DietOrderController::class);
Route::apiResource('dispense-items', \App\Http\Controllers\Api\DispenseItemController::class);
Route::apiResource('dose-forms', \App\Http\Controllers\Api\DoseFormController::class);
Route::apiResource('drug-administrations', \App\Http\Controllers\Api\DrugAdministrationController::class);
Route::apiResource('drug-orders', \App\Http\Controllers\Api\DrugOrderController::class);
// ElectronicSignature: append-only at the database level.
Route::get('electronic-signatures', [\App\Http\Controllers\Api\ElectronicSignatureController::class, 'index']);
Route::get('electronic-signatures/{id}', [\App\Http\Controllers\Api\ElectronicSignatureController::class, 'show']);

Route::apiResource('employees', \App\Http\Controllers\Api\EmployeeController::class);
Route::apiResource('employee-credentials', \App\Http\Controllers\Api\EmployeeCredentialController::class);
Route::apiResource('employment-contracts', \App\Http\Controllers\Api\EmploymentContractController::class);
Route::apiResource('encounter-diagnosis', \App\Http\Controllers\Api\EncounterDiagnosiController::class);
Route::apiResource('encounter-participants', \App\Http\Controllers\Api\EncounterParticipantController::class);
Route::apiResource('facilities', \App\Http\Controllers\Api\FacilityController::class);
Route::apiResource('field-restrictions', \App\Http\Controllers\Api\FieldRestrictionController::class);
Route::apiResource('fiscal-years', \App\Http\Controllers\Api\FiscalYearController::class);
// FxRate: append-only at the database level.
Route::get('fx-rates', [\App\Http\Controllers\Api\FxRateController::class, 'index']);
Route::get('fx-rates/{id}', [\App\Http\Controllers\Api\FxRateController::class, 'show']);

Route::apiResource('geo-aliases', \App\Http\Controllers\Api\GeoAliasController::class);
Route::apiResource('geo-districts', \App\Http\Controllers\Api\GeoDistrictController::class);
Route::apiResource('geo-provinces', \App\Http\Controllers\Api\GeoProvinceController::class);
Route::apiResource('geo-village-suggestions', \App\Http\Controllers\Api\GeoVillageSuggestionController::class);
Route::apiResource('gl-accounts', \App\Http\Controllers\Api\GlAccountController::class);
Route::apiResource('goods-receipts', \App\Http\Controllers\Api\GoodsReceiptController::class);
Route::apiResource('goods-receipt-lines', \App\Http\Controllers\Api\GoodsReceiptLineController::class);
Route::apiResource('hand-hygiene-audits', \App\Http\Controllers\Api\HandHygieneAuditController::class);
Route::apiResource('housekeeping-tasks', \App\Http\Controllers\Api\HousekeepingTaskController::class);
Route::apiResource('identifier-types', \App\Http\Controllers\Api\IdentifierTypeController::class);
Route::apiResource('imaging-modalities', \App\Http\Controllers\Api\ImagingModalityController::class);
Route::apiResource('imaging-reports', \App\Http\Controllers\Api\ImagingReportController::class);
Route::apiResource('imaging-studies', \App\Http\Controllers\Api\ImagingStudyController::class);
Route::apiResource('incidents', \App\Http\Controllers\Api\IncidentController::class);
Route::apiResource('infection-surveillances', \App\Http\Controllers\Api\InfectionSurveillanceController::class);
Route::apiResource('invoices', \App\Http\Controllers\Api\InvoiceController::class);
Route::apiResource('invoice-lines', \App\Http\Controllers\Api\InvoiceLineController::class);
Route::apiResource('item-uom-conversions', \App\Http\Controllers\Api\ItemUomConversionController::class);
Route::apiResource('job-grades', \App\Http\Controllers\Api\JobGradeController::class);
// JournalEntry: append-only at the database level.
Route::get('journal-entries', [\App\Http\Controllers\Api\JournalEntryController::class, 'index']);
Route::get('journal-entries/{id}', [\App\Http\Controllers\Api\JournalEntryController::class, 'show']);

Route::apiResource('lab-analysis', \App\Http\Controllers\Api\LabAnalysiController::class);
Route::apiResource('lab-critical-notifications', \App\Http\Controllers\Api\LabCriticalNotificationController::class);
Route::apiResource('lab-panels', \App\Http\Controllers\Api\LabPanelController::class);
Route::apiResource('lab-panel-items', \App\Http\Controllers\Api\LabPanelItemController::class);
Route::apiResource('lab-qc-runs', \App\Http\Controllers\Api\LabQcRunController::class);
Route::apiResource('lab-reflex-rules', \App\Http\Controllers\Api\LabReflexRuleController::class);
Route::apiResource('lab-reports', \App\Http\Controllers\Api\LabReportController::class);
Route::apiResource('lab-results', \App\Http\Controllers\Api\LabResultController::class);
Route::apiResource('lab-samples', \App\Http\Controllers\Api\LabSampleController::class);
Route::apiResource('lab-sample-items', \App\Http\Controllers\Api\LabSampleItemController::class);
Route::apiResource('lab-sections', \App\Http\Controllers\Api\LabSectionController::class);
Route::apiResource('lab-tests', \App\Http\Controllers\Api\LabTestController::class);
Route::apiResource('lab-test-result-options', \App\Http\Controllers\Api\LabTestResultOptionController::class);
Route::apiResource('leave-requests', \App\Http\Controllers\Api\LeaveRequestController::class);
Route::apiResource('leave-types', \App\Http\Controllers\Api\LeaveTypeController::class);
Route::apiResource('linen-transactions', \App\Http\Controllers\Api\LinenTransactionController::class);
Route::apiResource('locations', \App\Http\Controllers\Api\LocationController::class);
Route::apiResource('manufacturers', \App\Http\Controllers\Api\ManufacturerController::class);
Route::apiResource('mar-slots', \App\Http\Controllers\Api\MarSlotController::class);
Route::apiResource('meal-services', \App\Http\Controllers\Api\MealServiceController::class);
Route::apiResource('medical-wastes', \App\Http\Controllers\Api\MedicalWasteController::class);
Route::apiResource('moph-cases', \App\Http\Controllers\Api\MophCaseController::class);
Route::apiResource('moph-case-definitions', \App\Http\Controllers\Api\MophCaseDefinitionController::class);
Route::apiResource('moph-condition-maps', \App\Http\Controllers\Api\MophConditionMapController::class);
Route::apiResource('moph-indicators', \App\Http\Controllers\Api\MophIndicatorController::class);
Route::apiResource('moph-priority-conditions', \App\Http\Controllers\Api\MophPriorityConditionController::class);
Route::apiResource('moph-reports', \App\Http\Controllers\Api\MophReportController::class);
Route::apiResource('moph-report-lines', \App\Http\Controllers\Api\MophReportLineController::class);
Route::apiResource('mortuary-records', \App\Http\Controllers\Api\MortuaryRecordController::class);
Route::apiResource('notifications', \App\Http\Controllers\Api\NotificationController::class);
Route::apiResource('number-series', \App\Http\Controllers\Api\NumberSeriesController::class);
Route::apiResource('observations', \App\Http\Controllers\Api\ObservationController::class);
Route::apiResource('order-types', \App\Http\Controllers\Api\OrderTypeController::class);
Route::apiResource('org-units', \App\Http\Controllers\Api\OrgUnitController::class);
Route::apiResource('pack-barcodes', \App\Http\Controllers\Api\PackBarcodeController::class);
Route::apiResource('parties', \App\Http\Controllers\Api\PartyController::class);
Route::apiResource('patient-accounts', \App\Http\Controllers\Api\PatientAccountController::class);
Route::apiResource('patient-companions', \App\Http\Controllers\Api\PatientCompanionController::class);
Route::apiResource('patient-complaints', \App\Http\Controllers\Api\PatientComplaintController::class);
Route::apiResource('patient-contacts', \App\Http\Controllers\Api\PatientContactController::class);
Route::apiResource('patient-duplicate-candidates', \App\Http\Controllers\Api\PatientDuplicateCandidateController::class);
Route::apiResource('patient-identifiers', \App\Http\Controllers\Api\PatientIdentifierController::class);
Route::apiResource('patient-links', \App\Http\Controllers\Api\PatientLinkController::class);
Route::apiResource('patient-relations', \App\Http\Controllers\Api\PatientRelationController::class);
Route::apiResource('payers', \App\Http\Controllers\Api\PayerController::class);
Route::apiResource('payments', \App\Http\Controllers\Api\PaymentController::class);
Route::apiResource('payment-allocations', \App\Http\Controllers\Api\PaymentAllocationController::class);
Route::apiResource('payroll-lines', \App\Http\Controllers\Api\PayrollLineController::class);
Route::apiResource('payroll-periods', \App\Http\Controllers\Api\PayrollPeriodController::class);
Route::apiResource('payroll-runs', \App\Http\Controllers\Api\PayrollRunController::class);
Route::apiResource('permissions', \App\Http\Controllers\Api\PermissionController::class);
Route::apiResource('people', \App\Http\Controllers\Api\PersonController::class);
Route::apiResource('posting-rules', \App\Http\Controllers\Api\PostingRuleController::class);
Route::apiResource('practitioners', \App\Http\Controllers\Api\PractitionerController::class);
Route::apiResource('practitioner-qualifications', \App\Http\Controllers\Api\PractitionerQualificationController::class);
Route::apiResource('practitioner-specialties', \App\Http\Controllers\Api\PractitionerSpecialtyController::class);
Route::apiResource('procedure-performers', \App\Http\Controllers\Api\ProcedurePerformerController::class);
Route::apiResource('purchase-orders', \App\Http\Controllers\Api\PurchaseOrderController::class);
Route::apiResource('purchase-order-lines', \App\Http\Controllers\Api\PurchaseOrderLineController::class);
Route::apiResource('purchase-requisitions', \App\Http\Controllers\Api\PurchaseRequisitionController::class);
Route::apiResource('queue-tokens', \App\Http\Controllers\Api\QueueTokenController::class);
Route::apiResource('roles', \App\Http\Controllers\Api\RoleController::class);
Route::apiResource('role-permissions', \App\Http\Controllers\Api\RolePermissionController::class);
Route::apiResource('roster-assignments', \App\Http\Controllers\Api\RosterAssignmentController::class);
Route::apiResource('roster-requirements', \App\Http\Controllers\Api\RosterRequirementController::class);
Route::apiResource('shamsi-months', \App\Http\Controllers\Api\ShamsiMonthController::class);
Route::apiResource('shift-patterns', \App\Http\Controllers\Api\ShiftPatternController::class);
Route::apiResource('sterilization-cycles', \App\Http\Controllers\Api\SterilizationCycleController::class);
Route::apiResource('sterilization-sets', \App\Http\Controllers\Api\SterilizationSetController::class);
Route::apiResource('sterilization-usages', \App\Http\Controllers\Api\SterilizationUsageController::class);
Route::apiResource('stock-allocations', \App\Http\Controllers\Api\StockAllocationController::class);
Route::apiResource('stock-balances', \App\Http\Controllers\Api\StockBalanceController::class);
Route::apiResource('stock-counts', \App\Http\Controllers\Api\StockCountController::class);
Route::apiResource('stock-count-lines', \App\Http\Controllers\Api\StockCountLineController::class);
Route::apiResource('stock-items', \App\Http\Controllers\Api\StockItemController::class);
Route::apiResource('stock-locations', \App\Http\Controllers\Api\StockLocationController::class);
Route::apiResource('stock-lots', \App\Http\Controllers\Api\StockLotController::class);
Route::apiResource('stock-out-days', \App\Http\Controllers\Api\StockOutDayController::class);
Route::apiResource('stock-requisitions', \App\Http\Controllers\Api\StockRequisitionController::class);
Route::apiResource('stock-requisition-lines', \App\Http\Controllers\Api\StockRequisitionLineController::class);
Route::apiResource('stock-transfers', \App\Http\Controllers\Api\StockTransferController::class);
Route::apiResource('stock-transfer-lines', \App\Http\Controllers\Api\StockTransferLineController::class);
Route::apiResource('substances', \App\Http\Controllers\Api\SubstanceController::class);
Route::apiResource('suppliers', \App\Http\Controllers\Api\SupplierController::class);
Route::apiResource('surgeries', \App\Http\Controllers\Api\SurgeryController::class);
Route::apiResource('surgery-implants', \App\Http\Controllers\Api\SurgeryImplantController::class);
Route::apiResource('sync-conflicts', \App\Http\Controllers\Api\SyncConflictController::class);
Route::apiResource('sync-devices', \App\Http\Controllers\Api\SyncDeviceController::class);
Route::apiResource('tariffs', \App\Http\Controllers\Api\TariffController::class);
Route::apiResource('tariff-prices', \App\Http\Controllers\Api\TariffPriceController::class);
Route::apiResource('tax-rules', \App\Http\Controllers\Api\TaxRuleController::class);
Route::apiResource('therapeutic-moieties', \App\Http\Controllers\Api\TherapeuticMoietyController::class);
Route::apiResource('transfusion-reactions', \App\Http\Controllers\Api\TransfusionReactionController::class);
Route::apiResource('triages', \App\Http\Controllers\Api\TriageController::class);
Route::apiResource('units', \App\Http\Controllers\Api\UnitController::class);
Route::apiResource('uom-categories', \App\Http\Controllers\Api\UomCategoryController::class);
Route::apiResource('user-roles', \App\Http\Controllers\Api\UserRoleController::class);
Route::apiResource('virtual-products', \App\Http\Controllers\Api\VirtualProductController::class);
Route::apiResource('virtual-product-atcs', \App\Http\Controllers\Api\VirtualProductAtcController::class);
Route::apiResource('virtual-product-ingredients', \App\Http\Controllers\Api\VirtualProductIngredientController::class);
Route::apiResource('vitals', \App\Http\Controllers\Api\VitalController::class);
Route::apiResource('wards', \App\Http\Controllers\Api\WardController::class);
