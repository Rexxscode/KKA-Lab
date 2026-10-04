<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Guru berstatus pending boleh masuk (dasbor) tetapi semua fitur
 * guru (kelas, soal, modul, ekspor) dikunci sampai admin menyetujui.
 */
class EnsureGuruAktif
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->isGuru() && $user->status !== 'aktif') {
            return redirect()
                ->route('guru.dashboard')
                ->with('toast', ['type' => 'error', 'message' => '⏳ Akun gurumu masih menunggu verifikasi NPK oleh admin. Fitur ini terkunci untuk sementara.']);
        }

        return $next($request);
    }
}
