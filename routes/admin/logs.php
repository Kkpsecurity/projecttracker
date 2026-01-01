<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\LogsController;

/*
|--------------------------------------------------------------------------
| Admin Logs Routes
|--------------------------------------------------------------------------
|
| Routes for system activity logs management and monitoring
|
*/

Route::prefix('logs')->name('logs.')->group(function () {
    Route::get('/', [LogsController::class, 'index'])->name('index');
    Route::get('/data', [LogsController::class, 'getData'])->name('data');
    Route::post('/clear-old', [LogsController::class, 'clearOldLogs'])->name('clear-old');
    Route::get('/export', [LogsController::class, 'export'])->name('export');

    // Log Filtering and Search
    Route::get('/filter', [LogsController::class, 'filter'])->name('filter');
    Route::get('/search', [LogsController::class, 'search'])->name('search');
});
