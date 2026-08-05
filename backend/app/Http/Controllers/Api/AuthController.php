<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ActivityLogger;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            ActivityLogger::log(
                'Percobaan login API gagal',
                'LOGIN',
                'failed',
                null,
                $credentials['email']
            );

            return response()->json(['message' => 'Email atau password salah.'], 401);
        }

        if ($user->status !== 'active') {
            ActivityLogger::log('Login akun nonaktif', 'LOGIN', 'failed', $user->id, $user->email);
            return response()->json(['message' => 'Akun tidak aktif. Hubungi Super Admin.'], 403);
        }

        // Satu token aktif per perangkat/klien untuk mengurangi token terlantar.
        $user->tokens()->where('name', 'auth_token')->delete();
        $token = $user->createToken('auth_token')->plainTextToken;
        $user->forceFill(['last_login_at' => now()])->save();

        ActivityLogger::log('User Login API', 'LOGIN', 'success', $user->id, $user->email);

        return response()->json([
            'message' => 'Login berhasil.',
            'user' => $user->load('role'),
            'token' => $token,
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $user = $request->user();
        ActivityLogger::log('User Logout API', 'LOGOUT', 'success', $user->id, $user->email);
        $user->currentAccessToken()?->delete();

        return response()->json(['message' => 'Logout berhasil.']);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json($request->user()->load('role'));
    }
}
