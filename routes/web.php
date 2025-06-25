<?php

use Maatwebsite\Excel\Row;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\HB837\HB837Controller;
use App\Http\Controllers\Admin\Users\UserController;
use App\Http\Controllers\Admin\Owners\OwnerController;
use App\Http\Controllers\Admin\Plots\PlotController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Admin\HB837\GoogleMapsController;
use App\Http\Controllers\Admin\Services\BackupDBController;
use App\Http\Controllers\Admin\Consultants\ConsultantController;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

// Redirect root and /home to admin login or dashboard
Route::redirect('home', 'admin');
Route::get('/', function () {
    return redirect()->route('admin.login');
});

Route::get('login', function () {
    return redirect()->route('admin.login');
})->name('login');

/**
 * Public Admin Routes (Login, Password Reset)
 */
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login']);
    Route::post('logout', [LoginController::class, 'logout'])->name('logout');

    // Password Reset Routes
    Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');
});

/**
 * Protected Admin Routes (Requires Authentication)
 */
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/', function () {
        return redirect()->route('admin.home.index');
    })->name('dashboard');

    // ProTrack Dashboard Routes
    Route::prefix('home')->name('home.')->group(function () {
        Route::get('/', [HomeController::class, 'index'])->name('index');
        Route::get('/tabs/{tab}', [HomeController::class, 'index'])->name('tabs');
        Route::post('/process_new', [HomeController::class, 'process'])->name('process_new');
        Route::get('/detail/{id}', [HomeController::class, 'detail'])->name('detail');
        Route::post('/detail/update', [HomeController::class, 'detailProcess'])->name('detail.update');
        Route::get('/detail/delete/{id}', [HomeController::class, 'destroy'])->name('detail.delete');
        Route::get('/detach/{file}/{id}', [HomeController::class, 'detachFile'])->name('detach.file');
        Route::get('/download/{filename}', [HomeController::class, 'downloadFile'])->name('download');
    });

    // Profile Routes
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('change_password', [HomeController::class, 'changePassword'])->name('change_password');
        Route::post('password/process', [HomeController::class, 'passwordProcess'])->name('password.process');
    });

    Route::prefix('mapplots')->name('mapplots.')->group(function () {
        // Display maps view (with optional plot_id)
        Route::get('/{plot_id?}', [GoogleMapsController::class, 'mapPlots'])
            ->where('plot_id', '[0-9]+')
            ->name('index');

        // Store a new Plot
        Route::post('/store', [GoogleMapsController::class, 'store'])
            ->name('store');

        // Fetch addresses for a specific plot (Standardized with route param)
        Route::get('/plot-addresses/{plot_id}', [GoogleMapsController::class, 'getPlotAddresses'])
            ->where('plot_id', '[0-9]+')
            ->name('plot_addresses');

        // Add a new route for loading addresses (returns JSON)
        Route::get('/load_addresses', [GoogleMapsController::class, 'loadAddresses'])
            ->name('load_addresses');

        Route::post('/add-address', [GoogleMapsController::class, 'addAddressToPlot'])
            ->name('address.store');

        // Delete a specific plot address (Standardized to 'plot-address/{id}')
        Route::post('/plot-address/delete/{plotAddressId}', [GoogleMapsController::class, 'deleteAddressFromPlot'])
            ->where('plotAddressId', '[0-9]+')
            ->name('delete.address');

        // Delete a plot and its addresses (Ensured clean and structured deletion)
        Route::delete('/{plot}', [GoogleMapsController::class, 'deletePlotAndAddresses'])
            ->where('plot', '[0-9]+')
            ->name('destroy');

        Route::get('/macro-client-properties/{macro_client}', [GoogleMapsController::class, 'getMacroClientProperties'])
            ->where('macro_client', '.*')
            ->name('macro_client_properties');

    });


    // HB837 Routes
    Route::prefix('hb837')->name('hb837.')->group(function () {
        Route::get('/', [HB837Controller::class, 'index'])->name('index');
        Route::get('/tabs/{tab}', [HB837Controller::class, 'index'])->name('tabs');
        Route::get('/create', [HB837Controller::class, 'create'])->name('create');
        Route::post('/store', [HB837Controller::class, 'store'])->name('store');
        Route::delete('/destroy/{id}', [HB837Controller::class, 'deleteRecord'])->name('destroy');
        Route::get('/report/{id}', [HB837Controller::class, 'report'])->name('report');
        Route::get('/{id}/edit/{tab_id?}', [HB837Controller::class, 'edit'])->name('edit');
        Route::put('/update/{id}/{tab_id}', [HB837Controller::class, 'update'])->name('update');
    });

    Route::prefix('hb837/backup')->name('hb837.backup.')->group(function () {
        Route::get('/', [BackupDBController::class, 'index'])->name('dashboard');
        Route::post('/save', [BackupDBController::class, 'save'])->name('save');
        Route::post('/delete-file/{id}', [BackupDBController::class, 'deleteFile'])->name('delete_file');
        // 🔧 Cron toggle route
        Route::post('/toggle-cron', [BackupDBController::class, 'toggleCron'])->name('toggle_cron');
        Route::get('/download/{filename}', [BackupDBController::class, 'download'])->name('download');
        Route::post('/import', [BackupDBController::class, 'import'])->name('import');
        Route::post('/restore/{uuid}', [BackupDBController::class, 'restore'])->name('restore');

        // Test route for dashboard data (development only)
        Route::get('/test-dashboard', [BackupDBController::class, 'testDashboard'])->name('test_dashboard');
        Route::get('/test-validation', [BackupDBController::class, 'testBackupValidation'])->name('test_validation');
        Route::get('/test-backup-save', [BackupDBController::class, 'testBackupSave'])->name('test_backup_save');
        Route::get('/test-backup-logic', [BackupDBController::class, 'testBackupLogic'])->name('test_backup_logic');
        Route::get('/test-import-logic', [BackupDBController::class, 'testImportLogic'])->name('test_import_logic');



        Route::post('/export', [HB837Controller::class, 'export'])->name('export');
        Route::get('/direct-export', [HB837Controller::class, 'direct_export'])->name('direct_export');

        Route::get('/list/{status}', [HB837Controller::class, 'getTableList'])->name('getTableList');

        Route::post('/purge', [HB837Controller::class, 'purge'])->name('purge');


    });


  // User Management Routes
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::post('/store', [UserController::class, 'store'])->name('store');
        Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
        Route::post('/update/{user}', [UserController::class, 'update'])->name('update');
        Route::delete('/delete/{user}', [UserController::class, 'destroy'])->name('delete');
    });

    // Consultant Routes
    Route::prefix('consultants')->name('consultants.')->group(function () {
        Route::get('/', [ConsultantController::class, 'index'])->name('index');
        Route::get('/create', [ConsultantController::class, 'create'])->name('create');
        Route::post('/store', [ConsultantController::class, 'store'])->name('store');
        Route::get('/export', [ConsultantController::class, 'export'])->name('export');
        Route::get('/edit/{id}', [ConsultantController::class, 'edit'])->name('edit');
        Route::post('/update/{id}', [ConsultantController::class, 'update'])->name('update');
        Route::delete('/destroy/{consultant}', [ConsultantController::class, 'destroy'])->name('destroy');
        Route::get('/get/{id}', [ConsultantController::class, 'consultant_detail'])->name('get');

        Route::post('/detach/{id}', [ConsultantController::class, 'detachConsultants'])->name('detach');
    });

    // Owner Routes
    Route::prefix('owners')->name('owners.')->group(function () {
        Route::get('/', [OwnerController::class, 'index'])->name('index');
        Route::get('/create', [OwnerController::class, 'create'])->name('create');
        Route::post('/store', [OwnerController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [OwnerController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [OwnerController::class, 'update'])->name('update');
        Route::delete('/destroy/{owner}', [OwnerController::class, 'destroy'])->name('destroy');
        Route::get('/get/{id}', [OwnerController::class, 'owner_detail'])->name('get');
        // Export
        Route::get('/export', [OwnerController::class, 'export'])->name('export');

        Route::post('/detach/{id}', [OwnerController::class, 'detachOwners'])->name('detach');
    });
});
