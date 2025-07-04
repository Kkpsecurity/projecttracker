<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\LogsController;
use App\Http\Controllers\Admin\HB837\HB837Controller;
use App\Http\Controllers\Admin\HB837\InspectionCalendarController;
use App\Http\Controllers\Admin\HB837\HB837ImportConfigController;
use App\Http\Controllers\Admin\ConsultantController;
use App\Http\Controllers\Admin\GoogleMapsController;
use App\Http\Controllers\Admin\PlotsController;
use App\Http\Controllers\Admin\PlotGroupController;
// use App\Http\Controllers\Admin\MockPlotsController; // Uncomment for testing without database

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| Here is where you can register admin routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "auth" middleware group.
|
*/

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {

    // Admin Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

    // Admin Center - User Management
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::get('/{user}', [UserController::class, 'show'])->name('show');
        Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
        Route::put('/{user}', [UserController::class, 'update'])->name('update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');

        // AJAX and Data Routes
        Route::get('/data/table', [UserController::class, 'getData'])->name('data');

        // Additional User Actions
        Route::patch('/{user}/reset-password', [UserController::class, 'resetPassword'])->name('reset-password');
        Route::patch('/{user}/toggle-email-verification', [UserController::class, 'toggleEmailVerification'])->name('toggle-email-verification');
        Route::patch('/{user}/disable-two-factor', [UserController::class, 'disableTwoFactor'])->name('disable-two-factor');
        Route::post('/bulk-action', [UserController::class, 'bulkAction'])->name('bulk-action');
    });

    // Admin Center - System Settings
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

    // Admin Center - Activity Logs
    Route::prefix('logs')->name('logs.')->group(function () {
        Route::get('/', [LogsController::class, 'index'])->name('index');
        Route::get('/data', [LogsController::class, 'getData'])->name('data');
        Route::post('/clear-old', [LogsController::class, 'clearOldLogs'])->name('clear-old');
        Route::get('/export', [LogsController::class, 'export'])->name('export');

        // Log Filtering and Search
        Route::get('/filter', [LogsController::class, 'filter'])->name('filter');
        Route::get('/search', [LogsController::class, 'search'])->name('search');
    });

    // HB837 Management (Enhanced)
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

        // Parameterized routes (must come LAST)
        Route::get('/{hb837}', [HB837Controller::class, 'show'])->name('show');
        Route::get('/{hb837}/edit/{tab?}', [HB837Controller::class, 'edit'])->name('edit');
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

    // Google Maps & Plots Management
    Route::prefix('maps')->name('maps.')->group(function () {
        Route::get('/', [GoogleMapsController::class, 'index'])->name('index');
        Route::get('/plot/{plot}', [GoogleMapsController::class, 'showPlot'])->name('plot.show');
        Route::post('/plot', [GoogleMapsController::class, 'createPlot'])->name('plot.create');
        Route::patch('/plot/{plot}/coordinates', [GoogleMapsController::class, 'updatePlotCoordinates'])->name('plot.coordinates');
        Route::get('/export', [GoogleMapsController::class, 'exportPlots'])->name('export');

        // New enhanced features
        Route::post('/plot/from-address', [GoogleMapsController::class, 'createPlotFromAddress'])->name('plot.from-address');
        Route::get('/macro-client/plots', [GoogleMapsController::class, 'getMacroClientPlots'])->name('macro-client.plots');

        // API endpoints
        Route::prefix('api')->name('api.')->group(function () {
            Route::get('/plots', [GoogleMapsController::class, 'getPlotsData'])->name('plots');
            Route::get('/nearby', [GoogleMapsController::class, 'getNearbyPlots'])->name('nearby');
        });
    });

    Route::prefix('plots')->name('plots.')->group(function () {
        // DataTables and Bulk Operations (must be before parameterized routes)
        Route::get('/datatable', [PlotsController::class, 'datatable'])->name('datatable');
        Route::post('/bulk', [PlotsController::class, 'bulkAction'])->name('bulk');

        // Basic CRUD Routes
        Route::get('/', [PlotsController::class, 'index'])->name('index');
        Route::get('/create', [PlotsController::class, 'create'])->name('create');
        Route::post('/', [PlotsController::class, 'store'])->name('store');
        Route::get('/{plot}', [PlotsController::class, 'show'])->name('show');
        Route::get('/{plot}/edit', [PlotsController::class, 'edit'])->name('edit');
        Route::put('/{plot}', [PlotsController::class, 'update'])->name('update');
        Route::delete('/{plot}', [PlotsController::class, 'destroy'])->name('destroy');
    });

    // Plot Groups Management
    Route::prefix('plot-groups')->name('plot-groups.')->group(function () {
        Route::get('/', [PlotGroupController::class, 'index'])->name('index');
        Route::post('/', [PlotGroupController::class, 'store'])->name('store');
        Route::put('/{plotGroup}', [PlotGroupController::class, 'update'])->name('update');
        Route::delete('/{plotGroup}', [PlotGroupController::class, 'destroy'])->name('destroy');

        // Plot management within groups
        Route::post('/{plotGroup}/plots', [PlotGroupController::class, 'addPlot'])->name('add-plot');
        Route::delete('/{plotGroup}/plots/{plot}', [PlotGroupController::class, 'removePlot'])->name('remove-plot');

        // API endpoints
        Route::prefix('api')->name('api.')->group(function () {
            Route::get('/macro-client-plots', [PlotGroupController::class, 'getMacroClientPlots'])->name('macro-client-plots');
            Route::get('/{plotGroup}/plots', [PlotGroupController::class, 'getGroupPlots'])->name('group-plots');
        });
    });

    // Consultant Management
    Route::prefix('consultants')->name('consultants.')->group(function () {
        // Basic CRUD Routes
        Route::get('/', [ConsultantController::class, 'index'])->name('index');
        Route::get('/create', [ConsultantController::class, 'create'])->name('create');
        Route::post('/', [ConsultantController::class, 'store'])->name('store');

        // File Management (non-parameterized routes)
        Route::get('/files/{file}/download', [ConsultantController::class, 'downloadFile'])->name('files.download');
        Route::delete('/files/{file}', [ConsultantController::class, 'deleteFile'])->name('files.delete');

        // Parameterized routes (must come LAST)
        Route::get('/{consultant}', [ConsultantController::class, 'show'])->name('show');
        Route::get('/{consultant}/edit', [ConsultantController::class, 'edit'])->name('edit');
        Route::put('/{consultant}', [ConsultantController::class, 'update'])->name('update');
        Route::delete('/{consultant}', [ConsultantController::class, 'destroy'])->name('destroy');

        // File Management (parameterized routes for specific consultant records)
        Route::post('/{consultant}/files', [ConsultantController::class, 'uploadFile'])->name('files.upload');
    });

    // Future Admin Modules (placeholders)

    // Analytics & Reports
    Route::prefix('analytics')->name('analytics.')->group(function () {
        Route::get('/', function () {
            return view('admin.analytics.index', [
                'title' => 'Analytics Dashboard',
                'message' => 'Analytics and reporting functionality will be implemented here.'
            ]);
        })->name('index');
    });

    // System Backup & Maintenance
    Route::prefix('maintenance')->name('maintenance.')->group(function () {
        Route::get('/', function () {
            return view('admin.maintenance.index', [
                'title' => 'System Maintenance',
                'message' => 'System maintenance and backup functionality will be implemented here.'
            ]);
        })->name('index');
    });

    // HB837 Import Configuration Management
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

    // API Management
    Route::prefix('api')->name('api.')->group(function () {
        Route::get('/', function () {
            return view('admin.api.index', [
                'title' => 'API Management',
                'message' => 'API key management and integration settings will be implemented here.'
            ]);
        })->name('index');
    });
});
