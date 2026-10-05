<?php

namespace App\Services;

use App\Ai\Agents\TutorAgent;
use Laravel\Ai\Messages\AssistantMessage;
use Laravel\Ai\Messages\UserMessage;

class GeminiService
{
    public function configured(): bool
    {
        return (bool) config('services.gemini.key');
    }

    /**
     * Kirim chat ke Gemini via Laravel AI SDK sebagai tutor KKA yang ramah siswa.
     *
     * @param  array  $history  [['role' => 'user'|'model', 'text' => string]]
     */
    public function tutorChat(string $message, array $history = [], ?string $moduleContext = null): string
    {
        $key = config('services.gemini.key');
        $model = config('services.gemini.model', 'gemini-2.0-flash');

        if (! $key) {
            return 'Asisten AI belum dikonfigurasi. Minta gurumu mengisi GEMINI_API_KEY di file .env ya! (ambil gratis di AI Studio)';
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
            $response = (new TutorAgent($moduleContext))
                ->withMessages($messages)
                ->prompt(mb_substr($message, 0, 2000), provider: 'gemini', model: $model, timeout: 30);

            $text = trim((string) ($response->text ?? ''));

            if (! $text) {
                return 'Maaf, aku tidak mendapat jawaban. Coba tanya dengan kalimat lain ya!';
            }

            return $this->bersihkanMarkdown($text);
        } catch (\Throwable $e) {
            report($e);

            return 'Ups, koneksi ke AI gagal. Periksa internet lalu coba lagi!';
        }
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
