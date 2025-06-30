<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\Admin\HB837\HB837Controller as AdminHB837Controller;
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
    // Dashboard routes (to be implemented with proper controllers)
    Route::get('/home', function () {
        return view('home');
    })->name('home');
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Legacy HB837 routes (redirected to admin)
    Route::redirect('/hb837', '/admin/hb837');

    // Placeholder for other modules (to be implemented in later phases)
    // TODO: Implement when controllers are ready:
    // Route::resource('consultants', ConsultantController::class);
    // Route::get('/dashboard/analytics', [DashboardController::class, 'analytics'])->name('dashboard.analytics');

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
    // HB837 API routes (use Admin controller)
    Route::get('hb837/search', [AdminHB837Controller::class, 'search'])->name('api.hb837.search');
    Route::get('hb837/data', [AdminHB837Controller::class, 'getData'])->name('api.hb837.data');
    Route::get('hb837/data/{tab}', [AdminHB837Controller::class, 'getTabData'])->name('api.hb837.data.tab');

    // Placeholder for other API routes (to be implemented)
    // Route::get('consultants/search', [ConsultantController::class, 'search'])->name('api.consultants.search');
    // Route::get('dashboard/stats', [DashboardController::class, 'getStats'])->name('api.dashboard.stats');
});

// Admin Center Routes
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    // Admin Dashboard (redirect to main dashboard)
    Route::get('/dashboard', function () {
        return redirect()->route('dashboard');
    })->name('dashboard');

    // User Management Resource Routes
    Route::resource('users', \App\Http\Controllers\Admin\UserController::class);
    Route::get('users/data', [\App\Http\Controllers\Admin\UserController::class, 'getData'])->name('users.data');

    // Additional User Management Actions
    Route::patch('users/{user}/reset-password', [\App\Http\Controllers\Admin\UserController::class, 'resetPassword'])->name('users.reset-password');
    Route::patch('users/{user}/toggle-email-verification', [\App\Http\Controllers\Admin\UserController::class, 'toggleEmailVerification'])->name('users.toggle-email-verification');
    Route::patch('users/{user}/disable-two-factor', [\App\Http\Controllers\Admin\UserController::class, 'disableTwoFactor'])->name('users.disable-two-factor');
    Route::post('users/bulk-action', [\App\Http\Controllers\Admin\UserController::class, 'bulkAction'])->name('users.bulk-action');

    // HB837 Management Resource Routes
    Route::resource('hb837', AdminHB837Controller::class);

    // HB837 DataTables AJAX Routes
    Route::get('hb837/data', [AdminHB837Controller::class, 'getData'])->name('hb837.data');
    Route::get('hb837/data/{tab}', [AdminHB837Controller::class, 'getTabData'])->name('hb837.data.tab');
    Route::get('hb837/stats', [AdminHB837Controller::class, 'getStats'])->name('hb837.stats');

    // HB837 Bulk Actions
    Route::post('hb837/bulk-action', [AdminHB837Controller::class, 'bulkAction'])->name('hb837.bulk-action');
    Route::patch('hb837/{hb837}/status', [AdminHB837Controller::class, 'updateStatus'])->name('hb837.status');
    Route::patch('hb837/{hb837}/priority', [AdminHB837Controller::class, 'updatePriority'])->name('hb837.priority');

    // HB837 Additional Actions
    Route::post('hb837/{hb837}/duplicate', [AdminHB837Controller::class, 'duplicate'])->name('hb837.duplicate');
    Route::delete('hb837/{hb837}/ajax', [AdminHB837Controller::class, 'ajaxDestroy'])->name('hb837.ajax-destroy');

    // HB837 Import/Export Routes
    Route::prefix('hb837')->group(function () {
        Route::get('/import', [AdminHB837Controller::class, 'showImport'])->name('hb837.import.show');
        Route::post('/import', [AdminHB837Controller::class, 'import'])->name('hb837.import');
        Route::post('/import/process', [AdminHB837Controller::class, 'processImport'])->name('hb837.import.process');
        Route::post('/import/compare', [AdminHB837Controller::class, 'compareImport'])->name('hb837.import.compare');
        Route::post('/import/three-phase', [AdminHB837Controller::class, 'executeThreePhaseImport'])->name('hb837.three-phase-import');
        Route::get('/import/three-phase', [AdminHB837Controller::class, 'showThreePhaseImport'])->name('hb837.three-phase-import.show');
        Route::get('/export', [AdminHB837Controller::class, 'export'])->name('hb837.export');
        Route::get('/export/{format}', [AdminHB837Controller::class, 'exportFormat'])->name('hb837.export.format');

        // File Management
        Route::get('/{hb837}/files', [AdminHB837Controller::class, 'files'])->name('hb837.files');
        Route::post('/{hb837}/files', [AdminHB837Controller::class, 'uploadFile'])->name('hb837.files.upload');
        Route::get('/files/{file}/download', [AdminHB837Controller::class, 'downloadFile'])->name('hb837.files.download');
        Route::delete('/files/{file}', [AdminHB837Controller::class, 'deleteFile'])->name('hb837.files.delete');
    });

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
