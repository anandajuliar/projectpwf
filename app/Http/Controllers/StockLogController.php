<?php

namespace App\Http\Controllers;

use App\Http\Resources\StockLogResource;
use App\Models\StockLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class StockLogController extends Controller
{
    /**
     * Menampilkan semua riwayat perubahan stok dengan filter.
     * Dapat difilter berdasarkan produk, user, tipe, dan rentang tanggal.
     *
     * GET /api/stock-logs
     */
    public function index(Request $request): JsonResponse
    {
        $query = StockLog::with(['product', 'user', 'recipe'])->latest('created_at');

        // Filter berdasarkan produk
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        // Filter berdasarkan user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter berdasarkan tipe: reduce | add | recipe_reduce
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
            'message' => 'Riwayat perubahan stok berhasil diambil.',
            'data'    => StockLogResource::collection($logs->items()),
            'meta'    => [
                'total'        => $logs->total(),
                'per_page'     => $logs->perPage(),
                'current_page' => $logs->currentPage(),
                'last_page'    => $logs->lastPage(),
            ],
        ], Response::HTTP_OK);
    }

    /**
     * Menampilkan detail satu log.
     *
     * GET /api/stock-logs/{id}
     */
    public function show(StockLog $stockLog): JsonResponse
    {
        $stockLog->load(['product', 'user', 'recipe']);

        return response()->json([
            'success' => true,
            'message' => 'Detail log stok berhasil diambil.',
            'data'    => new StockLogResource($stockLog),
        ], Response::HTTP_OK);
    }
}
