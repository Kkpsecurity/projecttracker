<?php

use Illuminate\Support\Facades\Route;
use App\Modules\HB837\Controllers\HB837ModuleController;

/*
|--------------------------------------------------------------------------
| HB837 Module Routes
|--------------------------------------------------------------------------
|
| Routes for the HB837 Property Management Module
|
*/

Route::middleware(['web', 'auth'])->prefix('modules/hb837')->name('modules.hb837.')->group(function () {

    // Dashboard
    Route::get('/', [HB837ModuleController::class, 'index'])->name('index');

    // Import Routes - 3 Phase Upload
    Route::prefix('import')->name('import.')->group(function () {
        Route::get('/', [HB837ModuleController::class, 'showImport'])->name('index');
        Route::post('/upload', [HB837ModuleController::class, 'uploadFile'])->name('upload');
        Route::post('/map-fields', [HB837ModuleController::class, 'mapFields'])->name('map-fields');
        Route::post('/execute', [HB837ModuleController::class, 'executeImport'])->name('execute');
        Route::post('/rollback', [HB837ModuleController::class, 'rollbackImport'])->name('rollback');
    });

    // Export Routes
    Route::prefix('export')->name('export.')->group(function () {
        Route::post('/', [HB837ModuleController::class, 'export'])->name('execute');
        Route::get('/template', [HB837ModuleController::class, 'getTemplate'])->name('template');
        Route::post('/backup', [HB837ModuleController::class, 'createBackup'])->name('backup');
    });

    // Download Routes
    Route::get('/download/{file}', [HB837ModuleController::class, 'download'])->name('download');
    Route::get('/download-template/{file}', [HB837ModuleController::class, 'downloadTemplate'])->name('download-template');
    Route::get('/download-backup/{file}', [HB837ModuleController::class, 'downloadBackup'])->name('download-backup');

    // Statistics & API
    Route::get('/statistics', [HB837ModuleController::class, 'getStatistics'])->name('statistics');

    // Data table route
    Route::get('/data', [HB837ModuleController::class, 'getData'])->name('data');
});
