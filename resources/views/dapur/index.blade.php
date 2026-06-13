@extends('layouts.app')
@section('title', 'Dapur Produksi')

@section('content')
<div class="mb-8 border-b pb-4 flex justify-between items-start">
    <div>
        <h2 class="text-3xl font-bold text-gray-800">Dapur Produksi</h2>
        <p class="text-gray-500 mt-1" id="page-subtitle">Pilih resep dan eksekusi untuk memotong stok bahan secara otomatis.</p>
    </div>
    <div class="flex items-center gap-3">
        <button id="btn-tambah-resep" onclick="bukaModalCrudResep()" class="hidden bg-amber-600 hover:bg-amber-700 text-white font-bold py-2 px-5 rounded-lg shadow transition">
            + Buat Resep Baru
        </button>
        <span id="badge-role" class="hidden bg-amber-100 text-amber-800 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wide"></span>
    </div>
</div>

<div id="recipe-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-10">
    <div class="col-span-4 text-center py-12 text-gray-400">Memuat resep...</div>
</div>

<div id="chef-history-section" class="hidden">
    <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
        <span>📋</span> Riwayat Produksi Dapur Hari Ini
    </h3>
    <div class="bg-white shadow-md rounded-xl overflow-x-auto border border-gray-100">
        <table class="min-w-full bg-white text-sm">
            <thead class="bg-amber-50 text-amber-800 border-b border-amber-200">
                <tr>
                    <th class="text-left py-3 px-4 font-bold">Waktu</th>
                    <th class="text-left py-3 px-4 font-bold">Resep / Produk</th>
                    <th class="text-center py-3 px-4 font-bold">Porsi Dibuat</th>
                    <th class="text-left py-3 px-4 font-bold">Koki (User)</th>
                    <th class="text-left py-3 px-4 font-bold">Catatan</th>
                </tr>
            </thead>
            <tbody id="history-tbody" class="text-gray-700">
                <tr><td colspan="5" class="text-center py-6 text-gray-400">Memuat riwayat...</td></tr>
            </tbody>
        </table>
    </div>
</div>

<div id="modalResep" class="fixed inset-0 bg-black bg-opacity-60 hidden flex justify-center items-center z-50">
    <div class="bg-white p-0 rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden flex flex-col max-h-[90vh]">
        <div class="bg-amber-600 text-white px-8 py-5 flex justify-between items-center shrink-0">
            <h3 id="modal-judul" class="text-2xl font-bold line-clamp-1"></h3>
            <button onclick="tutupResep()" class="text-white hover:text-amber-200 text-3xl font-bold leading-none" aria-label="Tutup Modal">&times;</button>
        </div>

        <div class="px-8 py-6 overflow-y-auto grow">
            <div class="mb-4">
                <span id="modal-kategori" class="bg-amber-100 text-amber-800 text-xs font-bold px-3 py-1 rounded-full"></span>
            </div>
            <p id="modal-deskripsi" class="text-gray-600 text-sm mb-5 whitespace-pre-line"></p>

            <div class="mb-5">
                <h4 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-3">Komposisi Bahan (per porsi):</h4>
                <div id="modal-ingredients" class="space-y-2 bg-gray-50 p-3 rounded-xl border border-gray-100"></div>
            </div>

            <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-4">
                <label for="input-porsi" class="block text-sm font-bold text-amber-900 mb-2">Jumlah Porsi / Loyang yang Dibuat:</label>
                <div class="flex items-center gap-3">
                    <button type="button" aria-label="Kurangi Porsi" onclick="ubahPorsi(-1)" class="w-10 h-10 rounded-full bg-white border border-amber-300 text-amber-600 hover:bg-amber-100 text-xl font-bold flex items-center justify-center transition">−</button>
                    <input type="text" inputmode="numeric" id="input-porsi" value="1" 
                        class="w-24 text-center text-xl font-bold border-2 border-amber-400 rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-amber-600 bg-white">
                    <button type="button" aria-label="Tambah Porsi" onclick="ubahPorsi(1)" class="w-10 h-10 rounded-full bg-amber-500 hover:bg-amber-600 text-white text-xl font-bold flex items-center justify-center transition">+</button>
                    <span class="text-amber-800 font-semibold text-sm">porsi</span>
                </div>
            </div>

            <div class="mb-4">
                <input type="text" id="modal-note" maxlength="500" placeholder="Catatan masak (opsional)..." aria-label="Catatan produksi"
                    class="w-full p-3 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
            </div>

            <div id="modal-error" class="hidden bg-red-50 border-l-4 border-red-500 text-red-800 text-sm rounded-r-lg p-4 mb-4"></div>
        </div>

        <div class="p-6 border-t border-gray-100 shrink-0 bg-white">
            <button id="btn-eksekusi" onclick="eksekusiResep()"
                class="w-full bg-amber-500 hover:bg-amber-600 active:scale-95 text-white font-bold py-4 rounded-xl text-lg shadow-md transition transform flex justify-center items-center gap-2">
                <span>👨‍🍳</span> Mulai Produksi (Potong Stok)
            </button>
        </div>
    </div>
</div>

<div id="modalCrudResep" class="fixed inset-0 bg-black bg-opacity-60 hidden flex justify-center items-center z-50">
    <div class="bg-white p-0 rounded-2xl shadow-2xl w-full max-w-3xl overflow-hidden flex flex-col max-h-[95vh]">
        <div class="bg-gray-800 text-white px-8 py-4 flex justify-between items-center shrink-0">
            <h3 id="crud-judul" class="text-xl font-bold">Tambah Resep Baru</h3>
            <button onclick="tutupCrudResep()" class="text-gray-400 hover:text-white text-3xl font-bold leading-none" aria-label="Tutup Form">&times;</button>
        </div>

        <div class="px-8 py-6 overflow-y-auto grow">
            <input type="hidden" id="crud-id">
            
            <div class="grid grid-cols-2 gap-5 mb-6">
                <div class="col-span-2 md:col-span-1">
                    <label for="crud-nama" class="block text-sm font-bold text-gray-700 mb-1">Nama Resep <span class="text-red-500">*</span></label>
                    <input type="text" id="crud-nama" class="w-full p-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500" placeholder="Misal: Kue Sus Cokelat">
                </div>
                <div class="col-span-2 md:col-span-1">
                    <label for="crud-kategori" class="block text-sm font-bold text-gray-700 mb-1">Kategori</label>
                    <select id="crud-kategori" class="w-full p-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500">
                        <option value="">Pilih Kategori...</option>
                        <option value="Roti Manis">Roti Manis</option>
                        <option value="Roti Keras">Roti Keras (Sourdough/Baguette)</option>
                        <option value="Pastry Lapis">Pastry Lapis (Croissant/Danish)</option>
                        <option value="Kue Sus">Kue Sus (Choux)</option>
                        <option value="Cake">Cake / Bolu</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>
                <div class="col-span-2 md:col-span-1">
                    <label for="crud-default-porsi" class="block text-sm font-bold text-gray-700 mb-1">Default Porsi Dihasilkan <span class="text-red-500">*</span></label>
                    <input type="number" id="crud-default-porsi" value="1" min="1" class="w-full p-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500">
                </div>
                <div class="col-span-2 md:col-span-1">
                    <label for="crud-waktu" class="block text-sm font-bold text-gray-700 mb-1">Waktu Persiapan (Menit)</label>
                    <input type="number" id="crud-waktu" placeholder="Misal: 45" min="1" class="w-full p-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500">
                </div>
                <div class="col-span-2">
                    <label for="crud-deskripsi" class="block text-sm font-bold text-gray-700 mb-1">Deskripsi Tambahan</label>
                    <textarea id="crud-deskripsi" rows="2" class="w-full p-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 resize-none" placeholder="Instruksi singkat atau keterangan..."></textarea>
                </div>
            </div>

            <div class="border-t border-gray-200 pt-5">
                <div class="flex justify-between items-center mb-3">
                    <h4 class="font-bold text-gray-800">Komposisi Bahan Baku (per porsi) <span class="text-red-500">*</span></h4>
                    <button type="button" onclick="tambahBarisBahan()" class="bg-blue-100 hover:bg-blue-200 text-blue-700 font-bold py-1 px-3 rounded text-sm transition">
                        + Tambah Bahan
                    </button>
                </div>
                <div id="crud-bahan-container" class="space-y-3"></div>
            </div>

            <div id="crud-error" class="hidden mt-5 bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg p-3"></div>
        </div>

        <div class="p-5 border-t border-gray-200 shrink-0 bg-gray-50 flex justify-between items-center">
            <button type="button" id="btn-hapus-resep" onclick="hapusResepAdmin()" class="hidden text-red-600 hover:text-red-800 font-bold text-sm transition">
                🗑️ Hapus Resep
            </button>
            <div class="flex gap-3 ml-auto">
                <button type="button" onclick="tutupCrudResep()" class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 font-bold rounded-lg hover:bg-gray-100 transition">Batal</button>
                <button type="button" id="btn-simpan-resep" onclick="simpanCrudResep()" class="px-6 py-2.5 bg-gray-800 hover:bg-gray-900 text-white font-bold rounded-lg shadow transition">
                    Simpan Resep
                </button>
            </div>
        </div>
    </div>
</div>

<div id="toast" class="fixed bottom-6 right-6 z-[9999] hidden">
    <div id="toast-inner" class="px-6 py-4 rounded-xl shadow-2xl text-white font-bold text-sm max-w-md flex items-start gap-3 border-l-4">
        <span id="toast-icon" class="text-lg leading-none mt-0.5"></span>
        <span id="toast-msg" class="leading-relaxed"></span>
    </div>
</div>

<script>
const EMOJI_MAP = ['🥐','🍞','🥖','🥨','🥯','🥞','🧇','🧀','🍰','🧁','🥧'];
let authToken     = localStorage.getItem('auth_token');
let userRole      = localStorage.getItem('user_role');
let selectedRecipe = null;
let toastTimeout;
let dbProducts = []; 

document.addEventListener('DOMContentLoaded', function () {
    if (!authToken) { window.location.href = '/login'; return; }

    const badge = document.getElementById('badge-role');
    badge.innerText = userRole === 'admin' ? 'Akses: Admin' : 'Akses: Chef';
    badge.classList.remove('hidden');

    if (userRole === 'admin') {
        document.getElementById('page-subtitle').innerText = 'Manajemen katalog resep dan komposisi bahan baku standar.';
        document.getElementById('btn-tambah-resep').classList.remove('hidden');
        fetchProdukGudang(); 
    } else {
        document.getElementById('chef-history-section').classList.remove('hidden');
        muatRiwayatChef();
    }

    muatResep();
    setupInputPorsi();
});

function setupInputPorsi() {
    const inputPorsi = document.getElementById('input-porsi');
    inputPorsi.addEventListener('input', function(e) {
        let val = this.value.replace(/[^0-9]/g, '');
        if (val === '0') val = ''; 
        this.value = val;
    });

    inputPorsi.addEventListener('blur', function(e) {
        if (this.value === '' || parseInt(this.value) < 1) {
            this.value = '1';
        }
    });
}

function ubahPorsi(delta) {
    const input = document.getElementById('input-porsi');
    let val = parseInt(input.value) || 1;
    val = Math.max(1, val + delta); 
    input.value = val;
}

function tampilToast(type, msg) {
    const toast = document.getElementById('toast');
    const inner = document.getElementById('toast-inner');
    const icon = document.getElementById('toast-icon');
    
    if (type === 'success') {
        inner.className = "px-6 py-4 rounded-xl shadow-2xl text-white font-bold text-sm max-w-md flex items-start gap-3 border-l-4 border-amber-800 bg-amber-500";
        icon.innerText = "";
    } else {
        inner.className = "px-6 py-4 rounded-xl shadow-2xl text-white font-bold text-sm max-w-md flex items-start gap-3 border-l-4 border-red-800 bg-red-500";
        icon.innerText = "";
    }
    
    document.getElementById('toast-msg').innerText = msg;
    toast.classList.remove('hidden');
    clearTimeout(toastTimeout);
    toastTimeout = setTimeout(() => toast.classList.add('hidden'), 5000);
}

function escHtml(str) {
    const d = document.createElement('div');
    d.appendChild(document.createTextNode(str || ''));
    return d.innerHTML;
}

async function muatResep() {
    try {
        const res  = await fetch('/api/recipes?is_active=true&per_page=50', {
            headers: { 'Authorization': 'Bearer ' + authToken, 'Accept': 'application/json' }
        });
        if (res.status === 401) { window.location.href = '/login'; return; }
        const json = await res.json();

        if (json.success) renderResep(json.data);
        else tampilToast('error', 'Gagal memuat daftar resep.');
    } catch (e) {
        document.getElementById('recipe-grid').innerHTML = '<div class="col-span-4 text-center py-12 text-red-400">Gagal memuat resep. Periksa koneksi server.</div>';
    }
}

function renderResep(recipes) {
    const grid = document.getElementById('recipe-grid');
    if (!recipes.length) {
        grid.innerHTML = '<div class="col-span-4 text-center py-12 text-gray-400 border-2 border-dashed border-gray-200 rounded-xl">Belum ada resep yang tersedia.</div>';
        return;
    }

    grid.innerHTML = recipes.map((r, i) => {
        const emoji = EMOJI_MAP[i % EMOJI_MAP.length];
        const bgColors = ['bg-amber-100','bg-orange-100','bg-yellow-100','bg-rose-100'];
        const bg = bgColors[i % bgColors.length];

        return `
        <div onclick='klikCardResep(${JSON.stringify(r)})' class="bg-white p-0 rounded-2xl shadow-sm hover:shadow-xl border border-gray-100 overflow-hidden cursor-pointer transform transition hover:-translate-y-1 group">
            <div class="${bg} h-32 flex items-center justify-center relative overflow-hidden">
                <span class="text-6xl group-hover:scale-110 transition duration-300">${emoji}</span>
                ${userRole === 'admin' ? '<span class="absolute top-2 right-2 bg-white bg-opacity-70 text-gray-800 text-xs px-2 py-1 rounded font-bold">✏️ Edit</span>' : ''}
            </div>
            <div class="p-5 text-center">
                <h3 class="text-xl font-bold text-gray-800 mb-2 line-clamp-1">${escHtml(r.name)}</h3>
                <span class="bg-amber-50 text-amber-800 text-xs font-bold px-3 py-1 rounded-full border border-amber-100">${escHtml(r.category || 'Resep')}</span>
                <p class="text-xs text-gray-400 mt-3 font-semibold">${(r.ingredients || []).length} bahan baku</p>
            </div>
        </div>`;
    }).join('');
}

function klikCardResep(recipe) {
    if (userRole === 'admin') {
        bukaModalCrudResep(recipe);
    } else {
        bukaResepChef(recipe);
    }
}

async function bukaResepChef(recipe) {
    try {
        const res  = await fetch(`/api/recipes/${recipe.id}`, {
            headers: { 'Authorization': 'Bearer ' + authToken, 'Accept': 'application/json' }
        });
        const json = await res.json();
        if (!res.ok || !json.success) throw new Error();

        selectedRecipe = json.data;
        
        document.getElementById('modal-judul').innerText    = selectedRecipe.name;
        document.getElementById('modal-kategori').innerText = selectedRecipe.category || 'Resep';
        document.getElementById('modal-deskripsi').innerText = selectedRecipe.description || 'Tidak ada deskripsi.';
        document.getElementById('input-porsi').value        = selectedRecipe.default_portions || 1;
        document.getElementById('modal-note').value         = '';
        document.getElementById('modal-error').classList.add('hidden');

        const ingredients = selectedRecipe.ingredients || [];
        const ingHtml = ingredients.length
            ? ingredients.map(ing => {
                const isHabis = ing.stock_status === 'out';
                const dot = isHabis ? 'bg-red-500' : (ing.stock_status === 'low' ? 'bg-amber-400' : 'bg-green-400');
                return `
                <div class="flex justify-between items-center bg-white rounded-lg px-3 py-2 border border-gray-100 shadow-sm">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full ${dot} inline-block flex-shrink-0"></span>
                        <span class="font-bold text-gray-700 text-sm">${escHtml(ing.product_name)}</span>
                    </div>
                    <div class="text-right text-sm">
                        <span class="font-black text-amber-700">${ing.qty_per_portion} ${escHtml(ing.unit)}</span>
                        ${isHabis ? '<p class="text-[10px] text-red-500 font-bold uppercase">Stok Habis!</p>' : `<p class="text-[10px] text-gray-400">Sisa: ${ing.stock_available}</p>`}
                    </div>
                </div>`;
            }).join('')
            : '<p class="text-gray-400 text-sm text-center py-2">Resep ini belum memiliki bahan baku.</p>';

        document.getElementById('modal-ingredients').innerHTML = ingHtml;
        document.getElementById('modalResep').classList.remove('hidden');
        
    } catch (e) {
        tampilToast('error', 'Gagal memuat detail resep.');
    }
}

function tutupResep() {
    document.getElementById('modalResep').classList.add('hidden');
    selectedRecipe = null;
}

async function eksekusiResep() {
    if (!selectedRecipe) return;

    const portions = parseInt(document.getElementById('input-porsi').value);
    const note     = document.getElementById('modal-note').value.trim();
    const errEl    = document.getElementById('modal-error');
    const btn      = document.getElementById('btn-eksekusi');

    if (isNaN(portions) || portions < 1) {
        errEl.innerHTML = 'Jumlah porsi tidak valid (Minimal 1).';
        errEl.classList.remove('hidden');
        return;
    }

    errEl.classList.add('hidden');
    btn.disabled = true;
    btn.innerHTML = '<span class="animate-spin">⏳</span> Memproses...';

    try {
        const res = await fetch(`/api/recipes/${selectedRecipe.id}/execute`, {
            method: 'POST',
            headers: {
                'Authorization': 'Bearer ' + authToken,
                'Content-Type' : 'application/json',
                'Accept'       : 'application/json'
            },
            body: JSON.stringify({ portions, note: note || null })
        });

        const json = await res.json();
        
        if (res.ok && json.success) {
            const recipeName = selectedRecipe.name; 
            tutupResep(); 
            tampilToast('success', `Berhasil memproduksi ${portions} porsi ${recipeName}!`);
            muatRiwayatChef(); 
        } else {
            if (json.data?.insufficient_items?.length) {
                let listHtml = `<p class="font-bold mb-1">${json.message}</p><ul class="list-disc pl-5 mt-2 text-xs space-y-1">`;
                json.data.insufficient_items.forEach(i => {
                    listHtml += `<li><b>${escHtml(i.product_name)}</b>: Butuh ${i.required}${i.unit}, Sisa ${i.available}${i.unit} <span class="text-red-600 font-bold">(Kurang ${i.shortage}${i.unit})</span></li>`;
                });
                listHtml += '</ul>';
                errEl.innerHTML = listHtml;
            } else {
                let pesanError = json.message || 'Gagal mengeksekusi resep.';
                if (res.status === 422 && json.errors) {
                    pesanError = '';
                    for (const [field, messages] of Object.entries(json.errors)) {
                        pesanError += `• ${messages.join(', ')} <br>`;
                    }
                }
                errEl.innerHTML = pesanError;
            }
            errEl.classList.remove('hidden');
        }
    } catch (e) {
        console.error(e);
        tampilToast('error', 'Terjadi kesalahan sistem di frontend.');
    } finally {
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = '<span>👨‍🍳</span> Mulai Produksi (Potong Stok)';
        }
    }
}

async function muatRiwayatChef() {
    if (userRole !== 'chef') return;
    const tbody = document.getElementById('history-tbody');

    try {
        const res = await fetch(`/api/stock-logs?type=recipe_reduce&per_page=30`, {
            headers: { 'Authorization': 'Bearer ' + authToken, 'Accept': 'application/json' }
        });
        const json = await res.json();

        if (res.ok && json.success) {
            const logs = json.data;
            let groupedLogs = {};
            
            logs.forEach(log => {
                const key = log.recipe_id + '_' + log.created_at; 
                if (!groupedLogs[key]) {
                    const dateObj = new Date(log.created_at);
                    const timeStr = dateObj.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' }) + ', ' + 
                                    dateObj.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });

                    groupedLogs[key] = {
                        time: timeStr,
                        recipe_name: log.recipe ? log.recipe.name : 'Resep Terhapus',
                        portions: log.portions,
                        user: log.user ? log.user.name : 'Unknown',
                        note: log.note || '-'
                    };
                }
            });

            const uniqueProductions = Object.values(groupedLogs);

            if (uniqueProductions.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" class="text-center py-6 text-gray-400">Belum ada produksi.</td></tr>';
                return;
            }

            tbody.innerHTML = uniqueProductions.map(prod => `
                <tr class="border-b border-gray-50 hover:bg-amber-50/50 transition">
                    <td class="text-left py-3 px-4 font-semibold text-gray-600">${prod.time}</td>
                    <td class="text-left py-3 px-4 font-bold text-amber-900">${escHtml(prod.recipe_name)}</td>
                    <td class="text-center py-3 px-4"><span class="bg-amber-100 text-amber-800 font-black px-3 py-1 rounded-full">${prod.portions} Porsi</span></td>
                    <td class="text-left py-3 px-4 text-gray-600">${escHtml(prod.user)}</td>
                    <td class="text-left py-3 px-4 text-xs text-gray-500 italic max-w-xs truncate">${escHtml(prod.note)}</td>
                </tr>
            `).join('');
        }
    } catch (e) {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center py-6 text-red-400">Gagal memuat riwayat.</td></tr>';
    }
}

async function fetchProdukGudang() {
    try {
        const res = await fetch('/api/products?per_page=100', {
            headers: { 'Authorization': 'Bearer ' + authToken, 'Accept': 'application/json' }
        });
        const json = await res.json();
        if (json.success) dbProducts = json.data;
    } catch (e) { console.error("Gagal load produk admin", e); }
}

function bukaModalCrudResep(recipe = null) {
    document.getElementById('crud-error').classList.add('hidden');
    document.getElementById('crud-bahan-container').innerHTML = ''; 

    if (recipe) {
        document.getElementById('crud-judul').innerText = 'Edit Resep: ' + recipe.name;
        document.getElementById('crud-id').value = recipe.id;
        document.getElementById('crud-nama').value = recipe.name;
        document.getElementById('crud-kategori').value = recipe.category || '';
        document.getElementById('crud-default-porsi').value = recipe.default_portions;
        
        let desc = recipe.description || '';
        let prepTime = '';
        const prepMatch = desc.match(/Waktu persiapan: (\d+) menit\.\n?/);
        if (prepMatch) {
            prepTime = prepMatch[1];
            desc = desc.replace(/Waktu persiapan: \d+ menit\.\n?/, '').trim(); 
        }
        document.getElementById('crud-waktu').value = prepTime;
        document.getElementById('crud-deskripsi').value = desc;

        if (recipe.ingredients && recipe.ingredients.length > 0) {
            recipe.ingredients.forEach(ing => tambahBarisBahan(ing.product_id, ing.qty_per_portion, ing.note));
        } else {
            tambahBarisBahan(); 
        }

        document.getElementById('btn-hapus-resep').classList.remove('hidden');
        document.getElementById('btn-simpan-resep').innerText = 'Simpan Perubahan';
    } else {
        document.getElementById('crud-judul').innerText = 'Buat Resep Baru';
        document.getElementById('crud-id').value = '';
        document.getElementById('crud-nama').value = '';
        document.getElementById('crud-kategori').value = '';
        document.getElementById('crud-default-porsi').value = '1';
        document.getElementById('crud-waktu').value = '';
        document.getElementById('crud-deskripsi').value = '';
        
        tambahBarisBahan(); 

        document.getElementById('btn-hapus-resep').classList.add('hidden');
        document.getElementById('btn-simpan-resep').innerText = 'Simpan Resep Baru';
    }

    document.getElementById('modalCrudResep').classList.remove('hidden');
}

function tutupCrudResep() {
    document.getElementById('modalCrudResep').classList.add('hidden');
}

function tambahBarisBahan(productId = '', qty = '', note = '') {
    const container = document.getElementById('crud-bahan-container');
    const rowId = 'row_' + Date.now() + Math.random().toString(36).substr(2, 5);

    let optionsHtml = '<option value="">Pilih Bahan Baku...</option>';
    dbProducts.forEach(p => {
        const isSelected = p.id == productId ? 'selected' : '';
        optionsHtml += `<option value="${p.id}" ${isSelected}>${escHtml(p.name)} (${escHtml(p.unit)})</option>`;
    });

    const html = `
        <div id="${rowId}" class="flex gap-2 items-start bg-gray-50 p-2 rounded-lg border border-gray-200 ing-row">
            <div class="flex-grow">
                <select aria-label="Pilih Bahan Baku" class="ing-product w-full p-2 border border-gray-300 rounded focus:ring-1 focus:ring-amber-500 text-sm">
                    ${optionsHtml}
                </select>
            </div>
            <div class="w-24 shrink-0">
                <input type="number" aria-label="Jumlah Bahan" step="0.01" value="${qty}" class="ing-qty w-full p-2 border border-gray-300 rounded focus:ring-1 focus:ring-amber-500 text-sm" placeholder="Jumlah">
            </div>
            <div class="w-32 shrink-0">
                <input type="text" aria-label="Catatan Bahan" value="${escHtml(note)}" class="ing-note w-full p-2 border border-gray-300 rounded focus:ring-1 focus:ring-amber-500 text-sm" placeholder="Catatan">
            </div>
            <button type="button" aria-label="Hapus Baris" onclick="document.getElementById('${rowId}').remove()" class="w-10 h-10 shrink-0 bg-red-100 hover:bg-red-200 text-red-600 rounded font-bold flex items-center justify-center transition">&times;</button>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', html);
}

async function simpanCrudResep() {
    const id = document.getElementById('crud-id').value;
    const isEdit = !!id;
    const errEl = document.getElementById('crud-error');
    const btn = document.getElementById('btn-simpan-resep');

    const name = document.getElementById('crud-nama').value.trim();
    const category = document.getElementById('crud-kategori').value;
    const default_portions = parseInt(document.getElementById('crud-default-porsi').value);
    
    const rawWaktu = document.getElementById('crud-waktu').value.trim();
    const rawDesc = document.getElementById('crud-deskripsi').value.trim();
    let finalDesc = '';
    if (rawWaktu) finalDesc += `Waktu persiapan: ${rawWaktu} menit.\n`;
    if (rawDesc) finalDesc += rawDesc;

    const ingredientRows = document.querySelectorAll('.ing-row');
    const ingredients = [];
    
    ingredientRows.forEach(row => {
        const pId = row.querySelector('.ing-product').value;
        const pQty = parseFloat(row.querySelector('.ing-qty').value);
        const pNote = row.querySelector('.ing-note').value.trim();

        if (pId && !isNaN(pQty) && pQty > 0) {
            ingredients.push({
                product_id: parseInt(pId),
                qty_per_portion: pQty,
                note: pNote || null
            });
        }
    });

    if (!name || isNaN(default_portions) || default_portions < 1) {
        errEl.innerHTML = 'Nama resep dan default porsi wajib diisi dengan benar.';
        errEl.classList.remove('hidden'); return;
    }
    if (ingredients.length === 0) {
        errEl.innerHTML = 'Resep harus memiliki minimal 1 bahan baku yang valid (Pilih bahan dan isi jumlahnya).';
        errEl.classList.remove('hidden'); return;
    }

    const payload = {
        name,
        category: category || null,
        default_portions,
        description: finalDesc || null,
        ingredients
    };

    errEl.classList.add('hidden');
    btn.disabled = true;
    btn.innerText = '⏳ Menyimpan...';

    try {
        const endpoint = isEdit ? `/api/recipes/${id}` : '/api/recipes';
        const method = isEdit ? 'PUT' : 'POST';

        const res = await fetch(endpoint, {
            method: method,
            headers: { 'Authorization': 'Bearer ' + authToken, 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify(payload)
        });

        const json = await res.json();
        
        if (res.ok && json.success) {
            tutupCrudResep();
            tampilToast('success', `Resep "${name}" berhasil disimpan!`);
            muatResep();
        } else {
            let pesanError = json.message || 'Gagal menyimpan resep.';
            if (res.status === 422 && json.errors) {
                pesanError = '';
                for (const [field, messages] of Object.entries(json.errors)) {
                    pesanError += `• ${messages.join(', ')} <br>`;
                }
            }
            errEl.innerHTML = pesanError;
            errEl.classList.remove('hidden');
        }
    } catch(e) {
        errEl.innerHTML = 'Koneksi ke server gagal.';
        errEl.classList.remove('hidden');
    } finally {
        btn.disabled = false;
        btn.innerText = isEdit ? 'Simpan Perubahan' : 'Simpan Resep Baru';
    }
}

async function hapusResepAdmin() {
    const id = document.getElementById('crud-id').value;
    const name = document.getElementById('crud-nama').value;
    if (!id || !confirm(`AWAS! Yakin ingin menghapus resep "${name}"?\nTindakan ini tidak bisa dibatalkan.`)) return;

    try {
        const res = await fetch(`/api/recipes/${id}`, {
            method: 'DELETE',
            headers: { 'Authorization': 'Bearer ' + authToken, 'Accept': 'application/json' }
        });
        const json = await res.json();
        if (res.ok && json.success) {
            tutupCrudResep();
            tampilToast('success', `Resep "${name}" telah dihapus.`);
            muatResep();
        } else {
            tampilToast('error', json.message || 'Gagal menghapus resep.');
        }
    } catch(e) {
        tampilToast('error', 'Koneksi ke server gagal.');
    }
}
</script>
@endsection