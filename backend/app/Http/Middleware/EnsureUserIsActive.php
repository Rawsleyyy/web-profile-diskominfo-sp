<?php

namespace App\Http\Middleware;

use App\Helpers\ActivityLogger;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsActive
{
    /**
     * Menolak akun yang telah dinonaktifkan walaupun sesi/token masih aktif.
     * Berlaku untuk dashboard web/Livewire dan endpoint API terautentikasi.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || $user->status === 'active') {
            return $next($request);
        }

        ActivityLogger::log(
            subject: 'Akun dinonaktifkan saat sesi/token masih aktif',
            method: 'LOGOUT',
            status: 'failed',
            userId: $user->id,
            description: $user->email,
        );

        if ($request->is('api/*')) {
            $user->currentAccessToken()?->delete();

            return response()->json([
                'message' => 'Akun Anda telah dinonaktifkan. Hubungi Super Admin.',
                'code' => 'ADMIN_ACCOUNT_INACTIVE',
            ], 403);
        }

        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($request->expectsJson() || $request->is('livewire/*')) {
            return response()->json([
                'message' => 'Akun Anda telah dinonaktifkan. Hubungi Super Admin.',
                'code' => 'ADMIN_ACCOUNT_INACTIVE',
            ], 403);
        }

        return redirect()
            ->route('admin.login')
            ->with('error', 'Akun Anda telah dinonaktifkan. Hubungi Super Admin.');
    }
}
