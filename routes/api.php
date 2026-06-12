<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RecipeController;
use App\Http\Controllers\StockLogController;
use App\Http\Controllers\UserController;

// Import Controller untuk Tugas Kampus
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductApiController;

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
    Route::get('/auth/me',     [AuthController::class, 'me'])->name('auth.me');

    // Register user baru: hanya admin
    Route::post('/auth/register', [AuthController::class, 'register'])
        ->middleware('admin')
        ->name('auth.register');
});

// =====================================================================
// PRODUCT / BAHAN BAKU ROUTES (BakeLab Asli)
// =====================================================================

Route::middleware('auth:sanctum')->group(function () {

    // Ringkasan stok untuk dashboard (harus sebelum {product} agar tidak bentrok)
    Route::get('/products/summary', [ProductController::class, 'summary'])->name('products.summary');

    // Tersedia untuk semua role (chef & admin)
    Route::get('/products',         [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');

    // Riwayat log stok per produk (chef & admin)
    Route::get('/products/{product}/logs', [ProductController::class, 'logs'])->name('products.logs');

    // Potong stok manual (chef & admin)
    Route::post('/products/{product}/reduce', [ProductController::class, 'reduceStock'])->name('products.reduce');

    // Tambah stok / restock: hanya admin
    Route::post('/products/{product}/add', [ProductController::class, 'addStock'])
        ->middleware('admin')
        ->name('products.add');

    // Manajemen produk: hanya admin
    Route::middleware('admin')->group(function () {
        Route::post('/products',           [ProductController::class, 'store'])->name('products.store');
        Route::put('/products/{product}',  [ProductController::class, 'update'])->name('products.update');
        Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
    });
});

// =====================================================================
// RECIPE / RESEP ROUTES
// =====================================================================

Route::middleware('auth:sanctum')->group(function () {

    // Melihat resep: semua role
    Route::get('/recipes',          [RecipeController::class, 'index'])->name('recipes.index');
    Route::get('/recipes/{recipe}', [RecipeController::class, 'show'])->name('recipes.show');

    // Eksekusi resep — potong stok otomatis semua bahan (chef & admin)
    Route::post('/recipes/{recipe}/execute', [RecipeController::class, 'execute'])->name('recipes.execute');

    // Manajemen resep: hanya admin
    Route::middleware('admin')->group(function () {
        Route::post('/recipes',            [RecipeController::class, 'store'])->name('recipes.store');
        Route::put('/recipes/{recipe}',    [RecipeController::class, 'update'])->name('recipes.update');
        Route::delete('/recipes/{recipe}', [RecipeController::class, 'destroy'])->name('recipes.destroy');
    });
});

// =====================================================================
// STOCK LOG ROUTES
// =====================================================================

Route::middleware('auth:sanctum')->group(function () {
    // Semua log: chef & admin bisa melihat
    Route::get('/stock-logs',          [StockLogController::class, 'index'])->name('stock-logs.index');
    Route::get('/stock-logs/{stockLog}', [StockLogController::class, 'show'])->name('stock-logs.show');
});

// =====================================================================
// USER MANAGEMENT ROUTES (Admin only)
// =====================================================================

Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::post('/users',                      [UserController::class, 'store'])->name('users.store');
    Route::get('/users',                       [UserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}',                [UserController::class, 'show'])->name('users.show');
    Route::put('/users/{user}',                [UserController::class, 'update'])->name('users.update');
    Route::patch('/users/{user}/toggle-active', [UserController::class, 'toggleActive'])->name('users.toggle-active');
    Route::delete('/users/{user}',             [UserController::class, 'destroy'])->name('users.destroy');
});
