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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('category')->nullable()->comment('Kategori bahan, misal: tepung, gula, telur');
            $table->string('unit')->default('gram')->comment('Satuan: gram, kg, butir, ml, liter, sdm');
            $table->decimal('qty', 10, 2)->default(0)->comment('Stok saat ini');
            $table->decimal('min_qty', 10, 2)->default(0)->comment('Batas stok minimum (trigger peringatan)');
            $table->decimal('price_per_unit', 15, 2)->default(0)->comment('Harga per satuan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
