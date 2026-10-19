<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => redirect()->route('login'));

// Auth
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    // Users
    Route::middleware('permission:manage-users')->group(function () {
        Route::resource('users', UserController::class);
    });

    // Kategori
    Route::middleware('permission:manage-categories')->group(function () {
        Route::resource('categories', CategoryController::class);
    });

    // Produk
    Route::middleware('permission:manage-products')->group(function () {
        Route::resource('products', ProductController::class);
    });

    // Stok
    Route::middleware('permission:manage-stock')->group(function () {
        Route::get('/stocks', [StockController::class, 'index'])->name('stocks.index');
        Route::post('/stocks', [StockController::class, 'store'])->name('stocks.store');
        Route::delete('/stocks/{stock}', [StockController::class, 'destroy'])->name('stocks.destroy');
    });

    // Kasir / POS
    Route::middleware('permission:create-transaction')->group(function () {
        Route::get('/pos', [PosController::class, 'index'])->name('pos.index');
        Route::post('/pos', [PosController::class, 'store'])->name('pos.store');
        Route::get('/pos/receipt/{transaction}', [PosController::class, 'receipt'])->name('pos.receipt');
    });

    // Laporan
    Route::middleware('permission:view-reports')->group(function () {
        Route::get('/reports/sales', [ReportController::class, 'sales'])->name('reports.sales');
        Route::get('/reports/sales/export', [ReportController::class, 'exportSales'])->name('reports.sales.export');
        Route::get('/reports/stock', [ReportController::class, 'stock'])->name('reports.stock');
    });
});