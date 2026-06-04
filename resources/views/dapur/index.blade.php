@extends('layouts.app')
@section('title', 'Dapur Produksi')

@section('content')
<div class="mb-8 border-b pb-4 flex justify-between items-start">
    <div>
        <h2 class="text-3xl font-bold text-gray-800">Dapur Produksi</h2>
        <p class="text-gray-500 mt-1">Pilih resep dan eksekusi untuk memotong stok bahan secara otomatis.</p>
    </div>
    <span id="badge-role" class="hidden bg-amber-100 text-amber-800 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wide self-center"></span>
</div>

{{-- Grid Resep --}}
<div id="recipe-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
    <div class="col-span-4 text-center py-12 text-gray-400">Memuat resep...</div>
</div>

{{-- ============================================================ --}}
{{-- MODAL DETAIL RESEP & EKSEKUSI --}}
{{-- ============================================================ --}}
<div id="modalResep" class="fixed inset-0 bg-black bg-opacity-60 hidden flex justify-center items-center z-50">
    <div class="bg-white p-0 rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">
        {{-- Header modal --}}
        <div class="bg-amber-600 text-white px-8 py-5 flex justify-between items-center">
            <h3 id="modal-judul" class="text-2xl font-bold"></h3>
            <button onclick="tutupResep()" class="text-white hover:text-amber-200 text-3xl font-bold leading-none">&times;</button>
        </div>

        <div class="px-8 py-6">
            {{-- Kategori --}}
            <div class="mb-4">
                <span id="modal-kategori" class="bg-amber-100 text-amber-800 text-xs font-bold px-3 py-1 rounded-full"></span>
            </div>

            {{-- Deskripsi --}}
            <p id="modal-deskripsi" class="text-gray-600 text-sm mb-5 italic"></p>

            {{-- Komposisi bahan --}}
            <div class="mb-5">
                <h4 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-3">Komposisi Bahan (per porsi):</h4>
                <div id="modal-ingredients" class="space-y-2 max-h-56 overflow-y-auto pr-1"></div>
            </div>

            {{-- Input porsi --}}
            <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 mb-4">
                <label class="block text-sm font-bold text-gray-700 mb-2">Jumlah Porsi / Loyang yang Akan Dibuat:</label>
                <div class="flex items-center gap-3">
                    <button onclick="ubahPorsi(-1)" class="w-10 h-10 rounded-full bg-gray-200 hover:bg-gray-300 text-xl font-bold flex items-center justify-center transition">−</button>
                    <input type="number" id="input-porsi" value="1" min="1" max="999"
                        class="w-24 text-center text-xl font-bold border-2 border-amber-400 rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-amber-500">
                    <button onclick="ubahPorsi(1)" class="w-10 h-10 rounded-full bg-gray-200 hover:bg-gray-300 text-xl font-bold flex items-center justify-center transition">+</button>
                    <span class="text-gray-500 text-sm">porsi</span>
                </div>
            </div>

            {{-- Catatan opsional --}}
            <div class="mb-4">
                <input type="text" id="modal-note" maxlength="500" placeholder="Catatan produksi (opsional)..."
                    class="w-full p-3 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
            </div>

            {{-- Error --}}
            <div id="modal-error" class="hidden bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg p-3 mb-4"></div>

            {{-- Tombol eksekusi --}}
            <button id="btn-eksekusi" onclick="eksekusiResep()"
                class="w-full bg-green-600 hover:bg-green-700 active:scale-95 text-white font-bold py-4 rounded-xl text-lg shadow-md transition transform flex justify-center items-center gap-2">
                <span>👨‍🍳</span> Mulai Produksi (Potong Stok Otomatis)
            </button>
        </div>
    </div>
</div>

{{-- TOAST --}}
<div id="toast" class="fixed bottom-6 right-6 z-[100] hidden">
    <div id="toast-inner" class="px-6 py-4 rounded-xl shadow-xl text-white font-semibold text-sm max-w-sm flex items-center gap-3">
        <span id="toast-msg"></span>
    </div>
</div>

<script>
const EMOJI_MAP = ['🎂','🍰','🥐','🍫','🧁','🥖','🍩','🍪','🎂','🍮'];
let authToken     = localStorage.getItem('auth_token');
let userRole      = localStorage.getItem('user_role');
let selectedRecipe = null;
let toastTimeout;

document.addEventListener('DOMContentLoaded', function () {
    if (!authToken) { window.location.href = '/login'; return; }

    const badge = document.getElementById('badge-role');
    badge.innerText = userRole === 'admin' ? 'Admin' : '👨‍🍳 Chef';
    badge.classList.remove('hidden');

    muatResep();
});

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
        grid.innerHTML = '<div class="col-span-4 text-center py-12 text-gray-400">Belum ada resep yang tersedia. Admin perlu menambahkan resep terlebih dahulu.</div>';
        return;
    }

    grid.innerHTML = recipes.map((r, i) => {
        const emoji = EMOJI_MAP[i % EMOJI_MAP.length];
        const bgColors = ['bg-amber-100','bg-orange-100','bg-yellow-100','bg-rose-100','bg-lime-100','bg-sky-100','bg-purple-100','bg-pink-100'];
        const bg = bgColors[i % bgColors.length];

        return `
        <div onclick='bukaResep(${JSON.stringify(r)})' class="bg-white p-0 rounded-2xl shadow-lg border border-gray-100 overflow-hidden cursor-pointer transform transition hover:scale-105 hover:shadow-xl">
            <div class="${bg} h-32 flex items-center justify-center">
                <span class="text-6xl">${emoji}</span>
            </div>
            <div class="p-5 text-center">
                <h3 class="text-xl font-bold text-gray-800 mb-2 line-clamp-2">${escHtml(r.name)}</h3>
                <span class="bg-amber-100 text-amber-800 text-xs font-bold px-3 py-1 rounded-full">${escHtml(r.category || 'Resep')}</span>
                <p class="text-xs text-gray-400 mt-2">${(r.ingredients || []).length} bahan baku</p>
            </div>
        </div>`;
    }).join('');
}

async function bukaResep(recipe) {
    try {
        const res  = await fetch(`/api/recipes/${recipe.id}`, {
            headers: { 'Authorization': 'Bearer ' + authToken, 'Accept': 'application/json' }
        });
        const json = await res.json();
        if (!res.ok || !json.success) throw new Error();

        selectedRecipe = json.data;
        renderModalResep(selectedRecipe);
        document.getElementById('modalResep').classList.remove('hidden');
        document.getElementById('input-porsi').focus();
    } catch (e) {
        tampilToast('error', 'Gagal memuat detail resep.');
    }
}

function renderModalResep(r) {
    document.getElementById('modal-judul').innerText    = r.name;
    document.getElementById('modal-kategori').innerText = r.category || 'Resep';
    document.getElementById('modal-deskripsi').innerText = r.description || '';
    document.getElementById('input-porsi').value        = r.default_portions || 1;
    document.getElementById('modal-note').value         = '';
    document.getElementById('modal-error').classList.add('hidden');

    const ingredients = r.ingredients || [];
    const ingHtml = ingredients.length
        ? ingredients.map(ing => {
            const statusCfg = {
                'normal': { dot: 'bg-green-400', info: `Stok: ${ing.stock_available} ${ing.unit}` },
                'low':    { dot: 'bg-amber-400', info: `⚠️ Stok: ${ing.stock_available} ${ing.unit}` },
                'out':    { dot: 'bg-red-500',   info: `🚨 Stok habis!` },
            }[ing.stock_status] || { dot: 'bg-gray-400', info: '' };

            return `
            <div class="flex justify-between items-center bg-gray-50 rounded-lg px-3 py-2 border border-gray-100">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full ${statusCfg.dot} inline-block flex-shrink-0"></span>
                    <span class="font-semibold text-gray-700 text-sm">${escHtml(ing.product_name)}</span>
                    ${ing.note ? `<span class="text-xs text-gray-400 italic">(${escHtml(ing.note)})</span>` : ''}
                </div>
                <div class="text-right text-sm">
                    <span class="font-bold text-amber-700">${ing.qty_per_portion} ${escHtml(ing.unit)}</span>/porsi
                    <p class="text-xs text-gray-400">${statusCfg.info}</p>
                </div>
            </div>`;
        }).join('')
        : '<p class="text-gray-400 text-sm">Belum ada bahan yang didaftarkan.</p>';

    document.getElementById('modal-ingredients').innerHTML = ingHtml;
}

function tutupResep() {
    document.getElementById('modalResep').classList.add('hidden');
    selectedRecipe = null;
}

function ubahPorsi(delta) {
    const input = document.getElementById('input-porsi');
    const val   = Math.max(1, (parseInt(input.value) || 1) + delta);
    input.value = val;
}

async function eksekusiResep() {
    if (!selectedRecipe) return;

    const portions = parseInt(document.getElementById('input-porsi').value) || 1;
    const note     = document.getElementById('modal-note').value.trim();
    const errEl    = document.getElementById('modal-error');
    const btn      = document.getElementById('btn-eksekusi');

    if (portions < 1) {
        errEl.innerText = 'Jumlah porsi minimal 1.';
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
            const pesanToast = json.message || `✅ Berhasil memproduksi ${portions} porsi "${selectedRecipe.name}"!`;
            tutupResep();
            tampilToast('success', pesanToast);
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            // Tampilkan detail bahan yang tidak mencukupi
            if (json.data?.insufficient_items?.length) {
                const detail = json.data.insufficient_items.map(i =>
                    `• ${i.product_name}: butuh ${i.required} ${i.unit}, tersedia ${i.available} ${i.unit} (kurang ${i.shortage})`
                ).join('\n');
                errEl.innerText = json.message + '\n\n' + detail;
            } else {
                errEl.innerText = json.message || 'Eksekusi resep gagal.';
            }
            errEl.classList.remove('hidden');
            errEl.style.whiteSpace = 'pre-line';
        }
    } catch (e) {
        console.error("Crash JS:", e);
        tampilToast('error', 'Terjadi kesalahan sistem di frontend.');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<span>👨‍🍳</span> Mulai Produksi (Potong Stok Otomatis)';
    }
}

function tampilToast(type, msg) {
    const toast = document.getElementById('toast');
    const inner = document.getElementById('toast-inner');
    inner.className = `px-6 py-4 rounded-xl shadow-xl text-white font-semibold text-sm max-w-sm ${type === 'success' ? 'bg-green-600' : 'bg-red-600'}`;
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
</script>
@endsection