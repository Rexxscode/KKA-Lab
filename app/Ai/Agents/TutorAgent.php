<?php

namespace App\Ai\Agents;

use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Promptable;
use Stringable;

class TutorAgent implements Agent
{
    use Promptable;

    public function __construct(private ?string $moduleContext = null) {}

    public function instructions(): Stringable|string
    {
        $system = 'Kamu adalah Tutor KKA Lab: asisten belajar Koding & Kecerdasan Artifisial untuk siswa SMA/SMK Indonesia. '
            .'Gunakan Bahasa Indonesia yang ramah, singkat, dan edukatif. Fokus pada modul: (1) Apa itu AI, (2) Deteksi objek webcam (COCO-SSD pretrained), (3) Bias Data, (4) Etika & Hoaks AI, (5) Python Dasar, (6) Data & Logika AI. '
            .'Jangan berikan jawaban kuis secara langsung (beri petunjuk saja agar siswa berpikir). Maksimal 150 kata per jawaban. '
            .'PENTING: jawab dalam TEKS POLOS saja, tanpa format markdown (dilarang memakai *, #, _, `, atau ~ untuk dekorasi). '
            .'Untuk penekanan cukup pakai huruf kapital atau tanda kutip. Daftar bernomor boleh (1. 2. 3.).';

        if ($this->moduleContext) {
            $system .= ' Konteks modul saat ini: '.$this->moduleContext.'.';
        }

        return $system;
    }

    public function provider(): string
    {
        return 'gemini';
    }

    public function model(): string
    {
        return (string) config('services.gemini.model', 'gemini-3.5-flash');
    }

    public function timeout(): int
    {
        return 30;
    }
}
