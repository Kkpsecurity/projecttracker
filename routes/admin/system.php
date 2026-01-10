<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| System Management Routes
|--------------------------------------------------------------------------
|
| Routes for system maintenance, API management, and other administrative
| functions that don't fit into specific modules
|
*/

// System Backup & Maintenance
Route::prefix('maintenance')->name('maintenance.')->group(function () {
    Route::get('/', function () {
        return view('admin.maintenance.index', [
            'title' => 'System Maintenance',
            'message' => 'System maintenance and backup functionality will be implemented here.'
        ]);
    })->name('index');

    // Future maintenance routes
    Route::get('/backup', function () {
        return view('admin.maintenance.backup', [
            'title' => 'System Backup',
            'message' => 'Database backup and restore functionality.'
        ]);
    })->name('backup');

    Route::get('/cache', function () {
        return view('admin.maintenance.cache', [
            'title' => 'Cache Management',
            'message' => 'Application cache management and optimization.'
        ]);
    })->name('cache');

    Route::get('/database', function () {
        return view('admin.maintenance.database', [
            'title' => 'Database Maintenance',
            'message' => 'Database optimization and health checks.'
        ]);
    })->name('database');
});

// API Management
Route::prefix('api')->name('api.')->group(function () {
    Route::get('/', function () {
        return view('admin.api.index', [
            'title' => 'API Management',
            'message' => 'API key management and integration settings will be implemented here.'
        ]);
    })->name('index');

    // Future API management routes
    Route::get('/keys', function () {
        return view('admin.api.keys', [
            'title' => 'API Keys',
            'message' => 'Manage API keys and access tokens.'
        ]);
    })->name('keys');

    Route::get('/endpoints', function () {
        return view('admin.api.endpoints', [
            'title' => 'API Endpoints',
            'message' => 'View and configure API endpoints.'
        ]);
    })->name('endpoints');

    Route::get('/documentation', function () {
        return view('admin.api.documentation', [
            'title' => 'API Documentation',
            'message' => 'API documentation and usage guides.'
        ]);
    })->name('documentation');
});
