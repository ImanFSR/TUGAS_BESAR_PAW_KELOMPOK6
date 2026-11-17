<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('login'));

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {

    // ===== Owner + Staf =====
    Route::middleware('role:owner,staf')->group(function () {
        // TODO M3: PosController (index = layar kasir, store, show, process, complete, cancel)
        // TODO M3: StokHarianController (index, update)
        // Route::get('/pos', [PosController::class, 'index'])->name('pos.index');
        // PLACEHOLDER sementara (hapus saat PosController dibuat):
        Route::get('/pos', fn () => view('placeholder', ['title' => 'Kasir (POS)']))->name('pos.index');
    });

    // ===== Owner saja =====
    Route::middleware('role:owner')->group(function () {
        // TODO M3: DashboardController, MenuController, CategoryController, UserController, ReportController
        // Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        // PLACEHOLDER sementara (hapus saat DashboardController dibuat):
        Route::get('/dashboard', fn () => view('placeholder', ['title' => 'Dashboard Owner']))->name('dashboard');
        // Route::resource('menus', MenuController::class);
        // Route::resource('categories', CategoryController::class);
        // Route::resource('users', UserController::class);
        // Route::get('/laporan', [ReportController::class, 'index'])->name('reports.index');
        // Route::get('/laporan/csv', [ReportController::class, 'csv'])->name('reports.csv');
        // Route::get('/laporan/pdf', [ReportController::class, 'pdf'])->name('reports.pdf');
    });
});
