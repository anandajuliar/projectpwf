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
            <h3 class="text-3xl font-bold text-gray-800"><span id="stat-total">—</span> <span class="text-lg text-gray-400 font-normal">Item</span></h3>
        </div>
        <div class="p-3 bg-blue-100 rounded-full"><span class="text-2xl"></span></div>
    </div>

    <div class="bg-white p-6 rounded-xl shadow border-l-4 border-amber-500 flex items-center justify-between">
        <div>
            <p class="text-sm text-gray-500 font-semibold uppercase mb-1">Stok Rendah / Hampir Habis</p>
            <h3 class="text-3xl font-bold text-amber-600"><span id="stat-low">—</span> <span class="text-lg text-amber-400 font-normal">Item</span></h3>
        </div>
        <div class="p-3 bg-amber-100 rounded-full"><span class="text-2xl"></span></div>
    </div>

    <div class="bg-white p-6 rounded-xl shadow border-l-4 border-red-500 flex items-center justify-between">
        <div>
            <p class="text-sm text-gray-500 font-semibold uppercase mb-1">Stok Habis</p>
            <h3 class="text-3xl font-bold text-red-600"><span id="stat-out">—</span> <span class="text-lg text-red-400 font-normal">Item Kritis</span></h3>
        </div>
        <div class="p-3 bg-red-100 rounded-full"><span class="text-2xl"></span></div>
    </div>
</div>

<div class="bg-white shadow rounded-xl overflow-hidden border border-gray-100">
    <div class="bg-red-50 border-b border-red-100 p-4 flex justify-between items-center">
        <h3 class="font-bold text-red-800 text-lg flex items-center">
            <span class="mr-2"></span> Peringatan Stok Rendah & Habis
        </h3>
        <a href="/gudang" class="text-sm text-amber-700 hover:underline font-semibold">Lihat semua →</a>
    </div>

    <div id="alert-list" class="p-0">
        <div class="p-6 text-center text-gray-400">Memuat data...</div>
    </div>
</div>
@endsection

<script>
document.addEventListener('DOMContentLoaded', async function () {
    const token = localStorage.getItem('auth_token');
    if (!token) { window.location.href = '/login'; return; }

    try {
        const res  = await fetch('/api/products/summary', {
            headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' }
        });
        const json = await res.json();

        if (!res.ok || !json.success) throw new Error(json.message || 'Gagal');

        const d = json.data;
        document.getElementById('stat-total').innerText = d.total_products;
        document.getElementById('stat-low').innerText   = d.stock_low;
        document.getElementById('stat-out').innerText   = d.stock_out;

        const list = document.getElementById('alert-list');

        if (!d.alert_products.length) {
            list.innerHTML = '<div class="p-6 text-center text-green-600 font-semibold">Semua stok dalam kondisi aman!</div>';
            return;
        }

        list.innerHTML = d.alert_products.map(p => {
            const isOut   = p.stock_status === 'out';
            const badge   = isOut
                ? `<span class="bg-red-100 text-red-800 text-xs font-bold px-3 py-1 rounded-full">Habis</span>`
                : `<span class="bg-amber-100 text-amber-800 text-xs font-bold px-3 py-1 rounded-full">Rendah</span>`;
            const note    = isOut
                ? `<p class="text-xs text-red-500 mt-1 font-semibold">Segera Restock!</p>`
                : `<p class="text-xs text-amber-600 mt-1 font-semibold">Sisa: ${p.qty} ${p.unit} (min: ${p.min_qty})</p>`;

            return `
            <div class="p-4 border-b hover:bg-gray-50 flex justify-between items-center">
                <div>
                    <h4 class="font-bold text-gray-800">${escHtml(p.name)}</h4>
                    <p class="text-sm text-gray-500">Kategori: ${escHtml(p.category || '-')}</p>
                </div>
                <div class="text-right">
                    ${badge}
                    ${note}
                </div>
            </div>`;
        }).join('');

    } catch (e) {
        document.getElementById('alert-list').innerHTML = '<div class="p-6 text-center text-red-400">Gagal memuat data. Periksa koneksi.</div>';
        console.error(e);
    }
});

function escHtml(str) {
    const d = document.createElement('div');
    d.appendChild(document.createTextNode(str || ''));
    return d.innerHTML;
}
</script>