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

    // Test routes for UI development
    Route::get('/test/text-shadow', function () {
        return view('test.text-shadow-test');
    })->name('test.text-shadow');

    // Phase 1.5 Dark Mode Testing Route
    Route::get('/test/text-shadow-dark', function () {
        return view('test.text-shadow-dark-test');
    })->name('test.text-shadow-dark');

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

    // Help Center Routes
    Route::prefix('help')->name('help.')->group(function () {
        Route::get('/', [\App\Http\Controllers\HelpController::class, 'index'])->name('index');
        Route::get('/getting-started', [\App\Http\Controllers\HelpController::class, 'gettingStarted'])->name('getting-started');
        Route::get('/user-guide', [\App\Http\Controllers\HelpController::class, 'userGuide'])->name('user-guide');
        Route::get('/faq', [\App\Http\Controllers\HelpController::class, 'faq'])->name('faq');
        Route::get('/contact', [\App\Http\Controllers\HelpController::class, 'contact'])->name('contact');
        Route::get('/documentation', [\App\Http\Controllers\HelpController::class, 'documentation'])->name('documentation');
    });
});

// API routes for AJAX calls
Route::middleware(['auth'])->prefix('api')->group(function () {
    // Legacy API routes removed - now handled in routes/admin.php to avoid conflicts

    // Placeholder for other API routes (to be implemented)
    // Route::get('consultants/search', [ConsultantController::class, 'search'])->name('api.consultants.search');
    // Route::get('dashboard/stats', [DashboardController::class, 'getStats'])->name('api.dashboard.stats');
});

