<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;

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

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {

    // Admin Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

    // Analytics Section
    require_once __DIR__ . '/admin/analytics.php';

    // Admin Center - User Management
    require_once __DIR__ . '/admin/users.php';

    // Admin Center - System Settings
    require_once __DIR__ . '/admin/settings.php';

    // Admin Center - Activity Logs
    require_once __DIR__ . '/admin/logs.php';

    // HB837 Management (Enhanced)
    require_once __DIR__ . '/admin/hb837.php';

    // Google Maps & Plots Management
    require_once __DIR__ . '/admin/maps.php';

    // Plots & Property Management
    require_once __DIR__ . '/admin/plots.php';

    // Consultant Management
    require_once __DIR__ . '/admin/consultants.php';

    // HB837 Import Configuration Management
    require_once __DIR__ . '/admin/import-config.php';

    // System Management (Maintenance, API, etc.)
    require_once __DIR__ . '/admin/system.php';
});
