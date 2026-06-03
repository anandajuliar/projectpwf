<?php

namespace App\Http\Controllers;

use App\Http\Requests\Recipe\ExecuteRecipeRequest;
use App\Http\Requests\Recipe\StoreRecipeRequest;
use App\Http\Requests\Recipe\UpdateRecipeRequest;
use App\Http\Resources\RecipeResource;
use App\Http\Resources\StockLogResource;
use App\Models\Recipe;
use App\Models\RecipeIngredient;
use App\Models\StockLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class RecipeController extends Controller
{
    /**
     * Menampilkan semua resep.
     * Mendukung filter by category, status, dan search.
     *
     * GET /api/recipes
     */
    public function index(Request $request): JsonResponse
    {
        $query = Recipe::with(['creator', 'ingredients.product'])->latest();

        // Filter berdasarkan kategori
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Filter hanya resep aktif
        if ($request->filled('is_active')) {
            $query->where('is_active', filter_var($request->is_active, FILTER_VALIDATE_BOOLEAN));
        }

        // Pencarian berdasarkan nama
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $recipes = $query->paginate($request->input('per_page', 15));

        return response()->json([
            'success' => true,
            'message' => 'Daftar resep berhasil diambil.',
            'data'    => RecipeResource::collection($recipes->items()),
            'meta'    => [
                'total'        => $recipes->total(),
                'per_page'     => $recipes->perPage(),
                'current_page' => $recipes->currentPage(),
                'last_page'    => $recipes->lastPage(),
            ],
        ], Response::HTTP_OK);
    }

    /**
     * Membuat resep baru beserta bahan-bahannya.
     * Hanya admin (dijaga middleware).
     *
     * POST /api/recipes
     */
    public function store(StoreRecipeRequest $request): JsonResponse
    {
        $recipe = DB::transaction(function () use ($request) {
            $recipe = Recipe::create([
                'name'             => $request->name,
                'description'      => $request->description,
                'category'         => $request->category,
                'default_portions' => $request->default_portions,
                'created_by'       => $request->user()->id,
                'is_active'        => $request->input('is_active', true),
            ]);

            // Simpan semua bahan resep
            $ingredients = collect($request->ingredients)->map(fn ($ing) => [
                'recipe_id'       => $recipe->id,
                'product_id'      => $ing['product_id'],
                'qty_per_portion' => $ing['qty_per_portion'],
                'note'            => $ing['note'] ?? null,
            ]);

            RecipeIngredient::insert($ingredients->toArray());

            return $recipe;
        });

        $recipe->load(['creator', 'ingredients.product']);

        return response()->json([
            'success' => true,
            'message' => 'Resep berhasil dibuat.',
            'data'    => new RecipeResource($recipe),
        ], Response::HTTP_CREATED);
    }

    /**
     * Menampilkan detail satu resep beserta bahan-bahan dan stok saat ini.
     *
     * GET /api/recipes/{id}
     */
    public function show(Recipe $recipe): JsonResponse
    {
        $recipe->load(['creator', 'ingredients.product']);

        return response()->json([
            'success' => true,
            'message' => 'Detail resep berhasil diambil.',
            'data'    => new RecipeResource($recipe),
        ], Response::HTTP_OK);
    }

    /**
     * Memperbarui data resep dan komposisi bahannya.
     * Hanya admin (dijaga middleware).
     *
     * PUT /api/recipes/{id}
     */
    public function update(UpdateRecipeRequest $request, Recipe $recipe): JsonResponse
    {
        DB::transaction(function () use ($request, $recipe) {
            $recipe->update($request->only([
                'name', 'description', 'category', 'default_portions', 'is_active',
            ]));

            // Jika ada ingredients yang dikirim, replace seluruh komposisi
            if ($request->has('ingredients')) {
                $recipe->ingredients()->delete();

                $ingredients = collect($request->ingredients)->map(fn ($ing) => [
                    'recipe_id'       => $recipe->id,
                    'product_id'      => $ing['product_id'],
                    'qty_per_portion' => $ing['qty_per_portion'],
                    'note'            => $ing['note'] ?? null,
                ]);

                RecipeIngredient::insert($ingredients->toArray());
            }
        });

        $recipe->load(['creator', 'ingredients.product']);

        return response()->json([
            'success' => true,
            'message' => 'Resep berhasil diperbarui.',
            'data'    => new RecipeResource($recipe),
        ], Response::HTTP_OK);
    }

    /**
     * Menghapus resep.
     * Hanya admin (dijaga middleware).
     *
     * DELETE /api/recipes/{id}
     */
    public function destroy(Recipe $recipe): JsonResponse
    {
        $recipe->delete(); // ingredients terhapus via cascadeOnDelete

        return response()->json([
            'success' => true,
            'message' => 'Resep berhasil dihapus.',
        ], Response::HTTP_OK);
    }

    /**
     * =====================================================================
     * ENDPOINT UTAMA: Eksekusi Resep — Potong Stok Otomatis
     * =====================================================================
     * Memotong stok semua bahan baku sesuai komposisi resep × jumlah porsi
     * dalam satu transaksi atomik. Jika satu bahan saja tidak cukup,
     * seluruh operasi dibatalkan (rollback).
     *
     * POST /api/recipes/{id}/execute
     *
     * Request body:
     *   - portions (required) : jumlah porsi/loyang yang akan dibuat
     *   - note     (optional) : catatan produksi
     */
    public function execute(ExecuteRecipeRequest $request, Recipe $recipe): JsonResponse
    {
        if (! $recipe->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Resep ini tidak aktif dan tidak bisa dieksekusi.',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $portions    = $request->portions;
        $note        = $request->note;
        $ingredients = $recipe->ingredients()->with('product')->get();

        if ($ingredients->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Resep ini belum memiliki bahan baku.',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Validasi stok semua bahan SEBELUM memotong (fail-fast)
        $insufficientItems = [];
        foreach ($ingredients as $ingredient) {
            $product  = $ingredient->product;
            $required = $ingredient->qty_per_portion * $portions;

            if (! $product || $product->qty < $required) {
                $insufficientItems[] = [
                    'product_id'    => $ingredient->product_id,
                    'product_name'  => $product?->name ?? '(dihapus)',
                    'unit'          => $product?->unit ?? '-',
                    'required'      => (float) $required,
                    'available'     => (float) ($product?->qty ?? 0),
                    'shortage'      => (float) max(0, $required - ($product?->qty ?? 0)),
                ];
            }
        }

        if (! empty($insufficientItems)) {
            return response()->json([
                'success' => false,
                'message' => 'Stok tidak mencukupi untuk membuat ' . $portions . ' porsi "' . $recipe->name . '".',
                'data'    => [
                    'insufficient_items' => $insufficientItems,
                ],
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Potong stok semua bahan dalam satu transaksi
        $reducedItems = DB::transaction(function () use ($ingredients, $portions, $note, $recipe, $request) {
            $results = [];

            foreach ($ingredients as $ingredient) {
                $product  = $ingredient->product;
                $required = $ingredient->qty_per_portion * $portions;

                $previousQty = (float) $product->qty;
                $product->decrement('qty', $required);
                $product->refresh();

                // Catat ke stock_logs
                StockLog::create([
                    'product_id'  => $product->id,
                    'user_id'     => $request->user()->id,
                    'type'        => 'recipe_reduce',
                    'qty_before'  => $previousQty,
                    'qty_changed' => $required,
                    'qty_after'   => (float) $product->qty,
                    'unit'        => $product->unit,
                    'recipe_id'   => $recipe->id,
                    'portions'    => $portions,
                    'note'        => $note,
                ]);

                $results[] = [
                    'product_id'    => $product->id,
                    'product_name'  => $product->name,
                    'unit'          => $product->unit,
                    'qty_used'      => (float) $required,
                    'qty_before'    => $previousQty,
                    'qty_after'     => (float) $product->qty,
                    'stock_status'  => $product->stock_status,
                ];
            }

            return $results;
        });

        return response()->json([
            'success' => true,
            'message' => "Berhasil membuat {$portions} porsi \"{$recipe->name}\". Stok semua bahan telah dikurangi.",
            'data'    => [
                'recipe'    => ['id' => $recipe->id, 'name' => $recipe->name],
                'portions'  => $portions,
                'note'      => $note,
                'items_reduced' => $reducedItems,
            ],
        ], Response::HTTP_OK);
    }
}
