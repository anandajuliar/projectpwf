@extends('layouts.app')
@section('title', 'Manajemen Karyawan')

@section('content')
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
            </tr>
        </tbody>
    </table>
</div>

<div id="modalTambahChef" class="fixed inset-0 bg-black bg-opacity-50 hidden flex justify-center items-center z-50">
    <div class="bg-white p-8 rounded-2xl shadow-2xl w-full max-w-md transform transition-all">
        <h3 class="text-2xl font-bold text-gray-800 mb-6 border-b pb-3">Pendaftaran Chef Baru</h3>
        
        <form id="formTambahChef">
            <div class="mb-4">
                <label class="block text-sm font-bold text-gray-700 mb-2">Nama Lengkap</label>
                <input type="text" class="w-full p-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500" placeholder="Misal: Chef Juna" required>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-bold text-gray-700 mb-2">Email Login</label>
                <input type="email" class="w-full p-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500" placeholder="juna@pwf.com" required>
            </div>
            <div class="mb-6">
                <label class="block text-sm font-bold text-gray-700 mb-2">Password Sementara</label>
                <input type="password" class="w-full p-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500" placeholder="••••••••" required>
            </div>
            
            <div class="flex justify-end space-x-4 border-t pt-5">
                <button type="button" onclick="tutupModalChef()" class="px-5 py-2 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 transition">Batal</button>
                <button type="button" onclick="alert('Data pendaftaran siap ditembak ke endpoint /api/auth/register !')" class="px-5 py-2 bg-amber-600 text-white font-bold rounded-xl hover:bg-amber-700 transition shadow-md">Buat Akun</button>
            </div>
        </form>
    </div>
</div>

<script>
    // Proteksi satpam: Tendang kalau yang akses bukan Admin
    document.addEventListener("DOMContentLoaded", function() {
        if (localStorage.getItem('user_role') !== 'admin') {
            alert("Akses Ditolak! Anda bukan Admin.");
            window.location.href = '/products';
        }
    });

    // Kontrol Modal Pop-up
    function bukaModalChef() { 
        document.getElementById('modalTambahChef').classList.remove('hidden'); 
    }
    
    function tutupModalChef() { 
        document.getElementById('modalTambahChef').classList.add('hidden'); 
        document.getElementById('formTambahChef').reset();
    }
</script>
@endsection