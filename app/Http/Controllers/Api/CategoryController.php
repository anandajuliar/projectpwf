<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CategoryController extends Controller
{
    public function index()
    {
        try {
            $categories = Category::all();
            return response()->json([
                'message' => 'Berhasil mengambil data kategori',
                'data' => $categories
            ], 200);
        } catch (\Throwable $e) {
            Log::error('Error get category: ' . $e->getMessage());
            return response()->json(['message' => 'Terjadi kesalahan server'], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255'
            ]);

            $category = Category::create($validated);
            
            Log::info('Menambah kategori baru', ['data' => $category]);

            return response()->json([
                'message' => 'Kategori berhasil ditambahkan!!',
                'data' => $category
            ], 201);
        } catch (\Throwable $e) {
            Log::error('Error store category: ' . $e->getMessage());
            return response()->json(['message' => 'Gagal menambah data'], 500);
        }
    }

    public function update(Request $request, string $id)
    {
        try {
            $category = Category::find($id);
            
            if (!$category) {
                return response()->json(['message' => 'Kategori tidak ditemukan'], 404);
            }

            $validated = $request->validate([
                'name' => 'required|string|max:255'
            ]);

            $category->update($validated);

            Log::info('Update kategori', ['data' => $category]);

            return response()->json([
                'message' => 'Kategori berhasil diperbarui',
                'data' => $category
            ], 200);
        } catch (\Throwable $e) {
            Log::error('Error update category: ' . $e->getMessage());
            return response()->json(['message' => 'Gagal memperbarui data'], 500);
        }
    }

    public function destroy(string $id)
    {
        try {
            $category = Category::find($id);
            
            if (!$category) {
                return response()->json(['message' => 'Kategori tidak ditemukan'], 404);
            }

            $category->delete();
            Log::info('Hapus kategori ID: ' . $id);


            return response()->json(null, 204); 
        } catch (\Throwable $e) {
            Log::error('Error delete category: ' . $e->getMessage());
            return response()->json(['message' => 'Gagal menghapus data'], 500);
        }
    }
}