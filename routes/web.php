<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\DisplayAntrianController;
use App\Http\Controllers\OnlineRegistrationController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Registration\QueueController;
use App\Http\Controllers\Registration\VitalSignController;
use App\Http\Controllers\Settings\DoctorTariffController;
use App\Http\Controllers\Settings\DrugController;
use App\Http\Controllers\Settings\EmployeeController;
use App\Http\Controllers\Settings\LabTestMasterController;
use App\Http\Controllers\Settings\MenuController;
use App\Http\Controllers\Settings\ProcedureTariffController;
use App\Http\Controllers\Settings\RoleController;
use App\Http\Controllers\Settings\SpecializationController;
use App\Http\Controllers\Settings\TariffController;
use App\Http\Controllers\Settings\UserPermissionController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/register-online', [OnlineRegistrationController::class, 'create'])->name('register.online.create');
Route::post('/register-online', [OnlineRegistrationController::class, 'store'])->name('register.online.store');
Route::get('/register-online/success', [OnlineRegistrationController::class, 'success'])->name('register.online.success');

Route::get('/display-antrian', [DisplayAntrianController::class, 'index'])->name('display-antrian.index');
Route::get('/api/display-antrian/current', [DisplayAntrianController::class, 'current'])->name('display-antrian.current');

Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::prefix('registration')->name('registration.')->group(function () {
        Route::get('/queue', [QueueController::class, 'index'])->name('queue.index');
        Route::get('/queue/create', [QueueController::class, 'create'])->name('queue.create');
        Route::post('/queue', [QueueController::class, 'store'])->name('queue.store');
        Route::post('/queue/{visit}/check-in', [QueueController::class, 'checkIn'])->name('queue.check-in');
        Route::post('/queue/{visit}/call', [QueueController::class, 'call'])->name('queue.call');
        Route::post('/queue/{visit}/skip', [QueueController::class, 'skip'])->name('queue.skip');
        Route::post('/queue/{visit}/done', [QueueController::class, 'done'])->name('queue.done');

        Route::get('/vitals/{visit}/create', [VitalSignController::class, 'create'])->name('vitals.create');
        Route::post('/vitals/{visit}', [VitalSignController::class, 'store'])->name('vitals.store');
        Route::get('/vitals/{visit}/edit', [VitalSignController::class, 'edit'])->name('vitals.edit');
        Route::put('/vitals/{visit}', [VitalSignController::class, 'update'])->name('vitals.update');
    });

    Route::prefix('patients')->name('patients.')->group(function () {
        Route::resource('/', PatientController::class)->parameters(['' => 'patient']);
    });

    Route::prefix('settings')->name('settings.')->group(function () {
        Route::resource('menus', MenuController::class)
            ->middleware('can:settings.menu.view');
        Route::resource('roles', RoleController::class)
            ->middleware('can:settings.role.view');
        Route::resource('specializations', SpecializationController::class)
            ->middleware('can:master-data.specialization.view');
        Route::resource('employees', EmployeeController::class)
            ->middleware('can:master-data.employee.view');
        Route::resource('tariffs', TariffController::class)
            ->middleware('can:master-data.tariff.view');
        Route::resource('drugs', DrugController::class)
            ->middleware('can:master-data.drug.view');
        Route::resource('lab-test-masters', LabTestMasterController::class)
            ->middleware('can:master-data.lab-test.view');
        Route::resource('doctor-tariffs', DoctorTariffController::class)
            ->middleware('can:master-data.doctor-tariff.view');
        Route::resource('procedure-tariffs', ProcedureTariffController::class)
            ->middleware('can:master-data.procedure-tariff.view');

        Route::get('user-permissions', [UserPermissionController::class, 'index'])
            ->middleware('can:settings.permission.view')
            ->name('user-permissions.index');
        Route::get('user-permissions/{user}/edit', [UserPermissionController::class, 'edit'])
            ->middleware('can:settings.permission.assign')
            ->name('user-permissions.edit');
        Route::put('user-permissions/{user}', [UserPermissionController::class, 'update'])
            ->middleware('can:settings.permission.assign')
            ->name('user-permissions.update');
    });

    Route::get('/activity-log', [ActivityLogController::class, 'index'])
        ->middleware('can:activity-log.view')
        ->name('activity-log.index');
});

require __DIR__.'/auth.php';
