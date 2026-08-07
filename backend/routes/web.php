<?php

use App\Helpers\ActivityLogger;
use App\Livewire\Admin\Articles;
use App\Livewire\Admin\Awards;
use App\Livewire\Admin\DokumenPublikManager;
use App\Livewire\Admin\HeroSlides;
use App\Livewire\Admin\Layanan;
use App\Livewire\Admin\PodcastManager;
use App\Livewire\Admin\SkmManager;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Halaman awal backend
|--------------------------------------------------------------------------
| Backend Laravel hanya digunakan untuk autentikasi dan dashboard admin.
*/
Route::get('/', function () {
    return Auth::check()
        ? redirect()->route('admin.dashboard')
        : redirect()->route('admin.login');
})->name('home');

/*
|--------------------------------------------------------------------------
| Alias login
|--------------------------------------------------------------------------
| /login otomatis mengarah ke /admin/login.
*/
Route::redirect('/login', '/admin/login')->name('login');

/*
|--------------------------------------------------------------------------
| Login admin
|--------------------------------------------------------------------------
*/
Route::view('/admin/login', 'admin.login')
    ->middleware('guest')
    ->name('admin.login');

Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {
    Route::view('/dashboard', 'admin.dashboard')->name('dashboard');
    Route::view('/theme-settings', 'admin.theme-settings')->name('theme-settings');
    Route::get('/header', HeroSlides::class)->name('header');
    Route::view('/berita', 'admin.berita')->name('berita');
    Route::view('/pejabat', 'admin.pejabat')->name('pejabat');
    Route::get('/podcast', PodcastManager::class)->name('podcast');
    Route::get('/layanan', Layanan::class)->name('layanan');
    Route::view('/dokumen', 'admin.dokumen')->name('dokumen');
    Route::get('/dokumen-publik', DokumenPublikManager::class)->name('dokumen-publik');
    Route::get('/skm', SkmManager::class)->name('skm');
    Route::get('/articles', Articles::class)->name('articles');
    Route::get('/awards', Awards::class)->name('awards');

    Route::middleware('role:Super Admin')->group(function () {
        Route::view('/log-activity', 'admin.log-activity')->name('log-activity');
        Route::view('/users', 'admin.users')->name('users');
    });

    Route::post('/logout', function () {
        $user = Auth::user();
        if ($user) {
            ActivityLogger::log('User Logout Dashboard', 'LOGOUT', 'success', $user->id, $user->email);
        }
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        if (request()->expectsJson()) {
            return response()->json(['message' => 'Logout berhasil.']);
        }

        return redirect()->route('admin.login');
    })->name('logout');
});
