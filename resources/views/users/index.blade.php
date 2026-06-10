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
        <button onclick="bukaModalKaryawan()" class="bg-amber-600 hover:bg-amber-700 text-white font-bold py-2 px-4 rounded shadow transition">
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
<<<<<<< HEAD
        <tbody class="text-gray-700">
            <tr class="border-b hover:bg-gray-50">
                <td class="text-left py-3 px-4 flex items-center"><span class="text-2xl mr-2">👨‍💼</span> Admin Utama</td>
                <td class="text-left py-3 px-4">admin@pwf.com</td>
                <td class="text-left py-3 px-4"><span class="bg-blue-100 text-blue-800 text-xs font-bold px-3 py-1 rounded-full uppercase">Admin Gudang</span></td>
                <td class="text-center py-3 px-4 text-gray-400 italic">Tidak bisa dihapus</td>
            </tr>
            <tr class="border-b hover:bg-gray-50">
                <td class="text-left py-3 px-4 flex items-center"><span class="text-2xl mr-2">👨‍🍳</span> Chef Budi</td>
                <td class="text-left py-3 px-4">chef@pwf.com</td>
                <td class="text-left py-3 px-4"><span class="bg-amber-100 text-amber-800 text-xs font-bold px-3 py-1 rounded-full uppercase">Staf Dapur</span></td>
                <td class="text-center py-3 px-4">
                    <button class="text-red-500 hover:text-red-700 font-semibold" onclick="alert('Fungsi Cabut Akses disiapkan!')">Cabut Akses</button>
                </td>
=======
        <tbody id="tabel-pengguna" class="text-gray-700">
            <tr>
                <td colspan="4" class="text-center py-8 text-gray-400">Memuat data pengguna...</td>
>>>>>>> f5f09724dc2df00cc30be900d0299641fb86156b
            </tr>
        </tbody>
    </table>
</div>

{{-- Modal Tambah / Edit Karyawan --}}
<div id="modalKaryawan" class="fixed inset-0 bg-black bg-opacity-50 hidden flex justify-center items-center z-50">
    <div class="bg-white p-8 rounded-2xl shadow-2xl w-full max-w-md transform transition-all">
        <h3 id="modal-title" class="text-2xl font-bold text-gray-800 mb-1">Pendaftaran Chef Baru</h3>
        <p id="modal-desc" class="text-sm text-gray-500 mb-6">Isi data berikut untuk membuat akun staf dapur baru.</p>

        <form id="formKaryawan" novalidate>
            <input type="hidden" id="edit-id" value="">
            
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
                <label class="block text-sm font-bold text-gray-700 mb-2">Password <span id="password-hint" class="text-red-500">*</span></label>
                <input id="input-password" type="password"
                    class="w-full p-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 transition"
                    placeholder="Minimal 8 karakter">
                <p id="password-help" class="text-xs text-amber-600 mt-1 font-semibold hidden">
                    *Kosongkan kolom ini jika tidak ingin mereset password karyawan.
                </p>
            </div>

            <div id="form-error" class="hidden mb-4 bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl px-4 py-3"></div>

            <div class="flex justify-end space-x-4 border-t pt-5">
                <button type="button" onclick="tutupModalKaryawan()"
                    class="px-5 py-2 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 transition">Batal</button>
                <button type="button" id="btn-submit" onclick="submitKaryawan()"
                    class="px-5 py-2 bg-amber-600 text-white font-bold rounded-xl hover:bg-amber-700 transition shadow-md">
                    <span id="label-btn">Simpan Data</span>
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
    const ikon = tipe === 'sukses' ? '✅' : tipe === 'peringatan' ? '⚠️' : '❌';

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
// KONTROL MODAL (TAMBAH / EDIT)
// ===================================================================
function bukaModalKaryawan(user = null) {
    const modal = document.getElementById('modalKaryawan');
    document.getElementById('formKaryawan').reset();
    document.getElementById('form-error').classList.add('hidden');
    
    if (user) {
        // Mode Edit
        document.getElementById('modal-title').innerText = 'Edit Data Karyawan';
        document.getElementById('modal-desc').innerText = 'Perbarui data diri atau reset password karyawan.';
        document.getElementById('edit-id').value = user.id;
        document.getElementById('input-nama').value = user.name;
        document.getElementById('input-email').value = user.email;
        
        document.getElementById('password-hint').innerText = '(Opsional)';
        document.getElementById('password-help').classList.remove('hidden');
    } else {
        // Mode Tambah
        document.getElementById('modal-title').innerText = 'Pendaftaran Chef Baru';
        document.getElementById('modal-desc').innerText = 'Isi data berikut untuk membuat akun staf dapur baru.';
        document.getElementById('edit-id').value = '';
        
        document.getElementById('password-hint').innerText = '*';
        document.getElementById('password-help').classList.add('hidden');
    }
    modal.classList.remove('hidden');
}

function tutupModalKaryawan() {
    document.getElementById('modalKaryawan').classList.add('hidden');
    document.getElementById('btn-submit').disabled = false;
    document.getElementById('label-btn').innerText = 'Simpan Data';
}

// ===================================================================
// SUBMIT FORM (CREATE / UPDATE)
// ===================================================================
async function submitKaryawan() {
    const id       = document.getElementById('edit-id').value;
    const nama     = document.getElementById('input-nama').value.trim();
    const email    = document.getElementById('input-email').value.trim();
    const password = document.getElementById('input-password').value;
    const errorBox = document.getElementById('form-error');

    if (!nama || !email) {
        errorBox.textContent = 'Nama dan email wajib diisi.';
        errorBox.classList.remove('hidden');
        return;
    }
    if (!id && password.length < 8) {
        errorBox.textContent = 'Untuk akun baru, password wajib minimal 8 karakter.';
        errorBox.classList.remove('hidden');
        return;
    }

    const token = localStorage.getItem('auth_token');
    const isEdit = id !== '';
    const endpoint = isEdit ? `/api/users/${id}` : '/api/users'; // Jika edit tembak PUT, jika tambah tembak POST
    const method = isEdit ? 'PUT' : 'POST';

    const payload = { name: nama, email: email };
    
    // Kirim password hanya jika diisi
    if (password) {
        payload.password = password;
        payload.password_confirmation = password;
    }

    errorBox.classList.add('hidden');
    document.getElementById('btn-submit').disabled = true;
    document.getElementById('label-btn').innerText = 'Memproses...';

    try {
        const res = await fetch(endpoint, {
            method: method,
            headers: { 'Content-Type': 'application/json', 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' },
            body: JSON.stringify(payload),
        });
        const json = await res.json();

        if (res.ok && json.success) {
            tutupModalKaryawan();
            tampilkanToast(`Data ${nama} berhasil disimpan!`, 'sukses');
            muatDaftarPengguna();
        } else {
            errorBox.textContent = json.message || 'Gagal menyimpan data.';
            errorBox.classList.remove('hidden');
        }
    } catch (err) {
        errorBox.textContent = 'Koneksi ke server gagal.';
        errorBox.classList.remove('hidden');
    } finally {
        document.getElementById('btn-submit').disabled = false;
        document.getElementById('label-btn').innerText = 'Simpan Data';
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
            const ikon = isAdmin ? '👨‍💼' : '👨‍🍳';
            const isSelf = u.email === 'admin@pwf.com' || u.email === 'chef@bakelab.com'; 

            const aksi = `
                <td class="text-center py-3 px-4">
                    <button onclick='bukaModalKaryawan(${JSON.stringify(u)})' class="text-blue-500 hover:text-blue-700 font-semibold text-sm mr-3 transition">Edit</button>
                    ${isSelf ? '<span class="text-gray-400 italic text-sm">(Anda)</span>' : `<button onclick="cabutAkses(${u.id}, '${escHtml(u.name)}')" class="text-red-500 hover:text-red-700 font-semibold text-sm transition">Hapus</button>`}
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
// CABUT AKSES (Nonaktifkan / Hapus)
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