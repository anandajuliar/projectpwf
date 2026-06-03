<?php

namespace App\Http\Requests\Recipe;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRecipeRequest extends FormRequest
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
            'name'             => ['sometimes', 'string', 'max:255'],
            'description'      => ['nullable', 'string'],
            'category'         => ['nullable', 'string', 'max:100'],
            'default_portions' => ['sometimes', 'integer', 'min:1'],
            'is_active'        => ['sometimes', 'boolean'],

            // Mengganti seluruh komposisi bahan (opsional)
            'ingredients'                  => ['sometimes', 'array', 'min:1'],
            'ingredients.*.product_id'     => ['required_with:ingredients', 'integer', 'exists:products,id'],
            'ingredients.*.qty_per_portion'=> ['required_with:ingredients', 'numeric', 'min:0.01'],
            'ingredients.*.note'           => ['nullable', 'string', 'max:255'],
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
            'default_portions.min'                 => 'Jumlah porsi minimal 1.',
            'ingredients.min'                      => 'Resep harus memiliki setidaknya 1 bahan.',
            'ingredients.*.product_id.exists'      => 'Bahan baku tidak ditemukan.',
            'ingredients.*.qty_per_portion.min'    => 'Jumlah bahan per porsi harus lebih dari 0.',
        ];
    }
}
