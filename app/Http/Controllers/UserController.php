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
    /**
     * Menampilkan semua user (admin & chef).
     * Hanya admin yang bisa mengakses (dijaga middleware).
     *
     * GET /api/users
     */
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
     * Menampilkan detail satu user.
     * Hanya admin yang bisa mengakses (dijaga middleware).
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
     * Memperbarui data user (nama, email, password, role).
     * Hanya admin yang bisa mengakses (dijaga middleware).
     *
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
     * Hanya admin yang bisa mengakses (dijaga middleware).
     * Admin tidak bisa menonaktifkan dirinya sendiri.
     *
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
     * Hanya admin yang bisa mengakses (dijaga middleware).
     * Admin tidak bisa menghapus dirinya sendiri.
     *
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
