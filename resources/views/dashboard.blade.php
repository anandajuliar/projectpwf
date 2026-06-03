@extends('layouts.app')
@section('title', 'Pusat Kendali Admin')

@section('content')
<div class="mb-8 border-b pb-4">
    <h2 class="text-3xl font-bold text-gray-800">Dashboard Manajemen</h2>
    <p class="text-gray-500 mt-1">Ringkasan aset dan peringatan gudang BakeLab.</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    
    <div class="bg-white p-6 rounded-xl shadow border-l-4 border-blue-500 flex items-center justify-between">
        <div>
            <p class="text-sm text-gray-500 font-semibold uppercase mb-1">Total Variasi Bahan</p>
            <h3 class="text-3xl font-bold text-gray-800">120 <span class="text-lg text-gray-400 font-normal">Item</span></h3>
        </div>
        <div class="p-3 bg-blue-100 rounded-full">
            <span class="text-2xl">📦</span>
        </div>
    </div>

    <div class="bg-white p-6 rounded-xl shadow border-l-4 border-green-500 flex items-center justify-between">
        <div>
            <p class="text-sm text-gray-500 font-semibold uppercase mb-1">Estimasi Nilai Aset Gudang</p>
            <h3 class="text-3xl font-bold text-gray-800">Rp 15.450.000</h3>
        </div>
        <div class="p-3 bg-green-100 rounded-full">
            <span class="text-2xl">💰</span>
        </div>
    </div>

    <div class="bg-white p-6 rounded-xl shadow border-l-4 border-red-500 flex items-center justify-between">
        <div>
            <p class="text-sm text-gray-500 font-semibold uppercase mb-1">Stok Butuh Perhatian</p>
            <h3 class="text-3xl font-bold text-red-600">3 <span class="text-lg text-red-400 font-normal">Item Kritis</span></h3>
        </div>
        <div class="p-3 bg-red-100 rounded-full">
            <span class="text-2xl">⚠️</span>
        </div>
    </div>
</div>

<div class="bg-white shadow rounded-xl overflow-hidden border border-gray-100">
    <div class="bg-red-50 border-b border-red-100 p-4">
        <h3 class="font-bold text-red-800 text-lg flex items-center">
            <span class="mr-2">🚨</span> Peringatan Stok & Kedaluwarsa
        </h3>
    </div>
    
    <div class="p-0">
        <div class="p-4 border-b hover:bg-gray-50 flex justify-between items-center">
            <div>
                <h4 class="font-bold text-gray-800">Tepung Terigu Protein Tinggi</h4>
                <p class="text-sm text-gray-500">Kategori: Flours</p>
            </div>
            <div class="text-right">
                <span class="bg-red-100 text-red-800 text-xs font-bold px-3 py-1 rounded-full">Sisa 50 gram</span>
                <p class="text-xs text-red-500 mt-1 font-semibold">Segera Restock!</p>
            </div>
        </div>

        <div class="p-4 border-b hover:bg-gray-50 flex justify-between items-center">
            <div>
                <h4 class="font-bold text-gray-800">Ragi Instan (Yeast)</h4>
                <p class="text-sm text-gray-500">Kategori: Agen Pengembang</p>
            </div>
            <div class="text-right">
                <span class="bg-amber-100 text-amber-800 text-xs font-bold px-3 py-1 rounded-full">Sisa 20 Sachet</span>
                <p class="text-xs text-amber-600 mt-1 font-semibold">Expired dalam 12 Hari</p>
            </div>
        </div>
        
    </div>
</div>

@endsection