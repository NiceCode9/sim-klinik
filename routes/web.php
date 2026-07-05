<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Settings\MenuController;
use App\Http\Controllers\Settings\RoleController;
use App\Http\Controllers\Settings\UserPermissionController;
use Illuminate\Support\Facades\Route;

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

    Route::prefix('settings')->name('settings.')->group(function () {
        Route::resource('menus', MenuController::class)
            ->middleware('can:settings.menu.view');

        Route::resource('roles', RoleController::class)
            ->middleware('can:settings.role.view');

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
