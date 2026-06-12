<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ProductApiController extends Controller
{

    public function index()
    {
        try {

            $products = Product::all(); 

            return response()->json([
                'message' => 'Berhasil mengambil semua produk',
                'data' => $products
            ], 200);
        } catch (\Throwable $e) {
            Log::error('Gagal mengambil semua data produk', [
                'message' => $e->getMessage(),
            ]);
            return response()->json(['message' => 'Terjadi kesalahan server'], 500);
        }
    }


    public function store(Request $request)
    {
        try {

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'qty' => 'required|numeric',
                'price_per_unit' => 'required|numeric',

            ]);


            $validated['user_id'] = Auth::id();

            $product = Product::create($validated);

            Log::info('Menambah data produk', [
                'list' => $product
            ]);

            return response()->json([
                'message' => 'Produk berhasil ditambahkan!!',
                'data' => $product,
            ], 201);
        } catch (\Throwable $e) {
            Log::error('Error saat menambah product', [
                'message' => $e->getMessage(),
            ]);
            return response()->json(['message' => 'Gagal menambah data'], 500);
        }
    }


    public function show(int $id)
    {
        try {
            $product = Product::find($id);

            if (!$product) {
                return response()->json([
                    'message' => 'Product tidak ditemukan',
                ], 404);
            }

            return response()->json([
                'message' => 'Product retrieved successfully',
                'data' => $product
            ], 200);
        } catch (\Throwable $e) {
            Log::error('Gagal mengambil data produk', [
                'message' => $e->getMessage(),
            ]);
            return response()->json(['message' => 'Terjadi kesalahan server'], 500);
        }
    }


    public function update(Request $request, int $id)
    {
        try {
            $product = Product::find($id);

            if (!$product) {
                return response()->json([
                    'message' => 'Product tidak ditemukan',
                ], 404);
            }

            $validated = $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'qty' => 'sometimes|required|numeric',
                'price_per_unit' => 'sometimes|required|numeric',
            ]);

            $product->update($validated);

            Log::info('Memperbarui data produk', [
                'product_id' => $id,
                'updated_data' => $validated
            ]);

            return response()->json([
                'message' => 'Produk berhasil diperbarui!!',
                'data' => $product,
            ], 200);
        } catch (\Throwable $e) {
            Log::error('Error saat update product', [
                'message' => $e->getMessage(),
            ]);
            return response()->json(['message' => 'Gagal memperbarui data'], 500);
        }
    }


    public function destroy(int $id)
    {
        try {
            $product = Product::find($id);

            if (!$product) {
                return response()->json([
                    'message' => 'Product tidak ditemukan',
                ], 404);
            }

            $product->delete();

            Log::info('Menghapus data produk', [
                'product_id' => $id
            ]);

            return response()->json([
                'message' => 'Produk berhasil dihapus!!',
            ], 200); 
        } catch (\Throwable $e) {
            Log::error('Error saat hapus product', [
                'message' => $e->getMessage(),
            ]);
            return response()->json(['message' => 'Gagal menghapus data'], 500);
        }
    }
}