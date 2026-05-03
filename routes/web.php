<?php

use App\Http\Controllers\BerandaController;
use App\Http\Controllers\PengaturanController;
use App\Http\Controllers\AiSuggestionController;
use App\Http\Controllers\RencanaController;
use App\Http\Controllers\Admin\AdminDashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => redirect()->route('beranda'));

// ── User routes ──────────────────────────────────────────────────────────────
Route::middleware(['auth', 'maintenance'])->group(function () {
    Route::get('/beranda', [BerandaController::class, 'index'])->name('beranda');

    Route::post('/transaksi',              [BerandaController::class, 'store'])->name('transaksi.store');
    Route::put('/transaksi/{transaksi}',   [BerandaController::class, 'update'])->name('transaksi.update');
    Route::delete('/transaksi/{transaksi}',[BerandaController::class, 'destroy'])->name('transaksi.destroy');
    Route::get('/transaksi/export/{type}', [BerandaController::class, 'export'])->name('transaksi.export');

    // Settings
    Route::get('/pengaturan',                    [PengaturanController::class, 'index'])->name('pengaturan');
    Route::post('/pengaturan/profile',           [PengaturanController::class, 'updateProfile'])->name('pengaturan.profile');
    Route::post('/pengaturan/password',          [PengaturanController::class, 'changePassword'])->name('pengaturan.password');
    Route::delete('/pengaturan/delete-account',  [PengaturanController::class, 'deleteAccount'])->name('pengaturan.delete');

    // AI Suggestion
    Route::get('/ai/suggest', [AiSuggestionController::class, 'suggest'])->name('ai.suggest');

    // Rencana Keuangan (Budget & Goals)
    Route::get('/rencana',                          [RencanaController::class, 'index'])->name('rencana');
    Route::post('/rencana/budget',                  [RencanaController::class, 'storeBudget'])->name('rencana.budget.store');
    Route::post('/rencana/goal',                    [RencanaController::class, 'storeGoal'])->name('rencana.goal.store');
    Route::post('/rencana/{rencana}/add-dana',      [RencanaController::class, 'addDana'])->name('rencana.add-dana');
    Route::patch('/rencana/{rencana}/tingkatkan',   [RencanaController::class, 'tingkatkan'])->name('rencana.tingkatkan');
    Route::delete('/rencana/{rencana}',             [RencanaController::class, 'destroy'])->name('rencana.destroy');
});

// ── Admin routes ─────────────────────────────────────────────────────────────
Route::prefix('admin')->name('admin.')->group(function () {
    // Guest-only
    Route::middleware('guest')->group(function () {
        Route::get('/login',  [\App\Http\Controllers\Auth\AdminAuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [\App\Http\Controllers\Auth\AdminAuthController::class, 'login']);
    });

    Route::post('/logout', [\App\Http\Controllers\Auth\AdminAuthController::class, 'logout'])->name('logout');

    // Protected
    Route::middleware('admin')->group(function () {
        Route::get('/',                                    [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::post('/users',                              [AdminDashboardController::class, 'addUser'])->name('users.add');
        Route::post('/users/reset-password',               [AdminDashboardController::class, 'resetPassword'])->name('users.reset-password');
        Route::delete('/users/{user_id}',                  [AdminDashboardController::class, 'deleteUser'])->name('users.delete');
        Route::delete('/transactions/{transaction_id}',    [AdminDashboardController::class, 'deleteTransaction'])->name('transactions.delete');
        Route::post('/config',                             [AdminDashboardController::class, 'updateConfig'])->name('config.update');
        Route::get('/ajax/user-transactions',              [AdminDashboardController::class, 'userTransactions'])->name('ajax.user-transactions');
    });
});

// ── Auth ─────────────────────────────────────────────────────────────────────
require __DIR__.'/auth.php';
