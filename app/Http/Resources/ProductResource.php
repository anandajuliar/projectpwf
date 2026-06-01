<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'name'           => $this->name,
            'description'    => $this->description,
            'category'       => $this->category,
            'unit'           => $this->unit,
            'qty'            => (float) $this->qty,
            'min_qty'        => (float) $this->min_qty,
            'price_per_unit' => (float) $this->price_per_unit,
            'stock_status'   => $this->getStockStatus(),
            'created_at'     => $this->created_at?->toDateTimeString(),
            'updated_at'     => $this->updated_at?->toDateTimeString(),
        ];
    }

    /**
     * Menentukan status stok berdasarkan perbandingan qty vs min_qty.
     */
    private function getStockStatus(): string
    {
        if ($this->qty <= 0) {
            return 'out';     // Stok habis
        }

        if ($this->qty <= $this->min_qty) {
            return 'low';     // Stok hampir habis (perlu restock)
        }

        return 'normal';      // Stok aman
    }
}
