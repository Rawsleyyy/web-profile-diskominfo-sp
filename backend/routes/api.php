<?php

use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AwardController;
use App\Http\Controllers\Api\BeritaController;
use App\Http\Controllers\Api\CustomPageController;
use App\Http\Controllers\Api\DokumenController;
use App\Http\Controllers\Api\FaqController;
use App\Http\Controllers\Api\HeroSlideController;
use App\Http\Controllers\Api\KategoriController;
use App\Http\Controllers\Api\LayananController;
use App\Http\Controllers\Api\LogActivityController;
use App\Http\Controllers\Api\ModuleController;
use App\Http\Controllers\Api\MoniksController;
use App\Http\Controllers\Api\NavigationController;
use App\Http\Controllers\Api\PejabatController;
use App\Http\Controllers\Api\PodcastController;
use App\Http\Controllers\Api\SiteConfigController;
use App\Http\Controllers\Api\SkmController;
use App\Http\Controllers\Api\ThemeSettingController;
use App\Http\Controllers\Api\UserController;
use App\Models\VisitorLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1')->name('api.login');

Route::get('/site-config', [SiteConfigController::class, 'index']);
Route::get('/hero-slides', [HeroSlideController::class, 'index']);
Route::get('/navigation', [NavigationController::class, 'index']);
Route::get('/modules', [ModuleController::class, 'index']);
Route::get('/pages/{slug}', [CustomPageController::class, 'show']);

Route::get('/visitor-stats', function () {
    VisitorLog::firstOrCreate([
        'ip_address' => request()->ip(),
        'visit_date' => now()->toDateString(),
    ]);

    return response()->json([
        'hari_ini' => VisitorLog::whereDate('visit_date', Carbon::today())->count(),
        'kemarin' => VisitorLog::whereDate('visit_date', Carbon::yesterday())->count(),
        'bulan_ini' => VisitorLog::whereYear('visit_date', now()->year)->whereMonth('visit_date', now()->month)->count(),
        'total' => VisitorLog::count(),
    ]);
})->middleware('throttle:120,1');

Route::get('/berita', [BeritaController::class, 'index'])->middleware('module:berita');
Route::get('/berita/{id}', [BeritaController::class, 'show'])->middleware('module:berita');
Route::get('/articles', [ArticleController::class, 'index'])->middleware('module:articles');
Route::get('/articles/{id}', [ArticleController::class, 'show'])->middleware('module:articles');
Route::get('/pejabat', [PejabatController::class, 'index'])->middleware('module:struktur');
Route::get('/awards', [AwardController::class, 'index'])->middleware('module:awards');
Route::get('/dokumen', [DokumenController::class, 'index'])->middleware('module:ppid');
Route::get('/dokumen-publik', [DokumenController::class, 'indexPublik'])->middleware('module:ppid');
Route::get('/podcast', [PodcastController::class, 'index'])->middleware('module:podcast');
Route::get('/layanan', [LayananController::class, 'index'])->middleware('module:layanan');
Route::get('/kategori', [KategoriController::class, 'index'])->middleware('module:layanan');
Route::get('/theme', [ThemeSettingController::class, 'index']);
Route::get('/faqs', [FaqController::class, 'index'])->middleware('module:faq');
Route::post('/moniks/ask', [MoniksController::class, 'ask'])->middleware(['module:faq', 'throttle:30,1']);
Route::post('/skm/store', [SkmController::class, 'store'])->middleware(['module:skm', 'throttle:10,1']);
Route::get('/skm/stats', [SkmController::class, 'getStats'])->middleware('module:skm');

Route::middleware(['auth:sanctum', 'active.user'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('api.logout');
    Route::get('/me', [AuthController::class, 'me']);

    Route::post('/berita', [BeritaController::class, 'store']);
    Route::put('/berita/{id}', [BeritaController::class, 'update']);
    Route::delete('/berita/{id}', [BeritaController::class, 'destroy']);

    Route::post('/layanan', [LayananController::class, 'store']);
    Route::put('/layanan/{id}', [LayananController::class, 'update']);
    Route::delete('/layanan/{id}', [LayananController::class, 'destroy']);

    Route::post('/kategori', [KategoriController::class, 'store']);
    Route::put('/kategori/{id}', [KategoriController::class, 'update']);
    Route::delete('/kategori/{id}', [KategoriController::class, 'destroy']);
    Route::put('/theme', [ThemeSettingController::class, 'update']);

    Route::middleware('role:Super Admin')->group(function () {
        Route::get('/logs', [LogActivityController::class, 'index']);
        Route::apiResource('users', UserController::class)->except(['show']);
    });
});
