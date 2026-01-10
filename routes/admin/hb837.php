<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\HB837\HB837Controller;
use App\Http\Controllers\Admin\HB837\InspectionCalendarController;

/*
|--------------------------------------------------------------------------
| HB837 Management Routes
|--------------------------------------------------------------------------
|
| Routes for HB837 property inspection reports management, including
| CRUD operations, import/export, calendar integration, and file management
|
*/

Route::prefix('hb837')->name('hb837.')->group(function () {
    // Basic CRUD Routes (non-parameterized routes first)
    Route::get('/', [HB837Controller::class, 'index'])->name('index');
    Route::get('/create', [HB837Controller::class, 'create'])->name('create');
    Route::post('/', [HB837Controller::class, 'store'])->name('store');

    // DataTables AJAX Routes (specific routes before parameterized ones)
    Route::get('/data/table', [HB837Controller::class, 'getData'])->name('data');
    Route::get('/data/{tab}', [HB837Controller::class, 'getTabData'])->name('data.tab');
    Route::get('/stats', [HB837Controller::class, 'getStats'])->name('stats');

    // Bulk Actions
    Route::post('/bulk-action', [HB837Controller::class, 'bulkAction'])->name('bulk-action');

    // Import/Export Routes
    Route::get('/import', [HB837Controller::class, 'showImport'])->name('import.show');
    Route::post('/import', [HB837Controller::class, 'import'])->name('import');
    Route::post('/import/process', [HB837Controller::class, 'processImport'])->name('import.process');
    Route::post('/import/compare', [HB837Controller::class, 'compareImport'])->name('import.compare');
    Route::post('/import/three-phase', [HB837Controller::class, 'executeThreePhaseImport'])->name('three-phase-import');
    Route::get('/import/three-phase', [HB837Controller::class, 'showThreePhaseImport'])->name('three-phase-import.show');

    // Smart Import Routes (New)
    Route::get('/smart-import', [HB837Controller::class, 'showSmartImport'])->name('smart-import.show');
    Route::post('/smart-import/analyze', [HB837Controller::class, 'analyzeImportFile'])->name('import.analyze');
    Route::post('/smart-import/preview', [HB837Controller::class, 'previewImportData'])->name('import.preview');
    Route::post('/smart-import/execute', [HB837Controller::class, 'executeSmartImport'])->name('import.execute');
    Route::get('/import/results', [HB837Controller::class, 'showImportResults'])->name('import-results');

    Route::get('/export', [HB837Controller::class, 'export'])->name('export');
    Route::get('/export/{format}', [HB837Controller::class, 'exportFormat'])->name('export.format');
    Route::get('/export/template/{format}', [HB837Controller::class, 'exportTemplate'])->name('export.template');

    // Inspection Calendar Routes
    Route::get('/inspection-calendar', [InspectionCalendarController::class, 'index'])->name('inspection-calendar.index');
    Route::get('/inspection-calendar/events', [InspectionCalendarController::class, 'getEvents'])->name('inspection-calendar.events');
    Route::get('/inspection-calendar/statuses', [InspectionCalendarController::class, 'getStatuses'])->name('inspection-calendar.statuses');
    Route::get('/inspection-calendar/project/{id}', [InspectionCalendarController::class, 'getProjectDetails'])->name('inspection-calendar.project');
    Route::put('/inspection-calendar/project/{id}/date', [InspectionCalendarController::class, 'updateInspectionDate'])->name('inspection-calendar.update-date');

    // File Management (non-parameterized routes)
    Route::get('/files/{file}/download', [HB837Controller::class, 'downloadFile'])->name('files.download');
    Route::delete('/files/{file}', [HB837Controller::class, 'deleteFile'])->name('files.delete');

    // Tab-specific routes (MUST be after all specific routes)
    Route::get('/{tab}', [HB837Controller::class, 'index'])
        ->where('tab', 'all|active|quoted|completed|closed')
        ->name('tab');

    // Parameterized routes (must come LAST)
    Route::get('/{hb837}', [HB837Controller::class, 'show'])->name('show');
    Route::get('/{hb837}/edit/{tab?}', [HB837Controller::class, 'edit'])->name('edit');
    Route::get('/{hb837}/pdf-report', [HB837Controller::class, 'generatePdfReport'])->name('pdf-report');
    Route::put('/{hb837}', [HB837Controller::class, 'update'])->name('update');
    Route::delete('/{hb837}', [HB837Controller::class, 'destroy'])->name('destroy');
    Route::patch('/{hb837}/status', [HB837Controller::class, 'updateStatus'])->name('status');
    Route::patch('/{hb837}/priority', [HB837Controller::class, 'updatePriority'])->name('priority');
    Route::post('/{hb837}/duplicate', [HB837Controller::class, 'duplicate'])->name('duplicate');
    Route::delete('/{hb837}/ajax', [HB837Controller::class, 'ajaxDestroy'])->name('ajax-destroy');

    // File Management (parameterized routes for specific HB837 records)
    Route::get('/{hb837}/files', [HB837Controller::class, 'files'])->name('files');
    Route::post('/{hb837}/files', [HB837Controller::class, 'uploadFile'])->name('files.upload');
});
