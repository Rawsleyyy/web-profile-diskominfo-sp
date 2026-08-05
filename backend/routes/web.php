<?php

use App\Helpers\ActivityLogger;
use App\Http\Controllers\SpaAuthController;
use App\Livewire\Admin\Articles;
use App\Livewire\Admin\Awards;
use App\Livewire\Admin\DokumenPublikManager;
use App\Livewire\Admin\Layanan;
use App\Livewire\Admin\PodcastManager;
use App\Livewire\Admin\SkmManager;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');
Route::view('/admin/login', 'admin.login')->middleware('guest')->name('admin.login');
Route::post('/auth/login', [SpaAuthController::class, 'login'])->middleware('throttle:5,1')->name('auth.login');

Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {
    Route::view('/dashboard', 'admin.dashboard')->name('dashboard');
    Route::view('/theme-settings', 'admin.theme-settings')->name('theme-settings');
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
