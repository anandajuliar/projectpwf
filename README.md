# 🥐 BakeLab - Sistem Inventaris & Manajemen Dapur (SRS)

🔗 **Live Production URL:** [http://bakelab.studikinerja.my.id](http://bakelab.studikinerja.my.id)

---

## 📖 Deskripsi Proyek
BakeLab adalah sistem informasi berbasis web yang dirancang khusus untuk mendigitalisasi proses pencatatan inventaris (Gudang) dan manajemen operasional (Dapur) secara terintegrasi. Sistem ini memastikan ketersediaan bahan baku terpantau secara akurat dan *real-time* dengan fitur pemotongan stok otomatis saat proses produksi berjalan.

---

## 💻 Tech Stack
* **Framework Backend:** Laravel (PHP 8.3)
* **Database:** MySQL
* **Frontend:** Blade Templating Engine, Vite (CSS/JS)
* **Deployment:** cPanel Hosting (Live Production)

---

## ⚙️ System Requirements Specification (SRS)

### 1. Functional Requirements & Validation Rules
Sistem memiliki fitur utama yang dilengkapi dengan validasi logika bisnis (*Business Rules*) pada level server untuk memastikan integritas data:

#### **[FR-01] Autentikasi & Manajemen Pengguna**
* **Deskripsi:** akses admin & chef
* **Validation & Business Rules:**
  * Kredensial *login* (Email & Password) wajib diisi.
  * Pembuatan/pembaruan akun wajib menggunakan format email berstandar 
  * Nama karyawan minimal 3 huruf dan **hanya boleh berisi huruf serta spasi** (menolak karakter aneh/angka).
  * Password akun baru minimal 8 karakter.

#### **[FR-02] Manajemen Gudang (Inventory Control)**
* **Deskripsi:** Fitur CRUD untuk bahan baku dan pencatatan keluar-masuk (tambah/kurangi) stok.
* **Validation & Business Rules:**
  * Nama bahan baku wajib diisi, maksimal 255 karakter, dan divalidasi dengan Regex (hanya boleh huruf, angka, spasi, dan simbol `( ) ' & -`).
  * Satuan (*Unit*) harus terstandarisasi, hanya menerima nilai: `gram`, `kg`, `butir`, `ml`, `liter`, `sdm`, `sdt`, `pcs`.
  * Jumlah stok saat ini dan penetapan stok minimum wajib berupa angka dan **tidak boleh negatif** (`>= 0`).
  * Harga beli per satuan wajib berupa angka dengan nilai minimum **Rp 3.000**.
  * Untuk aktivitas Penambahan (`AddStock`) dan Pengurangan (`ReduceStock`) stok, kuantitas wajib diisi dengan nilai minimum `0.01` (mendukung desimal), dengan opsional catatan maksimal 500 karakter.

#### **[FR-03] Manajemen Dapur (Recipe & Production Control)**
* **Deskripsi:** pencatatan resep produk dan eksekusi produksi harian.
* **Validation & Business Rules:**
  * Nama resep divalidasi Regex (hanya boleh huruf, angka, spasi, dan simbol `( ) ' & - / , .`).
  * Pembuatan resep wajib menyertakan jumlah porsi *default* minimal `1`.
  * Setiap resep **wajib** memiliki sekurang-kurangnya 1 komponen bahan baku pendukung (*ingredients minimum 1*).
  * **[Validasi Eksekusi Produksi]** Saat resep dieksekusi/dimasak, input jumlah porsi wajib berupa bilangan bulat (`integer`) dengan batas rentang produksi **1 hingga 9999** porsi/loyang.

#### **[FR-04] Sinkronisasi & Pemotongan Stok Otomatis**
* **Deskripsi:** Sistem otomatis memotong stok Gudang berdasarkan pemakaian Dapur.
* **Business Rules:**
  * Sistem akan melakukan kalkulasi (*Portions x Qty Per Portion*) dan mengecek ketersediaan bahan baku di Gudang sebelum proses produksi disetujui.
  * Jika kuantitas bahan baku di Gudang kurang, sistem akan menolak aksi untuk mencegah anomali stok negatif.

