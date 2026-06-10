<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Recipe extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'description',
        'category',
        'default_portions',
        'created_by',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'default_portions' => 'integer',
            'is_active'        => 'boolean',
        ];
    }

    /**
     * Relasi ke user yang membuat resep.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relasi ke bahan-bahan resep.
     */
    public function ingredients(): HasMany
    {
        return $this->hasMany(RecipeIngredient::class);
    }

    /**
     * Relasi ke produk/bahan baku melalui recipe_ingredients.
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'recipe_ingredients')
                    ->withPivot(['qty_per_portion', 'note'])
                    ->withTimestamps();
    }

    /**
     * Scope: hanya resep yang aktif.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Menghitung total estimasi biaya bahan per porsi.
     */
    public function getCostPerPortionAttribute(): float
    {
        return $this->ingredients->sum(function ($ingredient) {
            return $ingredient->qty_per_portion * ($ingredient->product->price_per_unit ?? 0);
        });
    }
}
