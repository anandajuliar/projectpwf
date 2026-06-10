<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'           => ['sometimes', 'string', 'max:255', 'regex:/^[a-zA-Z0-9\s\(\)\'\&\-]+$/'],
            'description'    => ['sometimes', 'nullable', 'string'],
            'category'       => ['sometimes', 'nullable', 'string', 'max:100'],
            'unit'           => ['sometimes', 'string', 'in:gram,kg,butir,ml,liter,sdm,sdt,pcs'],
            'qty'            => ['sometimes', 'numeric', 'min:0'],
            'min_qty'        => ['sometimes', 'numeric', 'min:0'],
            'price_per_unit' => ['sometimes', 'numeric', 'min:3000'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.regex'             => 'Nama bahan hanya boleh berisi huruf, angka, spasi, dan simbol ( ) \' & -',
            'unit.in'                => 'Satuan harus salah satu dari: gram, kg, butir, ml, liter, sdm, sdt, pcs.',
            'qty.numeric'            => 'Jumlah stok harus berupa angka.',
            'qty.min'                => 'Jumlah stok tidak boleh negatif.',
            'min_qty.numeric'        => 'Stok minimum harus berupa angka.',
            'min_qty.min'            => 'Stok minimum tidak boleh negatif.',
            'price_per_unit.numeric' => 'Harga harus berupa angka.',
            'price_per_unit.min'     => 'Harga beli minimal Rp 3.000.',
        ];
    }
}