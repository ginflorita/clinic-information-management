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
