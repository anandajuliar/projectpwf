<?php

namespace App\Http\Requests\Recipe;

use Illuminate\Foundation\Http\FormRequest;

class StoreRecipeRequest extends FormRequest
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
            'name'             => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z0-9\s\(\)\'\&\-\/\,\.]+$/'],
            'description'      => ['nullable', 'string'],
            'category'         => ['nullable', 'string', 'max:100'],
            'default_portions' => ['required', 'integer', 'min:1'],
            'is_active'        => ['sometimes', 'boolean'],

            'ingredients'                  => ['required', 'array', 'min:1'],
            'ingredients.*.product_id'     => ['required', 'integer', 'exists:products,id'],
            'ingredients.*.qty_per_portion'=> ['required', 'numeric', 'min:0.01'],
            'ingredients.*.note'           => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'                    => 'Nama resep wajib diisi.',
            'name.regex'                       => 'Nama resep hanya boleh berisi huruf, angka, spasi, dan simbol ( ) \' & - / , .',
            'default_portions.required'        => 'Jumlah porsi default wajib diisi.',
            'default_portions.min'             => 'Jumlah porsi minimal 1.',
            'ingredients.required'             => 'Resep harus memiliki setidaknya 1 bahan.',
            'ingredients.min'                  => 'Resep harus memiliki setidaknya 1 bahan.',
            'ingredients.*.product_id.required'=> 'ID bahan baku wajib diisi.',
            'ingredients.*.product_id.exists'  => 'Bahan baku tidak ditemukan.',
            'ingredients.*.qty_per_portion.required'=> 'Jumlah bahan per porsi wajib diisi.',
            'ingredients.*.qty_per_portion.min'=> 'Jumlah bahan per porsi harus lebih dari 0.',
        ];
    }
}
