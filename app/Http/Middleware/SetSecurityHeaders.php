<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetSecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');
        // Host CDN yang memang dipakai aplikasi (runtime Pyodide + Chart.js
        // via jsdelivr). Tanpa ini worker Python/grafik rapor mati total.
        // Kamera/deteksi sudah dihapus, jadi host bobot model TF
        // (tfhub/kaggle/storage) tidak lagi diizinkan.
        $cdn = 'https://cdn.jsdelivr.net';

        // Izin localhost (Vite HMR) hanya saat pengembangan lokal.
        $dev = app()->environment('local') || (bool) config('app.debug');
        $localhostScript = $dev ? ' http://localhost:*' : '';
        $localhostConnect = $dev ? ' ws://localhost:* wss://localhost:*' : '';

        $response->headers->set('Content-Security-Policy', implode('; ', [
            "default-src 'self'",
            // Inline script/style dipakai Blade + Alpine; CDN jsdelivr untuk Chart.js.
            // 'unsafe-eval' wajib: Alpine menilai ekspresi x-data/@click via AsyncFunction.
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net" . $localhostScript,
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com",
            'font-src \'self\' https://fonts.gstatic.com',
            'img-src \'self\' data: blob:',
            "connect-src 'self' " . $cdn . $localhostConnect,
            "worker-src 'self'",
            "frame-ancestors 'self'",
            "object-src 'none'",
            "base-uri 'self'",
        ]));

        // HSTS hanya saat sudah HTTPS agar HTTP lokal tidak rusak.
        if ($request->isSecure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        return $response;
    }
}
