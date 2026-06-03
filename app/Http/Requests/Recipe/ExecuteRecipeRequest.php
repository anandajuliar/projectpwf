<?php

namespace App\Http\Requests\Recipe;

use Illuminate\Foundation\Http\FormRequest;

class ExecuteRecipeRequest extends FormRequest
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
            'portions' => ['required', 'integer', 'min:1', 'max:9999'],
            'note'     => ['nullable', 'string', 'max:500'],
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
            'portions.required' => 'Jumlah porsi/loyang yang akan dibuat wajib diisi.',
            'portions.integer'  => 'Jumlah porsi harus berupa bilangan bulat.',
            'portions.min'      => 'Jumlah porsi minimal 1.',
            'portions.max'      => 'Jumlah porsi maksimal 9999.',
            'note.max'          => 'Catatan maksimal 500 karakter.',
        ];
    }
}
