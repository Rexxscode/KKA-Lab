<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GeminiService
{
    public function configured(): bool
    {
        return (bool) config('services.gemini.key');
    }

    /**
     * Kirim chat ke Gemini sebagai tutor KKA yang ramah siswa.
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

        $system = 'Kamu adalah Tutor KKA Lab: asisten belajar Koding & Kecerdasan Artifisial untuk siswa SMA/SMK Indonesia. '
            . 'Gunakan Bahasa Indonesia yang ramah, singkat, dan edukatif. Fokus pada modul: (1) Apa itu AI, (2) Deteksi objek webcam (COCO-SSD pretrained), (3) Bias Data, (4) Etika & Hoaks AI, (5) Python Dasar, (6) Data & Logika AI. '
            . 'Jangan berikan jawaban kuis secara langsung (beri petunjuk saja agar siswa berpikir). Maksimal 150 kata per jawaban. '
            . 'PENTING: jawab dalam TEKS POLOS saja, tanpa format markdown (dilarang memakai *, #, _, `, atau ~ untuk dekorasi). '
            . 'Untuk penekanan cukup pakai huruf kapital atau tanda kutip. Daftar bernomor boleh (1. 2. 3.).';

        if ($moduleContext) {
            $system .= ' Konteks modul saat ini: ' . $moduleContext . '.';
        }

        $contents = [];
        foreach (array_slice($history, -8) as $h) {
            $contents[] = [
                'role' => ($h['role'] ?? 'user') === 'model' ? 'model' : 'user',
                'parts' => [['text' => mb_substr($h['text'] ?? '', 0, 2000)]],
            ];
        }
        $contents[] = ['role' => 'user', 'parts' => [['text' => mb_substr($message, 0, 2000)]]];

        try {
            // asJson() wajib: tanpanya Laravel mengirim form-encoded
            // sehingga API Gemini menolak dengan 400.
            $res = Http::timeout(30)
                ->withHeaders(['x-goog-api-key' => $key])
                ->asJson()
                ->post(
                    "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent",
                    [
                        'system_instruction' => ['parts' => [['text' => $system]]],
                        'contents' => $contents,
                        'generationConfig' => ['temperature' => 0.7, 'maxOutputTokens' => 500],
                    ]
                );

            if (! $res->successful()) {
                return 'Maaf, AI sedang sibuk (kode ' . $res->status() . '). Coba lagi sebentar ya!';
            }

            $text = $res->json('candidates.0.content.parts.0.text');

            if (! $text) {
                return 'Maaf, aku tidak mendapat jawaban. Coba tanya dengan kalimat lain ya!';
            }

            return $this->bersihkanMarkdown(trim($text));
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
