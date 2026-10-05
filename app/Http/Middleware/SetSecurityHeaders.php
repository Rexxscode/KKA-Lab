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
        $response->headers->set('Permissions-Policy', 'camera=(self), microphone=(), geolocation=()');
        $response->headers->set('Content-Security-Policy', implode('; ', [
            "default-src 'self'",
            // Inline script/style dipakai Blade + Alpine; CDN jsdelivr untuk Chart.js.
            "script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net http://localhost:*",
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com",
            'font-src \'self\' https://fonts.gstatic.com',
            'img-src \'self\' data: blob:',
            'connect-src \'self\' ws://localhost:* wss://localhost:*',
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
