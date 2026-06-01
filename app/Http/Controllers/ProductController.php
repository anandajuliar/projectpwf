<?php

namespace App\Http\Controllers;

use App\Http\Requests\Product\ReduceStockRequest;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends Controller
{
    /**
     * Menampilkan semua produk/bahan baku.
     * Mendukung filter by category, search by name, dan sort.
     *
     * GET /api/products
     */
    public function index(Request $request): JsonResponse
    {
        $query = Product::query();

        // Filter berdasarkan kategori
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Filter berdasarkan status stok
        if ($request->filled('stock_status')) {
            match ($request->stock_status) {
                'out'    => $query->where('qty', '<=', 0),
                'low'    => $query->where('qty', '>', 0)->whereColumn('qty', '<=', 'min_qty'),
                'normal' => $query->whereColumn('qty', '>', 'min_qty'),
                default  => null,
            };
        }

        // Pencarian berdasarkan nama
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Pengurutan
        $sortBy    = $request->input('sort_by', 'name');
        $sortOrder = $request->input('sort_order', 'asc');
        $allowedSorts = ['name', 'qty', 'created_at', 'price_per_unit'];

        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortOrder === 'desc' ? 'desc' : 'asc');
        }

        $products = $query->paginate($request->input('per_page', 15));

        return response()->json([
            'success' => true,
            'message' => 'Data produk berhasil diambil.',
            'data'    => ProductResource::collection($products->items()),
            'meta'    => [
                'total'        => $products->total(),
                'per_page'     => $products->perPage(),
                'current_page' => $products->currentPage(),
                'last_page'    => $products->lastPage(),
            ],
        ], Response::HTTP_OK);
    }

    /**
     * Membuat produk/bahan baku baru.
     * Hanya admin yang bisa mengakses (dijaga middleware).
     *
     * POST /api/products
     */
    public function store(StoreProductRequest $request): JsonResponse
    {
        $product = Product::create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil ditambahkan.',
            'data'    => new ProductResource($product),
        ], Response::HTTP_CREATED);
    }

    /**
     * Menampilkan detail satu produk.
     *
     * GET /api/products/{id}
     */
    public function show(Product $product): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Detail produk berhasil diambil.',
            'data'    => new ProductResource($product),
        ], Response::HTTP_OK);
    }

    /**
     * Memperbarui data produk.
     * Hanya admin yang bisa mengakses (dijaga middleware).
     *
     * PUT /api/products/{id}
     */
    public function update(UpdateProductRequest $request, Product $product): JsonResponse
    {
        $product->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil diperbarui.',
            'data'    => new ProductResource($product->fresh()),
        ], Response::HTTP_OK);
    }

    /**
     * Menghapus produk.
     * Hanya admin yang bisa mengakses (dijaga middleware).
     *
     * DELETE /api/products/{id}
     */
    public function destroy(Product $product): JsonResponse
    {
        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil dihapus.',
        ], Response::HTTP_OK);
    }

    /**
     * =====================================================================
     * ENDPOINT KHUSUS: Potong Stok Bahan Baku
     * =====================================================================
     * Endpoint ini memungkinkan chef/admin langsung mengurangi stok
     * berdasarkan jumlah yang digunakan untuk membuat kue/masakan.
     * Frontend tidak perlu kalkulasi manual.
     *
     * POST /api/products/{id}/reduce
     *
     * Request body:
     *   - qty  (required) : jumlah yang akan dikurangi
     *   - note (optional) : catatan penggunaan, misal "untuk 5 loyang brownies"
     */
    public function reduceStock(ReduceStockRequest $request, Product $product): JsonResponse
    {
        $reduceQty = $request->qty;

        // Validasi: stok yang tersisa harus cukup
        if ($product->qty < $reduceQty) {
            return response()->json([
                'success' => false,
                'message' => "Stok tidak mencukupi. Stok tersedia: {$product->qty} {$product->unit}, diminta: {$reduceQty} {$product->unit}.",
                'data'    => [
                    'available_qty' => (float) $product->qty,
                    'requested_qty' => (float) $reduceQty,
                    'unit'          => $product->unit,
                ],
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $previousQty = $product->qty;
        $product->decrement('qty', $reduceQty);
        $product->refresh();

        return response()->json([
            'success' => true,
            'message' => "Stok {$product->name} berhasil dikurangi sebesar {$reduceQty} {$product->unit}.",
            'data'    => [
                'product'      => new ProductResource($product),
                'previous_qty' => (float) $previousQty,
                'reduced_by'   => (float) $reduceQty,
                'current_qty'  => (float) $product->qty,
                'unit'         => $product->unit,
                'note'         => $request->note,
            ],
        ], Response::HTTP_OK);
    }
}
