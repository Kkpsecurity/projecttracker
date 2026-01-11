<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ConsultantController;

/*
|--------------------------------------------------------------------------
| Consultant Management Routes
|--------------------------------------------------------------------------
|
| Routes for managing consultants, including CRUD operations,
| file management, and consultant-specific features
|
*/

Route::prefix('consultants')->name('consultants.')->group(function () {
    // Basic CRUD Routes
    Route::get('/', [ConsultantController::class, 'index'])->name('index');
    Route::get('/create', [ConsultantController::class, 'create'])->name('create');
    Route::post('/', [ConsultantController::class, 'store'])->name('store');

    // Financial Report Route (must be before parameterized routes)
    Route::get('/report', [ConsultantController::class, 'financialReport'])->name('report');

    // File Management (non-parameterized routes)
    Route::get('/files/{file}/download', [ConsultantController::class, 'downloadFile'])->name('files.download');
    Route::delete('/files/{file}', [ConsultantController::class, 'deleteFile'])->name('files.delete');

    // Consultant Reports (must be before /{consultant})
    Route::get('/{consultant}/activity-report-pdf', [ConsultantController::class, 'activityReportPdf'])->name('activity-report-pdf');

    // Parameterized routes (must come LAST)
    Route::get('/{consultant}', [ConsultantController::class, 'show'])->name('show');
    Route::get('/{consultant}/edit', [ConsultantController::class, 'edit'])->name('edit');
    Route::put('/{consultant}', [ConsultantController::class, 'update'])->name('update');
    Route::delete('/{consultant}', [ConsultantController::class, 'destroy'])->name('destroy');

    // File Management (parameterized routes for specific consultant records)
    Route::post('/{consultant}/files', [ConsultantController::class, 'uploadFile'])->name('files.upload');

});
