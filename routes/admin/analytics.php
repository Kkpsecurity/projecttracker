<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AnalyticsController;

Route::prefix('analytics')->name('analytics.')->group(function () {
    Route::get('/', [AnalyticsController::class, 'index'])->name('index');
    Route::get('/project-trends', [AnalyticsController::class, 'getProjectTrendsData'])->name('project-trends');
    Route::get('/consultant-metrics', [AnalyticsController::class, 'getConsultantMetrics'])->name('consultant-metrics');
    Route::get('/realtime-stats', [AnalyticsController::class, 'realtimeStats'])->name('realtime-stats');
    Route::get('/export', [AnalyticsController::class, 'exportData'])->name('export');
    Route::get('/filtered-data', [AnalyticsController::class, 'getFilteredData'])->name('filtered-data');
    Route::get('/benchmarks', [AnalyticsController::class, 'getPerformanceBenchmarks'])->name('benchmarks');
});
