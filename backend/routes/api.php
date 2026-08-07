<?php

use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AwardController;
use App\Http\Controllers\Api\BeritaController;
use App\Http\Controllers\Api\DokumenController;
use App\Http\Controllers\Api\KategoriController;
use App\Http\Controllers\Api\LayananController;
use App\Http\Controllers\Api\LogActivityController;
use App\Http\Controllers\Api\HeroSlideController;
use App\Http\Controllers\Api\PejabatController;
use App\Http\Controllers\Api\PodcastController;
use App\Http\Controllers\Api\SkmController;
use App\Http\Controllers\Api\ThemeSettingController;
use App\Http\Controllers\Api\UserController;
use App\Models\VisitorLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login'])
    ->middleware('throttle:5,1')
    ->name('api.login');

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

Route::get('/berita', [BeritaController::class, 'index']);
Route::get('/berita/{id}', [BeritaController::class, 'show']);
Route::get('/articles', [ArticleController::class, 'index']);
Route::get('/articles/{id}', [ArticleController::class, 'show']);
Route::get('/pejabat', [PejabatController::class, 'index']);
Route::get('/awards', [AwardController::class, 'index']);
Route::get('/dokumen', [DokumenController::class, 'index']);
Route::get('/dokumen-publik', [DokumenController::class, 'indexPublik']);
Route::get('/podcast', [PodcastController::class, 'index']);
Route::get('/layanan', [LayananController::class, 'index']);
Route::get('/kategori', [KategoriController::class, 'index']);
Route::get('/theme', [ThemeSettingController::class, 'index']);
Route::get('/hero-slides', [HeroSlideController::class, 'index']);
Route::post('/skm/store', [SkmController::class, 'store'])->middleware('throttle:10,1');
Route::get('/skm/stats', [SkmController::class, 'getStats']);

Route::middleware('auth:sanctum')->group(function () {
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
