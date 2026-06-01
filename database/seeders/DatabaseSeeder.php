<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // =====================================================================
        // AKUN DEFAULT
        // =====================================================================

        // Admin utama — gunakan kredensial ini untuk login pertama kali
        User::create([
            'name'      => 'Admin Utama',
            'email'     => 'admin@pwf.com',
            'password'  => Hash::make('admin12345'),
            'role'      => 'admin',
            'is_active' => true,
        ]);

        // Chef demo
        User::create([
            'name'      => 'Chef Budi',
            'email'     => 'chef@pwf.com',
            'password'  => Hash::make('chef12345'),
            'role'      => 'chef',
            'is_active' => true,
        ]);

        // =====================================================================
        // DATA BAHAN BAKU CONTOH
        // =====================================================================

        $bahanBaku = [
            [
                'name'           => 'Tepung Terigu',
                'description'    => 'Tepung terigu protein sedang untuk kue dan roti',
                'category'       => 'Tepung & Bahan Kering',
                'unit'           => 'gram',
                'qty'            => 10000,
                'min_qty'        => 2000,
                'price_per_unit' => 0.015,
            ],
            [
                'name'           => 'Gula Pasir',
                'description'    => 'Gula pasir putih halus',
                'category'       => 'Pemanis',
                'unit'           => 'gram',
                'qty'            => 5000,
                'min_qty'        => 1000,
                'price_per_unit' => 0.016,
            ],
            [
                'name'           => 'Mentega',
                'description'    => 'Mentega tawar (unsalted butter)',
                'category'       => 'Lemak & Minyak',
                'unit'           => 'gram',
                'qty'            => 3000,
                'min_qty'        => 500,
                'price_per_unit' => 0.08,
            ],
            [
                'name'           => 'Telur Ayam',
                'description'    => 'Telur ayam segar ukuran besar',
                'category'       => 'Protein',
                'unit'           => 'butir',
                'qty'            => 60,
                'min_qty'        => 12,
                'price_per_unit' => 2500,
            ],
            [
                'name'           => 'Susu Cair UHT',
                'description'    => 'Susu cair full cream',
                'category'       => 'Susu & Dairy',
                'unit'           => 'ml',
                'qty'            => 5000,
                'min_qty'        => 1000,
                'price_per_unit' => 0.018,
            ],
            [
                'name'           => 'Cokelat Bubuk',
                'description'    => 'Dark cocoa powder tanpa gula',
                'category'       => 'Perisa & Topping',
                'unit'           => 'gram',
                'qty'            => 1500,
                'min_qty'        => 300,
                'price_per_unit' => 0.12,
            ],
            [
                'name'           => 'Baking Powder',
                'description'    => 'Baking powder pengembang kue',
                'category'       => 'Agen Pengembang',
                'unit'           => 'gram',
                'qty'            => 500,
                'min_qty'        => 100,
                'price_per_unit' => 0.05,
            ],
            [
                'name'           => 'Vanili Ekstrak',
                'description'    => 'Ekstrak vanili cair',
                'category'       => 'Perisa & Topping',
                'unit'           => 'ml',
                'qty'            => 200,
                'min_qty'        => 50,
                'price_per_unit' => 0.35,
            ],
            [
                'name'           => 'Keju Parut',
                'description'    => 'Keju cheddar parut siap pakai',
                'category'       => 'Susu & Dairy',
                'unit'           => 'gram',
                'qty'            => 100,   // Sengaja low stock untuk demo
                'min_qty'        => 200,
                'price_per_unit' => 0.18,
            ],
            [
                'name'           => 'Selai Cokelat',
                'description'    => 'Selai cokelat hazelnut',
                'category'       => 'Perisa & Topping',
                'unit'           => 'gram',
                'qty'            => 0,    // Sengaja habis untuk demo
                'min_qty'        => 300,
                'price_per_unit' => 0.09,
            ],
        ];

        foreach ($bahanBaku as $bahan) {
            Product::create($bahan);
        }
    }
}
