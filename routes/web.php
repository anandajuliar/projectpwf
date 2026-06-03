<?php
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/login');
});

Route::get('/login', function () {
    return view('auth.login');
});

// Update rute dashboard ke file yang baru kita bikin
Route::get('/dashboard', function () {
    return view('dashboard');
});

// Bikin rute sementara buat Gudang biar nggak 404
Route::get('/products', function () {
    return view('products.index');
})->name('products.index');

// Alias /gudang → sama dengan /products
Route::get('/gudang', function () {
    return view('products.index');
})->name('gudang');

// Rute untuk melihat halaman Dapur Produksi (Resep)
Route::get('/dapur', function () {
    return view('dapur.index');
})->name('dapur.index');

Route::get('/users', function () { return view('users.index'); })->name('users.index');