<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        /*
        |--------------------------------------------------------------------------
        | Redirect autentikasi
        |--------------------------------------------------------------------------
        | Belum login  -> halaman login admin
        | Sudah login  -> dashboard admin
        */
        $middleware->redirectGuestsTo(
            fn (Request $request) => route('admin.login')
        );

        $middleware->redirectUsersTo(
            fn (Request $request) => route('admin.dashboard')
        );

        $middleware->web(append: [
            \App\Http\Middleware\TrackVisitor::class,
            \App\Http\Middleware\EnsureUserIsActive::class,
            \App\Http\Middleware\ExpireAdminSession::class,
        ]);

        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Handle: token tidak ada/salah (401)
        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json(['message' => 'Silakan login terlebih dahulu.'], 401);
            }
        });

        // Handle: data tidak ditemukan (404)
        $exceptions->render(function (\Illuminate\Database\Eloquent\ModelNotFoundException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json(['message' => 'Data yang dicari tidak ditemukan.'], 404);
            }
        });

        // Handle: validasi gagal (422) - format lebih rapi
        $exceptions->render(function (\Illuminate\Validation\ValidationException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => 'Validasi gagal, cek kembali data yang dikirim.',
                    'errors' => $e->errors(),
                ], 422);
            }
        });
    })->create();
