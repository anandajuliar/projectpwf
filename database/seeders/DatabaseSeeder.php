<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Recipe;
use App\Models\RecipeIngredient;
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
        $admin = User::create([
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

        $bahanBakuData = [
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

        $products = [];
        foreach ($bahanBakuData as $bahan) {
            $products[] = Product::create($bahan);
        }

        // Map nama → object untuk kemudahan referensi di resep
        $productMap = collect($products)->keyBy('name');

        // =====================================================================
        // DATA RESEP CONTOH
        // =====================================================================

        // Resep 1: Brownies Cokelat
        $brownies = Recipe::create([
            'name'             => 'Brownies Cokelat',
            'description'      => 'Brownies cokelat klasik, padat dan lembab. Cocok untuk 1 loyang 20x20cm.',
            'category'         => 'Kue Panggang',
            'default_portions' => 1,
            'created_by'       => $admin->id,
            'is_active'        => true,
        ]);

        RecipeIngredient::insert([
            ['recipe_id' => $brownies->id, 'product_id' => $productMap['Tepung Terigu']->id,  'qty_per_portion' => 150,  'note' => 'Diayak terlebih dahulu'],
            ['recipe_id' => $brownies->id, 'product_id' => $productMap['Gula Pasir']->id,     'qty_per_portion' => 200,  'note' => null],
            ['recipe_id' => $brownies->id, 'product_id' => $productMap['Mentega']->id,        'qty_per_portion' => 150,  'note' => 'Dicairkan'],
            ['recipe_id' => $brownies->id, 'product_id' => $productMap['Telur Ayam']->id,     'qty_per_portion' => 3,    'note' => 'Suhu ruang'],
            ['recipe_id' => $brownies->id, 'product_id' => $productMap['Cokelat Bubuk']->id,  'qty_per_portion' => 50,   'note' => 'Diayak'],
            ['recipe_id' => $brownies->id, 'product_id' => $productMap['Vanili Ekstrak']->id, 'qty_per_portion' => 5,    'note' => null],
        ]);

        // Resep 2: Chiffon Cake Vanilla
        $chiffon = Recipe::create([
            'name'             => 'Chiffon Cake Vanilla',
            'description'      => 'Chiffon cake ringan dan lembut dengan aroma vanilla. Per resep = 1 cetakan chiffon 22cm.',
            'category'         => 'Kue Panggang',
            'default_portions' => 1,
            'created_by'       => $admin->id,
            'is_active'        => true,
        ]);

        RecipeIngredient::insert([
            ['recipe_id' => $chiffon->id, 'product_id' => $productMap['Tepung Terigu']->id,  'qty_per_portion' => 200,  'note' => 'Protein rendah'],
            ['recipe_id' => $chiffon->id, 'product_id' => $productMap['Gula Pasir']->id,     'qty_per_portion' => 150,  'note' => null],
            ['recipe_id' => $chiffon->id, 'product_id' => $productMap['Telur Ayam']->id,     'qty_per_portion' => 6,    'note' => 'Pisahkan kuning & putih'],
            ['recipe_id' => $chiffon->id, 'product_id' => $productMap['Susu Cair UHT']->id,  'qty_per_portion' => 100,  'note' => null],
            ['recipe_id' => $chiffon->id, 'product_id' => $productMap['Baking Powder']->id,  'qty_per_portion' => 10,   'note' => null],
            ['recipe_id' => $chiffon->id, 'product_id' => $productMap['Vanili Ekstrak']->id, 'qty_per_portion' => 10,   'note' => null],
        ]);

        // Resep 3: Nastar Keju (draft, nonaktif untuk demo)
        $nastar = Recipe::create([
            'name'             => 'Nastar Keju',
            'description'      => 'Nastar isi selai nanas dengan topping keju. Per resep menghasilkan ±60 buah.',
            'category'         => 'Kue Kering',
            'default_portions' => 1,
            'created_by'       => $admin->id,
            'is_active'        => false, // Draft
        ]);

        RecipeIngredient::insert([
            ['recipe_id' => $nastar->id, 'product_id' => $productMap['Tepung Terigu']->id, 'qty_per_portion' => 300, 'note' => null],
            ['recipe_id' => $nastar->id, 'product_id' => $productMap['Mentega']->id,       'qty_per_portion' => 200, 'note' => null],
            ['recipe_id' => $nastar->id, 'product_id' => $productMap['Gula Pasir']->id,    'qty_per_portion' => 50,  'note' => 'Gula halus'],
            ['recipe_id' => $nastar->id, 'product_id' => $productMap['Telur Ayam']->id,    'qty_per_portion' => 2,   'note' => 'Kuning telur saja'],
            ['recipe_id' => $nastar->id, 'product_id' => $productMap['Keju Parut']->id,    'qty_per_portion' => 100, 'note' => 'Untuk topping'],
        ]);
    }
}
