<?php

namespace App\Http\Controllers;

use App\Models\AiChat;
use App\Services\AiGagalException;
use App\Services\GeminiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

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

        // Kill-switch darurat: matikan chatbot tanpa deploy ulang
        // saat limit Google jebol (AI_CHAT_ENABLED=false di .env prod).
        if (! filter_var(env('AI_CHAT_ENABLED', true), FILTER_VALIDATE_BOOLEAN)) {
            return response()->json(['message' => 'Asisten AI lagi dimatikan sementara. Coba lagi nanti ya!'], 503);
        }

        $user = $request->user();

        // Guru yang belum diverifikasi NPK tak boleh memakai AI.
        if ($user->isGuru() && $user->status !== 'aktif') {
            return response()->json(['message' => 'Akun gurumu masih menunggu verifikasi admin, AI belum bisa dipakai.'], 403);
        }

        // Cooldown antar chat per user (default 10 detik) agar tombol
        // tak bisa dispam. Key gratisan Google mati kalau diburst.
        $jedaDetik = max(0, (int) env('AI_COOLDOWN_DETIK', 10));
        $kunciCd = 'ai:cd:'.$user->id;
        if ($jedaDetik > 0 && Cache::has($kunciCd)) {
            return response()->json([
                'message' => "Tunggu {$jedaDetik} detik sebelum tanya lagi ya!",
                'code' => 'AI_COOLDOWN',
                'retry_after' => $jedaDetik,
            ], 429);
        }

        // Budget global harian (default 500 request/hari seluruh user).
        // Melindungi key gratisan saat banyak siswa online bareng.
        $batasGlobal = max(1, (int) env('AI_GLOBAL_HARIAN', 500));
        $kunciGlobal = 'ai:global:'.today()->toDateString();
        Cache::add($kunciGlobal, 0, now()->endOfDay());
        $totalGlobal = (int) Cache::increment($kunciGlobal);
        if ($totalGlobal === 1) {
            Cache::put($kunciGlobal, 1, now()->endOfDay());
            $totalGlobal = 1;
        }
        if ($totalGlobal > $batasGlobal) {
            Cache::decrement($kunciGlobal);

            return response()->json([
                'message' => 'Asisten AI lagi ramai banget hari ini. Coba lagi besok ya!',
                'code' => 'AI_GLOBAL_HABIS',
            ], 429);
        }
        if ($totalGlobal >= (int) ceil($batasGlobal * 0.8)) {
            Log::warning('ai.global.mendekati-batas', [
                'total' => $totalGlobal, 'batas' => $batasGlobal,
            ]);
        }

        // Reservasi kuota atomik: cek + catat di dalam lock agar request
        // paralel tak bisa lolos ganda melewati batas harian.
        $kunci = 'ai:kuota:' . $user->id . ':' . today()->toDateString();
        $lock = Cache::lock($kunci, 10);
        if (! $lock->get()) {
            Cache::decrement($kunciGlobal);

            return response()->json(['message' => 'Masih diproses, coba lagi sebentar!'], 429);
        }
        try {
            if (AiChat::where('user_id', $user->id)->whereDate('created_at', today())->count() >= self::KUOTA_HARIAN) {
                Cache::decrement($kunciGlobal);

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

        // Tandai cooldown setelah lolos semua penjagaan.
        if ($jedaDetik > 0) {
            Cache::put($kunciCd, true, now()->addSeconds($jedaDetik));
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
            // Sertakan kode mesin (AI_KEY_INVALID, AI_MODEL_404, ...) agar
            // frontend bisa menampilkan bantuan yang tepat.
            $catatan->delete();
            // Tanpa key = tanpa panggilan Google, kembalikan budget global.
            if ($e->aiCode === 'AI_BELUM_KONFIGURASI') {
                Cache::decrement($kunciGlobal);
            }

            return response()->json(['reply' => $e->getMessage(), 'code' => $e->aiCode]);
        }

        $catatan->update(['reply' => $reply]);

        return response()->json(['reply' => $reply]);
    }
}
