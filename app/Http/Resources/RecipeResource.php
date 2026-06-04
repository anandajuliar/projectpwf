<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RecipeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'name'             => $this->name,
            'description'      => $this->description,
            'category'         => $this->category,
            'default_portions' => $this->default_portions,
            'is_active'        => (bool) $this->is_active,
            'creator'          => [
                'id'   => $this->creator?->id,
                'name' => $this->creator?->name,
            ],
            'ingredients'      => $this->when(
                $this->relationLoaded('ingredients'),
                fn () => $this->ingredients->map(fn ($ing) => [
                    'id'              => $ing->id,
                    'product_id'      => $ing->product_id,
                    'product_name'    => $ing->product?->name,
                    'unit'            => $ing->product?->unit,
                    'qty_per_portion' => (float) $ing->qty_per_portion,
                    'note'            => $ing->note,
                    'stock_available' => (float) ($ing->product?->qty ?? 0),
                    'stock_status'    => $ing->product?->stock_status,
                ])
            ),
            'cost_per_portion' => $this->when(
                $this->relationLoaded('ingredients'),
                fn () => (float) $this->cost_per_portion
            ),
            'created_at'       => $this->created_at?->toDateTimeString(),
            'updated_at'       => $this->updated_at?->toDateTimeString(),
        ];
    }
}
