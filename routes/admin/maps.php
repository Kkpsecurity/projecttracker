<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\GoogleMapsController;

/*
|--------------------------------------------------------------------------
| Google Maps & Mapping Routes
|--------------------------------------------------------------------------
|
| Routes for Google Maps integration, plot visualization, and location
| management features
|
*/

Route::prefix('maps')->name('maps.')->group(function () {
    Route::get('/', [GoogleMapsController::class, 'index'])->name('index');
    Route::get('/plot/{plot}', [GoogleMapsController::class, 'showPlot'])->name('plot.show');
    Route::post('/plot', [GoogleMapsController::class, 'createPlot'])->name('plot.create');
    Route::patch('/plot/{plot}/coordinates', [GoogleMapsController::class, 'updatePlotCoordinates'])->name('plot.coordinates');
    Route::get('/export', [GoogleMapsController::class, 'exportPlots'])->name('export');

    // Enhanced features
    Route::post('/plot/from-address', [GoogleMapsController::class, 'createPlotFromAddress'])->name('plot.from-address');
    Route::get('/macro-client/plots', [GoogleMapsController::class, 'getMacroClientPlots'])->name('macro-client.plots');
    Route::get('/plot-group/plots', [GoogleMapsController::class, 'getPlotGroupPlots'])->name('plot-group.plots');
    Route::post('/plot-group/create-plot', [GoogleMapsController::class, 'createPlotInGroup'])->name('plot-group.create-plot');

    // API endpoints
    Route::prefix('api')->name('api.')->group(function () {
        Route::get('/plots', [GoogleMapsController::class, 'getPlotsData'])->name('plots');
        Route::get('/nearby', [GoogleMapsController::class, 'getNearbyPlots'])->name('nearby');
    });
});
