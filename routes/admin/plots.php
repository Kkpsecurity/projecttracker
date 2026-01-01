<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\PlotsController;
use App\Http\Controllers\Admin\PlotGroupController;
use App\Http\Controllers\Admin\PlotManagementController;
use App\Http\Controllers\Admin\MacroClientController;

/*
|--------------------------------------------------------------------------
| Plots & Property Management Routes
|--------------------------------------------------------------------------
|
| Routes for property plots management, including individual plots,
| plot groups, macro client assignments, and bulk operations
|
*/

// Individual Plots Management
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

// Plot Management - Client Interface
Route::prefix('plot-clients')->name('plot-clients.')->group(function () {
    Route::get('/', [PlotManagementController::class, 'clients'])->name('index');
    Route::get('/data', [PlotManagementController::class, 'clientsData'])->name('data');
    Route::get('/{clientName}', [PlotManagementController::class, 'showClient'])->name('show');
    Route::post('/assign-plots', [PlotManagementController::class, 'assignPlotsToClient'])->name('assign-plots');
    Route::post('/create-from-groups', [PlotManagementController::class, 'createClientFromGroups'])->name('create-from-groups');

    // API endpoints for AJAX
    Route::prefix('api')->name('api.')->group(function () {
        Route::get('/clients', [PlotManagementController::class, 'getClients'])->name('clients');
        Route::get('/client-details', [PlotManagementController::class, 'getClientDetails'])->name('client-details');
        Route::get('/export', [PlotManagementController::class, 'exportClientData'])->name('export');
        Route::get('/report', [PlotManagementController::class, 'generateClientReport'])->name('report');
    });
});

// Plot Management - Groups Interface
Route::prefix('plot-groups')->name('plot-groups-management.')->group(function () {
    Route::get('/', [PlotManagementController::class, 'groups'])->name('index');
    Route::get('/data', [PlotManagementController::class, 'groupsData'])->name('data');
    Route::post('/assign-to-client', [PlotManagementController::class, 'assignGroupsToClient'])->name('assign-to-client');
    Route::post('/bulk-assign', [PlotManagementController::class, 'bulkAssignGroups'])->name('bulk-assign');
    Route::post('/create-group', [PlotManagementController::class, 'createGroup'])->name('create-group');
    Route::post('/create-client', [PlotManagementController::class, 'createClientFromPlots'])->name('create-client');
    Route::post('/create-plot-and-assign', [PlotManagementController::class, 'createPlotAndAssign'])->name('create-plot-and-assign');

    // API endpoints for AJAX
    Route::prefix('api')->name('api.')->group(function () {
        Route::get('/groups', [PlotManagementController::class, 'getGroups'])->name('groups');
        Route::get('/clients', [PlotManagementController::class, 'getClients'])->name('clients');
    });
});

// Plot Groups Settings (Create/Edit Groups)
Route::prefix('plot-group-settings')->name('plot-groups.')->group(function () {
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

// Unified Plot Management
Route::prefix('plot-management')->name('plot-management.')->group(function () {
    Route::get('/', [PlotManagementController::class, 'index'])->name('index');
    Route::post('/assign-to-client', [PlotManagementController::class, 'assignToClient'])->name('assign-to-client');
    Route::post('/move-plots', [PlotManagementController::class, 'movePlots'])->name('move-plots');
    Route::post('/create-group', [PlotManagementController::class, 'createGroup'])->name('create-group');
    Route::get('/data', [PlotManagementController::class, 'getData'])->name('data');
});

// Macro Client Management
Route::prefix('macro-clients')->name('macro-clients.')->group(function () {
    Route::get('/', [MacroClientController::class, 'index'])->name('index');
    Route::get('/client-details', [MacroClientController::class, 'getClientDetails'])->name('client-details');
    Route::get('/properties-data', [MacroClientController::class, 'getClientPropertiesData'])->name('properties-data');
    Route::get('/plots', [MacroClientController::class, 'getMacroClientPlots'])->name('plots');
});
