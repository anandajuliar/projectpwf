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
    public function run(): void
    {
        // =====================================================================
        // AKUN DEFAULT
        // =====================================================================
        $admin = User::create([
            'name'      => 'Admin Utama',
            'email'     => 'admin@pwf.com',
            'password'  => Hash::make('admin12345'),
            'role'      => 'admin',
            'is_active' => true,
        ]);

        User::create([
            'name'      => 'Chef Budi',
            'email'     => 'chef@pwf.com',
            'password'  => Hash::make('chef12345'),
            'role'      => 'chef',
            'is_active' => true,
        ]);

        // =====================================================================
        // DATA BAHAN BAKU 
        // =====================================================================
        $bahanBakuData = [
            ['name' => 'Tepung Terigu Protein Tinggi', 'category' => 'Tepung & Bahan Kering', 'unit' => 'gram', 'qty' => 15000, 'min_qty' => 2000, 'price_per_unit' => 0.015],
            ['name' => 'Tepung Terigu Protein Sedang', 'category' => 'Tepung & Bahan Kering', 'unit' => 'gram', 'qty' => 10000, 'min_qty' => 2000, 'price_per_unit' => 0.014],
            ['name' => 'Gula Pasir', 'category' => 'Pemanis', 'unit' => 'gram', 'qty' => 8000, 'min_qty' => 1000, 'price_per_unit' => 0.016],
            ['name' => 'Mentega (Unsalted)', 'category' => 'Lemak & Minyak', 'unit' => 'gram', 'qty' => 5000, 'min_qty' => 1000, 'price_per_unit' => 0.08],
            ['name' => 'Korsvet (Pastry Margarine)', 'category' => 'Lemak & Minyak', 'unit' => 'gram', 'qty' => 4000, 'min_qty' => 1000, 'price_per_unit' => 0.06],
            ['name' => 'Telur Ayam', 'category' => 'Protein', 'unit' => 'butir', 'qty' => 100, 'min_qty' => 20, 'price_per_unit' => 2500],
            ['name' => 'Susu Cair UHT', 'category' => 'Susu & Dairy', 'unit' => 'ml', 'qty' => 10000, 'min_qty' => 2000, 'price_per_unit' => 0.018],
            ['name' => 'Cokelat Batang (Dark)', 'category' => 'Perisa & Topping', 'unit' => 'gram', 'qty' => 3000, 'min_qty' => 500, 'price_per_unit' => 0.15],
            ['name' => 'Ragi Instan', 'category' => 'Agen Pengembang', 'unit' => 'gram', 'qty' => 500, 'min_qty' => 100, 'price_per_unit' => 0.2],
            ['name' => 'Baking Powder', 'category' => 'Agen Pengembang', 'unit' => 'gram', 'qty' => 500, 'min_qty' => 100, 'price_per_unit' => 0.05],
            ['name' => 'Keju Cheddar Parut', 'category' => 'Susu & Dairy', 'unit' => 'gram', 'qty' => 2000, 'min_qty' => 500, 'price_per_unit' => 0.18],
            ['name' => 'Garam Halus', 'category' => 'Pemanis', 'unit' => 'gram', 'qty' => 1000, 'min_qty' => 200, 'price_per_unit' => 0.01],
            ['name' => 'Air Es', 'category' => 'Cairan', 'unit' => 'ml', 'qty' => 99999, 'min_qty' => 0, 'price_per_unit' => 0],
            ['name' => 'Kismis', 'category' => 'Perisa & Topping', 'unit' => 'gram', 'qty' => 1000, 'min_qty' => 200, 'price_per_unit' => 0.1],
            ['name' => 'Kacang Almond Iris', 'category' => 'Perisa & Topping', 'unit' => 'gram', 'qty' => 800, 'min_qty' => 150, 'price_per_unit' => 0.25]
        ];

        $products = [];
        foreach ($bahanBakuData as $bahan) {
            $products[] = Product::create($bahan);
        }
        $productMap = collect($products)->keyBy('name');

        // =====================================================================
        // DATA 20 RESEP PASTRY & ROTI
        // =====================================================================
        $recipesData = [
            ['name' => 'Croissant Butter', 'cat' => 'Pastry Lapis'],
            ['name' => 'Pain au Chocolat', 'cat' => 'Pastry Lapis'],
            ['name' => 'Baguette Klasik', 'cat' => 'Roti Keras'],
            ['name' => 'Danish Kismis', 'cat' => 'Pastry Lapis'],
            ['name' => 'Almond Croissant', 'cat' => 'Pastry Lapis'],
            ['name' => 'Roti Tawar Gandum', 'cat' => 'Roti Tawar'],
            ['name' => 'Brioche Burger Bun', 'cat' => 'Roti Manis'],
            ['name' => 'Cinnamon Roll', 'cat' => 'Roti Manis'],
            ['name' => 'Kouign-Amann', 'cat' => 'Pastry Lapis'],
            ['name' => 'Cheese Twist (Savoury)', 'cat' => 'Pastry Lapis'],
            ['name' => 'Sourdough Country Loaf', 'cat' => 'Roti Keras'],
            ['name' => 'Roti Manis Isi Cokelat', 'cat' => 'Roti Manis'],
            ['name' => 'Roti Sisir Mentega', 'cat' => 'Roti Manis'],
            ['name' => 'Choux au Craquelin', 'cat' => 'Kue Sus'],
            ['name' => 'Eclair Cokelat', 'cat' => 'Kue Sus'],
            ['name' => 'Focaccia Rosemary', 'cat' => 'Roti Gurih'],
            ['name' => 'Puff Pastry Horn', 'cat' => 'Pastry Lapis'],
            ['name' => 'Roti Sosis Keju', 'cat' => 'Roti Gurih'],
            ['name' => 'Ciabatta', 'cat' => 'Roti Keras'],
            ['name' => 'Mille-Feuille', 'cat' => 'Pastry Lapis'],
        ];

        // Looping pembuat resep otomatis
        foreach ($recipesData as $index => $rd) {
            $recipe = Recipe::create([
                'name'             => $rd['name'],
                'description'      => 'Resep standar operasional untuk ' . $rd['name'] . '. Waktu persiapan ±' . rand(30, 120) . ' menit.',
                'category'         => $rd['cat'],
                'default_portions' => 1,
                'created_by'       => $admin->id,
                'is_active'        => true,
            ]);

            // Selalu butuh Tepung
            $tepung = (rand(1, 10) > 5) ? 'Tepung Terigu Protein Tinggi' : 'Tepung Terigu Protein Sedang';
            RecipeIngredient::create(['recipe_id' => $recipe->id, 'product_id' => $productMap[$tepung]->id, 'qty_per_portion' => rand(250, 600), 'note' => null]);
            
            // Selalu butuh Cairan
            $cair = (rand(1, 10) > 4) ? 'Susu Cair UHT' : 'Air Es';
            RecipeIngredient::create(['recipe_id' => $recipe->id, 'product_id' => $productMap[$cair]->id, 'qty_per_portion' => rand(150, 350), 'note' => null]);
            
            // Butuh Pengembang (Kecuali puff pastry biasa)
            if ($rd['cat'] !== 'Kue Sus' && $rd['cat'] !== 'Pastry Lapis') {
                RecipeIngredient::create(['recipe_id' => $recipe->id, 'product_id' => $productMap['Ragi Instan']->id, 'qty_per_portion' => rand(5, 12), 'note' => null]);
            }
            
            // Butuh Lemak
            $lemak = ($rd['cat'] == 'Pastry Lapis') ? 'Korsvet (Pastry Margarine)' : 'Mentega (Unsalted)';
            RecipeIngredient::create(['recipe_id' => $recipe->id, 'product_id' => $productMap[$lemak]->id, 'qty_per_portion' => rand(50, 250), 'note' => null]);
            
            // Gula & Garam
            RecipeIngredient::create(['recipe_id' => $recipe->id, 'product_id' => $productMap['Gula Pasir']->id, 'qty_per_portion' => rand(10, 100), 'note' => null]);
            RecipeIngredient::create(['recipe_id' => $recipe->id, 'product_id' => $productMap['Garam Halus']->id, 'qty_per_portion' => rand(3, 8), 'note' => null]);
            
            // Bahan Tambahan Berdasarkan Nama
            if (str_contains(strtolower($rd['name']), 'chocolat') || str_contains(strtolower($rd['name']), 'cokelat')) {
                RecipeIngredient::create(['recipe_id' => $recipe->id, 'product_id' => $productMap['Cokelat Batang (Dark)']->id, 'qty_per_portion' => rand(50, 150), 'note' => 'Potong dadu']);
            }
            if (str_contains(strtolower($rd['name']), 'kismis')) {
                RecipeIngredient::create(['recipe_id' => $recipe->id, 'product_id' => $productMap['Kismis']->id, 'qty_per_portion' => rand(50, 100), 'note' => 'Rendam air hangat']);
            }
            if (str_contains(strtolower($rd['name']), 'cheese') || str_contains(strtolower($rd['name']), 'keju')) {
                RecipeIngredient::create(['recipe_id' => $recipe->id, 'product_id' => $productMap['Keju Cheddar Parut']->id, 'qty_per_portion' => rand(80, 150), 'note' => 'Bagi 2 untuk topping & isi']);
            }
            if (str_contains(strtolower($rd['name']), 'almond')) {
                RecipeIngredient::create(['recipe_id' => $recipe->id, 'product_id' => $productMap['Kacang Almond Iris']->id, 'qty_per_portion' => rand(30, 60), 'note' => 'Sangrai sebentar']);
            }
        }
    }
}