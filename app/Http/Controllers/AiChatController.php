<?php

namespace App\Http\Controllers;

use App\Models\AiChat;
use App\Services\AiGagalException;
use App\Services\GeminiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AiChatController extends Controller
{
    public const KUOTA_HARIAN = 30;

    public function chat(Request $request, GeminiService $gemini)
    {
        $data = $request->validate([
            'message' => 'required|string|max:2000',
            'history' => 'nullable|array|max:20',
            'history.*.role' => 'nullable|in:user,model',
            'history.*.text' => 'nullable|string|max:2000',
            'module' => 'nullable|string|exists:modules,slug',
        ]);

        $user = $request->user();

        // Guru yang belum diverifikasi NPK tak boleh memakai AI.
        if ($user->isGuru() && $user->status !== 'aktif') {
            return response()->json(['message' => 'Akun gurumu masih menunggu verifikasi admin, AI belum bisa dipakai.'], 403);
        }

        // Reservasi kuota atomik: cek + catat di dalam lock agar request
        // paralel tak bisa lolos ganda melewati batas harian.
        $kunci = 'ai:kuota:' . $user->id . ':' . today()->toDateString();
        $lock = Cache::lock($kunci, 10);
        if (! $lock->get()) {
            return response()->json(['message' => 'Masih diproses, coba lagi sebentar!'], 429);
        }
        try {
            if (AiChat::where('user_id', $user->id)->whereDate('created_at', today())->count() >= self::KUOTA_HARIAN) {
                return response()->json(['message' => 'Kuota AI harian habis (30/hari). Coba lagi besok ya!'], 429);
            }
            $catatan = AiChat::create([
                'user_id' => $user->id,
                'module_slug' => $data['module'] ?? null,
                'message' => $data['message'],
                'reply' => '',
            ]);
        } finally {
            $lock->release();
        }

        try {
            $reply = $gemini->tutorChat(
                $data['message'],
                $data['history'] ?? [],
                $data['module'] ?? null,
                $user
            );
        } catch (AiGagalException $e) {
            // Gagal bukan jawaban: hapus reservasi agar tak tersimpan
            // sebagai riwayat dan tak memakan kuota harian.
            $catatan->delete();

            return response()->json(['reply' => $e->getMessage()]);
        }

        $catatan->update(['reply' => $reply]);

        return response()->json(['reply' => $reply]);
    }
}
