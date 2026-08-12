<?php

use App\Helpers\ActivityLogger;
use App\Http\Controllers\SpaAuthController;
use App\Livewire\Admin\Articles;
use App\Livewire\Admin\Awards;
use App\Livewire\Admin\DokumenPublikManager;
use App\Livewire\Admin\HomepageManager;
use App\Livewire\Admin\Layanan;
use App\Livewire\Admin\MediaLibrary;
use App\Livewire\Admin\ModulesManager;
use App\Livewire\Admin\NavigationManager;
use App\Livewire\Admin\PagesManager;
use App\Livewire\Admin\PodcastManager;
use App\Livewire\Admin\PublishingManager;
use App\Livewire\Admin\RolePermissionsManager;
use App\Livewire\Admin\SiteSettingsManager;
use App\Livewire\Admin\SkmManager;
use App\Livewire\Admin\TemplatePresetsManager;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');
Route::view('/admin/login', 'admin.login')->middleware('guest')->name('admin.login');
Route::post('/auth/login', [SpaAuthController::class, 'login'])->middleware('throttle:5,1')->name('auth.login');

Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {
    Route::view('/dashboard', 'admin.dashboard')->middleware('permission:dashboard.view')->name('dashboard');

    Route::middleware('permission:builder.manage')->group(function () {
        Route::get('/modules', ModulesManager::class)->name('modules');
        Route::get('/pages', PagesManager::class)->name('pages');
        Route::get('/navigation', NavigationManager::class)->name('navigation');
        Route::get('/homepage', HomepageManager::class)->name('homepage');
        Route::get('/templates', TemplatePresetsManager::class)->name('templates');
    });

    Route::middleware('permission:theme.manage')->group(function () {
        Route::get('/site-settings', SiteSettingsManager::class)->name('site-settings');
        Route::view('/theme-settings', 'admin.theme-settings')->name('theme-settings');
    });

    Route::get('/media', MediaLibrary::class)->middleware('permission:media.manage')->name('media');
    Route::get('/publish', PublishingManager::class)->middleware('permission:site.publish')->name('publish');

    Route::middleware('permission:content.manage')->group(function () {
        Route::view('/berita', 'admin.berita')->name('berita');
        Route::view('/pejabat', 'admin.pejabat')->name('pejabat');
        Route::get('/podcast', PodcastManager::class)->name('podcast');
        Route::get('/layanan', Layanan::class)->name('layanan');
        Route::view('/dokumen', 'admin.dokumen')->name('dokumen');
        Route::get('/dokumen-publik', DokumenPublikManager::class)->name('dokumen-publik');
        Route::get('/skm', SkmManager::class)->name('skm');
        Route::get('/articles', Articles::class)->name('articles');
        Route::get('/awards', Awards::class)->name('awards');
    });

    Route::view('/log-activity', 'admin.log-activity')->middleware('permission:logs.view')->name('log-activity');
    Route::view('/users', 'admin.users')->middleware('permission:users.manage')->name('users');
    Route::get('/roles', RolePermissionsManager::class)->middleware('permission:roles.manage')->name('roles');

    Route::post('/logout', function () {
        $user = Auth::user();
        if ($user) ActivityLogger::log('User Logout Dashboard', 'LOGOUT', 'success', $user->id, $user->email);
        Auth::logout(); request()->session()->invalidate(); request()->session()->regenerateToken();
        return request()->expectsJson() ? response()->json(['message'=>'Logout berhasil.']) : redirect()->route('admin.login');
    })->name('logout');
});
