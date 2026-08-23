<?php

use App\Http\Controllers\Admin\AppointmentTypeController;
use App\Http\Controllers\Admin\ChairController;
use App\Http\Controllers\Admin\ConsentTypeController;
use App\Http\Controllers\Admin\DiagnosisController;
use App\Http\Controllers\Admin\InventoryCategoryController;
use App\Http\Controllers\Admin\InventoryUnitController;
use App\Http\Controllers\Admin\LabController;
use App\Http\Controllers\Admin\MedicationController;
use App\Http\Controllers\Admin\PaymentMethodController;
use App\Http\Controllers\Admin\ProcedureCategoryController;
use App\Http\Controllers\Admin\ProcedureController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProviderController;
use App\Http\Controllers\Admin\RecallTypeController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Admin\ToothConditionController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\ClinicalNoteController;
use App\Http\Controllers\ConsentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DentalHistoryController;
use App\Http\Controllers\EncounterController;
use App\Http\Controllers\EncounterDiagnosisController;
use App\Http\Controllers\GoodsReceiptController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\InvoiceAdjustmentController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\LabOrderController;
use App\Http\Controllers\MedicalHistoryController;
use App\Http\Controllers\OdontogramController;
use App\Http\Controllers\OdontogramEntryController;
use App\Http\Controllers\PatientAddressController;
use App\Http\Controllers\PatientAllergyController;
use App\Http\Controllers\PatientConditionController;
use App\Http\Controllers\PatientContactController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\PatientIdentifierController;
use App\Http\Controllers\PatientLedgerController;
use App\Http\Controllers\PatientRelationshipController;
use App\Http\Controllers\PatientTimelineController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PerioController;
use App\Http\Controllers\PerioToothRecordController;
use App\Http\Controllers\PrescriptionController;
use App\Http\Controllers\PrescriptionItemController;
use App\Http\Controllers\ProcedureRecordController;
use App\Http\Controllers\ProductBatchController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\PurchaseOrderItemController;
use App\Http\Controllers\QueueEntryController;
use App\Http\Controllers\RecallController;
use App\Http\Controllers\ReferralController;
use App\Http\Controllers\StockMovementController;
use App\Http\Controllers\TreatmentPlanController;
use App\Http\Controllers\TreatmentPlanItemController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route(auth()->check() ? 'dashboard' : 'login');
});

Route::get('/dashboard', [DashboardController::class, 'show'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::middleware('module:patients')->group(function () {
        Route::resource('patients', PatientController::class)->except('destroy');
        Route::post('patients/{patient}/archive', [PatientController::class, 'archive'])->name('patients.archive');
        Route::post('patients/{patient}/restore', [PatientController::class, 'restore'])->name('patients.restore');
        Route::post('patients/{patient}/addresses', [PatientAddressController::class, 'store'])->name('patients.addresses.store');
        Route::delete('patients/{patient}/addresses/{address}', [PatientAddressController::class, 'destroy'])->name('patients.addresses.destroy');
        Route::post('patients/{patient}/contacts', [PatientContactController::class, 'store'])->name('patients.contacts.store');
        Route::delete('patients/{patient}/contacts/{contact}', [PatientContactController::class, 'destroy'])->name('patients.contacts.destroy');
        Route::post('patients/{patient}/relationships', [PatientRelationshipController::class, 'store'])->name('patients.relationships.store');
        Route::delete('patients/{patient}/relationships/{relationship}', [PatientRelationshipController::class, 'destroy'])->name('patients.relationships.destroy');
        Route::post('patients/{patient}/identifiers', [PatientIdentifierController::class, 'store'])->name('patients.identifiers.store');
        Route::delete('patients/{patient}/identifiers/{identifier}', [PatientIdentifierController::class, 'destroy'])->name('patients.identifiers.destroy');
        Route::get('patients/{patient}/medical-history', [MedicalHistoryController::class, 'edit'])->name('patients.medical-history.edit');
        Route::put('patients/{patient}/medical-history', [MedicalHistoryController::class, 'update'])->name('patients.medical-history.update');
        Route::get('patients/{patient}/dental-history', [DentalHistoryController::class, 'edit'])->name('patients.dental-history.edit');
        Route::put('patients/{patient}/dental-history', [DentalHistoryController::class, 'update'])->name('patients.dental-history.update');
        Route::post('patients/{patient}/conditions', [PatientConditionController::class, 'store'])->name('patients.conditions.store');
        Route::delete('patients/{patient}/conditions/{condition}', [PatientConditionController::class, 'destroy'])->name('patients.conditions.destroy');
        Route::post('patients/{patient}/allergies', [PatientAllergyController::class, 'store'])->name('patients.allergies.store');
        Route::delete('patients/{patient}/allergies/{allergy}', [PatientAllergyController::class, 'destroy'])->name('patients.allergies.destroy');
        Route::get('patients/{patient}/odontogram', [OdontogramController::class, 'show'])->name('patients.odontogram.show');
        Route::get('patients/{patient}/periodontal', [PerioController::class, 'show'])->name('patients.periodontal.show');
        Route::get('patients/{patient}/prescriptions', [PrescriptionController::class, 'patientHistory'])->name('patients.prescriptions.show');
        Route::post('patients/{patient}/payments', [PaymentController::class, 'storeSplit'])->name('patients.payments.store');
        Route::get('patients/{patient}/ledger', [PatientLedgerController::class, 'show'])->name('patients.ledger.show');
        Route::get('patients/{patient}/timeline', [PatientTimelineController::class, 'show'])->name('patients.timeline.show');
        Route::post('patients/{patient}/recalls', [RecallController::class, 'store'])->name('patients.recalls.store');
        Route::post('patients/{patient}/consents', [ConsentController::class, 'store'])->name('patients.consents.store');
        Route::post('patients/{patient}/consents/{consent}/revoke', [ConsentController::class, 'revoke'])->name('patients.consents.revoke');
        Route::post('patients/{patient}/referrals', [ReferralController::class, 'store'])->name('patients.referrals.store');
    });

    Route::middleware('module:appointments')->group(function () {
        Route::resource('appointments', AppointmentController::class)->except('destroy', 'show');
        Route::patch('appointments/{appointment}/reschedule', [AppointmentController::class, 'reschedule'])->name('appointments.reschedule');
        Route::post('appointments/{appointment}/cancel', [AppointmentController::class, 'cancel'])->name('appointments.cancel');
        Route::post('appointments/{appointment}/no-show', [AppointmentController::class, 'markNoShow'])->name('appointments.no-show');
        Route::post('appointments/{appointment}/encounter', [EncounterController::class, 'startFromAppointment'])->name('appointments.encounter.start');
    });

    Route::middleware('module:encounters')->group(function () {
        Route::resource('encounters', EncounterController::class)->only('index', 'create', 'store', 'show');
        Route::post('encounters/{encounter}/complete', [EncounterController::class, 'complete'])->name('encounters.complete');
        Route::post('encounters/{encounter}/notes', [ClinicalNoteController::class, 'store'])->name('encounters.notes.store');
        Route::put('encounters/{encounter}/notes/{note}', [ClinicalNoteController::class, 'update'])->name('encounters.notes.update');
        Route::post('encounters/{encounter}/notes/{note}/sign', [ClinicalNoteController::class, 'sign'])->name('encounters.notes.sign');
        Route::post('encounters/{encounter}/notes/{note}/amend', [ClinicalNoteController::class, 'amend'])->name('encounters.notes.amend');
        Route::post('encounters/{encounter}/odontogram-entries', [OdontogramEntryController::class, 'store'])->name('encounters.odontogram-entries.store');
        Route::post('encounters/{encounter}/perio-tooth-records', [PerioToothRecordController::class, 'store'])->name('encounters.perio-tooth-records.store');
        Route::post('encounters/{encounter}/diagnoses', [EncounterDiagnosisController::class, 'store'])->name('encounters.diagnoses.store');
        Route::patch('encounters/{encounter}/diagnoses/{encounterDiagnosis}/status', [EncounterDiagnosisController::class, 'updateStatus'])->name('encounters.diagnoses.status');
        Route::post('encounters/{encounter}/procedure-records', [ProcedureRecordController::class, 'store'])->name('encounters.procedure-records.store');
        Route::post('encounters/{encounter}/procedure-records/{procedureRecord}/void', [ProcedureRecordController::class, 'void'])->name('encounters.procedure-records.void');
        Route::post('encounters/{encounter}/invoice', [InvoiceController::class, 'generateFromEncounter'])->name('encounters.invoice.generate');
        Route::post('encounters/{encounter}/prescriptions', [PrescriptionController::class, 'store'])->name('encounters.prescriptions.store');
        Route::post('encounters/{encounter}/prescriptions/{prescription}/cancel', [PrescriptionController::class, 'cancel'])->name('encounters.prescriptions.cancel');
        Route::post('encounters/{encounter}/prescriptions/{prescription}/items', [PrescriptionItemController::class, 'store'])->name('encounters.prescriptions.items.store');
    });

    Route::middleware('module:treatment_plans')->group(function () {
        Route::resource('treatment-plans', TreatmentPlanController::class)->only('index', 'create', 'store', 'show');
        Route::post('treatment-plans/{treatmentPlan}/transition', [TreatmentPlanController::class, 'transition'])->name('treatment-plans.transition');
        Route::post('treatment-plans/{treatmentPlan}/items', [TreatmentPlanItemController::class, 'store'])->name('treatment-plans.items.store');
        Route::patch('treatment-plans/{treatmentPlan}/items/{item}/status', [TreatmentPlanItemController::class, 'updateStatus'])->name('treatment-plans.items.status');
    });

    Route::middleware('module:laboratory')->group(function () {
        Route::resource('lab-orders', LabOrderController::class)->only('index', 'create', 'store');
        Route::post('lab-orders/{labOrder}/transition', [LabOrderController::class, 'transition'])->name('lab-orders.transition');
    });

    Route::middleware('module:invoices')->group(function () {
        Route::resource('invoices', InvoiceController::class)->only('index', 'show');
        Route::post('invoices/{invoice}/payments', [PaymentController::class, 'store'])->name('invoices.payments.store');
        Route::post('invoices/{invoice}/payments/{payment}/void', [PaymentController::class, 'void'])->name('invoices.payments.void');
        Route::post('invoices/{invoice}/adjustments', [InvoiceAdjustmentController::class, 'store'])->name('invoices.adjustments.store');
    });

    Route::middleware('module:queue')->group(function () {
        Route::get('queue', [QueueEntryController::class, 'index'])->name('queue.index');
        Route::post('queue', [QueueEntryController::class, 'store'])->name('queue.store');
        Route::post('queue/{queueEntry}/call', [QueueEntryController::class, 'call'])->name('queue.call');
        Route::post('queue/{queueEntry}/start', [QueueEntryController::class, 'start'])->name('queue.start');
        Route::post('queue/{queueEntry}/complete', [QueueEntryController::class, 'complete'])->name('queue.complete');
        Route::post('queue/{queueEntry}/skip', [QueueEntryController::class, 'skip'])->name('queue.skip');
    });

    Route::middleware('module:audit_logs')->group(function () {
        Route::get('audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
    });

    Route::middleware('module:recalls')->group(function () {
        Route::get('recalls', [RecallController::class, 'index'])->name('recalls.index');
        Route::post('recalls/{recall}/complete', [RecallController::class, 'complete'])->name('recalls.complete');
        Route::post('recalls/{recall}/cancel', [RecallController::class, 'cancel'])->name('recalls.cancel');
    });

    Route::middleware('module:referrals')->group(function () {
        Route::get('referrals', [ReferralController::class, 'index'])->name('referrals.index');
        Route::post('referrals/{referral}/transition', [ReferralController::class, 'transition'])->name('referrals.transition');
    });

    Route::middleware('module:inventory')->group(function () {
        Route::get('inventory', [InventoryController::class, 'index'])->name('inventory.index');
        Route::get('inventory/{product}', [InventoryController::class, 'show'])->name('inventory.show');
        Route::post('inventory/{product}/batches', [ProductBatchController::class, 'store'])->name('inventory.batches.store');
        Route::post('inventory/{product}/stock-out', [StockMovementController::class, 'stockOut'])->name('inventory.stock-out');
        Route::post('inventory/{product}/adjust', [StockMovementController::class, 'adjust'])->name('inventory.adjust');
    });

    Route::middleware('module:purchase_orders')->group(function () {
        Route::resource('purchase-orders', PurchaseOrderController::class)->only('index', 'create', 'store', 'show');
        Route::post('purchase-orders/{purchaseOrder}/transition', [PurchaseOrderController::class, 'transition'])->name('purchase-orders.transition');
        Route::post('purchase-orders/{purchaseOrder}/items', [PurchaseOrderItemController::class, 'store'])->name('purchase-orders.items.store');
        Route::post('purchase-orders/{purchaseOrder}/receipts', [GoodsReceiptController::class, 'store'])->name('purchase-orders.receipts.store');
    });

    Route::prefix('admin')->name('admin.')->group(function () {
        Route::middleware('module:master_data')->group(function () {
            Route::resource('procedure-categories', ProcedureCategoryController::class)->except('show');
            Route::resource('procedures', ProcedureController::class)->except('show');
            Route::resource('tooth-conditions', ToothConditionController::class)->except('show');
            Route::resource('diagnoses', DiagnosisController::class)->except('show');
            Route::resource('consent-types', ConsentTypeController::class)->except('show');
            Route::resource('medications', MedicationController::class)->except('show');
            Route::resource('recall-types', RecallTypeController::class)->except('show');
            Route::resource('providers', ProviderController::class)->except('show');
            Route::resource('chairs', ChairController::class)->except('show');
            Route::resource('appointment-types', AppointmentTypeController::class)->except('show');
            Route::resource('payment-methods', PaymentMethodController::class)->except('show');
            Route::resource('inventory-categories', InventoryCategoryController::class)->except('show');
            Route::resource('inventory-units', InventoryUnitController::class)->except('show');
            Route::resource('suppliers', SupplierController::class)->except('show');
            Route::resource('labs', LabController::class)->except('show');
            Route::resource('products', ProductController::class)->except('show');
        });

        Route::middleware('admin')->group(function () {
            Route::resource('users', UserController::class)->except('show', 'destroy');
            Route::resource('roles', RoleController::class)->except('show');
        });
    });
});

require __DIR__.'/auth.php';
