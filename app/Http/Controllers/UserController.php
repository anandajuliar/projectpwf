<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{

    public function index(Request $request): JsonResponse
    {
        $query = User::query();

        // Filter berdasarkan role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Filter berdasarkan status aktif
        if ($request->filled('is_active')) {
            $query->where('is_active', filter_var($request->is_active, FILTER_VALIDATE_BOOLEAN));
        }

        // Pencarian berdasarkan nama atau email
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $users = $query->latest()->paginate($request->input('per_page', 20));

        return response()->json([
            'success' => true,
            'message' => 'Daftar user berhasil diambil.',
            'data'    => UserResource::collection($users->items()),
            'meta'    => [
                'total'        => $users->total(),
                'per_page'     => $users->perPage(),
                'current_page' => $users->currentPage(),
                'last_page'    => $users->lastPage(),
            ],
        ], Response::HTTP_OK);
    }
    
    /**
     * Membuat akun karyawan baru 
     *
     * POST /api/users
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            // Validasi Nama: Wajib huruf (besar/kecil) dan spasi saja
            'name'     => ['required', 'string', 'min:3', 'max:255', 'regex:/^[a-zA-Z\s]+$/'],
            // Validasi Email: Standar email valid (huruf, angka, @, titik, dll)
            'email'    => ['required', 'string','min:5', 'email:rfc,dns', 'max:255', 'unique:users,email'],
            // Validasi Password: Minimal 8 karakter
            'password' => ['required', 'string', 'min:8'],
        ], [
            'name.min'     => 'Nama karyawan minimal harus 3 huruf.',
            'name.regex'   => 'Nama karyawan hanya boleh berisi huruf dan spasi.',
            'email.min'    => 'Email minimal harus 5 karakter.',
            'email.email'  => 'Format email tidak valid. Pastikan ada tanda @ dan titik (.).',
            'email.unique' => 'Email ini sudah terdaftar di sistem. Gunakan email lain.',
            'password.min' => 'Password minimal harus 8 karakter.',
        ]);

        $user = User::create([
            'name'      => $validated['name'],
            'email'     => $validated['email'],
            'password'  => Hash::make($validated['password']),
            'role'      => 'chef', 
            'is_active' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => "Akun staf dapur untuk {$user->name} berhasil dibuat.",
            'data'    => new UserResource($user),
        ], Response::HTTP_CREATED);
    }

    /**
     * Menampilkan detail satu user.
     *
     *
     * GET /api/users/{id}
     */
    public function show(User $user): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Detail user berhasil diambil.',
            'data'    => new UserResource($user),
        ], Response::HTTP_OK);
    }

    /**
     * Memperbarui data user      
     * PUT /api/users/{id}
     */
    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        $data = $request->only(['name', 'email', 'role', 'is_active']);

        // Hash password baru jika ada
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Data user berhasil diperbarui.',
            'data'    => new UserResource($user->fresh()),
        ], Response::HTTP_OK);
    }

    /**
     * Mengaktifkan atau menonaktifkan user.
     * PATCH /api/users/{id}/toggle-active
     */
    public function toggleActive(Request $request, User $user): JsonResponse
    {
        // Cegah admin menonaktifkan akunnya sendiri
        if ($request->user()->id === $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak bisa menonaktifkan akun Anda sendiri.',
            ], Response::HTTP_FORBIDDEN);
        }

        $user->update(['is_active' => ! $user->is_active]);

        $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return response()->json([
            'success' => true,
            'message' => "Akun {$user->name} berhasil {$status}.",
            'data'    => new UserResource($user->fresh()),
        ], Response::HTTP_OK);
    }

    /**
     * Menghapus user.

     * DELETE /api/users/{id}
     */
    public function destroy(Request $request, User $user): JsonResponse
    {
        // Cegah admin menghapus dirinya sendiri
        if ($request->user()->id === $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak bisa menghapus akun Anda sendiri.',
            ], Response::HTTP_FORBIDDEN);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => "Akun {$user->name} berhasil dihapus.",
        ], Response::HTTP_OK);
    }
}
