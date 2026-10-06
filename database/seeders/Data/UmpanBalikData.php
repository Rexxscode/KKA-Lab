<?php

namespace Database\Seeders\Data;

/**
 * Sampel catatan guru untuk siswa (tampil di rapor).
 */
class UmpanBalikData
{
    public static function daftar(): array
    {
        return [
            'Pertahankan! Pemahaman konsepmu kuat, tinggal jaga konsistensi di modul berikutnya.',
            'Bagus, tapi baca pembahasan soal yang salah ya — polanya sering keluar lagi.',
            'Coba kerjakan tanpa mencontek catatan dulu, biar tahu mana yang benar-benar paham.',
            'Progresmu cepat! Tantang dirimu kejar skor 100 di percobaan berikutnya.',
            'Jangan lewatkan bagian praktiknya, konsep tanpa coba langsung cepat lupa.',
            'Nilai menanjak terus. Fokus ke 2 soal terakhir yang masih salah.',
            'Sudah bagus! Ajak teman yang tertinggal belajar bareng, mengajar itu menguatkan ingatan.',
            'Pelan tapi pasti. Ulangi modul ini minggu depan agar tidak lupa.',
            'Hebat, lulus di percobaan pertama! Simpan energimu untuk modul Python.',
            'Perhatikan detail soal cerita — jawabanmu sering benar konsepnya tapi salah baca angka.',
        ];
    }
}
