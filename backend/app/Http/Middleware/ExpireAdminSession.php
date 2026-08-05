<?php

namespace App\Http\Middleware;

use App\Helpers\ActivityLogger;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ExpireAdminSession
{
    /**
     * Memaksa session admin berakhir pada waktu absolut yang ditentukan saat login.
     * Middleware ini ditempatkan pada grup web agar juga memproteksi request Livewire.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Hanya diberlakukan untuk dashboard admin dan request Livewire.
        if (! $request->is('admin/*') && ! $request->is('livewire/*')) {
            return $next($request);
        }

        if (! Auth::guard('web')->check()) {
            return $next($request);
        }

        $expiresAt = (int) $request->session()->get('admin_session_expires_at', 0);

        // Kompatibilitas untuk session lama yang sudah aktif sebelum fitur ini dipasang.
        if ($expiresAt === 0) {
            $expiresAt = now()
                ->addMinutes((int) config('session.admin_timeout', 15))
                ->timestamp;

            $request->session()->put('admin_session_expires_at', $expiresAt);
        }

        if (now()->timestamp < $expiresAt) {
            return $next($request);
        }

        $user = Auth::guard('web')->user();

        if ($user) {
            ActivityLogger::log(
                'Session admin berakhir otomatis: '.$user->email,
                'LOGOUT',
                'success',
                $user->id,
                'Batas waktu session admin telah habis'
            );
        }

        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($request->expectsJson() || $request->is('livewire/*')) {
            return response()->json([
                'message' => 'Session admin telah berakhir. Silakan login kembali.',
                'code' => 'ADMIN_SESSION_EXPIRED',
            ], 401);
        }

        $loginUrl = (string) config('app.admin_login_url', route('admin.login'));
        $separator = str_contains($loginUrl, '?') ? '&' : '?';

        return redirect()->away($loginUrl.$separator.'expired=1');
    }
}
