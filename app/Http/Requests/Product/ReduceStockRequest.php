<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class ReduceStockRequest extends FormRequest
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
            'qty'    => ['required', 'numeric', 'min:0.01'],
            'note'   => ['nullable', 'string', 'max:500'],
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
            'qty.required' => 'Jumlah pengurangan stok wajib diisi.',
            'qty.numeric'  => 'Jumlah pengurangan harus berupa angka.',
            'qty.min'      => 'Jumlah pengurangan harus lebih dari 0.',
            'note.max'     => 'Catatan maksimal 500 karakter.',
        ];
    }
}
