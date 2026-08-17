<?php

use App\Http\Controllers\Admin\AppointmentTypeController;
use App\Http\Controllers\Admin\ChairController;
use App\Http\Controllers\Admin\InventoryCategoryController;
use App\Http\Controllers\Admin\InventoryUnitController;
use App\Http\Controllers\Admin\PaymentMethodController;
use App\Http\Controllers\Admin\ProcedureCategoryController;
use App\Http\Controllers\Admin\ProcedureController;
use App\Http\Controllers\Admin\ProviderController;
use App\Http\Controllers\Admin\ToothConditionController;
use App\Http\Controllers\DentalHistoryController;
use App\Http\Controllers\MedicalHistoryController;
use App\Http\Controllers\PatientAddressController;
use App\Http\Controllers\PatientAllergyController;
use App\Http\Controllers\PatientConditionController;
use App\Http\Controllers\PatientContactController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\PatientIdentifierController;
use App\Http\Controllers\PatientRelationshipController;
use App\Http\Controllers\ProfileController;
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

    Route::prefix('admin')->name('admin.')->group(function () {
        Route::resource('procedure-categories', ProcedureCategoryController::class)->except('show');
        Route::resource('procedures', ProcedureController::class)->except('show');
        Route::resource('tooth-conditions', ToothConditionController::class)->except('show');
        Route::resource('providers', ProviderController::class)->except('show');
        Route::resource('chairs', ChairController::class)->except('show');
        Route::resource('appointment-types', AppointmentTypeController::class)->except('show');
        Route::resource('payment-methods', PaymentMethodController::class)->except('show');
        Route::resource('inventory-categories', InventoryCategoryController::class)->except('show');
        Route::resource('inventory-units', InventoryUnitController::class)->except('show');
    });
});

require __DIR__.'/auth.php';
