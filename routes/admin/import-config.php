<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\HB837\HB837ImportConfigController;

/*
|--------------------------------------------------------------------------
| HB837 Import Configuration Routes
|--------------------------------------------------------------------------
|
| Routes for managing HB837 import field mappings and configuration settings
|
*/

Route::prefix('hb837-import-config')->name('hb837-import-config.')->group(function () {
    Route::get('/', [HB837ImportConfigController::class, 'index'])->name('index');
    Route::get('/create', [HB837ImportConfigController::class, 'create'])->name('create');
    Route::post('/', [HB837ImportConfigController::class, 'store'])->name('store');

    // Mapped Fields View - MUST be before parameterized routes
    Route::get('/mapped-fields', [HB837ImportConfigController::class, 'mappedFields'])->name('mapped-fields');

    Route::get('/{field}', [HB837ImportConfigController::class, 'show'])->name('show');
    Route::get('/{field}/edit', [HB837ImportConfigController::class, 'edit'])->name('edit');
    Route::put('/{field}', [HB837ImportConfigController::class, 'update'])->name('update');
    Route::delete('/{field}', [HB837ImportConfigController::class, 'destroy'])->name('destroy');

    // Additional actions
    Route::post('/{field}/create-column', [HB837ImportConfigController::class, 'createColumn'])->name('create-column');
    Route::post('/sync-config', [HB837ImportConfigController::class, 'syncConfig'])->name('sync');
    Route::post('/import-schema', [HB837ImportConfigController::class, 'importSchema'])->name('import-schema');
});
