<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'description',
        'category',
        'unit',
        'qty',
        'min_qty',
        'price_per_unit',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'qty'            => 'decimal:2',
            'min_qty'        => 'decimal:2',
            'price_per_unit' => 'decimal:2',
        ];
    }

    /**
     * Scope: produk dengan stok habis.
     */
    public function scopeOutOfStock($query)
    {
        return $query->where('qty', '<=', 0);
    }

    /**
     * Scope: produk dengan stok rendah (di bawah atau sama dengan min_qty).
     */
    public function scopeLowStock($query)
    {
        return $query->where('qty', '>', 0)->whereColumn('qty', '<=', 'min_qty');
    }

    /**
     * Scope: produk dengan stok normal (di atas min_qty).
     */
    public function scopeNormalStock($query)
    {
        return $query->whereColumn('qty', '>', 'min_qty');
    }

    /**
     * Mendapatkan status stok berdasarkan perbandingan qty dan min_qty.
     */
    public function getStockStatusAttribute(): string
    {
        if ($this->qty <= 0) {
            return 'out';
        }

        if ($this->qty <= $this->min_qty) {
            return 'low';
        }

        return 'normal';
    }
}
