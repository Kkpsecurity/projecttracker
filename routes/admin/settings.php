<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\SettingsController;

Route::prefix('settings')->name('settings.')->group(function () {
    Route::get('/', [SettingsController::class, 'index'])->name('index');
    Route::put('/', [SettingsController::class, 'update'])->name('update');
    Route::post('/reset', [SettingsController::class, 'reset'])->name('reset');
    Route::get('/toggle-maintenance', [SettingsController::class, 'toggleMaintenance'])->name('toggle-maintenance');

    // Additional Settings Routes
    Route::post('/upload-logo', [SettingsController::class, 'uploadLogo'])->name('upload-logo');
    Route::post('/upload-favicon', [SettingsController::class, 'uploadFavicon'])->name('upload-favicon');
    Route::get('/export', [SettingsController::class, 'export'])->name('export');
    Route::post('/import', [SettingsController::class, 'import'])->name('import');
});