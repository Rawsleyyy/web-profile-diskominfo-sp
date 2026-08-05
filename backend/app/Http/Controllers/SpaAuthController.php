<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Helpers\ActivityLogger;

/**
 * Login berbasis SESSION COOKIE (bukan token) — dipakai khusus oleh
 * frontend React (Login.jsx) via pola Sanctum SPA authentication,
 * supaya setelah login, sesi yang sama langsung dikenali oleh
 * dashboard admin Livewire di domain yang sama (localhost, beda port).
 *
 * Ini terpisah dari App\Http\Controllers\Api\AuthController yang
 * mengeluarkan Bearer token untuk kebutuhan API murni/mobile.
 */
class SpaAuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            ActivityLogger::log(
                'Percobaan login gagal: ' . $request->email,
                'POST',
                'failed',
                null,
                'Email atau password salah'
            );

            return response()->json(['message' => 'Email atau password salah.'], 422);
        }

        if ($user->status === 'inactive') {
            ActivityLogger::log(
                'Percobaan login akun nonaktif: ' . $request->email,
                'POST',
                'failed',
                $user->id,
                'Akun berstatus tidak aktif'
            );

            return response()->json(['message' => 'Akun ini sudah dinonaktifkan. Hubungi Super Admin.'], 403);
        }

        // Jangan memakai remember cookie karena admin wajib login ulang setelah timeout.
        Auth::login($user, false);
        $request->session()->regenerate();

        $expiresAt = now()->addMinutes((int) config('session.admin_timeout', 15));
        $request->session()->put('admin_session_expires_at', $expiresAt->timestamp);

        $user->forceFill(['last_login_at' => now()])->save();

        ActivityLogger::log('User Login: ' . $user->email, 'POST', 'success', $user->id);

        return response()->json([
            'message' => 'Login berhasil',
            'user'       => $user->load('role'),
            'expires_at' => $expiresAt->toIso8601String(),
        ]);
    }
}
