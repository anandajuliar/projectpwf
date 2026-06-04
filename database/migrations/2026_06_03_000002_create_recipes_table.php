<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Tabel resep utama
        Schema::create('recipes', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Nama resep, misal: Brownies Cokelat, Chiffon Cake');
            $table->text('description')->nullable()->comment('Deskripsi singkat resep');
            $table->string('category')->nullable()->comment('Kategori resep, misal: Kue Kering, Roti, Minuman');
            $table->unsignedSmallInteger('default_portions')->default(1)
                  ->comment('Jumlah porsi/loyang default dari resep ini');
            $table->foreignId('created_by')
                  ->constrained('users')
                  ->cascadeOnDelete()
                  ->comment('Admin yang membuat resep');
            $table->boolean('is_active')->default(true)->comment('Resep aktif atau diarsipkan');
            $table->timestamps();
        });

        // Tabel komposisi bahan per resep
        Schema::create('recipe_ingredients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recipe_id')
                  ->constrained('recipes')
                  ->cascadeOnDelete();
            $table->foreignId('product_id')
                  ->constrained('products')
                  ->cascadeOnDelete()
                  ->comment('Bahan baku yang digunakan');
            $table->decimal('qty_per_portion', 10, 2)
                  ->comment('Jumlah bahan yang dibutuhkan per 1 porsi/loyang');
            $table->string('note', 255)->nullable()
                  ->comment('Catatan bahan, misal: diayak terlebih dahulu');

            $table->unique(['recipe_id', 'product_id'], 'unique_recipe_product');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recipe_ingredients');
        Schema::dropIfExists('recipes');
    }
};
