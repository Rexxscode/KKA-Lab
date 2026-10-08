<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->append(\App\Http\Middleware\SetSecurityHeaders::class);
        $middleware->alias([
            'role' => \App\Http\Middleware\EnsureRole::class,
            'guru.aktif' => \App\Http\Middleware\EnsureGuruAktif::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Visitor yang mencoba halaman auth diarahkan ke login dengan pesan jelas.
        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Silakan login terlebih dahulu untuk mengakses fitur pembelajaran.'], 401);
            }

            return redirect()
                ->guest(route('login'))
                ->with('toast', ['type' => 'error', 'message' => 'Silakan login terlebih dahulu untuk mengakses fitur pembelajaran.']);
        });
    })->create();
