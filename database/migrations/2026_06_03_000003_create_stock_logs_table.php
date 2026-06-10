<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * NOTE: Tabel ini dibuat SETELAH tabel recipes (lihat migration 2026_06_03_000002).
     */
    public function up(): void
    {
        Schema::create('stock_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete()
                  ->comment('Produk/bahan baku yang stoknya berubah');
            
            // Kalau User/Chef dihapus, histori masak tetap utuh (user_id jadi null)
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete()
                  ->comment('User yang melakukan perubahan stok');
                  
            $table->enum('type', ['reduce', 'add', 'recipe_reduce'])
                  ->comment('Jenis perubahan: reduce=potong manual, add=tambah/restock, recipe_reduce=potong via resep');
            $table->decimal('qty_before', 10, 2)->comment('Qty stok sebelum perubahan');
            $table->decimal('qty_changed', 10, 2)->comment('Jumlah yang berubah (selalu positif)');
            $table->decimal('qty_after', 10, 2)->comment('Qty stok setelah perubahan');
            $table->string('unit', 50)->comment('Satuan unit bahan');
            
            // Kalau resep dihapus, riwayat log tidak hancur
            $table->foreignId('recipe_id')->nullable()->constrained('recipes')->nullOnDelete()
                  ->comment('ID resep (jika type=recipe_reduce)');
                  
            $table->unsignedSmallInteger('portions')->nullable()->comment('Jumlah porsi yang dieksekusi');
            $table->string('note', 500)->nullable()->comment('Catatan penggunaan');
            $table->timestamps();

            $table->index(['product_id', 'created_at']);
            $table->index(['user_id', 'created_at']);
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_logs');
    }
};
