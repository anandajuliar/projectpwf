@extends('layouts.app')
@section('title', 'Manajemen Karyawan')

@section('content')

{{-- Toast Notification --}}
<div id="toast-container" class="fixed top-5 right-5 z-[9999] flex flex-col gap-3 pointer-events-none"></div>

<div class="mb-8 border-b pb-4">
    <h2 class="text-3xl font-bold text-gray-800">Manajemen Karyawan</h2>
    <p class="text-gray-500 mt-1">Kelola akses akun staf dapur (Chef) dan Admin.</p>
</div>

<div class="bg-white shadow-md rounded overflow-x-auto">
    <div class="p-4 border-b bg-gray-50 flex justify-between items-center">
        <h3 class="font-bold text-gray-700">Daftar Pengguna Aktif</h3>
        <button onclick="bukaModalChef()" class="bg-amber-600 hover:bg-amber-700 text-white font-bold py-2 px-4 rounded shadow transition">
            + Daftarkan Chef Baru
        </button>
    </div>
    <table class="min-w-full bg-white">
        <thead class="bg-gray-800 text-white">
            <tr>
                <th class="w-1/4 text-left py-3 px-4 uppercase font-semibold text-sm">Nama Karyawan</th>
                <th class="w-1/4 text-left py-3 px-4 uppercase font-semibold text-sm">Email Akses</th>
                <th class="w-1/4 text-left py-3 px-4 uppercase font-semibold text-sm">Posisi (Role)</th>
                <th class="text-center py-3 px-4 uppercase font-semibold text-sm">Aksi</th>
            </tr>
        </thead>
        <tbody id="tabel-pengguna" class="text-gray-700">
            <tr>
                <td colspan="4" class="text-center py-8 text-gray-400">Memuat data pengguna...</td>
            </tr>
        </tbody>
    </table>
</div>

{{-- Modal Tambah Chef --}}
<div id="modalTambahChef" class="fixed inset-0 bg-black bg-opacity-50 hidden flex justify-center items-center z-50">
    <div class="bg-white p-8 rounded-2xl shadow-2xl w-full max-w-md transform transition-all">
        <h3 class="text-2xl font-bold text-gray-800 mb-1">Pendaftaran Chef Baru</h3>
        <p class="text-sm text-gray-500 mb-6">Isi data berikut untuk membuat akun staf dapur baru.</p>

        <form id="formTambahChef" novalidate>
            <div class="mb-4">
                <label class="block text-sm font-bold text-gray-700 mb-2">Nama Lengkap</label>
                <input id="input-nama" type="text"
                    class="w-full p-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 transition"
                    placeholder="Contoh: Chef Juna" required>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-bold text-gray-700 mb-2">Alamat Email</label>
                <input id="input-email" type="email"
                    class="w-full p-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 transition"
                    placeholder="chef.juna@bakelab.com" required>
            </div>
            <div class="mb-6">
                <label class="block text-sm font-bold text-gray-700 mb-2">Password Sementara</label>
                <input id="input-password" type="password"
                    class="w-full p-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 transition"
                    placeholder="Minimal 8 karakter" required>
                <p class="text-xs text-gray-400 mt-1">Chef dapat mengubah password setelah login pertama.</p>
            </div>

            {{-- Pesan error inline --}}
            <div id="form-error" class="hidden mb-4 bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl px-4 py-3"></div>

            <div class="flex justify-end space-x-4 border-t pt-5">
                <button type="button" onclick="tutupModalChef()"
                    class="px-5 py-2 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 transition">
                    Batal
                </button>
                <button type="button" id="btn-buat-akun" onclick="submitBuatAkun()"
                    class="px-5 py-2 bg-amber-600 text-white font-bold rounded-xl hover:bg-amber-700 transition shadow-md flex items-center gap-2">
                    <svg id="icon-loading" class="hidden animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                    <span id="label-btn">Buat Akun</span>
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    @keyframes slideInRight {
        from { opacity: 0; transform: translateX(60px); }
        to   { opacity: 1; transform: translateX(0); }
    }
    @keyframes fadeOut {
        from { opacity: 1; }
        to   { opacity: 0; }
    }
    .toast-enter { animation: slideInRight 0.35s ease forwards; }
    .toast-exit  { animation: fadeOut 0.4s ease forwards; }
</style>

<script>
// ===================================================================
// TOAST NOTIFICATION
// ===================================================================
function tampilkanToast(pesan, tipe = 'sukses') {
    const container = document.getElementById('toast-container');
    const warna = tipe === 'sukses'
        ? 'bg-green-600 text-white'
        : tipe === 'peringatan'
        ? 'bg-amber-500 text-white'
        : 'bg-red-600 text-white';
    const ikon = tipe === 'sukses' ? '' : tipe === 'peringatan' ? '' : '';

    const toast = document.createElement('div');
    toast.className = `pointer-events-auto ${warna} rounded-xl shadow-lg px-5 py-4 flex items-start gap-3 max-w-sm toast-enter`;
    toast.innerHTML = `
        <span class="text-lg leading-none mt-0.5">${ikon}</span>
        <p class="text-sm font-medium leading-snug">${pesan}</p>
    `;
    container.appendChild(toast);

    setTimeout(() => {
        toast.classList.add('toast-exit');
        setTimeout(() => toast.remove(), 400);
    }, 4000);
}

// ===================================================================
// PROTEKSI HALAMAN: Hanya Admin
// ===================================================================
document.addEventListener("DOMContentLoaded", function () {
    const role = localStorage.getItem('user_role');
    if (role !== 'admin') {
        tampilkanToast('Anda tidak memiliki akses ke halaman ini.', 'error');
        setTimeout(() => { window.location.href = '/products'; }, 1500);
        return;
    }
    muatDaftarPengguna();
});

// ===================================================================
// KONTROL MODAL
// ===================================================================
function bukaModalChef() {
    document.getElementById('modalTambahChef').classList.remove('hidden');
    document.getElementById('form-error').classList.add('hidden');
    document.getElementById('formTambahChef').reset();
}

function tutupModalChef() {
    document.getElementById('modalTambahChef').classList.add('hidden');
    document.getElementById('formTambahChef').reset();
    document.getElementById('form-error').classList.add('hidden');
    setLoadingBtn(false);
}

// ===================================================================
// SET LOADING STATE TOMBOL
// ===================================================================
function setLoadingBtn(loading) {
    const btn   = document.getElementById('btn-buat-akun');
    const ikon  = document.getElementById('icon-loading');
    const label = document.getElementById('label-btn');
    btn.disabled    = loading;
    ikon.classList.toggle('hidden', !loading);
    label.textContent = loading ? 'Memproses...' : 'Buat Akun';
}

// ===================================================================
// SUBMIT FORM → API /api/auth/register
// ===================================================================
async function submitBuatAkun() {
    const nama     = document.getElementById('input-nama').value.trim();
    const email    = document.getElementById('input-email').value.trim();
    const password = document.getElementById('input-password').value;
    const errorBox = document.getElementById('form-error');

    // Validasi sisi klien
    if (!nama || !email || !password) {
        errorBox.textContent = 'Harap lengkapi semua kolom sebelum melanjutkan.';
        errorBox.classList.remove('hidden');
        return;
    }
    if (password.length < 8) {
        errorBox.textContent = 'Password harus terdiri dari minimal 8 karakter.';
        errorBox.classList.remove('hidden');
        return;
    }
    errorBox.classList.add('hidden');

    const token = localStorage.getItem('auth_token');
    if (!token) {
        tampilkanToast('Sesi Anda telah berakhir. Silakan login kembali.', 'error');
        setTimeout(() => { window.location.href = '/login'; }, 1500);
        return;
    }

    setLoadingBtn(true);

    try {
        const res = await fetch('/api/auth/register', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer ' + token,
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                name: nama,
                email: email,
                password: password,
                password_confirmation: password,
                role: 'chef',
            }),
        });

        const json = await res.json();

        if (res.ok && json.success) {
            tutupModalChef();
            tampilkanToast(`Akun untuk ${nama} berhasil dibuat. Chef dapat langsung login.`, 'sukses');
            muatDaftarPengguna(); // Refresh tabel
        } else {
            // Tangani pesan error dari server secara ramah
            let pesanError = 'Terjadi kendala saat membuat akun. Silakan coba lagi.';
            if (json.errors) {
                const pesanList = Object.values(json.errors).flat();
                if (pesanList.length > 0) pesanError = pesanList[0];
            } else if (json.message) {
                const msg = json.message.toLowerCase();
                if (msg.includes('email') && (msg.includes('taken') || msg.includes('unique'))) {
                    pesanError = 'Alamat email ini sudah terdaftar. Gunakan email yang berbeda.';
                } else if (res.status === 401 || res.status === 403) {
                    pesanError = 'Anda tidak memiliki wewenang untuk melakukan tindakan ini.';
                } else if (res.status === 422) {
                    pesanError = 'Data yang dimasukkan tidak valid. Periksa kembali isian form.';
                } else {
                    pesanError = json.message;
                }
            }
            errorBox.textContent = pesanError;
            errorBox.classList.remove('hidden');
        }
    } catch (err) {
        errorBox.textContent = 'Tidak dapat terhubung ke server. Periksa koneksi internet Anda.';
        errorBox.classList.remove('hidden');
        console.error(err);
    } finally {
        setLoadingBtn(false);
    }
}

// ===================================================================
// MUAT DAFTAR PENGGUNA DARI API
// ===================================================================
async function muatDaftarPengguna() {
    const token = localStorage.getItem('auth_token');
    const tbody = document.getElementById('tabel-pengguna');

    try {
        const res  = await fetch('/api/users', {
            headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' }
        });
        const json = await res.json();

        if (!res.ok || !json.success) throw new Error();

        const users = json.data || [];
        if (!users.length) {
            tbody.innerHTML = '<tr><td colspan="4" class="text-center py-8 text-gray-400">Belum ada pengguna terdaftar.</td></tr>';
            return;
        }

        tbody.innerHTML = users.map(u => {
            const isAdmin = u.role === 'admin';
            const badgeClass = isAdmin
                ? 'bg-blue-100 text-blue-800'
                : 'bg-amber-100 text-amber-800';
            const badgeLabel = isAdmin ? 'Admin Gudang' : 'Staf Dapur';
            const ikon = isAdmin ? '' : '';
            const aksi = isAdmin
                ? '<td class="text-center py-3 px-4 text-gray-400 italic text-sm">Tidak bisa dihapus</td>'
                : `<td class="text-center py-3 px-4">
                       <button onclick="cabutAkses(${u.id}, '${escHtml(u.name)}')"
                           class="text-red-500 hover:text-red-700 font-semibold text-sm transition">
                           Cabut Akses
                       </button>
                   </td>`;

            return `<tr class="border-b hover:bg-gray-50">
                <td class="text-left py-3 px-4 flex items-center">
                    <span class="text-2xl mr-2">${ikon}</span> ${escHtml(u.name)}
                </td>
                <td class="text-left py-3 px-4">${escHtml(u.email)}</td>
                <td class="text-left py-3 px-4">
                    <span class="${badgeClass} text-xs font-bold px-3 py-1 rounded-full uppercase">${badgeLabel}</span>
                </td>
                ${aksi}
            </tr>`;
        }).join('');

    } catch (e) {
        tbody.innerHTML = '<tr><td colspan="4" class="text-center py-8 text-red-400">Gagal memuat data. Muat ulang halaman untuk mencoba lagi.</td></tr>';
    }
}

// ===================================================================
// CABUT AKSES (Hapus User)
// ===================================================================
async function cabutAkses(userId, namaUser) {
    if (!confirm(`Yakin ingin mencabut akses untuk ${namaUser}?\nChef tersebut tidak akan bisa login setelah ini.`)) return;

    const token = localStorage.getItem('auth_token');
    try {
        const res = await fetch(`/api/users/${userId}`, {
            method: 'DELETE',
            headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' }
        });

        if (res.ok) {
            tampilkanToast(`Akses ${namaUser} berhasil dicabut.`, 'sukses');
            muatDaftarPengguna();
        } else {
            tampilkanToast('Gagal mencabut akses. Silakan coba lagi.', 'error');
        }
    } catch (e) {
        tampilkanToast('Tidak dapat terhubung ke server.', 'error');
    }
}

function escHtml(str) {
    const d = document.createElement('div');
    d.appendChild(document.createTextNode(str || ''));
    return d.innerHTML;
}
</script>
@endsection