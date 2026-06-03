@extends('layouts.app')
@section('title', 'Login')

@section('content')
<div class="flex justify-center items-center mt-10">
    <div class="bg-white p-8 rounded-lg shadow-lg w-96 border-t-4 border-amber-600">
        <h2 class="text-2xl font-bold text-center mb-6 text-gray-800">Login Karyawan</h2>
        
        <div id="error-message" class="hidden mb-4 p-3 bg-red-100 text-red-700 text-sm rounded border-l-4 border-red-500"></div>
        
        <form id="loginForm">
            <div class="mb-4">
                <label class="block text-sm font-semibold mb-2">Email</label>
                <input type="email" id="email" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-amber-500" placeholder="admin@pwf.com" required>
            </div>
            <div class="mb-6">
                <label class="block text-sm font-semibold mb-2">Password</label>
                <input type="password" id="password" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-amber-500" placeholder="••••••••" required>
            </div>
            <button type="submit" id="loginBtn" class="w-full bg-amber-600 text-white font-bold py-2 px-4 rounded hover:bg-amber-700 transition">
                Masuk Sistem
            </button>
        </form>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const loginForm = document.getElementById('loginForm');
        
        if(loginForm) {
            loginForm.addEventListener('submit', async function(e) {
                e.preventDefault(); 
                
                const email = document.getElementById('email').value;
                const password = document.getElementById('password').value;
                const errorBox = document.getElementById('error-message');
                const loginBtn = document.getElementById('loginBtn');
                
                loginBtn.innerHTML = 'Mengecek...';
                loginBtn.disabled = true;

                try {
                    const response = await fetch('/api/auth/login', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ email: email, password: password })
                    });

                    // Kita simpan seluruh balasan backend di variabel 'responBackend'
                    const responBackend = await response.json();
                    
                    console.log("BALASAN ASLI DARI BACKEND:", responBackend);

                    if (response.ok && responBackend.success) {
                        // KITA BONGKAR LACINYA: ambil token dari dalam objek 'data'
                        const token = responBackend.data.token;
                        
                        if (token) {
                            // Simpan token
                            localStorage.setItem('auth_token', token);
                            
                            // Ambil role asli dari database (atau fallback ke admin kalau error)
                            const userRole = responBackend.data.user.role || 'admin';
                            localStorage.setItem('user_role', userRole);

                            // Langsung arahkan ke halaman yang sesuai
                            window.location.href = userRole === 'admin' ? '/dashboard' : '/products';
                        } else {
                            alert("Token tetap tidak ketemu di dalam laci data!");
                            loginBtn.innerHTML = 'Masuk Sistem';
                            loginBtn.disabled = false;
                        }
                    } else {
                        errorBox.innerHTML = "Ditolak Backend: " + (responBackend.message || "Email/Password salah!");
                        errorBox.classList.remove('hidden');
                        loginBtn.innerHTML = 'Masuk Sistem';
                        loginBtn.disabled = false;
                    }
                } catch (error) {
                    console.error('Error Jaringan:', error);
                    errorBox.innerHTML = "Gagal terhubung ke server!";
                    errorBox.classList.remove('hidden');
                    loginBtn.innerHTML = 'Masuk Sistem';
                    loginBtn.disabled = false;
                }
            });
        }
    });
</script>
@endsection