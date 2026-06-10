<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockLog extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'product_id',
        'user_id',
        'type',
        'qty_before',
        'qty_changed',
        'qty_after',
        'unit',
        'recipe_id',
        'portions',
        'note',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'qty_before'  => 'decimal:2',
            'qty_changed' => 'decimal:2',
            'qty_after'   => 'decimal:2',
            'portions'    => 'integer',
        ];
    }

    /**
     * Relasi ke produk/bahan baku.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    /**
     * Relasi ke user yang melakukan perubahan.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke resep (nullable — hanya ada jika type=recipe_reduce).
     */
    public function recipe(): BelongsTo
    {
        return $this->belongsTo(Recipe::class)->withTrashed();
    }

    // =====================================================================
    // Scopes
    // =====================================================================

    /**
     * Scope: filter berdasarkan tipe perubahan.
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope: filter berdasarkan produk.
     */
    public function scopeForProduct($query, int $productId)
    {
        return $query->where('product_id', $productId);
    }

    /**
     * Scope: filter berdasarkan user.
     */
    public function scopeByUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope: filter berdasarkan rentang tanggal.
     */
    public function scopeInDateRange($query, ?string $from, ?string $to)
    {
        if ($from) {
            $query->whereDate('created_at', '>=', $from);
        }
        if ($to) {
            $query->whereDate('created_at', '<=', $to);
        }

        return $query;
    }
}
