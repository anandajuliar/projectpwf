<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'           => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z0-9\s\(\)\'\&\-]+$/'],
            'description'    => ['nullable', 'string'],
            'category'       => ['nullable', 'string', 'max:100'],
            'unit'           => ['required', 'string', 'in:gram,kg,butir,ml,liter,sdm,sdt,pcs'],
            'qty'            => ['required', 'numeric', 'min:0'],
            'min_qty'        => ['required', 'numeric', 'min:0'],
            'price_per_unit' => ['required', 'numeric', 'min:3000'],
        ];
    }

    /**
     * Custom validation messages in Bahasa Indonesia.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required'           => 'Nama bahan wajib diisi.',
            'name.regex'              => 'Nama bahan hanya boleh berisi huruf, angka, spasi, dan simbol ( ) \' & -',
            'unit.required'           => 'Satuan wajib diisi.',
            'unit.in'                 => 'Satuan harus salah satu dari: gram, kg, butir, ml, liter, sdm, sdt, pcs.',
            'qty.required'            => 'Jumlah stok wajib diisi.',
            'qty.numeric'             => 'Jumlah stok harus berupa angka.',
            'qty.min'                 => 'Jumlah stok tidak boleh negatif.',
            'min_qty.required'        => 'Stok minimum wajib diisi.',
            'min_qty.numeric'         => 'Stok minimum harus berupa angka.',
            'min_qty.min'             => 'Stok minimum tidak boleh negatif.',
            'price_per_unit.required' => 'Harga per satuan wajib diisi.',
            'price_per_unit.numeric'  => 'Harga harus berupa angka.',
            'price_per_unit.min'      => 'Harga beli minimal Rp 3.000.',
        ];
    }
}
