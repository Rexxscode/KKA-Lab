<?php

namespace Database\Seeders;

use App\Models\Badge;
use App\Models\Module;
use App\Models\Quiz;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@kkalab.sch.id'],
            ['name' => 'Admin KKA Lab', 'password' => Hash::make('password'), 'role' => 'admin']
        );

        $guru = User::firstOrCreate(
            ['email' => 'guru@kkalab.sch.id'],
            ['name' => 'Bu Marissa (Guru)', 'password' => Hash::make('password'), 'role' => 'guru']
        );

        $siswa = User::firstOrCreate(
            ['email' => 'siswa@kkalab.sch.id'],
            ['name' => 'Siswa Demo', 'password' => Hash::make('password'), 'role' => 'siswa']
        );

        $modules = [
            ['title' => 'Modul 1: Apa itu AI?', 'slug' => 'apa-itu-ai', 'order' => 1, 'xp_reward' => 100,
             'description' => 'Konsep dasar AI, machine learning, dan contoh penerapan di kehidupan sehari-hari.'],
            ['title' => 'Modul 2: Latih AI-mu', 'slug' => 'latih-ai-mu', 'order' => 2, 'xp_reward' => 200,
             'description' => 'Hands-on melatih model klasifikasi gambar langsung di browser dengan webcam (KNN + MobileNet, privasi terjaga).'],
            ['title' => 'Modul 3: Bias Data', 'slug' => 'bias-data', 'order' => 3, 'xp_reward' => 150,
             'description' => 'Simulasi interaktif dataset tidak seimbang dan dampaknya pada keadilan model.'],
            ['title' => 'Modul 4: Etika & Hoaks AI', 'slug' => 'etika-hoaks-ai', 'order' => 4, 'xp_reward' => 150,
             'description' => 'Studi kasus etika AI, deepfake, dan cara mengenali hoaks hasil AI.'],
        ];

        foreach ($modules as $m) {
            $module = Module::firstOrCreate(['slug' => $m['slug']], $m);
            Badge::firstOrCreate(
                ['module_id' => $module->id],
                ['name' => 'Badge ' . $module->title, 'icon_path' => null]
            );
        }

        $quizzes = [
            'apa-itu-ai' => [
                ['q' => 'Apa kepanjangan AI?', 'o' => ['Artificial Intelligence', 'Automated Internet', 'Anti Virus', 'Analog Input'], 'c' => 0],
                ['q' => 'Contoh penerapan AI di sekolah?', 'o' => ['Deteksi plagiarisme & chatbot belajar', 'Menghapus papan tulis otomatis', 'Mengganti guru sepenuhnya', 'Mematikan listrik'], 'c' => 0],
                ['q' => 'Machine Learning adalah...', 'o' => ['Komputer belajar dari data', 'Komputer dirakit manual', 'Belajar mengetik cepat', 'Mesin cuci pintar'], 'c' => 0],
                ['q' => 'Data yang baik untuk AI harus...', 'o' => ['Banyak, beragam, dan berlabel benar', 'Sedikit dan rahasia', 'Acak tanpa tujuan', 'Hanya satu contoh'], 'c' => 0],
                ['q' => 'AI yang baik bersifat...', 'o' => ['Membantu manusia, bukan menggantikan nilai manusia', 'Selalu benar 100%', 'Bebas tanpa aturan', 'Rahasia tanpa bisa dijelaskan'], 'c' => 0],
            ],
            'latih-ai-mu' => [
                ['q' => 'Langkah pertama melatih model gambar?', 'o' => ['Kumpulkan contoh tiap kelas', 'Langsung uji tanpa data', 'Hapus webcam', 'Matikan browser'], 'c' => 0],
                ['q' => 'Kenapa butuh banyak contoh per kelas?', 'o' => ['Agar model mengenali variasi', 'Agar baterai cepat habis', 'Agar file besar', 'Tidak ada alasan'], 'c' => 0],
                ['q' => 'Data webcam di KKA Lab...', 'o' => ['Hanya diproses di browser, tidak dikirim ke server', 'Dikirim ke semua guru', 'Diunggah ke internet publik', 'Disimpan permanen'], 'c' => 0],
                ['q' => 'Jika model salah menebak, yang dilakukan?', 'o' => ['Tambah contoh & latih ulang', 'Buang laptop', 'Menyerah', 'Tutup mata'], 'c' => 0],
                ['q' => 'KNN Classifier bekerja dengan...', 'o' => ['Membandingkan kemiripan fitur gambar', 'Menebak acak', 'Menghafal password', 'Menghitung nilai rapor'], 'c' => 0],
            ],
            'bias-data' => [
                ['q' => 'Bias data artinya...', 'o' => ['Dataset tidak mewakili semua kelompok', 'Data terlalu besar', 'Data terlalu cepat', 'Komputer rusak'], 'c' => 0],
                ['q' => 'Dampak dataset tidak seimbang?', 'o' => ['Model akurat di satu kelompok, buruk di lainnya', 'Model jadi lebih cepat', 'Tidak ada dampak', 'Komputer meledak'], 'c' => 0],
                ['q' => 'Cara mengurangi bias?', 'o' => ['Tambah data kelompok yang kurang & evaluasi adil', 'Hapus semua data', 'Latih sekali saja', 'Sembunyikan hasil'], 'c' => 0],
                ['q' => 'Contoh bias dalam rekrutmen AI?', 'o' => ['Model menolak kelompok tertentu karena data historis timpang', 'Model terlalu ramah', 'Model suka bercanda', 'Model meminta gaji'], 'c' => 0],
                ['q' => 'Fairness dalam AI berarti...', 'o' => ['Hasil adil untuk semua kelompok', 'Semua jawaban sama persis', 'Gratis untuk semua', 'Tanpa aturan'], 'c' => 0],
            ],
            'etika-hoaks-ai' => [
                ['q' => 'Deepfake adalah...', 'o' => ['Konten palsu hasil AI yang terlihat asli', 'Kopi deep', 'Ikan dalam', 'Tugas dalam'], 'c' => 0],
                ['q' => 'Ciri hoaks hasil AI?', 'o' => ['Tangan/jari aneh, teks gibberish, sumber tidak jelas', 'Selalu ada logo resmi', 'Pasti dari TV', 'Tidak bisa dikenali'], 'c' => 0],
                ['q' => 'Sebelum menyebar info, kita harus...', 'o' => ['Cek sumber, tanggal, dan verifikasi silang', 'Langsung share ke semua grup', 'Percaya judul saja', 'Abaikan'], 'c' => 0],
                ['q' => 'Penggunaan AI yang etis...', 'o' => ['Izin, transparan, dan bertanggung jawab', 'Sembunyi-sembunyi', 'Menipu orang', 'Melanggar privasi'], 'c' => 0],
                ['q' => 'Jika menemukan konten AI yang menipu?', 'o' => ['Laporkan & edukasi orang lain', 'Ikut menyebarkan', 'Diam saja', 'Tertawa saja'], 'c' => 0],
            ],
        ];

        foreach ($quizzes as $slug => $items) {
            $module = Module::where('slug', $slug)->first();
            foreach ($items as $item) {
                Quiz::firstOrCreate(
                    ['module_id' => $module->id, 'question' => $item['q']],
                    ['options' => $item['o'], 'correct_index' => $item['c'], 'points' => 20]
                );
            }
        }
    }
}
