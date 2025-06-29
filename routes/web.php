<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\ConsultantController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HB837Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Redirect root to login
Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication routes
Auth::routes(['register' => false]); // Disable registration for security

// Protected routes
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/home', [DashboardController::class, 'index'])->name('home');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/analytics', [DashboardController::class, 'analytics'])->name('dashboard.analytics');

    // Consultants management
    Route::resource('consultants', ConsultantController::class);
    Route::get('consultants/{consultant}/files', [ConsultantController::class, 'files'])->name('consultants.files');
    Route::post('consultants/{consultant}/files', [ConsultantController::class, 'uploadFile'])->name('consultants.files.upload');
    Route::get('consultants/files/{file}/download', [ConsultantController::class, 'downloadFile'])->name('consultants.files.download');
    Route::delete('consultants/files/{file}', [ConsultantController::class, 'deleteFile'])->name('consultants.files.delete');

    // HB837 Projects management
    Route::resource('hb837', HB837Controller::class);
    Route::get('hb837/{hb837}/files', [HB837Controller::class, 'files'])->name('hb837.files');
    Route::post('hb837/{hb837}/files', [HB837Controller::class, 'uploadFile'])->name('hb837.files.upload');
    Route::get('hb837/files/{file}/download', [HB837Controller::class, 'downloadFile'])->name('hb837.files.download');
    Route::delete('hb837/files/{file}', [HB837Controller::class, 'deleteFile'])->name('hb837.files.delete');

    // Quick actions
    Route::patch('hb837/{hb837}/status', [HB837Controller::class, 'updateStatus'])->name('hb837.status');
    Route::patch('hb837/{hb837}/priority', [HB837Controller::class, 'updatePriority'])->name('hb837.priority');

    // ProTrack - Enhanced Project Management System
    Route::prefix('projects')->name('projects.')->group(function () {
        // Main project routes (placeholder for Phase 2)
        Route::get('/', function () {
            return view('projects.index', [
                'projects' => collect([]), // Empty for now
                'stats' => [
                    'total_projects' => 0,
                    'active_projects' => 0,
                    'completed_projects' => 0,
                    'overdue_projects' => 0,
                ]
            ]);
        })->name('index');

        Route::get('/create', function () {
            return view('projects.create');
        })->name('create');

        // TODO: Add full project management routes in Phase 2
        // Route::resource('projects', ProjectController::class);
        // Route::get('projects/data', [ProjectController::class, 'getData'])->name('projects.data');
    });

    // Account Management routes
    Route::middleware(['auth'])->prefix('account')->name('account.')->group(function () {
        // Account Dashboard
        Route::get('/dashboard', [App\Http\Controllers\AccountController::class, 'dashboard'])->name('dashboard');
        Route::get('/settings', [App\Http\Controllers\AccountController::class, 'settings'])->name('settings');
        Route::get('/security', [App\Http\Controllers\AccountController::class, 'security'])->name('security');

        // Profile Management
        Route::patch('/profile/name', [App\Http\Controllers\AccountController::class, 'updateName'])->name('update.name');
        Route::patch('/profile/email', [App\Http\Controllers\AccountController::class, 'updateEmail'])->name('update.email');

        // Password Management
        Route::patch('/password', [App\Http\Controllers\AccountController::class, 'updatePassword'])->name('update.password');

        // Security Features
        Route::post('/two-factor/enable', [App\Http\Controllers\AccountController::class, 'enableTwoFactor'])->name('two-factor.enable');
        Route::delete('/two-factor/disable', [App\Http\Controllers\AccountController::class, 'disableTwoFactor'])->name('two-factor.disable');

        // Session Management
        Route::get('/sessions', [App\Http\Controllers\AccountController::class, 'sessions'])->name('sessions');
        Route::delete('/sessions/{session}', [App\Http\Controllers\AccountController::class, 'revokeSession'])->name('sessions.revoke');
        Route::delete('/sessions/others', [App\Http\Controllers\AccountController::class, 'revokeOtherSessions'])->name('sessions.revoke-others');

        // Account Deletion
        Route::delete('/delete', [App\Http\Controllers\AccountController::class, 'deleteAccount'])->name('delete');
    });
});

// API routes for AJAX calls
Route::middleware(['auth'])->prefix('api')->group(function () {
    Route::get('consultants/search', [ConsultantController::class, 'search'])->name('api.consultants.search');
    Route::get('hb837/search', [HB837Controller::class, 'search'])->name('api.hb837.search');
    Route::get('dashboard/stats', [DashboardController::class, 'getStats'])->name('api.dashboard.stats');
});

// Admin Center Routes
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    // User Management Resource Routes
    Route::resource('users', \App\Http\Controllers\Admin\UserController::class);
    Route::get('users/data', [\App\Http\Controllers\Admin\UserController::class, 'getData'])->name('users.data');

    // Additional User Management Actions
    Route::patch('users/{user}/reset-password', [\App\Http\Controllers\Admin\UserController::class, 'resetPassword'])->name('users.reset-password');
    Route::patch('users/{user}/toggle-email-verification', [\App\Http\Controllers\Admin\UserController::class, 'toggleEmailVerification'])->name('users.toggle-email-verification');
    Route::patch('users/{user}/disable-two-factor', [\App\Http\Controllers\Admin\UserController::class, 'disableTwoFactor'])->name('users.disable-two-factor');
    Route::post('users/bulk-action', [\App\Http\Controllers\Admin\UserController::class, 'bulkAction'])->name('users.bulk-action');

    // System Settings
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\SettingsController::class, 'index'])->name('index');
        Route::put('/', [\App\Http\Controllers\Admin\SettingsController::class, 'update'])->name('update');
        Route::post('/reset', [\App\Http\Controllers\Admin\SettingsController::class, 'reset'])->name('reset');
        Route::get('/toggle-maintenance', [\App\Http\Controllers\Admin\SettingsController::class, 'toggleMaintenance'])->name('toggle-maintenance');
    });

    // Activity Logs (placeholder)
    Route::get('logs', function () {
        return view('admin.logs.index');
    })->name('logs.index');
});
