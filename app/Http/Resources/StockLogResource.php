<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StockLogResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'type'        => $this->type,
            'type_label'  => $this->getTypeLabel(),
            'product'     => [
                'id'   => $this->product?->id,
                'name' => $this->product?->name,
                'unit' => $this->unit,
            ],
            'user'        => [
                'id'   => $this->user?->id,
                'name' => $this->user?->name,
                'role' => $this->user?->role,
            ],
            'qty_before'  => (float) $this->qty_before,
            'qty_changed' => (float) $this->qty_changed,
            'qty_after'   => (float) $this->qty_after,
            'unit'        => $this->unit,
            
            'recipe_id'   => $this->recipe_id, 
            'portions'    => $this->portions,  

            'recipe'      => $this->when($this->recipe_id !== null, [
                'id'       => $this->recipe?->id,
                'name'     => $this->recipe?->name,
            ]),
            'note'        => $this->note,
            'created_at'  => $this->created_at?->toDateTimeString(),
        ];
    }

    /**
     * Mengubah tipe ke label yang mudah dibaca.
     */
    private function getTypeLabel(): string
    {
        return match ($this->type) {
            'reduce'        => 'Pengurangan Manual',
            'add'           => 'Penambahan / Restock',
            'recipe_reduce' => 'Penggunaan Resep',
            default         => $this->type,
        };
    }
}