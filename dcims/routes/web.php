<?php

use App\Http\Controllers\Admin\AppointmentTypeController;
use App\Http\Controllers\Admin\ChairController;
use App\Http\Controllers\Admin\DiagnosisController;
use App\Http\Controllers\Admin\InventoryCategoryController;
use App\Http\Controllers\Admin\InventoryUnitController;
use App\Http\Controllers\Admin\PaymentMethodController;
use App\Http\Controllers\Admin\ProcedureCategoryController;
use App\Http\Controllers\Admin\ProcedureController;
use App\Http\Controllers\Admin\ProviderController;
use App\Http\Controllers\Admin\ToothConditionController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\ClinicalNoteController;
use App\Http\Controllers\DentalHistoryController;
use App\Http\Controllers\EncounterController;
use App\Http\Controllers\EncounterDiagnosisController;
use App\Http\Controllers\MedicalHistoryController;
use App\Http\Controllers\OdontogramController;
use App\Http\Controllers\OdontogramEntryController;
use App\Http\Controllers\PatientAddressController;
use App\Http\Controllers\PatientAllergyController;
use App\Http\Controllers\PatientConditionController;
use App\Http\Controllers\PatientContactController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\PatientIdentifierController;
use App\Http\Controllers\PatientRelationshipController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QueueEntryController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

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

    Route::resource('appointments', AppointmentController::class)->except('destroy', 'show');
    Route::patch('appointments/{appointment}/reschedule', [AppointmentController::class, 'reschedule'])->name('appointments.reschedule');
    Route::post('appointments/{appointment}/cancel', [AppointmentController::class, 'cancel'])->name('appointments.cancel');
    Route::post('appointments/{appointment}/no-show', [AppointmentController::class, 'markNoShow'])->name('appointments.no-show');
    Route::post('appointments/{appointment}/encounter', [EncounterController::class, 'startFromAppointment'])->name('appointments.encounter.start');

    Route::resource('encounters', EncounterController::class)->only('index', 'create', 'store', 'show');
    Route::post('encounters/{encounter}/complete', [EncounterController::class, 'complete'])->name('encounters.complete');
    Route::post('encounters/{encounter}/notes', [ClinicalNoteController::class, 'store'])->name('encounters.notes.store');
    Route::put('encounters/{encounter}/notes/{note}', [ClinicalNoteController::class, 'update'])->name('encounters.notes.update');
    Route::post('encounters/{encounter}/notes/{note}/sign', [ClinicalNoteController::class, 'sign'])->name('encounters.notes.sign');
    Route::post('encounters/{encounter}/notes/{note}/amend', [ClinicalNoteController::class, 'amend'])->name('encounters.notes.amend');
    Route::post('encounters/{encounter}/odontogram-entries', [OdontogramEntryController::class, 'store'])->name('encounters.odontogram-entries.store');
    Route::post('encounters/{encounter}/diagnoses', [EncounterDiagnosisController::class, 'store'])->name('encounters.diagnoses.store');
    Route::patch('encounters/{encounter}/diagnoses/{encounterDiagnosis}/status', [EncounterDiagnosisController::class, 'updateStatus'])->name('encounters.diagnoses.status');

    Route::get('queue', [QueueEntryController::class, 'index'])->name('queue.index');
    Route::post('queue', [QueueEntryController::class, 'store'])->name('queue.store');
    Route::post('queue/{queueEntry}/call', [QueueEntryController::class, 'call'])->name('queue.call');
    Route::post('queue/{queueEntry}/start', [QueueEntryController::class, 'start'])->name('queue.start');
    Route::post('queue/{queueEntry}/complete', [QueueEntryController::class, 'complete'])->name('queue.complete');
    Route::post('queue/{queueEntry}/skip', [QueueEntryController::class, 'skip'])->name('queue.skip');

    Route::prefix('admin')->name('admin.')->group(function () {
        Route::resource('procedure-categories', ProcedureCategoryController::class)->except('show');
        Route::resource('procedures', ProcedureController::class)->except('show');
        Route::resource('tooth-conditions', ToothConditionController::class)->except('show');
        Route::resource('diagnoses', DiagnosisController::class)->except('show');
        Route::resource('providers', ProviderController::class)->except('show');
        Route::resource('chairs', ChairController::class)->except('show');
        Route::resource('appointment-types', AppointmentTypeController::class)->except('show');
        Route::resource('payment-methods', PaymentMethodController::class)->except('show');
        Route::resource('inventory-categories', InventoryCategoryController::class)->except('show');
        Route::resource('inventory-units', InventoryUnitController::class)->except('show');
    });
});

require __DIR__.'/auth.php';
