<?php

namespace Database\Seeders\Data;

/**
 * Sampel tanya-jawab Tutor AI per modul (Bahasa Indonesia).
 * Struktur: module_slug (null = umum), message, reply.
 */
class ObrolanAiData
{
    public static function daftar(): array
    {
        return [
            ['module' => 'apa-itu-ai', 'message' => 'Apa bedanya AI dengan program biasa?', 'reply' => 'Program biasa ikut aturan tetap yang ditulis programmer, sedangkan AI menemukan polanya sendiri dari data. Contoh: kalkulator bukan AI, tapi kamera HP yang fokus ke wajah adalah AI.'],
            ['module' => 'apa-itu-ai', 'message' => 'Apakah ChatGPT termasuk AI sempit?', 'reply' => 'Ya! Walaupun terasa pintar, ChatGPT hanya ahli di satu bidang: bahasa. Ia tidak bisa menyetir atau memasak. Semua AI saat ini adalah narrow AI.'],
            ['module' => 'latih-ai-mu', 'message' => 'Kenapa kotak deteksiku bergoyang terus?', 'reply' => 'Biasanya karena cahaya kurang atau ambang confidence terlalu rendah. Coba dekatkan benda ke kamera, perbaiki cahaya, lalu naikkan ambang ke 60-70%.'],
            ['module' => 'latih-ai-mu', 'message' => 'Apakah foto webcamku dikirim ke server?', 'reply' => 'Tidak! Model COCO-SSD berjalan 100% di browser-mu. Frame kamera tidak pernah diunggah ke mana pun.'],
            ['module' => 'latih-ai-mu', 'message' => 'Bisa tidak model mengenali botol bekalku yang unik?', 'reply' => 'Tidak dengan model pretrained — ia hanya tahu 80 kelas COCO. Untuk benda custom, model harus dilatih ulang dengan foto-foto benda itu.'],
            ['module' => 'bias-data', 'message' => 'Contoh bias data di dunia nyata?', 'reply' => 'Sistem absensi wajah yang gagal mengenali sebagian siswa karena data latihnya minim variasi wajah. Akibat klasik dataset timpang.'],
            ['module' => 'bias-data', 'message' => 'Gimana cara tahu dataset-ku adil?', 'reply' => 'Uji model per kelompok, bukan cuma rata-rata total. Kalau akurasi kelompok kecil jauh lebih rendah, dataset-mu timpang.'],
            ['module' => 'etika-hoaks-ai', 'message' => 'Gimana cara cepat cek foto viral itu asli atau AI?', 'reply' => 'Ingat rumus CHECK: Cek sumber, Hindari judul bombastis, Eja tanggal, Cek gambar (tangan/jari/teks aneh), Kroscek ke media tepercaya.'],
            ['module' => 'etika-hoaks-ai', 'message' => 'Bolehkah aku pakai foto teman untuk eksperimen AI?', 'reply' => 'Hanya dengan izin! Foto adalah data pribadi. Tanpa izin berarti melanggar privasi, walau untuk tugas sekolah.'],
            ['module' => 'python-dasar-ai', 'message' => 'Bedanya print dan return?', 'reply' => 'print hanya menampilkan ke layar, sedangkan return mengirim nilai kembali ke pemanggil agar bisa dipakai kode lain.'],
            ['module' => 'python-dasar-ai', 'message' => 'Kenapa list mulai dari indeks 0?', 'reply' => 'Itu konvensi dari bahasa C: indeks = jarak dari awal memori. Jadi buah[1] adalah unsur kedua. Memang membingungkan di awal, tapi lama-lama terbiasa!'],
            ['module' => 'data-logika-ai', 'message' => 'Kapan aturan manual harus diganti machine learning?', 'reply' => 'Saat polanya terlalu rumit ditulis tangan tetapi contohnya melimpah — misal mengenali wajah. Itulah jembatan dari Modul 6 ke Modul 2.'],
            ['module' => null, 'message' => 'Kasih tips belajar biar cepat paham AI dong!', 'reply' => 'Urutanku: 1) baca bahan sampai akhir, 2) coba praktiknya langsung, 3) kerjakan kuis, 4) ulangi yang skornya di bawah 90. Konsisten 30 menit sehari mengalahkan begadang semalam!'],
        ];
    }
}
