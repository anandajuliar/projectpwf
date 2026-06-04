@extends('layouts.app')
@section('title', 'Gudang & Dapur')

@section('content')
<div class="mb-8 border-b pb-4">
    <h2 class="text-3xl font-bold text-gray-800" id="page-title">Memuat...</h2>
    <p class="text-gray-500 mt-1" id="page-desc">Silakan tunggu sebentar.</p>
</div>

<div id="admin-view" class="hidden">
    <div class="flex justify-end mb-4">
        <button onclick="bukaModalTambah()" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded shadow transition">
            + Tambah Bahan Baku Baru
        </button>
    </div>
    
    <div class="bg-white shadow-md rounded overflow-x-auto">
        <table class="min-w-full bg-white">
            <thead class="bg-amber-700 text-white">
                <tr>
                    <th class="w-1/3 text-left py-3 px-4 uppercase font-semibold text-sm">Nama Bahan</th>
                    <th class="w-1/4 text-left py-3 px-4 uppercase font-semibold text-sm">Stok Gudang</th>
                    <th class="w-1/4 text-left py-3 px-4 uppercase font-semibold text-sm">Harga Satuan</th>
                    <th class="text-center py-3 px-4 uppercase font-semibold text-sm">Aksi Master</th>
                </tr>
            </thead>
            <tbody class="text-gray-700">
                <tr class="border-b">
                    <td class="text-left py-3 px-4">Tepung Terigu Protein Tinggi</td>
                    <td class="text-left py-3 px-4 font-bold text-red-600">50 gram</td>
                    <td class="text-left py-3 px-4">Rp 15.000</td>
                    <td class="text-center py-3 px-4">
                        <button class="text-blue-500 hover:text-blue-700 mr-3 font-semibold">Edit</button>
                        <button class="text-red-500 hover:text-red-700 font-semibold">Hapus</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<div id="chef-view" class="hidden">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <div class="bg-white p-6 rounded-2xl shadow-lg border border-gray-100 flex flex-col justify-between">
            <div>
                <span class="bg-red-100 text-red-800 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wide">Stok Kritis</span>
                <h3 class="text-2xl font-bold text-gray-800 mt-3 mb-1 line-clamp-2">Tepung Terigu Protein Tinggi</h3>
                <p class="text-red-600 font-extrabold text-2xl mb-6">Sisa: 50 g</p>
            </div>
            <button class="w-full bg-amber-500 hover:bg-amber-600 text-white font-bold py-4 rounded-xl text-lg shadow-md transition transform active:scale-95">
                👨‍🍳 Gunakan Bahan
            </button>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-lg border border-gray-100 flex flex-col justify-between">
            <div>
                <span class="bg-blue-100 text-blue-800 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wide">Dairy</span>
                <h3 class="text-2xl font-bold text-gray-800 mt-3 mb-1 line-clamp-2">Susu Cair Full Cream</h3>
                <p class="text-green-600 font-extrabold text-2xl mb-6">Sisa: 2000 ml</p>
            </div>
            <button class="w-full bg-amber-500 hover:bg-amber-600 text-white font-bold py-4 rounded-xl text-lg shadow-md transition transform active:scale-95">
                👨‍🍳 Gunakan Bahan
            </button>
        </div>
    </div>
</div>

<div id="modalTambahBahan" class="fixed inset-0 bg-black bg-opacity-50 hidden flex justify-center items-center z-50">
    <div class="bg-white p-8 rounded-2xl shadow-2xl w-full max-w-lg transform transition-all">
        <div class="flex justify-between items-center mb-6 border-b pb-3">
            <h3 class="text-2xl font-bold text-gray-800 flex items-center"><span class="mr-2">📦</span> Tambah Bahan Baku</h3>
            <button onclick="tutupModalTambah()" class="text-gray-400 hover:text-red-500 text-3xl font-bold leading-none">&times;</button>
        </div>
        
        <form id="formTambahBahan">
            <div class="grid grid-cols-2 gap-5 mb-4">
                <div class="col-span-2">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Nama Bahan Baku <span class="text-red-500">*</span></label>
                    <input type="text" id="inputNama" class="w-full p-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500" placeholder="Contoh: Keju Mozarella" required>
                </div>
                
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Kategori <span class="text-red-500">*</span></label>
                    <select id="inputKategori" class="w-full p-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 bg-white" required>
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
                        <option value="ml">Mililiter (ml)</option>
                        <option value="butir">Butir</option>
                        <option value="pcs">Pcs</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Stok Masuk <span class="text-red-500">*</span></label>
                    <input type="number" id="inputQty" class="w-full p-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500" placeholder="Misal: 1000" min="0" required>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Batas Kritis <span class="text-red-500">*</span></label>
                    <input type="number" id="inputMinQty" class="w-full p-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500" placeholder="Misal: 200" min="0" required>
                </div>
                
                <div class="col-span-2">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Harga Beli per Satuan (Rp) <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <span class="absolute left-4 top-3 text-gray-500 font-bold">Rp</span>
                        <input type="number" step="0.01" id="inputHarga" class="w-full p-3 pl-10 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500" placeholder="Misal: 15.5" required>
                    </div>
                </div>
            </div>
            
            <div class="flex justify-end mt-8 space-x-4 border-t pt-5">
                <button type="button" onclick="tutupModalTambah()" class="px-6 py-3 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 transition">Batal</button>
                <button type="button" onclick="alert('Formulir siap disambungkan ke Backend!')" class="px-6 py-3 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 transition shadow-lg">Simpan Bahan</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const token = localStorage.getItem('auth_token');
        const role = localStorage.getItem('user_role');
        const title = document.getElementById('page-title');
        const desc = document.getElementById('page-desc');

        // 1. Proteksi Halaman
        if (!token) {
            window.location.href = '/login';
            return;
        }

        // 2. Setup Tampilan Awal Berdasarkan Role
        if (role === 'admin') {
            title.innerText = 'Manajemen Master Gudang';
            desc.innerText = 'Kontrol penuh data bahan baku dan inventaris.';
            document.getElementById('admin-view').classList.remove('hidden');
        } else if (role === 'chef') {
            title.innerText = 'Tablet Dapur BakeLab';
            desc.innerText = 'Pilih dan potong bahan baku yang akan digunakan.';
            document.getElementById('chef-view').classList.remove('hidden');
        }

        // 3. Tarik Data Asli dari Backend
        fetchProducts(token, role);
    });

    async function fetchProducts(token, role) {
        try {
            const response = await fetch('/api/products', {
                method: 'GET',
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json'
                }
            });

            const result = await response.json();

            if (response.ok && result.data) {
                renderData(result.data, role);
            } else {
                alert("Gagal mengambil data dari server!");
            }
        } catch (error) {
            console.error('Error Jaringan:', error);
        }
    }

    function renderData(products, role) {
        const adminTbody = document.querySelector('#admin-view tbody');
        const chefGrid = document.querySelector('#chef-view .grid');
        
        adminTbody.innerHTML = '';
        chefGrid.innerHTML = '';

        products.forEach(product => {
            const isCritical = product.qty <= product.min_qty;
            const stockColor = isCritical ? 'text-red-600' : 'text-green-600';
            const badge = isCritical ? `<span class="text-xs text-red-500 block">(Kritis!)</span>` : '';

            if (role === 'admin') {
                adminTbody.innerHTML += `
                    <tr class="border-b hover:bg-gray-50">
                        <td class="text-left py-3 px-4">${product.name}</td>
                        <td class="text-left py-3 px-4">
                            <span class="${stockColor} font-bold">${product.qty} ${product.unit}</span>
                            ${badge}
                        </td>
                        <td class="text-left py-3 px-4">Rp ${product.price_per_unit || 0}</td>
                        <td class="text-center py-3 px-4">
                            <button class="text-blue-500 hover:text-blue-700 mr-3 font-semibold">Edit</button>
                            <button class="text-red-500 hover:text-red-700 font-semibold">Hapus</button>
                        </td>
                    </tr>
                `;
            } else if (role === 'chef') {
                chefGrid.innerHTML += `
                    <div class="bg-white p-6 rounded-2xl shadow-lg border border-gray-100 flex flex-col justify-between">
                        <div>
                            <span class="bg-amber-100 text-amber-800 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wide">${product.category}</span>
                            <h3 class="text-2xl font-bold text-gray-800 mt-3 mb-1 line-clamp-2">${product.name}</h3>
                            <p class="${stockColor} font-extrabold text-2xl mb-6">Sisa: ${product.qty} ${product.unit}</p>
                        </div>
                        <button onclick="kurangiStok(${product.id})" class="w-full bg-amber-500 hover:bg-amber-600 text-white font-bold py-4 rounded-xl text-lg shadow-md transition transform active:scale-95">
                            👨‍🍳 Gunakan Bahan
                        </button>
                    </div>
                `;
            }
        });
    }

    async function kurangiStok(id) {
        // 1. Minta Chef masukin jumlah yang mau dipake
        const jumlahDipakai = window.prompt("Berapa banyak bahan yang mau dipakai? (Masukkan angka saja):");

        // 2. Cegah error kalau Chef klik cancel atau masukin huruf
        if (jumlahDipakai === null) return; 
        if (jumlahDipakai.trim() === "" || isNaN(jumlahDipakai) || jumlahDipakai <= 0) {
            alert("❌ Masukkan angka yang valid dong!");
            return;
        }

        const token = localStorage.getItem('auth_token');
        const role = localStorage.getItem('user_role');

        try {
            // 3. Tembak endpoint Backend!
            const response = await fetch(`/api/products/${id}/reduce`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ qty: parseInt(jumlahDipakai) }) 
            });

            const result = await response.json();

            if (response.ok) {
                alert("✅ Sukses: " + (result.message || "Bahan berhasil dipotong!"));
                // 4. Langsung refresh data di layar biar angkanya update!
                fetchProducts(token, role);
            } else {
                alert("❌ Gagal: " + (result.message || "Stok gak cukup atau ada error."));
            }
        } catch (error) {
            console.error('Error Jaringan:', error);
            alert("❌ Gagal terhubung ke server backend!");
        }
    }

    // ==========================================
    // FUNGSI KONTROL MODAL ADMIN
    // ==========================================
    function bukaModalTambah() {
        document.getElementById('modalTambahBahan').classList.remove('hidden');
    }

    function tutupModalTambah() {
        document.getElementById('modalTambahBahan').classList.add('hidden');
        document.getElementById('formTambahBahan').reset(); 
    }
</script>
@endsection