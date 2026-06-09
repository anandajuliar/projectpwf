@extends('layouts.app')
@section('title', 'Login')

@section('content')
<div class="flex justify-center items-center mt-10">
    <div class="bg-white p-8 rounded-lg shadow-lg w-96 border-t-4 border-amber-600">
        <h2 class="text-2xl font-bold text-center mb-2 text-gray-800">Login Karyawan</h2>
        <p class="text-center text-sm text-gray-500 mb-6">Masukkan kredensial akun Anda untuk melanjutkan.</p>

        {{-- Pesan Error --}}
        <div id="error-message" class="hidden mb-4 p-3 bg-red-50 text-red-700 text-sm rounded-lg border border-red-200 flex items-start gap-2">
            <span class="mt-0.5">❌</span>
            <span id="error-text"></span>
        </div>

        <form id="loginForm" novalidate>
            <div class="mb-4">
                <label class="block text-sm font-semibold mb-2 text-gray-700">Alamat Email</label>
                <input type="email" id="email"
                    class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500 transition"
                    placeholder="contoh@bakelab.com" required>
            </div>
            <div class="mb-6">
                <label class="block text-sm font-semibold mb-2 text-gray-700">Password</label>
                <input type="password" id="password"
                    class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500 transition"
                    placeholder="••••••••" required>
            </div>
            <button type="submit" id="loginBtn"
                class="w-full bg-amber-600 text-white font-bold py-3 px-4 rounded-lg hover:bg-amber-700 transition flex items-center justify-center gap-2">
                <svg id="spinner" class="hidden animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                </svg>
                <span id="btn-label">Masuk Sistem</span>
            </button>
        </form>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const loginForm = document.getElementById('loginForm');
        if (!loginForm) return;

        loginForm.addEventListener('submit', async function (e) {
            e.preventDefault();

            const email    = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value;
            const errorBox = document.getElementById('error-message');
            const errorTxt = document.getElementById('error-text');
            const loginBtn = document.getElementById('loginBtn');
            const spinner  = document.getElementById('spinner');
            const btnLabel = document.getElementById('btn-label');

            // Sembunyikan error lama
            errorBox.classList.add('hidden');

            // Validasi dasar sisi klien
            if (!email || !password) {
                errorTxt.textContent = 'Email dan password wajib diisi.';
                errorBox.classList.remove('hidden');
                return;
            }

            // Set loading state
            loginBtn.disabled  = true;
            spinner.classList.remove('hidden');
            btnLabel.textContent = 'Memeriksa...';

            try {
                const response = await fetch('/api/auth/login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ email, password }),
                });

                const result = await response.json();

                if (response.ok && result.success) {
                    const token    = result.data?.token;
                    const userRole = result.data?.user?.role || 'chef';

                    if (token) {
                        localStorage.setItem('auth_token', token);
                        localStorage.setItem('user_role', userRole);

                        // Arahkan sesuai role
                        btnLabel.textContent = 'Berhasil, mengalihkan...';
                        window.location.href = userRole === 'admin' ? '/dashboard' : '/products';
                    } else {
                        // Respons berhasil tapi token tidak tersedia — seharusnya tidak terjadi
                        errorTxt.textContent = 'Terjadi kesalahan pada sistem. Silakan coba lagi atau hubungi administrator.';
                        errorBox.classList.remove('hidden');
                        resetBtn();
                    }
                } else {
                    // Tangani berbagai kode status secara ramah
                    let pesan = 'Email atau password yang Anda masukkan tidak sesuai.';

                    if (response.status === 403) {
                        pesan = 'Akun Anda telah dinonaktifkan. Silakan hubungi administrator untuk bantuan.';
                    } else if (response.status === 429) {
                        pesan = 'Terlalu banyak percobaan login. Mohon tunggu beberapa saat sebelum mencoba kembali.';
                    } else if (response.status === 422) {
                        pesan = 'Format email tidak valid. Periksa kembali email yang Anda masukkan.';
                    } else if (result.message && !result.message.toLowerCase().includes('password')) {
                        // Tampilkan pesan dari server hanya jika tidak mengandung info sensitif
                        pesan = result.message;
                    }

                    errorTxt.textContent = pesan;
                    errorBox.classList.remove('hidden');
                    resetBtn();
                }
            } catch (err) {
                // Error jaringan / server tidak merespons
                errorTxt.textContent = 'Tidak dapat terhubung ke server. Periksa koneksi internet Anda dan coba lagi.';
                errorBox.classList.remove('hidden');
                resetBtn();
            }
        });

        function resetBtn() {
            const loginBtn = document.getElementById('loginBtn');
            const spinner  = document.getElementById('spinner');
            const btnLabel = document.getElementById('btn-label');
            loginBtn.disabled    = false;
            spinner.classList.add('hidden');
            btnLabel.textContent = 'Masuk Sistem';
        }
    });
</script>
@endsection