<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;

Route::prefix('users')->name('users.')->group(function () {
    Route::get('/', [UserController::class, 'index'])->name('index');
    Route::get('/create', [UserController::class, 'create'])->name('create');
    Route::post('/', [UserController::class, 'store'])->name('store');
    Route::get('/{user}', [UserController::class, 'show'])->name('show');
    Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
    Route::put('/{user}', [UserController::class, 'update'])->name('update');
    Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');

    // AJAX and Data Routes
    Route::get('/data/table', [UserController::class, 'getData'])->name('data');

    // Additional User Actions
    Route::patch('/{user}/reset-password', [UserController::class, 'resetPassword'])->name('reset-password');
    Route::patch('/{user}/toggle-email-verification', [UserController::class, 'toggleEmailVerification'])->name('toggle-email-verification');
    Route::patch('/{user}/disable-two-factor', [UserController::class, 'disableTwoFactor'])->name('disable-two-factor');
    Route::post('/bulk-action', [UserController::class, 'bulkAction'])->name('bulk-action');
});