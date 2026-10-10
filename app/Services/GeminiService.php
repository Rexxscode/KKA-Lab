<?php

namespace App\Services;

use App\Ai\Agents\TutorAgent;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Laravel\Ai\Messages\AssistantMessage;
use Laravel\Ai\Messages\UserMessage;

class GeminiService
{
    public function configured(): bool
    {
        return (bool) config('services.gemini.key');
    }

    /**
     * Kirim chat ke Gemini via Laravel AI SDK sebagai tutor KobotAI yang ramah siswa.
     *
     * Melempar {@see AiGagalException} bila AI tak bisa menjawab (belum
     * dikonfigurasi, respons kosong, atau jaringan gagal) — pemanggil
     * wajib menampilkannya TANPA menyimpan ke riwayat/kuota.
     *
     * @param  array  $history  [['role' => 'user'|'model', 'text' => string]]
     *
     * @throws AiGagalException
     */
    public function tutorChat(string $message, array $history = [], ?string $moduleContext = null, ?User $user = null): string
    {
        $key = config('services.gemini.key');
        $model = config('services.gemini.model', 'gemini-3.8-flash');

        if (! $key) {
            throw new AiGagalException('Asisten AI belum dikonfigurasi. Minta gurumu mengisi GEMINI_API_KEY di file .env ya! (ambil gratis di AI Studio)', 'AI_BELUM_KONFIGURASI');
        }

        $messages = [];
        foreach (array_slice($history, -8) as $h) {
            $text = mb_substr($h['text'] ?? '', 0, 2000);
            if (($h['role'] ?? 'user') === 'model') {
                $messages[] = new AssistantMessage($text);
            } else {
                $messages[] = new UserMessage($text);
            }
        }

        try {
            $response = (new TutorAgent(user: $user, moduleContext: $moduleContext))
                ->withMessages($messages)
                ->prompt(mb_substr($message, 0, 2000), provider: 'gemini', model: $model, timeout: 30);

            $text = trim((string) ($response->text ?? ''));

            if (! $text) {
                throw new AiGagalException('Maaf, aku tidak mendapat jawaban. Coba tanya dengan kalimat lain ya!', 'AI_KOSONG');
            }

            return $this->bersihkanMarkdown($text);
        } catch (AiGagalException $e) {
            throw $e;
        } catch (\Throwable $e) {
            report($e);

            [$pesan, $kode] = $this->petakanGalat($e, $model);
            Log::warning('ai.gagal', ['model' => $model, 'kode' => $kode, 'error' => mb_substr($e->getMessage(), 0, 500)]);

            throw new AiGagalException($pesan, $kode);
        }
    }

    /**
     * Petakan galat mentah Google/SDK jadi pesan ramah + kode mesin.
     * Pesan ramah tampil di chat, kode dipakai frontend + log.
     */
    private function petakanGalat(\Throwable $e, string $model): array
    {
        $mentah = strtolower($e->getMessage().' '.get_class($e));
        // Sertakan body respons HTTP bila SDK menyimpannya.
        try {
            if (method_exists($e, 'getResponse') && $e->getResponse()) {
                $mentah .= ' '.strtolower((string) $e->getResponse()->getBody());
            }
        } catch (\Throwable) {
        }
        if (method_exists($e, 'getPrevious') && $e->getPrevious()) {
            $mentah .= ' '.strtolower($e->getPrevious()->getMessage());
        }

        $mengandung = fn (...$kata) => collect($kata)->contains(fn ($k) => str_contains($mentah, $k));

        if ($mengandung('401', '403', 'unauthenticated', 'api_key_invalid', 'access_token_type_unsupported', 'permission_denied', 'api key not valid')) {
            return ['Kunci API Gemini ditolak Google (401/403). Minta guru/admin membuat Standard key baru diawali AIza di AI Studio lalu isi ke GEMINI_API_KEY.', 'AI_KEY_INVALID'];
        }
        if ($mengandung('404', 'not_found', 'not found', 'is not found', 'unknown model')) {
            return ["Model {$model} tidak dikenal / tidak ada akses. Cek penulisan GEMINI_MODEL di .env (contoh: gemini-3.8-flash).", 'AI_MODEL_404'];
        }
        if ($mengandung('429', 'resource_exhausted', 'quota', 'rate limit', 'too many requests')) {
            return ['Limit Google Gemini habis. Tunggu sebentar lalu coba lagi ya!', 'AI_KUOTA_GOOGLE'];
        }
        if ($mengandung('timeout', 'timed out', 'curl error 28', 'connection timed out', 'could not resolve', 'failed to connect', 'network')) {
            return ['Ups, koneksi ke AI gagal. Periksa internet lalu coba lagi!', 'AI_TIMEOUT'];
        }

        return ['Ups, koneksi ke AI gagal. Periksa internet lalu coba lagi!', 'AI_NETWORK'];
    }

    /**
     * Buang sisa format markdown agar tampil bersih di chat teks polos.
     */
    private function bersihkanMarkdown(string $text): string
    {
        // ```blok kode``` -> isi saja
        $text = preg_replace('/```(\w*)\n?(.*?)```/s', '$2', $text);
        // **tebal** dan __tebal__ -> polos
        $text = preg_replace('/\*\*(.*?)\*\*/s', '$1', $text);
        $text = preg_replace('/__(.*?)__/s', '$1', $text);
        // `kode` -> polos
        $text = preg_replace('/`([^`]*)`/', '$1', $text);
        // # Judul -> polos
        $text = preg_replace('/^#{1,6}\s*/m', '', $text);
        // Sisa * / _ / ~ yang menggantung
        $text = str_replace(['**', '__', '~~'], '', $text);
        // Rapikan baris kosong berlebih
        $text = preg_replace("/\n{3,}/", "\n\n", trim($text));

        return $text;
    }
}
