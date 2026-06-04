<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecipeIngredient extends Model
{
    /**
     * Nonaktifkan timestamps pada tabel pivot/komposisi ini.
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'recipe_id',
        'product_id',
        'qty_per_portion',
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
            'qty_per_portion' => 'decimal:2',
        ];
    }

    /**
     * Relasi ke resep induk.
     */
    public function recipe(): BelongsTo
    {
        return $this->belongsTo(Recipe::class);
    }

    /**
     * Relasi ke produk/bahan baku.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
