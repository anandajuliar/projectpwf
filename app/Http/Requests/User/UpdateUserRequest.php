<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; 
    }

    public function rules(): array
    {
        $userId = $this->route('user')?->id;

        return [
            // Validasi Nama: Huruf & Spasi
            'name'     => ['sometimes', 'string', 'min:3', 'max:255', 'regex:/^[a-zA-Z\s]+$/'],
            // Validasi Email: Menghindari duplikat kecuali milik user ini sendiri
            'email'    => [
                'sometimes',
                'string',
                'min:5',
                'email:rfc,dns',
                Rule::unique('users', 'email')->ignore($userId),
            ],
            // Validasi Password: Minimal 8 karakter
            'password' => ['sometimes', 'string', 'min:8'],
            'role'     => ['sometimes', 'in:admin,chef'],
            'is_active'=> ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.min'     => 'Nama karyawan minimal harus 3 huruf.',
            'name.regex'   => 'Nama karyawan hanya boleh berisi huruf dan spasi.',
            'email.min'    => 'Email minimal harus 5 karakter.',
            'email.email'  => 'Format email tidak valid. Pastikan ada tanda @ dan titik (.).',
            'email.unique' => 'Email ini sudah digunakan oleh pengguna lain.',
            'password.min' => 'Password baru minimal harus 8 karakter.',
        ];
    }
}