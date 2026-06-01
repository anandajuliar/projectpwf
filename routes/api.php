<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — Proyek PWF
|--------------------------------------------------------------------------
|
| Semua route di sini diawali dengan prefix /api secara otomatis
| oleh Laravel melalui konfigurasi di bootstrap/app.php.
|
*/

// =====================================================================
// AUTH ROUTES
// =====================================================================

// Public: Login tidak memerlukan token
Route::post('/auth/login', [AuthController::class, 'login'])->name('auth.login');

// Protected: Harus login terlebih dahulu
Route::middleware('auth:sanctum')->group(function () {
    // Logout & profil (semua role)
    Route::post('/auth/logout', [AuthController::class, 'logout'])->name('auth.logout');
    Route::get('/auth/me', [AuthController::class, 'me'])->name('auth.me');

    // Register chef baru: hanya admin
    Route::post('/auth/register', [AuthController::class, 'register'])
        ->middleware('admin')
        ->name('auth.register');
});

// =====================================================================
// PRODUCT / BAHAN BAKU ROUTES
// =====================================================================

Route::middleware('auth:sanctum')->group(function () {
    // Tersedia untuk semua role (chef & admin)
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');

    // Endpoint khusus potong stok (chef & admin)
    Route::post('/products/{product}/reduce', [ProductController::class, 'reduceStock'])->name('products.reduce');

    // Manajemen produk: hanya admin
    Route::middleware('admin')->group(function () {
        Route::post('/products', [ProductController::class, 'store'])->name('products.store');
        Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
        Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
    });
});
