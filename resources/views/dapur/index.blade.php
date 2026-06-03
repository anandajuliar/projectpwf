@extends('layouts.app')
@section('title', 'Dapur Produksi')

@section('content')
<div class="mb-8 border-b pb-4">
    <h2 class="text-3xl font-bold text-gray-800">Dapur Produksi</h2>
    <p class="text-gray-500 mt-1">Pilih resep yang akan diproduksi hari ini.</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
    
    <div onclick="bukaResep('Croissant Butter', 'Tepung Terigu (500g), Mentega (250g), Ragi (10g), Susu (200ml)', 'Membutuhkan waktu proofing 2 jam.')" class="bg-white p-0 rounded-2xl shadow-lg border border-gray-100 overflow-hidden cursor-pointer transform transition hover:scale-105">
        <div class="bg-amber-100 h-32 flex items-center justify-center">
            <span class="text-6xl">🥐</span>
        </div>
        <div class="p-6 text-center">
            <h3 class="text-2xl font-bold text-gray-800 mb-2">Croissant Butter</h3>
            <span class="bg-blue-100 text-blue-800 text-xs font-bold px-3 py-1 rounded-full">Bake: 20 Menit</span>
        </div>
    </div>

    <div onclick="bukaResep('Pain au Chocolat', 'Tepung Terigu (450g), Mentega (200g), Cokelat Batang (150g), Ragi (10g)', 'Pastikan suhu adonan tetap dingin.')" class="bg-white p-0 rounded-2xl shadow-lg border border-gray-100 overflow-hidden cursor-pointer transform transition hover:scale-105">
        <div class="bg-amber-800 h-32 flex items-center justify-center">
            <span class="text-6xl">🍫</span>
        </div>
        <div class="p-6 text-center">
            <h3 class="text-2xl font-bold text-gray-800 mb-2">Pain au Chocolat</h3>
            <span class="bg-blue-100 text-blue-800 text-xs font-bold px-3 py-1 rounded-full">Bake: 25 Menit</span>
        </div>
    </div>

    <div onclick="bukaResep('Baguette Klasik', 'Tepung Terigu Protein Tinggi (600g), Air (400ml), Ragi (8g), Garam (12g)', 'Bentuk memanjang dan kerat bagian atasnya.')" class="bg-white p-0 rounded-2xl shadow-lg border border-gray-100 overflow-hidden cursor-pointer transform transition hover:scale-105">
        <div class="bg-orange-200 h-32 flex items-center justify-center">
            <span class="text-6xl">🥖</span>
        </div>
        <div class="p-6 text-center">
            <h3 class="text-2xl font-bold text-gray-800 mb-2">Baguette Klasik</h3>
            <span class="bg-blue-100 text-blue-800 text-xs font-bold px-3 py-1 rounded-full">Bake: 30 Menit</span>
        </div>
    </div>
</div>

<div id="modalResep" class="fixed inset-0 bg-black bg-opacity-50 hidden flex justify-center items-center z-50">
    <div class="bg-white p-8 rounded-2xl shadow-2xl w-full max-w-md transform transition-all">
        <div class="flex justify-between items-center mb-6">
            <h3 id="modalJudul" class="text-2xl font-bold text-gray-800">Nama Resep</h3>
            <button onclick="tutupResep()" class="text-gray-400 hover:text-red-500 text-2xl font-bold">&times;</button>
        </div>
        
        <div class="mb-6">
            <h4 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-2">Komposisi Bahan:</h4>
            <p id="modalBahan" class="text-gray-700 bg-gray-50 p-4 rounded-lg border border-gray-100"></p>
        </div>
        
        <div class="mb-8">
            <h4 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-2">Catatan Produksi:</h4>
            <p id="modalCatatan" class="text-gray-600 italic"></p>
        </div>
        
        <button onclick="produksiSekarang()" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-4 rounded-xl text-lg shadow-md transition transform active:scale-95 flex justify-center items-center">
            <span class="mr-2">👨‍🍳</span> Mulai Produksi (Potong Stok)
        </button>
    </div>
</div>

<script>
    function bukaResep(judul, bahan, catatan) {
        document.getElementById('modalJudul').innerText = judul;
        document.getElementById('modalBahan').innerText = bahan;
        document.getElementById('modalCatatan').innerText = catatan;
        document.getElementById('modalResep').classList.remove('hidden');
    }

    function tutupResep() {
        document.getElementById('modalResep').classList.add('hidden');
    }

    function produksiSekarang() {
        alert("Simulasi Produksi: Instruksi memotong " + document.getElementById('modalJudul').innerText + " sedang disiapkan untuk API Backend!");
        tutupResep();
    }
</script>
@endsection