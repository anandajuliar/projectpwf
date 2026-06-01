<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
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
     * Semua field bersifat opsional (PATCH-style update).
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'           => ['sometimes', 'string', 'max:255'],
            'description'    => ['sometimes', 'nullable', 'string'],
            'category'       => ['sometimes', 'nullable', 'string', 'max:100'],
            'unit'           => ['sometimes', 'string', 'in:gram,kg,butir,ml,liter,sdm,sdt,pcs'],
            'qty'            => ['sometimes', 'numeric', 'min:0'],
            'min_qty'        => ['sometimes', 'numeric', 'min:0'],
            'price_per_unit' => ['sometimes', 'numeric', 'min:0'],
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
            'unit.in'                => 'Satuan harus salah satu dari: gram, kg, butir, ml, liter, sdm, sdt, pcs.',
            'qty.numeric'            => 'Jumlah stok harus berupa angka.',
            'qty.min'                => 'Jumlah stok tidak boleh negatif.',
            'min_qty.numeric'        => 'Stok minimum harus berupa angka.',
            'min_qty.min'            => 'Stok minimum tidak boleh negatif.',
            'price_per_unit.numeric' => 'Harga harus berupa angka.',
            'price_per_unit.min'     => 'Harga tidak boleh negatif.',
        ];
    }
}
