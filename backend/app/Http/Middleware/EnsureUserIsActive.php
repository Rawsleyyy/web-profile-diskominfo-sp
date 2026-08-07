<?php

namespace App\Http\Middleware;

use App\Helpers\ActivityLogger;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsActive
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->is('admin/*') && ! $request->is('livewire/*')) {
            return $next($request);
        }

        $user = Auth::guard('web')->user();

        if (! $user || $user->status === 'active') {
            return $next($request);
        }

        ActivityLogger::log(
            subject: 'Akun admin dinonaktifkan saat sesi masih berjalan',
            method: 'LOGOUT',
            status: 'failed',
            userId: $user->id,
            description: $user->email,
        );

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
