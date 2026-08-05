<?php

namespace App\Livewire\Admin;

use App\Helpers\ActivityLogger;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Livewire\Component;

class Login extends Component
{
    public string $email = '';
    public string $password = '';

    public function login(): void
    {
        $credentials = $this->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $rateLimitKey = Str::lower($credentials['email']).'|'.request()->ip();
        if (RateLimiter::tooManyAttempts($rateLimitKey, 5)) {
            $seconds = RateLimiter::availableIn($rateLimitKey);
            $this->addError('email', 'Terlalu banyak percobaan login. Coba lagi dalam '.$seconds.' detik.');
            return;
        }

        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Auth::attempt($credentials, false)) {
            RateLimiter::hit($rateLimitKey, 60);
            ActivityLogger::log('Percobaan login dashboard gagal', 'LOGIN', 'failed', null, $this->email);
            $this->addError('email', 'Email atau password salah.');
            return;
        }

        if ($user->status !== 'active') {
            Auth::logout();
            ActivityLogger::log('Login dashboard akun nonaktif', 'LOGIN', 'failed', $user->id, $user->email);
            $this->addError('email', 'Akun dinonaktifkan. Hubungi Super Admin.');
            return;
        }

        RateLimiter::clear($rateLimitKey);
        request()->session()->regenerate();
        $expiresAt = now()->addMinutes((int) config('session.admin_timeout', 15));
        request()->session()->put('admin_session_expires_at', $expiresAt->timestamp);
        $user->forceFill(['last_login_at' => now()])->save();

        ActivityLogger::log('User Login Dashboard', 'LOGIN', 'success', $user->id, $user->email);
        $this->redirectRoute('admin.dashboard', navigate: false);
    }

    public function render()
    {
        return view('livewire.admin.login');
    }
}
