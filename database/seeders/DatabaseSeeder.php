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
             'description' => 'Kumpulkan contoh Kelas A & B dari webcam, latih model KNN lokal di browser, lalu uji real-time.'],
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
                ['q' => 'Apa kepanjangan AI?', 'o' => ['Artificial Intelligence', 'Automated Internet', 'Anti Virus', 'Analog Input'], 'c' => 0, 'e' => 'AI adalah Artificial Intelligence: sistem komputer yang meniru kecerdasan manusia.'],
                ['q' => 'Contoh penerapan AI di sekolah?', 'o' => ['Deteksi plagiarisme & chatbot belajar', 'Menghapus papan tulis otomatis', 'Mengganti guru sepenuhnya', 'Mematikan listrik'], 'c' => 0, 'e' => 'Deteksi plagiarisme dan chatbot belajar memakai AI; guru tetap berperan membimbing.'],
                ['q' => 'Machine Learning adalah...', 'o' => ['Komputer belajar dari data', 'Komputer dirakit manual', 'Belajar mengetik cepat', 'Mesin cuci pintar'], 'c' => 0, 'e' => 'Machine Learning = komputer menemukan pola dari data, bukan dari aturan yang diketik manual.'],
                ['q' => 'Data yang baik untuk AI harus...', 'o' => ['Banyak, beragam, dan berlabel benar', 'Sedikit dan rahasia', 'Acak tanpa tujuan', 'Hanya satu contoh'], 'c' => 0, 'e' => 'AI belajar dari contoh: makin banyak, beragam, dan benar labelnya, makin pintar modelnya.'],
                ['q' => 'AI yang baik bersifat...', 'o' => ['Membantu manusia, bukan menggantikan nilai manusia', 'Selalu benar 100%', 'Bebas tanpa aturan', 'Rahasia tanpa bisa dijelaskan'], 'c' => 0, 'e' => 'AI adalah alat bantu; keputusan penting tetap di tangan manusia.'],
                ['q' => 'Mana yang BUKAN contoh AI?', 'o' => ['Rekomendasi film', 'Filter spam email', 'Kalkulator biasa', 'Pengenalan wajah HP'], 'c' => 2, 'e' => 'Kalkulator hanya menghitung dengan aturan tetap dan tidak belajar dari data, jadi bukan AI.'],
                ['q' => 'Apa beda utama program biasa dan AI?', 'o' => ['Program biasa lebih mahal', 'Program biasa ikut aturan tetap, AI belajar pola dari data', 'AI tidak butuh listrik', 'Tidak ada bedanya'], 'c' => 1, 'e' => 'Program biasa menjalankan aturan yang ditulis programmer; AI menemukan aturannya sendiri dari data.'],
                ['q' => 'Istilah "model" dalam AI artinya...', 'o' => ['Miniatur robot', 'Hasil latihan AI yang bisa menebak data baru', 'Nama programmer', 'Jenis kabel'], 'c' => 1, 'e' => 'Model adalah "otak" hasil latihan yang dipakai untuk menebak data yang belum pernah dilihat.'],
                ['q' => 'Apa itu chatbot?', 'o' => ['Robot yang bisa berjalan', 'Jenis virus', 'Program yang bercakap dengan manusia memakai bahasa', 'Aplikasi kalkulator'], 'c' => 2, 'e' => 'Chatbot memahami dan membalas bahasa manusia, misalnya asisten belajar atau layanan pelanggan.'],
                ['q' => 'Manakah contoh AI sempit (narrow AI)?', 'o' => ['Robot yang bisa melakukan semua pekerjaan manusia', 'Komputer yang punya perasaan', 'Kamera HP yang mengenali wajah', 'AI yang sadar diri'], 'c' => 2, 'e' => 'AI masa kini adalah narrow AI: hebat di satu tugas spesifik seperti pengenalan wajah.'],
                ['q' => 'Apa peran manusia dalam AI yang bertanggung jawab?', 'o' => ['Tidak perlu sama sekali', 'Mengawasi, mengoreksi, dan memutuskan akhir', 'Hanya menonton', 'Menyerahkan semua keputusan ke AI'], 'c' => 1, 'e' => 'Manusia wajib mengawasi dan memegang keputusan akhir, terutama untuk hal penting.'],
                ['q' => 'Data berlabel artinya...', 'o' => ['Data rahasia', 'Data yang sudah diberi keterangan benar, mis. foto + nama "kucing"', 'Data tanpa isi', 'Data yang dihapus'], 'c' => 1, 'e' => 'Label adalah "kunci jawaban" saat latihan, misalnya foto kucing berlabel kucing.'],
                ['q' => 'AI bisa salah karena...', 'o' => ['Datanya sedikit, bias, atau salah label', 'AI tidak pernah salah', 'Karena listrik padam saja', 'Karena hari libur'], 'c' => 0, 'e' => 'Kualitas AI = kualitas datanya. Data sedikit, timpang, atau salah label membuat AI salah.'],
                ['q' => 'Manfaat AI yang sehat untuk belajar?', 'o' => ['Membantu menjelaskan materi & latihan soal', 'Mengerjakan semua tugas tanpa belajar', 'Mengganti ujian', 'Mencontek saat ulangan'], 'c' => 0, 'e' => 'Gunakan AI untuk memahami materi, bukan untuk menghindari belajar.'],
                ['q' => 'Asisten suara di HP (Siri/Asisten Google) termasuk...', 'o' => ['Bukan AI', 'Robot fisik', 'AI umum setara manusia', 'AI sempit yang pintar di tugas suara'], 'c' => 3, 'e' => 'Asisten suara adalah narrow AI: hebat mengenali suara dan perintah, tapi tidak punya kecerdasan umum.'],
            ],
            'latih-ai-mu' => [
                ['q' => 'Langkah pertama melatih model gambar?', 'o' => ['Kumpulkan contoh tiap benda', 'Langsung uji tanpa data', 'Hapus webcam', 'Matikan browser'], 'c' => 0, 'e' => 'Tanpa contoh, model tidak punya apa pun untuk dipelajari. Kumpulkan dulu, baru latih.'],
                ['q' => 'Kenapa butuh banyak contoh per benda?', 'o' => ['Agar baterai cepat habis', 'Agar model mengenali benda dalam berbagai variasi', 'Agar file besar', 'Tidak ada alasan'], 'c' => 1, 'e' => 'Banyak contoh dari sudut, jarak, dan cahaya berbeda membuat model tahan terhadap variasi.'],
                ['q' => 'Data webcam di KKA Lab...', 'o' => ['Dikirim ke semua guru', 'Diunggah ke internet publik', 'Hanya diproses di browser, tidak dikirim ke server', 'Disimpan permanen'], 'c' => 2, 'e' => 'Privasi terjaga: frame kamera diolah lokal di perangkatmu dan tidak pernah diunggah.'],
                ['q' => 'Jika model salah menebak, yang dilakukan?', 'o' => ['Buang laptop', 'Menyerah', 'Tambah contoh & latih ulang', 'Tutup mata'], 'c' => 2, 'e' => 'Model yang salah = sinyal butuh data lebih baik. Tambah contoh yang mewakili, lalu latih ulang.'],
                ['q' => 'KNN Classifier bekerja dengan...', 'o' => ['Menebak acak', 'Menghafal password', 'Membandingkan kemiripan fitur gambar', 'Menghitung nilai rapor'], 'c' => 2, 'e' => 'KNN menyimpan ciri tiap contoh, lalu menebak gambar baru dengan contoh latih yang paling mirip.'],
                ['q' => 'Urutan tahap machine learning yang benar?', 'o' => ['Langsung pakai tanpa uji', 'Kumpulkan data → latih → uji → perbaiki', 'Uji dulu baru kumpulkan data', 'Asal-asalan dulu'], 'c' => 1, 'e' => 'Alur baku ML: kumpulkan data, latih model, uji dengan data baru, lalu perbaiki yang kurang.'],
                ['q' => 'Apa itu "benda" (kelas) saat melatih model?', 'o' => ['Ukuran foto', 'Kategori yang ingin dikenali, mis. Botol vs Buku', 'Jenis kamera', 'Kelas di sekolah'], 'c' => 1, 'e' => 'Kelas adalah kategori tebakan. Di Modul 2 kamu bebas menamai dua bendamu sendiri.'],
                ['q' => 'Apa fungsi data uji (test data)?', 'o' => ['Mempercantik tampilan', 'Menghapus data latih', 'Menambah memori', 'Mengukur apakah model bisa menebak data yang belum pernah dilihat'], 'c' => 3, 'e' => 'Data uji tidak ikut latihan, jadi ia mengukur kemampuan model sesungguhnya pada hal baru.'],
                ['q' => 'Overfitting artinya...', 'o' => ['Model hafal data latih tapi gagal di data baru', 'Model berjalan sangat cepat', 'Model gratis', 'Model offline'], 'c' => 0, 'e' => 'Seperti murid yang hafal soal tapi tidak paham konsep: nilai latihan bagus, nilai ujian jatuh.'],
                ['q' => 'Apa itu transfer learning (MobileNet di Modul 2)?', 'o' => ['Memakai model yang sudah pintar sebagai titik awal lalu diadaptasi', 'Memindahkan file antar laptop', 'Belajar sambil transfer uang', 'Mengganti model tiap hari'], 'c' => 0, 'e' => 'MobileNet sudah dilatih jutaan gambar; KNN tinggal memakai cirinya untuk bendamu. Hemat waktu dan data.'],
                ['q' => 'KNN menentukan jawaban dengan cara...', 'o' => ['Bertanya ke guru', 'Mencari contoh latih yang paling mirip lalu ikut kategorinya', 'Menebak acak', 'Menghitung rata-rata rapor'], 'c' => 1, 'e' => 'KNN = tetangga terdekat: gambar baru dikategorikan mengikuti contoh latih yang paling mirip cirinya.'],
                ['q' => 'Jika akurasi model hanya 60%, langkah terbaik?', 'o' => ['Hapus aplikasinya', 'Kurangi contohnya', 'Biarkan saja', 'Tambah contoh yang beragam dan latih ulang'], 'c' => 3, 'e' => 'Akurasi rendah hampir selalu soal data: tambah contoh bervariasi, seimbangkan tiap benda, latih ulang.'],
                ['q' => 'Apa itu label yang salah (mislabel)?', 'o' => ['Foto yang gelap', 'Foto yang besar', 'Foto kucing diberi label "anjing"', 'Foto tanpa warna'], 'c' => 2, 'e' => 'Label salah mengajari model hal yang salah. Cek ulang label saat model bertingkah aneh.'],
                ['q' => 'Contoh tiap benda harus bervariasi (jarak, cahaya, sudut) karena...', 'o' => ['Agar file foto besar', 'Agar kamera cepat rusak', 'Tidak penting', 'Agar model kenal bendanya dalam kondisi apa pun'], 'c' => 3, 'e' => 'Model hanya sepintar variasinya. Foto itu-itu saja = gagal saat kondisi berubah.'],
                ['q' => 'Mengapa jumlah contoh tiap benda sebaiknya seimbang?', 'o' => ['Agar adil, model tidak berat sebelah ke benda yang contohnya banyak', 'Agar hemat baterai', 'Agar cepat selesai saja', 'Tidak ada alasan'], 'c' => 0, 'e' => 'Kelas mayoritas mendominasi tebakan KNN. Ketidakseimbangan = bibit bias data (lihat Modul 3).'],
            ],
            'bias-data' => [
                ['q' => 'Bias data artinya...', 'o' => ['Dataset tidak mewakili semua kelompok', 'Data terlalu besar', 'Data terlalu cepat', 'Komputer rusak'], 'c' => 0, 'e' => 'Bias = kemiringan. Dataset bias membuat model hanya pintar untuk kelompok tertentu.'],
                ['q' => 'Dampak dataset tidak seimbang?', 'o' => ['Model akurat di satu kelompok, buruk di lainnya', 'Model jadi lebih cepat', 'Tidak ada dampak', 'Komputer meledak'], 'c' => 0, 'e' => 'Mayoritas data mendominasi latihan, sehingga kelompok minoritas dikenali buruk.'],
                ['q' => 'Cara mengurangi bias?', 'o' => ['Tambah data kelompok yang kurang & evaluasi adil', 'Hapus semua data', 'Latih sekali saja', 'Sembunyikan hasil'], 'c' => 0, 'e' => 'Lengkapi data yang kurang, lalu uji model per kelompok, bukan cuma rata-rata total.'],
                ['q' => 'Contoh bias dalam rekrutmen AI?', 'o' => ['Model menolak kelompok tertentu karena data historis timpang', 'Model terlalu ramah', 'Model suka bercanda', 'Model meminta gaji'], 'c' => 0, 'e' => 'Jika data lama hanya berisi satu golongan, model mewarisi ketidakadilan itu.'],
                ['q' => 'Fairness dalam AI berarti...', 'o' => ['Hasil adil untuk semua kelompok', 'Semua jawaban sama persis', 'Gratis untuk semua', 'Tanpa aturan'], 'c' => 0, 'e' => 'Fairness = tidak ada kelompok yang dirugikan sistematis oleh model.'],
                ['q' => 'Dataset seimbang artinya...', 'o' => ['Datanya sedikit', 'Tiap kelompok terwakili proporsional dan cukup', 'Datanya rahasia', 'Semua data sama persis'], 'c' => 1, 'e' => 'Keseimbangan membuat model belajar semua kelompok secara setara sejak awal.'],
                ['q' => 'Jika data wajah 95% satu suku, risikonya?', 'o' => ['Model lebih cepat', 'Model gagal mengenali suku lain secara adil', 'Tidak ada risiko', 'Kamera rusak'], 'c' => 1, 'e' => 'Ini kasus nyata yang pernah terjadi: error rate melonjak pada kelompok yang minim data.'],
                ['q' => 'Apa itu data historis yang timpang?', 'o' => ['Data kemarin sore', 'Catatan masa lalu yang sudah tidak adil sejak awal', 'Data yang dihapus', 'Data masa depan'], 'c' => 1, 'e' => 'AI meniru masa lalu. Jika masa lalunya timpang, model ikut timpang kecuali diperbaiki.'],
                ['q' => 'Mengapa akurasi total 95% bisa menipu?', 'o' => ['Matematika salah', 'Bisa jadi 100% di kelompok besar tapi 50% di kelompok kecil', 'Angka 95 selalu benar', 'Tidak mungkin'], 'c' => 1, 'e' => 'Rata-rata menyembunyikan kegagalan minoritas. Selalu bedah akurasi per kelompok.'],
                ['q' => 'Evaluasi yang adil dilakukan dengan...', 'o' => ['Sekali uji cukup', 'Menguji model per kelompok, bukan cuma rata-rata', 'Tidak perlu diuji', 'Tanya teman'], 'c' => 1, 'e' => 'Uji terpisah per kelompok (disaggregated evaluation) adalah standar praktik AI yang adil.'],
                ['q' => 'Siapa yang bertanggung jawab atas bias model?', 'o' => ['Komputernya sendiri', 'Manusia pembuat & penggunanya', 'Tidak ada', 'Listriknya'], 'c' => 1, 'e' => 'AI tidak punya niat; bias berasal dari pilihan data dan desain manusia.'],
                ['q' => 'Apa hubungan jumlah contoh dan keadilan?', 'o' => ['Tidak ada hubungan', 'Kelompok dengan contoh sedikit cenderung dikenali buruk', 'Sedikit contoh lebih adil', 'Banyak contoh tidak adil'], 'c' => 1, 'e' => 'Model butuh cukup contoh tiap kelompok — persis seperti latihan KNN di Modul 2.'],
                ['q' => 'Langkah pertama memperbaiki dataset timpang?', 'o' => ['Hapus semua data', 'Kenali kelompok mana yang kurang lalu tambah datanya', 'Sembunyikan masalah', 'Ganti nama file'], 'c' => 1, 'e' => 'Audit dulu komposisi datamu, baru lengkapi yang kurang. Jangan menutup-nutupi.'],
                ['q' => 'Mengapa transparansi data penting?', 'o' => ['Agar keren', 'Agar orang bisa memeriksa dan memperbaiki ketidakadilan', 'Agar mahal', 'Tidak penting'], 'c' => 1, 'e' => 'Dataset yang terbuka komposisinya bisa diaudit publik dan diperbaiki bersama.'],
                ['q' => 'Contoh nyata bias AI di wajah?', 'o' => ['AI yang terlalu sopan', 'Sistem absensi yang gagal mengenali sebagian siswa', 'AI yang suka musik', 'AI yang cepat'], 'c' => 1, 'e' => 'Sistem yang dilatih minim variasi wajah gagal di dunia nyata yang beragam.'],
            ],
            'etika-hoaks-ai' => [
                ['q' => 'Deepfake adalah...', 'o' => ['Konten palsu hasil AI yang terlihat asli', 'Kopi deep', 'Ikan dalam', 'Tugas dalam'], 'c' => 0, 'e' => 'Deepfake memakai AI untuk memalsukan wajah/suara sehingga tampak seperti orang asli.'],
                ['q' => 'Ciri hoaks hasil AI?', 'o' => ['Tangan/jari aneh, teks gibberish, sumber tidak jelas', 'Selalu ada logo resmi', 'Pasti dari TV', 'Tidak bisa dikenali'], 'c' => 0, 'e' => 'AI masih sering gagal menggambar tangan, teks, dan detail kecil — di situlah letak petunjuknya.'],
                ['q' => 'Sebelum menyebar info, kita harus...', 'o' => ['Cek sumber, tanggal, dan verifikasi silang', 'Langsung share ke semua grup', 'Percaya judul saja', 'Abaikan'], 'c' => 0, 'e' => 'Rumus CHECK: cek sumber, tanggal, gambar, lalu kroscek ke media tepercaya.'],
                ['q' => 'Penggunaan AI yang etis...', 'o' => ['Izin, transparan, dan bertanggung jawab', 'Sembunyi-sembunyi', 'Menipu orang', 'Melanggar privasi'], 'c' => 0, 'e' => 'Etika = izin pemilik data, terbuka bahwa itu buatan AI, dan siap tanggung jawab.'],
                ['q' => 'Jika menemukan konten AI yang menipu?', 'o' => ['Laporkan & edukasi orang lain', 'Ikut menyebarkan', 'Diam saja', 'Tertawa saja'], 'c' => 0, 'e' => 'Laporkan ke platform dan beri tahu sekitar agar tidak ikut tertipu.'],
                ['q' => 'Apa itu privasi data?', 'o' => ['Data gratis', 'Hak orang atas data pribadinya', 'Data publik semua', 'Tidak ada artinya'], 'c' => 1, 'e' => 'Foto, suara, dan datamu adalah milikmu. Orang lain wajib izin sebelum memakai.'],
                ['q' => 'Sebelum memakai foto orang untuk AI, harus...', 'o' => ['Langsung pakai', 'Minta izin', 'Sembunyikan', 'Jual datanya'], 'c' => 1, 'e' => 'Izin adalah syarat mutlak — tanpa izin berarti melanggar privasi.'],
                ['q' => 'Apa itu disinformasi?', 'o' => ['Berita biasa', 'Informasi salah yang disebar sengaja untuk menipu', 'Iklan', 'Cerita lucu'], 'c' => 1, 'e' => 'Disinformasi = hoaks by design. Bedakan dengan misinformasi yang tersebar tanpa niat menipu.'],
                ['q' => 'Apa itu misinformasi?', 'o' => ['Iklan resmi', 'Info salah yang tersebar tanpa niat menipu', 'Berita TV', 'Pesan guru'], 'c' => 1, 'e' => 'Contoh: meneruskan info keliru karena panik. Solusinya sama: verifikasi dulu.'],
                ['q' => 'Mengapa hoaks AI berbahaya?', 'o' => ['Tidak berbahaya', 'Terlihat meyakinkan sehingga mudah dipercaya & disebar', 'Lucu saja', 'Hanya untuk hiburan'], 'c' => 1, 'e' => 'Kualitas AI membuat palsu sulit dibedakan — kewaspadaanmu adalah pertahanan utama.'],
                ['q' => 'Ciri video deepfake?', 'o' => ['Selalu hitam putih', 'Gerakan bibir & kedipan tidak wajar', 'Selalu pendek', 'Ada logo TV'], 'c' => 1, 'e' => 'Perhatikan sinkronisasi bibir-suara dan kedipan yang kaku atau hilang.'],
                ['q' => 'Apa itu verifikasi silang?', 'o' => ['Menyilang jalan', 'Mengecek info yang sama di beberapa sumber tepercaya', 'Mengalikan angka', 'Menghapus chat'], 'c' => 1, 'e' => 'Satu sumber bisa salah; tiga sumber tepercaya yang sepakat jauh lebih meyakinkan.'],
                ['q' => 'Aturan bijak memakai AI generatif?', 'o' => ['Bebas menipu', 'Beri label buatan-AI saat menyebar & jangan untuk menipu', 'Sembunyikan sumber', 'Ngaku-ngaku karya sendiri'], 'c' => 1, 'e' => 'Transparansi + tidak menipu + hormati karya orang: tiga rambu utama kreator AI.'],
                ['q' => 'Jika teman menyebar hoaks, sikapmu?', 'o' => ['Ikut menyebar', 'Ingatkan baik-baik + beri fakta benarnya', 'Diam & tertawa', 'Marahi di depan umum'], 'c' => 1, 'e' => 'Edukasi dengan empati lebih efektif daripada mempermalukan.'],
                ['q' => 'Mengapa tetap butuh berpikir kritis di era AI?', 'o' => ['Tidak butuh, AI selalu benar', 'Berpikir itu melelahkan', 'AI bisa salah & dimanfaatkan untuk menipu', 'Guru melarang'], 'c' => 2, 'e' => 'AI hanyalah alat: sehebat apa pun, verifikasi manusia tetap tak tergantikan.'],
            ],
        ];

        foreach ($quizzes as $slug => $items) {
            $module = Module::where('slug', $slug)->first();
            foreach ($items as $item) {
                $quiz = Quiz::firstOrCreate(
                    ['module_id' => $module->id, 'question' => $item['q']],
                    ['options' => $item['o'], 'correct_index' => $item['c'], 'points' => 20, 'explanation' => $item['e'] ?? null]
                );
                // Lengkapi pembahasan soal lama yang belum punya.
                if (! $quiz->explanation && isset($item['e'])) {
                    $quiz->update(['explanation' => $item['e']]);
                }
            }
        }
    }
}
