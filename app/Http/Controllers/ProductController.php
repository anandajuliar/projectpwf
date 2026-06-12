<?php

namespace App\Http\Controllers;

use App\Http\Requests\Product\AddStockRequest;
use App\Http\Requests\Product\ReduceStockRequest;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Http\Resources\StockLogResource;
use App\Models\Product;
use App\Models\StockLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends Controller
{
    /**
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

        $previousQty = (float) $product->qty;
        $product->decrement('qty', $reduceQty);
        $product->refresh();

        // Catat ke stock_logs
        StockLog::create([
            'product_id'  => $product->id,
            'user_id'     => $request->user()->id,
            'type'        => 'reduce',
            'qty_before'  => $previousQty,
            'qty_changed' => $reduceQty,
            'qty_after'   => (float) $product->qty,
            'unit'        => $product->unit,
            'note'        => $request->note,
        ]);

        return response()->json([
            'success' => true,
            'message' => "Stok {$product->name} berhasil dikurangi sebesar {$reduceQty} {$product->unit}.",
            'data'    => [
                'product'      => new ProductResource($product),
                'previous_qty' => $previousQty,
                'reduced_by'   => (float) $reduceQty,
                'current_qty'  => (float) $product->qty,
                'unit'         => $product->unit,
                'note'         => $request->note,
            ],
        ], Response::HTTP_OK);
    }


    public function addStock(AddStockRequest $request, Product $product): JsonResponse
    {
        $addQty = $request->qty;

        $previousQty = (float) $product->qty;
        $product->increment('qty', $addQty);
        $product->refresh();

        // Catat ke stock_logs
        StockLog::create([
            'product_id'  => $product->id,
            'user_id'     => $request->user()->id,
            'type'        => 'add',
            'qty_before'  => $previousQty,
            'qty_changed' => $addQty,
            'qty_after'   => (float) $product->qty,
            'unit'        => $product->unit,
            'note'        => $request->note,
        ]);

        return response()->json([
            'success' => true,
            'message' => "Stok {$product->name} berhasil ditambah sebesar {$addQty} {$product->unit}.",
            'data'    => [
                'product'      => new ProductResource($product),
                'previous_qty' => $previousQty,
                'added_by'     => (float) $addQty,
                'current_qty'  => (float) $product->qty,
                'unit'         => $product->unit,
                'note'         => $request->note,
            ],
        ], Response::HTTP_OK);
    }

    public function summary(): JsonResponse
    {
        $total  = Product::count();
        $out    = Product::outOfStock()->count();
        $low    = Product::lowStock()->count();
        $normal = Product::normalStock()->count();

        // Ambil produk yang stoknya habis atau rendah untuk notifikasi
        $alertProducts = Product::where(function ($q) {
            $q->where('qty', '<=', 0)
              ->orWhereColumn('qty', '<=', 'min_qty');
        })
        ->orderByRaw('qty ASC')
        ->get(['id', 'name', 'category', 'unit', 'qty', 'min_qty']);

        $alertData = $alertProducts->map(fn ($p) => [
            'id'           => $p->id,
            'name'         => $p->name,
            'category'     => $p->category,
            'unit'         => $p->unit,
            'qty'          => (float) $p->qty,
            'min_qty'      => (float) $p->min_qty,
            'stock_status' => $p->stock_status,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Ringkasan stok berhasil diambil.',
            'data'    => [
                'total_products'  => $total,
                'stock_out'       => $out,
                'stock_low'       => $low,
                'stock_normal'    => $normal,
                'alert_products'  => $alertData,
            ],
        ], Response::HTTP_OK);
    }

    
    public function logs(Request $request, Product $product): JsonResponse
    {
        $query = $product->stockLogs()
                         ->with(['user', 'recipe'])
                         ->latest();

        // Filter berdasarkan tipe
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter berdasarkan rentang tanggal
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->paginate($request->input('per_page', 20));

        return response()->json([
            'success' => true,
            'message' => "Riwayat stok {$product->name} berhasil diambil.",
            'data'    => StockLogResource::collection($logs->items()),
            'meta'    => [
                'product'      => ['id' => $product->id, 'name' => $product->name],
                'total'        => $logs->total(),
                'per_page'     => $logs->perPage(),
                'current_page' => $logs->currentPage(),
                'last_page'    => $logs->lastPage(),
            ],
        ], Response::HTTP_OK);
    }
}
