<?php

use App\Helpers\ActivityLogger;
use App\Http\Controllers\SpaAuthController;
use App\Livewire\Admin\Articles;
use App\Livewire\Admin\Awards;
use App\Livewire\Admin\DokumenPublikManager;
use App\Livewire\Admin\FaqManager;
use App\Livewire\Admin\HomepageManager;
use App\Livewire\Admin\HeaderSettingsManager;
use App\Livewire\Admin\HeroSlides;
use App\Livewire\Admin\Layanan;
use App\Livewire\Admin\ModulesManager;
use App\Livewire\Admin\NavigationManager;
use App\Livewire\Admin\PagesManager;
use App\Livewire\Admin\PodcastManager;
use App\Livewire\Admin\SiteSettingsManager;
use App\Livewire\Admin\SkmManager;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');
Route::view('/admin/login', 'admin.login')->middleware('guest')->name('admin.login');
Route::post('/auth/login', [SpaAuthController::class, 'login'])->middleware('throttle:5,1')->name('auth.login');

Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {
    Route::view('/dashboard', 'admin.dashboard')->name('dashboard');

    // Website Builder: konfigurasi lintas-instansi.
    Route::get('/site-settings', SiteSettingsManager::class)->name('site-settings');
    Route::get('/header-settings', HeaderSettingsManager::class)->name('header-settings');
    Route::get('/hero-slides', HeroSlides::class)->name('hero-slides');
    Route::view('/theme-settings', 'admin.theme-settings')->name('theme-settings');
    Route::get('/modules', ModulesManager::class)->name('modules');
    Route::get('/pages', PagesManager::class)->name('pages');
    Route::get('/navigation', NavigationManager::class)->name('navigation');
    Route::get('/homepage', HomepageManager::class)->name('homepage');
    Route::get('/faq', FaqManager::class)->name('faq');

    // Modul konten yang sudah tersedia.
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
