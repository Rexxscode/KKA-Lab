<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    /**
     * Laravel memecah parameter middleware per koma menjadi argumen
     * terpisah (role:a,b -> 'a', 'b'), jadi terima sebagai variadic.
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $allowed = array_map('trim', $roles);
        $user = $request->user();
        // Visitor (belum login) diarahkan ke login dengan pesan yang jelas.
        if (! $user) {
            return redirect()
                ->guest(route('login'))
                ->with('toast', ['type' => 'error', 'message' => 'Silakan login terlebih dahulu untuk mengakses fitur pembelajaran.']);
        }
        if (! in_array($user->role, $allowed, true)) {
            abort(403, 'Akses ditolak untuk peran ini.');
        }

        return $next($request);
    }
}
