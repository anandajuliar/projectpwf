<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    /**
     * Login chef/admin dan generate API token Sanctum.
     *
     * POST /api/auth/login
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->only('email', 'password');

        if (! Auth::attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau password salah.',
            ], Response::HTTP_UNAUTHORIZED);
        }

        /** @var User $user */
        $user = Auth::user();

        if (! $user->is_active) {
            Auth::logout();

            return response()->json([
                'success' => false,
                'message' => 'Akun Anda telah dinonaktifkan. Hubungi admin.',
            ], Response::HTTP_FORBIDDEN);
        }

        // Hapus token lama sebelum membuat token baru (satu sesi per user)
        $user->tokens()->delete();

        $token = $user->createToken('api-token-' . $user->id)->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil.',
            'data'    => [
                'user'  => new UserResource($user),
                'token' => $token,
                'token_type' => 'Bearer',
            ],
        ], Response::HTTP_OK);
    }

    /**
     * Register chef baru. Hanya bisa diakses oleh admin (dijaga middleware).
     *
     * POST /api/auth/register
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'role'      => $request->input('role', 'chef'),
            'is_active' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Akun chef berhasil dibuat.',
            'data'    => new UserResource($user),
        ], Response::HTTP_CREATED);
    }

    /**
     * Logout: hapus (revoke) semua token Sanctum milik user saat ini.
     *
     * POST /api/auth/logout
     */
    public function logout(Request $request): JsonResponse
    {
        // Revoke hanya token yang sedang aktif
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil. Token telah dicabut.',
        ], Response::HTTP_OK);
    }

    /**
     * Mendapatkan data profil user yang sedang login.
     *
     * GET /api/auth/me
     */
    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Data profil berhasil diambil.',
            'data'    => new UserResource($request->user()),
        ], Response::HTTP_OK);
    }
}
