@extends('layouts.app')
@section('title', 'Gudang & Dapur')

@section('content')
<div class="mb-8 border-b pb-4">
    <h2 class="text-3xl font-bold text-gray-800" id="page-title">Memuat...</h2>
    <p class="text-gray-500 mt-1" id="page-desc">Silakan tunggu sebentar.</p>
</div>

<div id="admin-view" class="hidden">
    <div class="flex flex-wrap gap-3 mb-4 justify-between items-center">
        <div class="flex gap-2">
            <input type="text" id="searchInput" onkeyup="cariProduk()" placeholder="Cari nama bahan..." class="border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
            <select id="filterStatus" onchange="cariProduk()" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 bg-white">
                <option value="">Semua Status</option>
                <option value="normal">✅ Stok Normal</option>
                <option value="low">⚠️ Stok Rendah</option>
                <option value="out">🚨 Stok Habis</option>
            </select>
        </div>
        <button onclick="bukaModalTambah()" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-5 rounded-lg shadow transition">
            + Tambah Bahan Baku
        </button>
    </div>

    <div class="bg-white shadow-md rounded-xl overflow-x-auto">
        <table class="min-w-full bg-white">
            <thead class="bg-amber-700 text-white">
                <tr>
                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Nama Bahan</th>
                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Kategori</th>
                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Stok Gudang</th>
                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Min. Stok</th>
                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Harga/Satuan</th>
                    <th class="text-center py-3 px-4 uppercase font-semibold text-sm">Aksi</th>
                </tr>
            </thead>
            <tbody id="admin-tbody" class="text-gray-700">
                <tr><td colspan="6" class="text-center py-8 text-gray-400">Memuat data...</td></tr>
            </tbody>
        </table>
    </div>

    <div id="admin-pagination" class="flex justify-between items-center mt-4 text-sm text-gray-600"></div>
</div>

<div id="chef-view" class="hidden">
    <div class="mb-4 flex gap-2">
        <input type="text" id="chefSearch" onkeyup="cariProdukChef()" placeholder="Cari bahan baku..." class="border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 w-full max-w-xs">
    </div>
    <div id="chef-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <div class="col-span-4 text-center py-12 text-gray-400">Memuat data bahan...</div>
    </div>
    <div id="chef-pagination" class="flex justify-center gap-2 mt-6"></div>
</div>

<div id="modalKurangiStok" class="fixed inset-0 bg-black bg-opacity-60 hidden flex justify-center items-center z-50">
    <div class="bg-white p-8 rounded-2xl shadow-2xl w-full max-w-md">
        <div class="flex justify-between items-center mb-5 border-b pb-3">
            <h3 class="text-xl font-bold text-gray-800 flex items-center">
                <span class="mr-2">✂️</span> Gunakan Bahan
            </h3>
            <button onclick="tutupModalKurangi()" class="text-gray-400 hover:text-red-500 text-3xl font-bold leading-none">&times;</button>
        </div>

        <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-5">
            <p class="text-sm text-amber-700 font-semibold">Bahan yang dipilih:</p>
            <p id="kurangi-nama" class="text-xl font-bold text-gray-800 mt-1"></p>
            <p id="kurangi-stok-info" class="text-sm text-gray-500 mt-1"></p>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-bold text-gray-700 mb-2">Jumlah yang Digunakan <span class="text-red-500">*</span></label>
            <div class="flex items-center gap-2">
                <input type="number" id="kurangi-qty" min="0.01" step="0.01"
                    class="w-full p-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500"
                    placeholder="Masukkan jumlah...">
                <span id="kurangi-unit" class="text-gray-600 font-semibold whitespace-nowrap"></span>
            </div>
        </div>

        <div class="mb-6">
            <label class="block text-sm font-bold text-gray-700 mb-2">Catatan (opsional)</label>
            <input type="text" id="kurangi-note" maxlength="500"
                class="w-full p-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500"
                placeholder="Misal: untuk 3 loyang brownies">
        </div>

        <div id="kurangi-error" class="hidden bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg p-3 mb-4"></div>

        <div class="flex justify-end gap-3 border-t pt-5">
            <button type="button" onclick="tutupModalKurangi()" class="px-5 py-3 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 transition">Batal</button>
            <button type="button" id="btn-kurangi-submit" onclick="submitKurangiStok()"
                class="px-6 py-3 bg-amber-500 text-white font-bold rounded-xl hover:bg-amber-600 transition shadow-lg flex items-center gap-2">
                <span>✂️</span> Potong Stok
            </button>
        </div>
    </div>
</div>

<div id="modalTambahBahan" class="fixed inset-0 bg-black bg-opacity-50 hidden flex justify-center items-center z-50">
    <div class="bg-white p-8 rounded-2xl shadow-2xl w-full max-w-lg transform transition-all">
        <div class="flex justify-between items-center mb-6 border-b pb-3">
            <h3 class="text-2xl font-bold text-gray-800 flex items-center"><span class="mr-2">📦</span> <span id="modal-tambah-judul">Tambah Bahan Baku</span></h3>
            <button onclick="tutupModalTambah()" class="text-gray-400 hover:text-red-500 text-3xl font-bold leading-none">&times;</button>
        </div>

        <form id="formTambahBahan">
            <input type="hidden" id="editProductId" value="">
            <div class="grid grid-cols-2 gap-5 mb-4">
                <div class="col-span-2">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Nama Bahan Baku <span class="text-red-500">*</span></label>
                    <input type="text" id="inputNama" class="w-full p-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500" placeholder="Contoh: Keju Mozarella" required>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Kategori</label>
                    <select id="inputKategori" class="w-full p-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 bg-white">
                        <option value="">Pilih Kategori...</option>
                        <option value="Tepung & Bahan Kering">Tepung & Bahan Kering</option>
                        <option value="Pemanis">Pemanis</option>
                        <option value="Lemak & Minyak">Lemak & Minyak</option>
                        <option value="Susu & Dairy">Susu & Dairy</option>
                        <option value="Protein">Protein</option>
                        <option value="Perisa & Topping">Perisa & Topping</option>
                        <option value="Agen Pengembang">Agen Pengembang</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Satuan (Unit) <span class="text-red-500">*</span></label>
                    <select id="inputUnit" class="w-full p-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 bg-white" required>
                        <option value="gram">Gram (g)</option>
                        <option value="kg">Kilogram (kg)</option>
                        <option value="ml">Mililiter (ml)</option>
                        <option value="liter">Liter (L)</option>
                        <option value="butir">Butir</option>
                        <option value="pcs">Pcs</option>
                        <option value="sdm">Sendok Makan (sdm)</option>
                        <option value="sdt">Sendok Teh (sdt)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Stok Awal <span class="text-red-500">*</span></label>
                    <input type="number" step="0.01" id="inputQty" class="w-full p-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500" placeholder="Misal: 1000" min="0" required>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Batas Stok Minimum <span class="text-red-500">*</span></label>
                    <input type="number" step="0.01" id="inputMinQty" class="w-full p-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500" placeholder="Misal: 200" min="0" required>
                </div>

                <div class="col-span-2">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Harga Beli per Satuan (Rp) <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <span class="absolute left-4 top-3 text-gray-500 font-bold">Rp</span>
                        <input type="text" inputmode="numeric" id="inputHarga" class="w-full p-3 pl-10 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500" placeholder="Misal: 15.000" required>
                    </div>
                </div>

                <div class="col-span-2">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Deskripsi</label>
                    <textarea id="inputDeskripsi" rows="2" class="w-full p-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 resize-none" placeholder="Keterangan singkat bahan..."></textarea>
                </div>
            </div>

            <div id="form-error" class="hidden bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg p-3 mb-4"></div>

            <div class="flex justify-end mt-4 space-x-4 border-t pt-5">
                <button type="button" onclick="tutupModalTambah()" class="px-6 py-3 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 transition">Batal</button>
                <button type="button" id="btn-simpan" onclick="simpanProduk()"
                    class="px-6 py-3 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 transition shadow-lg">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<div id="modalRestok" class="fixed inset-0 bg-black bg-opacity-60 hidden flex justify-center items-center z-50">
    <div class="bg-white p-8 rounded-2xl shadow-2xl w-full max-w-md">
        <div class="flex justify-between items-center mb-5 border-b pb-3">
            <h3 class="text-xl font-bold text-gray-800 flex items-center">
                <span class="mr-2">📥</span> Tambah Stok / Restock
            </h3>
            <button onclick="tutupModalRestok()" class="text-gray-400 hover:text-red-500 text-3xl font-bold leading-none">&times;</button>
        </div>

        <div class="bg-green-50 border border-green-200 rounded-xl p-4 mb-5">
            <p class="text-sm text-green-700 font-semibold">Bahan yang direstok:</p>
            <p id="restok-nama" class="text-xl font-bold text-gray-800 mt-1"></p>
            <p id="restok-stok-info" class="text-sm text-gray-500 mt-1"></p>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-bold text-gray-700 mb-2">Jumlah yang Ditambahkan <span class="text-red-500">*</span></label>
            <div class="flex items-center gap-2">
                <input type="number" id="restok-qty" min="0.01" step="0.01"
                    class="w-full p-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500"
                    placeholder="Masukkan jumlah...">
                <span id="restok-unit" class="text-gray-600 font-semibold whitespace-nowrap"></span>
            </div>
        </div>

        <div class="mb-6">
            <label class="block text-sm font-bold text-gray-700 mb-2">Catatan (opsional)</label>
            <input type="text" id="restok-note" maxlength="500"
                class="w-full p-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500"
                placeholder="Misal: pembelian dari Toko Sari Baru">
        </div>

        <div id="restok-error" class="hidden bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg p-3 mb-4"></div>

        <div class="flex justify-end gap-3 border-t pt-5">
            <button type="button" onclick="tutupModalRestok()" class="px-5 py-3 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 transition">Batal</button>
            <button type="button" id="btn-restok-submit" onclick="submitRestok()"
                class="px-6 py-3 bg-green-600 text-white font-bold rounded-xl hover:bg-green-700 transition shadow-lg flex items-center gap-2">
                <span>📥</span> Tambah Stok
            </button>
        </div>
    </div>
</div>

<div id="toast" class="fixed bottom-6 right-6 z-[100] hidden">
    <div id="toast-inner" class="px-6 py-4 rounded-xl shadow-xl text-white font-semibold text-sm max-w-sm flex items-center gap-3">
        <span id="toast-icon"></span>
        <span id="toast-msg"></span>
    </div>
</div>

<script>
let authToken   = localStorage.getItem('auth_token');
let userRole    = localStorage.getItem('user_role');
let currentPage = 1;
let selectedProductId   = null;
let selectedProductData = null;
let searchTimeout       = null;

document.addEventListener("DOMContentLoaded", function () {
    if (!authToken) { window.location.href = '/login'; return; }

    const title = document.getElementById('page-title');
    const desc  = document.getElementById('page-desc');

    if (userRole === 'admin') {
        title.innerText = 'Manajemen Gudang';
        desc.innerText  = 'Kontrol penuh data bahan baku dan inventaris.';
        document.getElementById('admin-view').classList.remove('hidden');
    } else {
        title.innerText = 'Tablet Dapur BakeLab';
        desc.innerText  = 'Pilih dan potong bahan baku yang akan digunakan.';
        document.getElementById('chef-view').classList.remove('hidden');
    }

    muatProduk();
});

async function muatProduk(page = 1) {
    currentPage = page;
    const search = document.getElementById(userRole === 'admin' ? 'searchInput' : 'chefSearch')?.value || '';
    const status = document.getElementById('filterStatus')?.value || '';

    let url = `/api/products?page=${page}&per_page=12`;
    if (search) url += `&search=${encodeURIComponent(search)}`;
    if (status) url += `&stock_status=${encodeURIComponent(status)}`;

    try {
        const res = await fetch(url, {
            headers: { 'Authorization': 'Bearer ' + authToken, 'Accept': 'application/json' }
        });

        if (res.status === 401) { window.location.href = '/login'; return; }

        const json = await res.json();
        if (json.success) {
            renderData(json.data, userRole);
            renderPaginasi(json.meta);
        } else {
            tampilError('Gagal mengambil data produk.');
        }
    } catch (e) {
        tampilError('Koneksi ke server gagal.');
        console.error(e);
    }
}

function cariProduk() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => muatProduk(1), 400);
}
function cariProdukChef() { cariProduk(); }

function renderData(products, role) {
    if (role === 'admin') renderAdmin(products);
    else renderChef(products);
}

function renderAdmin(products) {
    const tbody = document.getElementById('admin-tbody');
    if (!products.length) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center py-10 text-gray-400">Tidak ada bahan baku ditemukan.</td></tr>';
        return;
    }

    tbody.innerHTML = products.map(p => {
        const statusBadge = {
            'normal': `<span class="bg-green-100 text-green-800 text-xs font-bold px-2 py-0.5 rounded-full">Normal</span>`,
            'low':    `<span class="bg-amber-100 text-amber-800 text-xs font-bold px-2 py-0.5 rounded-full">⚠️ Rendah</span>`,
            'out':    `<span class="bg-red-100 text-red-800 text-xs font-bold px-2 py-0.5 rounded-full">🚨 Habis</span>`,
        }[p.stock_status] || '';

        const stockColor = p.stock_status === 'normal' ? 'text-green-700 font-bold' : p.stock_status === 'low' ? 'text-amber-600 font-bold' : 'text-red-600 font-bold';

        return `
        <tr class="border-b hover:bg-gray-50 transition">
            <td class="text-left py-3 px-4">
                <p class="font-semibold text-gray-800">${escHtml(p.name)}</p>
                <p class="text-xs text-gray-400">${escHtml(p.description || '')}</p>
            </td>
            <td class="text-left py-3 px-4 text-sm">${escHtml(p.category || '-')}</td>
            <td class="text-left py-3 px-4">
                <span class="${stockColor}">${p.qty} ${escHtml(p.unit)}</span>
                <div class="mt-0.5">${statusBadge}</div>
            </td>
            <td class="text-left py-3 px-4 text-sm text-gray-500">${p.min_qty} ${escHtml(p.unit)}</td>
            <td class="text-left py-3 px-4 text-sm">Rp ${formatAngka(p.price_per_unit)}</td>
            <td class="text-center py-3 px-4">
                <div class="flex items-center justify-center gap-1 flex-wrap">
                    <button onclick='bukaModalRestok(${JSON.stringify(p)})' title="Tambah Stok / Restock"
                        class="bg-green-100 hover:bg-green-200 text-green-800 text-xs font-bold px-2 py-1 rounded-lg transition">📥 Restok</button>
                    <button onclick='bukaModalEdit(${JSON.stringify(p)})' title="Edit Produk"
                        class="bg-blue-100 hover:bg-blue-200 text-blue-800 text-xs font-bold px-2 py-1 rounded-lg transition">✏️ Edit</button>
                    <button onclick="hapusProduk(${p.id}, '${escHtml(p.name)}')" title="Hapus Produk"
                        class="bg-red-100 hover:bg-red-200 text-red-800 text-xs font-bold px-2 py-1 rounded-lg transition">🗑️ Hapus</button>
                </div>
            </td>
        </tr>`;
    }).join('');
}

function renderChef(products) {
    const grid = document.getElementById('chef-grid');
    if (!products.length) {
        grid.innerHTML = '<div class="col-span-4 text-center py-12 text-gray-400">Tidak ada bahan baku ditemukan.</div>';
        return;
    }

    const statusCfg = {
        'normal': { badge: 'bg-green-100 text-green-800', label: 'Stok Aman',   color: 'text-green-600', disabled: false },
        'low':    { badge: 'bg-amber-100 text-amber-800', label: '⚠️ Stok Rendah', color: 'text-amber-500', disabled: false },
        'out':    { badge: 'bg-red-100 text-red-800',    label: '🚨 Stok Habis', color: 'text-red-500',   disabled: true  },
    };

    grid.innerHTML = products.map(p => {
        const cfg = statusCfg[p.stock_status] || statusCfg['normal'];
        const btnAttr = cfg.disabled
            ? 'disabled class="w-full bg-gray-300 text-gray-500 font-bold py-4 rounded-xl text-lg cursor-not-allowed"'
            : `onclick='bukaModalKurangi(${JSON.stringify(p)})' class="w-full bg-amber-500 hover:bg-amber-600 active:scale-95 text-white font-bold py-4 rounded-xl text-lg shadow-md transition transform"`;

        return `
        <div class="bg-white p-6 rounded-2xl shadow-lg border border-gray-100 flex flex-col justify-between">
            <div>
                <span class="${cfg.badge} text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wide">${cfg.label}</span>
                <h3 class="text-xl font-bold text-gray-800 mt-3 mb-1 line-clamp-2">${escHtml(p.name)}</h3>
                <p class="text-xs text-gray-400 mb-1">${escHtml(p.category || '')}</p>
                <p class="${cfg.color} font-extrabold text-2xl mb-6">Sisa: ${p.qty} ${escHtml(p.unit)}</p>
            </div>
            <button ${btnAttr}>
                👨‍🍳 Gunakan Bahan
            </button>
        </div>`;
    }).join('');
}

function renderPaginasi(meta) {
    if (!meta || meta.last_page <= 1) {
        document.getElementById(userRole === 'admin' ? 'admin-pagination' : 'chef-pagination').innerHTML = '';
        return;
    }

    const containerId = userRole === 'admin' ? 'admin-pagination' : 'chef-pagination';
    const el = document.getElementById(containerId);

    el.innerHTML = `
        <span class="text-gray-500">Halaman ${meta.current_page} dari ${meta.last_page} (${meta.total} item)</span>
        <div class="flex gap-2">
            <button onclick="muatProduk(${meta.current_page - 1})" ${meta.current_page <= 1 ? 'disabled' : ''}
                class="px-3 py-1 rounded border ${meta.current_page <= 1 ? 'text-gray-300 border-gray-200' : 'border-amber-400 text-amber-600 hover:bg-amber-50'}">‹ Prev</button>
            <button onclick="muatProduk(${meta.current_page + 1})" ${meta.current_page >= meta.last_page ? 'disabled' : ''}
                class="px-3 py-1 rounded border ${meta.current_page >= meta.last_page ? 'text-gray-300 border-gray-200' : 'border-amber-400 text-amber-600 hover:bg-amber-50'}">Next ›</button>
        </div>`;
}

function bukaModalKurangi(product) {
    selectedProductData = product;
    selectedProductId   = product.id;
    document.getElementById('kurangi-nama').innerText     = product.name;
    document.getElementById('kurangi-stok-info').innerText = `Stok saat ini: ${product.qty} ${product.unit}`;
    document.getElementById('kurangi-unit').innerText      = product.unit;
    document.getElementById('kurangi-qty').value           = '';
    document.getElementById('kurangi-note').value          = '';
    document.getElementById('kurangi-error').classList.add('hidden');
    document.getElementById('modalKurangiStok').classList.remove('hidden');
    setTimeout(() => document.getElementById('kurangi-qty').focus(), 100);
}

function tutupModalKurangi() {
    document.getElementById('modalKurangiStok').classList.add('hidden');
}

async function submitKurangiStok() {
    const qty  = parseFloat(document.getElementById('kurangi-qty').value);
    const note = document.getElementById('kurangi-note').value.trim();
    const errEl = document.getElementById('kurangi-error');

    if (!qty || qty <= 0) {
        errEl.innerHTML = 'Jumlah yang digunakan harus lebih dari 0.';
        errEl.classList.remove('hidden');
        return;
    }

    const btn = document.getElementById('btn-kurangi-submit');
    btn.disabled = true;
    btn.innerText = '⏳ Memproses...';
    errEl.classList.add('hidden');

    try {
        const res = await fetch(`/api/products/${selectedProductId}/reduce`, {
            method: 'POST',
            headers: {
                'Authorization': 'Bearer ' + authToken,
                'Content-Type' : 'application/json',
                'Accept'       : 'application/json'
            },
            body: JSON.stringify({ qty, note: note || null })
        });

        const json = await res.json();

        if (res.ok && json.success) {
            tutupModalKurangi();
            tampilToast('success', `✅ Stok ${selectedProductData.name} berhasil dikurangi ${qty} ${selectedProductData.unit}!`);
            muatProduk(currentPage);
        } else {
            let pesanError = json.message || 'Gagal memotong stok.';
            if (res.status === 422 && json.errors) {
                pesanError = '';
                for (const [field, messages] of Object.entries(json.errors)) {
                    pesanError += `• ${messages.join(', ')} <br>`;
                }
            }
            errEl.innerHTML = pesanError;
            errEl.classList.remove('hidden');
        }
    } catch (e) {
        errEl.innerHTML = 'Koneksi ke server gagal.';
        errEl.classList.remove('hidden');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<span>✂️</span> Potong Stok';
    }
}

function bukaModalRestok(product) {
    selectedProductData = product;
    selectedProductId   = product.id;
    document.getElementById('restok-nama').innerText      = product.name;
    document.getElementById('restok-stok-info').innerText = `Stok saat ini: ${product.qty} ${product.unit}`;
    document.getElementById('restok-unit').innerText      = product.unit;
    document.getElementById('restok-qty').value           = '';
    document.getElementById('restok-note').value          = '';
    document.getElementById('restok-error').classList.add('hidden');
    document.getElementById('modalRestok').classList.remove('hidden');
    setTimeout(() => document.getElementById('restok-qty').focus(), 100);
}

function tutupModalRestok() {
    document.getElementById('modalRestok').classList.add('hidden');
}

async function submitRestok() {
    const qty  = parseFloat(document.getElementById('restok-qty').value);
    const note = document.getElementById('restok-note').value.trim();
    const errEl = document.getElementById('restok-error');

    if (!qty || qty <= 0) {
        errEl.innerHTML = 'Jumlah tambahan stok harus lebih dari 0.';
        errEl.classList.remove('hidden');
        return;
    }

    const btn = document.getElementById('btn-restok-submit');
    btn.disabled = true;
    btn.innerText = '⏳ Memproses...';
    errEl.classList.add('hidden');

    try {
        const res = await fetch(`/api/products/${selectedProductId}/add`, {
            method: 'POST',
            headers: {
                'Authorization': 'Bearer ' + authToken,
                'Content-Type' : 'application/json',
                'Accept'       : 'application/json'
            },
            body: JSON.stringify({ qty, note: note || null })
        });

        const json = await res.json();

        if (res.ok && json.success) {
            tutupModalRestok();
            tampilToast('success', `📥 Stok ${selectedProductData.name} berhasil ditambah ${qty} ${selectedProductData.unit}!`);
            muatProduk(currentPage);
        } else {
            let pesanError = json.message || 'Gagal menambah stok.';
            if (res.status === 422 && json.errors) {
                pesanError = '';
                for (const [field, messages] of Object.entries(json.errors)) {
                    pesanError += `• ${messages.join(', ')} <br>`;
                }
            }
            errEl.innerHTML = pesanError;
            errEl.classList.remove('hidden');
        }
    } catch (e) {
        errEl.innerHTML = 'Koneksi ke server gagal.';
        errEl.classList.remove('hidden');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<span>📥</span> Tambah Stok';
    }
}

function bukaModalTambah() {
    document.getElementById('modal-tambah-judul').innerText = 'Tambah Bahan Baku';
    document.getElementById('editProductId').value = '';
    document.getElementById('formTambahBahan').reset();
    document.getElementById('form-error').classList.add('hidden');
    document.getElementById('btn-simpan').innerText = 'Simpan';
    document.getElementById('modalTambahBahan').classList.remove('hidden');
}

function bukaModalEdit(product) {
    document.getElementById('modal-tambah-judul').innerText = 'Edit Bahan Baku';
    document.getElementById('editProductId').value  = product.id;
    document.getElementById('inputNama').value      = product.name;
    
    const catSelect = document.getElementById('inputKategori');
    if (product.category && !Array.from(catSelect.options).some(opt => opt.value === product.category)) {
        catSelect.add(new Option(product.category, product.category));
    }
    catSelect.value = product.category || '';
    
    document.getElementById('inputUnit').value      = product.unit;
    document.getElementById('inputQty').value       = Math.abs(parseFloat(product.qty));
    document.getElementById('inputMinQty').value    = Math.abs(parseFloat(product.min_qty));
    
    let hargaAsli = Math.abs(parseFloat(product.price_per_unit));
    document.getElementById('inputHarga').value     = new Intl.NumberFormat('id-ID').format(hargaAsli);
    document.getElementById('inputDeskripsi').value = product.description || '';
    
    document.getElementById('form-error').classList.add('hidden');
    document.getElementById('btn-simpan').innerText = 'Simpan Perubahan';
    document.getElementById('modalTambahBahan').classList.remove('hidden');
}

function tutupModalTambah() {
    document.getElementById('modalTambahBahan').classList.add('hidden');
    document.getElementById('formTambahBahan').reset();
}

async function simpanProduk() {
    const editId   = document.getElementById('editProductId').value;
    const isEdit   = !!editId;
    const errEl    = document.getElementById('form-error');
    const btn      = document.getElementById('btn-simpan');

    const rawQty   = parseFloat(document.getElementById('inputQty').value);
    const rawMin   = parseFloat(document.getElementById('inputMinQty').value);
    
    const cleanHargaStr = document.getElementById('inputHarga').value.replace(/\./g, '');
    const rawHarga      = parseFloat(cleanHargaStr);

    const payload = {
        name          : document.getElementById('inputNama').value.trim(),
        category      : document.getElementById('inputKategori').value || null,
        unit          : document.getElementById('inputUnit').value,
        qty           : isNaN(rawQty) ? 0 : Math.abs(rawQty),
        min_qty       : isNaN(rawMin) ? 0 : Math.abs(rawMin),
        price_per_unit: isNaN(rawHarga) ? 0 : Math.abs(rawHarga),
        description   : document.getElementById('inputDeskripsi').value.trim() || null,
    };

    if (!payload.name) {
        errEl.innerHTML = 'Nama bahan wajib diisi.';
        errEl.classList.remove('hidden');
        return;
    }
    if (!/^[a-zA-Z0-9\s\(\)\'\&\-]+$/.test(payload.name)) {
        errEl.innerHTML = "Nama bahan hanya boleh berisi huruf, angka, spasi, dan simbol ( ) ' & -";
        errEl.classList.remove('hidden');
        return;
    }
    if (payload.price_per_unit < 3000) {
        errEl.innerHTML = 'Harga beli per satuan tidak boleh di bawah Rp 3.000.';
        errEl.classList.remove('hidden');
        return;
    }

    errEl.classList.add('hidden');
    btn.disabled = true;
    btn.innerText = '⏳ Menyimpan...';

    try {
        const url    = isEdit ? `/api/products/${editId}` : '/api/products';
        const method = isEdit ? 'PUT' : 'POST';

        const res = await fetch(url, {
            method,
            headers: {
                'Authorization': 'Bearer ' + authToken,
                'Content-Type' : 'application/json',
                'Accept'       : 'application/json'
            },
            body: JSON.stringify(payload)
        });

        const json = await res.json();

        if (res.ok && json.success) {
            tutupModalTambah();
            tampilToast('success', isEdit ? `✏️ Bahan "${payload.name}" berhasil diperbarui!` : `📦 Bahan "${payload.name}" berhasil ditambahkan!`);
            muatProduk(currentPage);
        } else {
            let pesanError = json.message || 'Gagal menyimpan data.';
            if (res.status === 422 && json.errors) {
                pesanError = '';
                for (const [field, messages] of Object.entries(json.errors)) {
                    pesanError += `• ${messages.join(', ')} <br>`;
                }
            }
            errEl.innerHTML = pesanError;
            errEl.classList.remove('hidden');
        }
    } catch (e) {
        errEl.innerHTML = 'Koneksi ke server gagal.';
        errEl.classList.remove('hidden');
    } finally {
        btn.disabled = false;
        btn.innerText = isEdit ? 'Simpan Perubahan' : 'Simpan';
    }
}

async function hapusProduk(id, nama) {
    if (!confirm(`Yakin hapus bahan "${nama}"? Aksi ini tidak bisa dibatalkan.`)) return;

    try {
        const res = await fetch(`/api/products/${id}`, {
            method : 'DELETE',
            headers: { 'Authorization': 'Bearer ' + authToken, 'Accept': 'application/json' }
        });
        const json = await res.json();

        if (res.ok && json.success) {
            tampilToast('success', `🗑️ Bahan "${nama}" berhasil dihapus.`);
            muatProduk(currentPage);
        } else {
            tampilToast('error', json.message || 'Gagal menghapus.');
        }
    } catch (e) {
        tampilToast('error', 'Koneksi ke server gagal.');
    }
}

let toastTimeout;
function tampilToast(type, msg) {
    const toast = document.getElementById('toast');
    const inner = document.getElementById('toast-inner');
    const msgEl = document.getElementById('toast-msg');

    inner.className = `px-6 py-4 rounded-xl shadow-xl text-white font-semibold text-sm max-w-sm flex items-center gap-3 ${
        type === 'success' ? 'bg-green-600' : 'bg-red-600'
    }`;
    msgEl.innerText = msg;
    toast.classList.remove('hidden');

    clearTimeout(toastTimeout);
    toastTimeout = setTimeout(() => toast.classList.add('hidden'), 4000);
}

function tampilError(msg) {
    tampilToast('error', msg);
}

function escHtml(str) {
    const div = document.createElement('div');
    div.appendChild(document.createTextNode(str || ''));
    return div.innerHTML;
}

function formatAngka(num) {
    return parseFloat(num).toLocaleString('id-ID');
}

document.getElementById('inputHarga').addEventListener('input', function(e) {
    let angka = this.value.replace(/[^0-9]/g, '');
    
    if (angka === '') {
        this.value = '';
        return;
    }
    
    this.value = new Intl.NumberFormat('id-ID').format(angka);
});
</script>
@endsection