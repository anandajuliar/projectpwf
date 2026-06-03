<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BakeLab - @yield('title', 'Sistem Gudang')</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800">
    
    <nav class="bg-amber-600 p-4 text-white shadow-md">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-2xl font-bold tracking-wider cursor-pointer" onclick="window.location.href='/'">🥐 BakeLab</h1>
            
            <div id="nav-menu" class="hidden items-center"> 
                <a href="/dashboard" id="menu-dashboard" class="px-3 font-semibold hover:text-amber-200">Dashboard</a>
                
                <a href="/users" id="menu-karyawan" class="px-3 font-semibold hover:text-amber-200 hidden">Akun Karyawan</a>
                
                <a href="/products" id="menu-gudang" class="px-3 font-semibold hover:text-amber-200">Stok Gudang</a>
                
                <a href="/dapur" id="menu-dapur" class="px-3 font-semibold hover:text-amber-200">Dapur Produksi</a>
                
                <button onclick="logout()" class="ml-4 px-4 py-2 text-sm bg-red-700 font-bold rounded hover:bg-red-800 transition">Logout</button>
            </div>
        </div>
    </nav>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const token = localStorage.getItem('auth_token');
            const role = localStorage.getItem('user_role');
            
            // Tampilkan navigasi jika user sudah login
            if(token && window.location.pathname !== '/login') {
                document.getElementById('nav-menu').classList.remove('hidden');
                document.getElementById('nav-menu').classList.add('flex');
                
                // ATUR HAK AKSES MODUL VISUAL
                if(role === 'chef') {
                    // Chef dilarang lihat Dashboard utama
                    document.getElementById('menu-dashboard').style.display = 'none';
                    // Ubah nama label biar lebih ramah buat Chef di dapur
                    document.getElementById('menu-gudang').innerText = 'Cek Sisa Stok';
                } else if(role === 'admin') {
                    // Admin melihat semua menu dengan nama formal manajemen
                    document.getElementById('menu-gudang').innerText = 'Master Gudang';
                    document.getElementById('menu-karyawan').classList.remove('hidden')
                }
            }
        });

        function logout() {
            localStorage.removeItem('auth_token');
            localStorage.removeItem('user_role');
            window.location.href = '/login';
        }
    </script>

    <main class="container mx-auto p-6 mt-4">
        @yield('content')
    </main>

</body>
</html>