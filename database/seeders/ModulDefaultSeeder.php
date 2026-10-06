<?php

namespace Database\Seeders;

use App\Models\Badge;
use App\Models\Module;
use App\Models\Quiz;
use Illuminate\Database\Seeder;

class ModulDefaultSeeder extends Seeder
{
    public function run(): void
    {


        $modules = [
            ['title' => 'Modul 1: Apa itu AI?', 'slug' => 'apa-itu-ai', 'order' => 1, 'xp_reward' => 100,
             'description' => 'Konsep dasar AI, machine learning, dan contoh penerapan di kehidupan sehari-hari.'],
            ['title' => 'Modul 2: Latih AI-mu', 'slug' => 'latih-ai-mu', 'order' => 2, 'xp_reward' => 200,
             'description' => 'Deteksi objek real-time via webcam (COCO-SSD pretrained): bounding box, label Indonesia, dan confidence.'],
            ['title' => 'Modul 3: Bias Data', 'slug' => 'bias-data', 'order' => 3, 'xp_reward' => 150,
             'description' => 'Simulasi interaktif dataset tidak seimbang dan dampaknya pada keadilan model.'],
            ['title' => 'Modul 4: Etika & Hoaks AI', 'slug' => 'etika-hoaks-ai', 'order' => 4, 'xp_reward' => 150,
             'description' => 'Studi kasus etika AI, deepfake, dan cara mengenali hoaks hasil AI.'],
            ['title' => 'Modul 5: Python Dasar untuk AI', 'slug' => 'python-dasar-ai', 'order' => 5, 'xp_reward' => 150,
             'description' => 'Python asli di browser: print, variabel, tipe data, list, loop, fungsi, dan dict sebagai fondasi AI.'],
            ['title' => 'Modul 6: Data & Logika AI dengan Python', 'slug' => 'data-logika-ai', 'order' => 6, 'xp_reward' => 200,
             'description' => 'Olah data nyata: rata-rata, filter, dan mini-classifier berbasis aturan sebagai jembatan ke machine learning.'],
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
                ['q' => 'Apa kepanjangan AI?', 'o' => ['Artificial Intelligence', 'Automated Internet', 'Anti Virus', 'Analog Input', 'Automatic Intelligence'], 'c' => 0, 'e' => 'AI adalah Artificial Intelligence: sistem komputer yang meniru kecerdasan manusia.'],
                ['q' => 'Contoh penerapan AI di sekolah?', 'o' => ['Deteksi plagiarisme & chatbot belajar', 'Menghapus papan tulis otomatis', 'Mengganti guru sepenuhnya', 'Mematikan listrik', 'Mengganti kapur dengan spidol'], 'c' => 0, 'e' => 'Deteksi plagiarisme dan chatbot belajar memakai AI; guru tetap berperan membimbing.'],
                ['q' => 'Machine Learning adalah...', 'o' => ['Komputer belajar dari data', 'Komputer dirakit manual', 'Belajar mengetik cepat', 'Mesin cuci pintar', 'Komputer yang dirakit oleh robot'], 'c' => 0, 'e' => 'Machine Learning = komputer menemukan pola dari data, bukan dari aturan yang diketik manual.'],
                ['q' => 'Data yang baik untuk AI harus...', 'o' => ['Banyak, beragam, dan berlabel benar', 'Sedikit dan rahasia', 'Acak tanpa tujuan', 'Hanya satu contoh', 'Banyak tapi semuanya sama persis'], 'c' => 0, 'e' => 'AI belajar dari contoh: makin banyak, beragam, dan benar labelnya, makin pintar modelnya.'],
                ['q' => 'AI yang baik bersifat...', 'o' => ['Membantu manusia, bukan menggantikan nilai manusia', 'Selalu benar 100%', 'Bebas tanpa aturan', 'Rahasia tanpa bisa dijelaskan', 'Menggantikan semua pekerjaan manusia'], 'c' => 0, 'e' => 'AI adalah alat bantu; keputusan penting tetap di tangan manusia.'],
                ['q' => 'Mana yang BUKAN contoh AI?', 'o' => ['Rekomendasi film', 'Filter spam email', 'Kalkulator biasa', 'Pengenalan wajah HP', 'Terjemahan bahasa otomatis'], 'c' => 2, 'e' => 'Kalkulator hanya menghitung dengan aturan tetap dan tidak belajar dari data, jadi bukan AI.'],
                ['q' => 'Apa beda utama program biasa dan AI?', 'o' => ['Program biasa lebih mahal', 'Program biasa ikut aturan tetap, AI belajar pola dari data', 'AI tidak butuh listrik', 'Tidak ada bedanya', 'AI tidak pernah salah'], 'c' => 1, 'e' => 'Program biasa menjalankan aturan yang ditulis programmer; AI menemukan aturannya sendiri dari data.'],
                ['q' => 'Istilah "model" dalam AI artinya...', 'o' => ['Miniatur robot', 'Hasil latihan AI yang bisa menebak data baru', 'Nama programmer', 'Jenis kabel', 'Nilai rapor siswa'], 'c' => 1, 'e' => 'Model adalah "otak" hasil latihan yang dipakai untuk menebak data yang belum pernah dilihat.'],
                ['q' => 'Apa itu chatbot?', 'o' => ['Robot yang bisa berjalan', 'Jenis virus', 'Program yang bercakap dengan manusia memakai bahasa', 'Aplikasi kalkulator', 'Asisten suara di HP'], 'c' => 2, 'e' => 'Chatbot memahami dan membalas bahasa manusia, misalnya asisten belajar atau layanan pelanggan.'],
                ['q' => 'Manakah contoh AI sempit (narrow AI)?', 'o' => ['Robot yang bisa melakukan semua pekerjaan manusia', 'Komputer yang punya perasaan', 'Kamera HP yang mengenali wajah', 'AI yang sadar diri', 'Kalkulator biasa'], 'c' => 2, 'e' => 'AI masa kini adalah narrow AI: hebat di satu tugas spesifik seperti pengenalan wajah. Bahkan ChatGPT/Claude/Gemini pun narrow AI (khusus bahasa).'],
                ['q' => 'Apa peran manusia dalam AI yang bertanggung jawab?', 'o' => ['Tidak perlu sama sekali', 'Mengawasi, mengoreksi, dan memutuskan akhir', 'Hanya menonton', 'Menyerahkan semua keputusan ke AI', 'Membiarkan AI memutuskan hukuman'], 'c' => 1, 'e' => 'Manusia wajib mengawasi dan memegang keputusan akhir, terutama untuk hal penting.'],
                ['q' => 'Data berlabel artinya...', 'o' => ['Data rahasia', 'Data yang sudah diberi keterangan benar, mis. foto + nama "kucing"', 'Data tanpa isi', 'Data yang dihapus', 'Data yang sudah dihapus permanen'], 'c' => 1, 'e' => 'Label adalah "kunci jawaban" saat latihan, misalnya foto kucing berlabel kucing.'],
                ['q' => 'AI bisa salah karena...', 'o' => ['Datanya sedikit, bias, atau salah label', 'AI tidak pernah salah', 'Karena listrik padam saja', 'Karena hari libur', 'Karena AI sedang lelah'], 'c' => 0, 'e' => 'Kualitas AI = kualitas datanya. Data sedikit, timpang, atau salah label membuat AI salah.'],
                ['q' => 'Manfaat AI yang sehat untuk belajar?', 'o' => ['Membantu menjelaskan materi & latihan soal', 'Mengerjakan semua tugas tanpa belajar', 'Mengganti ujian', 'Mencontek saat ulangan', 'Mendapatkan jawaban tanpa memahami'], 'c' => 0, 'e' => 'Gunakan AI untuk memahami materi, bukan untuk menghindari belajar.'],
                ['q' => 'Asisten suara di HP (Siri/Asisten Google) termasuk...', 'o' => ['Bukan AI', 'Robot fisik', 'AI umum setara manusia', 'AI sempit yang pintar di tugas suara', 'Filter foto otomatis'], 'c' => 3, 'e' => 'Asisten suara adalah narrow AI: hebat mengenali suara dan perintah, tapi tidak punya kecerdasan umum.'],
            ],
            'latih-ai-mu' => [
                ['q' => 'Bedanya klasifikasi gambar dan deteksi objek?', 'o' => ['Sama saja', 'Klasifikasi menjawab satu label untuk seluruh foto, deteksi memberi label + kotak lokasi tiap benda', 'Deteksi hanya untuk video', 'Klasifikasi butuh kamera', 'Deteksi tidak butuh model'], 'c' => 1, 'e' => 'Deteksi objek = klasifikasi + lokalisasi: tiap benda dapat label dan bounding box-nya sendiri.'],
                ['q' => 'Bounding box pada deteksi objek berfungsi untuk...?', 'o' => ['Menghitung nilai rapor', 'Menandai posisi benda di dalam gambar', 'Mempercepat internet', 'Mengganti label', 'Menghapus latar'], 'c' => 1, 'e' => 'Bounding box adalah kotak koordinat yang menunjukkan di mana benda berada.'],
                ['q' => 'Data webcam di KKA Lab...', 'o' => ['Dikirim ke semua guru', 'Diunggah ke internet publik', 'Hanya diproses di browser, tidak dikirim ke server', 'Disimpan permanen', 'Dijual ke pengiklan'], 'c' => 2, 'e' => 'Privasi terjaga: frame kamera diolah lokal di perangkatmu dan tidak pernah diunggah.'],
                ['q' => 'Jika kotak deteksi bergoyang atau salah, langkah pertama?', 'o' => ['Buang laptop', 'Menyerah', 'Perbaiki posisi, cahaya, dan cek ambang confidence', 'Tutup mata', 'Mengganti laptop baru'], 'c' => 2, 'e' => 'Model pretrained tidak kamu latih ulang; perbaiki kondisi pengambilan dan ambangnya.'],
                ['q' => 'COCO-SSD disebut model pretrained karena...?', 'o' => ['Menebak acak', 'Menghafal password', 'Sudah dilatih dengan ratusan ribu foto sebelum dipakai', 'Menghitung nilai rapor', 'Dilatih siswa saat praktikum'], 'c' => 2, 'e' => 'Pretrained = bobotnya sudah jadi dari latihan besar; siswa tinggal memakai.'],
                ['q' => 'Alur memakai model pretrained yang benar?', 'o' => ['Langsung percaya semua hasil', 'Siapkan kamera → muat model → deteksi → sesuaikan ambang', 'Uji dulu baru muat model', 'Asal-asalan dulu', 'Tidur dulu baru mulai'], 'c' => 1, 'e' => 'Alur pakai-pretrained: siapkan input, muat model, deteksi, lalu evaluasi ambangnya.'],
                ['q' => '"Kelas" pada COCO-SSD artinya...?', 'o' => ['Ukuran foto', 'Kategori bawaan model: Botol/Buku/Laptop dan 77 lainnya (total 80)', 'Jenis kamera', 'Kelas di sekolah', 'Nilai ujian tiap siswa'], 'c' => 1, 'e' => 'Kelas COCO-SSD tetap: 80 kategori umum, tidak bisa tambah custom tanpa melatih model baru.'],
                ['q' => 'Apa fungsi data uji (test data)?', 'o' => ['Mempercantik tampilan', 'Menghapus data latih', 'Menambah memori', 'Mengukur apakah model bisa menebak data yang belum pernah dilihat', 'Untuk mempercepat loading halaman'], 'c' => 3, 'e' => 'Data uji tidak ikut latihan, jadi ia mengukur kemampuan model sesungguhnya pada hal baru.'],
                ['q' => 'Overfitting artinya...', 'o' => ['Model hafal data latih tapi gagal di data baru', 'Model berjalan sangat cepat', 'Model gratis', 'Model offline', 'Komputer yang terlalu panas'], 'c' => 0, 'e' => 'Seperti murid yang hafal soal tapi tidak paham konsep: nilai latihan bagus, nilai ujian jatuh.'],
                ['q' => 'Apa itu transfer learning (model pretrained di Modul 2)?', 'o' => ['Memakai model yang sudah pintar sebagai titik awal lalu dipakai langsung', 'Memindahkan file antar laptop', 'Belajar sambil transfer uang', 'Mengganti model tiap hari', 'Belajar kelompok bersama teman'], 'c' => 0, 'e' => 'Backbone COCO-SSD sudah dilatih jutaan gambar, lalu dipakai tanpa latih ulang. Hemat waktu dan data.'],
                ['q' => 'Confidence 91% pada label "Botol" artinya...?', 'o' => ['Model 91% yakin melihat botol di kotak itu', 'Foto 91% buram', '91 piksel terdeteksi', 'Baterai tinggal 91%', 'Ditemukan 91 benda'], 'c' => 0, 'e' => 'Confidence = skor keyakinan model pada temuan itu, bukan jumlah benda.'],
                ['q' => 'Dua kotak menumpuk pada benda yang sama biasanya berarti...?', 'o' => ['Ada dua benda kembar', 'Kamera rusak', 'Tidak ada arti', 'Model memberi kandidat ganda — naikkan ambang', 'Hapus aplikasinya'], 'c' => 3, 'e' => 'Kandidat ganda itu wajar; ambang lebih tinggi menyaring temuan yang lemah.'],
                ['q' => 'Benda custom seperti "botol bekal biru milikku" tidak dikenal COCO-SSD karena...?', 'o' => ['Kamera rusak', 'Foto terlalu gelap', 'Label itu tidak ada di 80 kelas latihnya', 'Internet lambat', 'Layar terlalu kecil'], 'c' => 2, 'e' => 'Model hanya tahu kelas yang pernah dilihat saat pretraining.'],
                ['q' => 'Botol 91% di meja terang turun ke 45% saat lampu dimatikan. Pelajarannya...?', 'o' => ['Modelnya rusak', 'Ganti laptop', 'Listrik sekolah jelek', 'Kondisi cahaya memengaruhi confidence — perbaiki cahaya dulu', 'Tidak penting'], 'c' => 3, 'e' => 'Evaluasi jujur: kondisi pengambilan mengubah hasil, bukan cuma modelnya.'],
                ['q' => 'Ambang confidence 70% artinya...?', 'o' => ['Hanya temuan dengan keyakinan ≥70% yang ditampilkan', 'Model berjalan 70% lebih cepat', 'Terdeteksi 70 benda', 'Baterai hemat 70%', 'Kamera zoom 70%'], 'c' => 0, 'e' => 'Ambang = filter: sembunyikan temuan yang kurang meyakinkan.'],
            ],
            'bias-data' => [
                ['q' => 'Bias data artinya...', 'o' => ['Dataset tidak mewakili semua kelompok', 'Data terlalu besar', 'Data terlalu cepat', 'Komputer rusak', 'Data yang disimpan di awan'], 'c' => 0, 'e' => 'Bias = kemiringan. Dataset bias membuat model hanya pintar untuk kelompok tertentu.'],
                ['q' => 'Dampak dataset tidak seimbang?', 'o' => ['Model akurat di satu kelompok, buruk di lainnya', 'Model jadi lebih cepat', 'Tidak ada dampak', 'Komputer meledak', 'Model menjadi lebih murah'], 'c' => 0, 'e' => 'Mayoritas data mendominasi latihan, sehingga kelompok minoritas dikenali buruk.'],
                ['q' => 'Cara mengurangi bias?', 'o' => ['Tambah data kelompok yang kurang & evaluasi adil', 'Hapus semua data', 'Latih sekali saja', 'Sembunyikan hasil', 'Mengganti nama file datanya'], 'c' => 0, 'e' => 'Lengkapi data yang kurang, lalu uji model per kelompok, bukan cuma rata-rata total.'],
                ['q' => 'Contoh bias dalam rekrutmen AI?', 'o' => ['Model menolak kelompok tertentu karena data historis timpang', 'Model terlalu ramah', 'Model suka bercanda', 'Model meminta gaji', 'Model yang memberi gaji besar'], 'c' => 0, 'e' => 'Jika data lama hanya berisi satu golongan, model mewarisi ketidakadilan itu.'],
                ['q' => 'Fairness dalam AI berarti...', 'o' => ['Hasil adil untuk semua kelompok', 'Semua jawaban sama persis', 'Gratis untuk semua', 'Tanpa aturan', 'Semua orang mendapat nilai sama'], 'c' => 0, 'e' => 'Fairness = tidak ada kelompok yang dirugikan sistematis oleh model.'],
                ['q' => 'Dataset seimbang artinya...', 'o' => ['Datanya sedikit', 'Tiap kelompok terwakili proporsional dan cukup', 'Datanya rahasia', 'Semua data sama persis', 'Data yang diacak urutannya'], 'c' => 1, 'e' => 'Keseimbangan membuat model belajar semua kelompok secara setara sejak awal.'],
                ['q' => 'Jika data wajah 95% satu suku, risikonya?', 'o' => ['Model lebih cepat', 'Model gagal mengenali suku lain secara adil', 'Tidak ada risiko', 'Kamera rusak', 'Kamera menjadi lebih jernih'], 'c' => 1, 'e' => 'Ini kasus nyata yang pernah terjadi: error rate melonjak pada kelompok yang minim data.'],
                ['q' => 'Apa itu data historis yang timpang?', 'o' => ['Data kemarin sore', 'Catatan masa lalu yang sudah tidak adil sejak awal', 'Data yang dihapus', 'Data masa depan', 'Data yang usianya sudah tua'], 'c' => 1, 'e' => 'AI meniru masa lalu. Jika masa lalunya timpang, model ikut timpang kecuali diperbaiki.'],
                ['q' => 'Mengapa akurasi total 95% bisa menipu?', 'o' => ['Matematika salah', 'Bisa jadi 100% di kelompok besar tapi 50% di kelompok kecil', 'Angka 95 selalu benar', 'Tidak mungkin', 'Angka 95 tidak boleh dipakai'], 'c' => 1, 'e' => 'Rata-rata menyembunyikan kegagalan minoritas. Selalu bedah akurasi per kelompok.'],
                ['q' => 'Evaluasi yang adil dilakukan dengan...', 'o' => ['Sekali uji cukup', 'Menguji model per kelompok, bukan cuma rata-rata', 'Tidak perlu diuji', 'Tanya teman', 'Menilai sekali lalu pulang'], 'c' => 1, 'e' => 'Uji terpisah per kelompok (disaggregated evaluation) adalah standar praktik AI yang adil.'],
                ['q' => 'Siapa yang bertanggung jawab atas bias model?', 'o' => ['Komputernya sendiri', 'Manusia pembuat & penggunanya', 'Tidak ada', 'Listriknya', 'Tukang listrik sekolah'], 'c' => 1, 'e' => 'AI tidak punya niat; bias berasal dari pilihan data dan desain manusia.'],
                ['q' => 'Apa hubungan jumlah contoh dan keadilan?', 'o' => ['Tidak ada hubungan', 'Kelompok dengan contoh sedikit cenderung dikenali buruk', 'Sedikit contoh lebih adil', 'Banyak contoh tidak adil', 'Contoh sedikit membuat model cepat'], 'c' => 1, 'e' => 'Model butuh cukup contoh tiap kelompok — dataset timpang melahirkan bias.'],
                ['q' => 'Langkah pertama memperbaiki dataset timpang?', 'o' => ['Hapus semua data', 'Kenali kelompok mana yang kurang lalu tambah datanya', 'Sembunyikan masalah', 'Ganti nama file', 'Mengganti judul laporannya'], 'c' => 1, 'e' => 'Audit dulu komposisi datamu, baru lengkapi yang kurang. Jangan menutup-nutupi.'],
                ['q' => 'Mengapa transparansi data penting?', 'o' => ['Agar keren', 'Agar orang bisa memeriksa dan memperbaiki ketidakadilan', 'Agar mahal', 'Tidak penting', 'Agar datanya terlihat mahal'], 'c' => 1, 'e' => 'Dataset yang terbuka komposisinya bisa diaudit publik dan diperbaiki bersama.'],
                ['q' => 'Contoh nyata bias AI di wajah?', 'o' => ['AI yang terlalu sopan', 'Sistem absensi yang gagal mengenali sebagian siswa', 'AI yang suka musik', 'AI yang cepat', 'AI yang bisa bernyanyi'], 'c' => 1, 'e' => 'Sistem yang dilatih minim variasi wajah gagal di dunia nyata yang beragam.'],
            ],
            'etika-hoaks-ai' => [
                ['q' => 'Deepfake adalah...', 'o' => ['Konten palsu hasil AI yang terlihat asli', 'Kopi deep', 'Ikan dalam', 'Tugas dalam', 'Kopi yang sangat kental'], 'c' => 0, 'e' => 'Deepfake memakai AI untuk memalsukan wajah/suara sehingga tampak seperti orang asli.'],
                ['q' => 'Ciri hoaks hasil AI?', 'o' => ['Tangan/jari aneh, teks gibberish, sumber tidak jelas', 'Selalu ada logo resmi', 'Pasti dari TV', 'Tidak bisa dikenali', 'Selalu berwarna hitam putih'], 'c' => 0, 'e' => 'AI masih sering gagal menggambar tangan, teks, dan detail kecil — di situlah letak petunjuknya.'],
                ['q' => 'Sebelum menyebar info, kita harus...', 'o' => ['Cek sumber, tanggal, dan verifikasi silang', 'Langsung share ke semua grup', 'Percaya judul saja', 'Abaikan', 'Menambah bumbu caption'], 'c' => 0, 'e' => 'Rumus CHECK: cek sumber, tanggal, gambar, lalu kroscek ke media tepercaya.'],
                ['q' => 'Penggunaan AI yang etis...', 'o' => ['Izin, transparan, dan bertanggung jawab', 'Sembunyi-sembunyi', 'Menipu orang', 'Melanggar privasi', 'Memakai nama samaran'], 'c' => 0, 'e' => 'Etika = izin pemilik data, terbuka bahwa itu buatan AI, dan siap tanggung jawab.'],
                ['q' => 'Jika menemukan konten AI yang menipu?', 'o' => ['Laporkan & edukasi orang lain', 'Ikut menyebarkan', 'Diam saja', 'Tertawa saja', 'Mengoleksi kontennya'], 'c' => 0, 'e' => 'Laporkan ke platform dan beri tahu sekitar agar tidak ikut tertipu.'],
                ['q' => 'Apa itu privasi data?', 'o' => ['Data gratis', 'Hak orang atas data pribadinya', 'Data publik semua', 'Tidak ada artinya', 'Data yang boleh disebar bebas'], 'c' => 1, 'e' => 'Foto, suara, dan datamu adalah milikmu. Orang lain wajib izin sebelum memakai.'],
                ['q' => 'Sebelum memakai foto orang untuk AI, harus...', 'o' => ['Langsung pakai', 'Minta izin', 'Sembunyikan', 'Jual datanya', 'Mengedit dulu baru izin'], 'c' => 1, 'e' => 'Izin adalah syarat mutlak — tanpa izin berarti melanggar privasi.'],
                ['q' => 'Apa itu disinformasi?', 'o' => ['Berita biasa', 'Informasi salah yang disebar sengaja untuk menipu', 'Iklan', 'Cerita lucu', 'Diskon informasi belanja'], 'c' => 1, 'e' => 'Disinformasi = hoaks by design. Bedakan dengan misinformasi yang tersebar tanpa niat menipu.'],
                ['q' => 'Apa itu misinformasi?', 'o' => ['Iklan resmi', 'Info salah yang tersebar tanpa niat menipu', 'Berita TV', 'Pesan guru', 'Misi informasi rahasia'], 'c' => 1, 'e' => 'Contoh: meneruskan info keliru karena panik. Solusinya sama: verifikasi dulu.'],
                ['q' => 'Mengapa hoaks AI berbahaya?', 'o' => ['Tidak berbahaya', 'Terlihat meyakinkan sehingga mudah dipercaya & disebar', 'Lucu saja', 'Hanya untuk hiburan', 'Membuat HP cepat panas'], 'c' => 1, 'e' => 'Kualitas AI membuat palsu sulit dibedakan — kewaspadaanmu adalah pertahanan utama.'],
                ['q' => 'Ciri video deepfake?', 'o' => ['Selalu hitam putih', 'Gerakan bibir & kedipan tidak wajar', 'Selalu pendek', 'Ada logo TV', 'Selalu berdurasi 10 jam'], 'c' => 1, 'e' => 'Perhatikan sinkronisasi bibir-suara dan kedipan yang kaku atau hilang.'],
                ['q' => 'Apa itu verifikasi silang?', 'o' => ['Menyilang jalan', 'Mengecek info yang sama di beberapa sumber tepercaya', 'Mengalikan angka', 'Menghapus chat', 'Menyalakan dua HP sekaligus'], 'c' => 1, 'e' => 'Satu sumber bisa salah; tiga sumber tepercaya yang sepakat jauh lebih meyakinkan.'],
                ['q' => 'Aturan bijak memakai AI generatif?', 'o' => ['Bebas menipu', 'Beri label buatan-AI saat menyebar & jangan untuk menipu', 'Sembunyikan sumber', 'Ngaku-ngaku karya sendiri', 'Memakai filter sebanyak mungkin'], 'c' => 1, 'e' => 'Transparansi + tidak menipu + hormati karya orang: tiga rambu utama kreator AI.'],
                ['q' => 'Jika teman menyebar hoaks, sikapmu?', 'o' => ['Ikut menyebar', 'Ingatkan baik-baik + beri fakta benarnya', 'Diam & tertawa', 'Marahi di depan umum', 'Memuji keberaniannya'], 'c' => 1, 'e' => 'Edukasi dengan empati lebih efektif daripada mempermalukan.'],
                ['q' => 'Mengapa tetap butuh berpikir kritis di era AI?', 'o' => ['Tidak butuh, AI selalu benar', 'Berpikir itu melelahkan', 'AI bisa salah & dimanfaatkan untuk menipu', 'Guru melarang', 'Berpikir sambil berlari'], 'c' => 2, 'e' => 'AI hanyalah alat: sehebat apa pun, verifikasi manusia tetap tak tergantikan.'],
            ],
        ];

        foreach ($quizzes as $slug => $items) {
            $module = Module::where('slug', $slug)->first();
            foreach ($items as $item) {
                $quiz = Quiz::firstOrCreate(
                    ['module_id' => $module->id, 'teacher_id' => null, 'question' => $item['q']],
                    ['options' => $item['o'], 'correct_index' => $item['c'], 'points' => 20, 'explanation' => $item['e'] ?? null]
                );
                // Lengkapi pembahasan soal lama yang belum punya.
                if (! $quiz->explanation && isset($item['e'])) {
                    $quiz->update(['explanation' => $item['e']]);
                }
                // Lengkapi opsi E untuk soal lama yang baru 4 opsi.
                $opts = $quiz->options ?? [];
                if (count($opts) < 5 && isset($item['o'][4]) && ! in_array($item['o'][4], $opts)) {
                    $opts[] = $item['o'][4];
                    $quiz->update(['options' => $opts]);
                }
            }
        }

        // 30 soal Python (15 per modul) — jawaban benar disebar acak A–E.
        $quizzesPython = [
            'python-dasar-ai' => [
                ['q' => 'Apa keluaran dari print("Halo AI")?', 'o' => ['HaloAI (tanpa spasi)', '"Halo AI" lengkap dengan tanda kutip', 'Halo AI', 'Error karena huruf kapital', 'Baris kosong'], 'c' => 2, 'e' => 'print menampilkan isi tanda kutip tanpa tanda kutipnya: Halo AI.'],
                ['q' => 'Cara benar menyimpan nama di variabel?', 'o' => ['nama = "Sinta"', '"Sinta" = nama', 'nama == "Sinta"', 'var nama = Sinta', 'nama: Sinta;'], 'c' => 0, 'e' => 'Variabel memakai satu tanda sama dengan: nama = "Sinta". Dua sama dengan (==) itu pembanding.'],
                ['q' => 'type(17) menghasilkan...', 'o' => ['str', 'float', 'bool', 'int', 'list'], 'c' => 3, 'e' => '17 tanpa koma desimal bertipe int (bilangan bulat).'],
                ['q' => 'buah = ["apel", "mangga", "pisang"]. Nilai buah[1]?', 'o' => ['apel', 'mangga', 'pisang', 'buah', 'Error'], 'c' => 1, 'e' => 'Indeks list mulai dari 0, jadi indeks 1 adalah "mangga".'],
                ['q' => 'Fungsi len(["a", "b", "c"]) mengembalikan...', 'o' => ['"abc"', 'a', '2', '"3"', '3'], 'c' => 4, 'e' => 'len menghitung jumlah isi list: 3 (bertipe int, bukan teks "3").'],
                ['q' => 'for i in range(3): print(i) mencetak...', 'o' => ['0 1 2', '1 2 3', '0 1 2 3', '3 2 1', '0 0 0'], 'c' => 0, 'e' => 'range(3) menghasilkan 0, 1, 2 — berhenti sebelum angka 3.'],
                ['q' => 'Tanda bahwa sebuah blok fungsi dimulai?', 'o' => ['Kurung kurawal { }', 'Baris def ... diakhiri titik dua lalu baris menjorok', 'Kata end di akhir fungsi', 'Titik koma di tiap baris', 'Huruf kapital di awal baris'], 'c' => 1, 'e' => 'Python memakai titik dua + indentasi (jorokan) untuk menandai isi fungsi.'],
                ['q' => 'siswa = {"nama": "Budi", "nilai": 85}. Cara mengambil angka 85?', 'o' => ['siswa[0]', 'siswa(nilai)', 'siswa.nilai', 'nilai["Budi"]', 'siswa["nilai"]'], 'c' => 4, 'e' => 'Dict diakses dengan kuncinya dalam kurung siku: siswa["nilai"].'],
                ['q' => 'f"Halo, {nama}!" dengan nama = "Ayu" menghasilkan...', 'o' => ['Halo, nama!', 'Halo, Ayu!', '"Halo, {nama}!"', 'Halo, {Ayu}!', 'Error'], 'c' => 1, 'e' => 'f-string mengganti {nama} dengan isi variabel: Halo, Ayu!'],
                ['q' => 'x = 75. if x >= 80: ... else: ... Cabang mana yang berjalan?', 'o' => ['Cabang if', 'Error karena >=', 'Kedua cabang', 'Cabang else', 'Program berhenti total'], 'c' => 3, 'e' => '75 tidak memenuhi syarat >= 80, jadi cabang else yang dijalankan.'],
                ['q' => 'nilai = [70, 80]. Perintah menambah 90 ke akhir list?', 'o' => ['nilai + 90', 'nilai.add(90)', 'nilai.append(90)', 'append(nilai, 90)', 'nilai[3] = 90'], 'c' => 2, 'e' => 'Metode append menambah satu unsur ke akhir list.'],
                ['q' => 'Perulangan while perlu ... agar tidak jalan selamanya?', 'o' => ['Komentar penjelas', 'Kondisi yang lama-lama menjadi salah (mis. penghitung bertambah)', 'Huruf kapital di awal', 'Koneksi internet', 'Dua variabel cadangan'], 'c' => 1, 'e' => 'while berhenti saat kondisinya salah; tanpa perubahan kondisi ia abadi (infinite loop).'],
                ['q' => 'Tanda pagar (#) di Python berarti...', 'o' => ['Awal bagian error', 'Akhir program', 'Simbol perkalian', 'Kode rahasia', 'Komentar: teks diabaikan komputer'], 'c' => 4, 'e' => 'Teks setelah # adalah komentar untuk manusia; Python melewatinya.'],
                ['q' => 'type("85") menghasilkan...', 'o' => ['str', 'int', 'float', 'bool', 'list'], 'c' => 0, 'e' => 'Tanda kutip membuat 85 menjadi teks (str), walau isinya angka.'],
                ['q' => 'Bedanya print dan return dalam fungsi?', 'o' => ['Sama persis', 'return menghapus fungsi', 'print mengirim nilai ke pemanggil, return hanya menampilkan', 'return mengirim nilai ke pemanggil, print hanya menampilkan', 'Keduanya wajib dipakai bersama'], 'c' => 3, 'e' => 'print hanya menampilkan ke layar; return mengembalikan nilai agar bisa dipakai kode lain.'],
            ],
            'data-logika-ai' => [
                ['q' => 'nilai = [70, 80, 90]. Cara menghitung rata-rata di Python?', 'o' => ['(70+80+90)/2', 'sum(nilai) / len(nilai)', 'nilai / 3', 'rata(nilai)', 'sum(nilai) + len(nilai)'], 'c' => 1, 'e' => 'Jumlahkan semua lalu bagi banyaknya data: sum(nilai) / len(nilai) = 80.'],
                ['q' => 'Ingin hanya siswa umur 17 tahun ke atas. Syarat filternya?', 'o' => ['umur = 17', 'umur > 17 saja', 'umur < 17', 'umur >= 17', 'umur != 17'], 'c' => 3, 'e' => '"17 tahun ke atas" termasuk angka 17, jadi syaratnya umur >= 17.'],
                ['q' => 'Aturan: jika nilai >= 80 maka "lulus", selain itu "remidi". Nilai 85 hasilnya?', 'o' => ['lulus', 'remidi', 'error', 'tidak dapat ditentukan', 'keduanya'], 'c' => 0, 'e' => '85 memenuhi syarat >= 80, jadi labelnya "lulus". Inilah classifier berbasis aturan paling sederhana.'],
                ['q' => 'Tinggi badan [150, 165, 158]. Nilai terbesar?', 'o' => ['150', '158', 'jumlah semuanya', 'rata-ratanya', '165'], 'c' => 4, 'e' => 'Fungsi max() mengambil yang terbesar: 165.'],
                ['q' => 'sum([]) / len([]) menghasilkan...', 'o' => ['0', '-1', 'Error (pembagian dengan nol)', '[]', 'None otomatis'], 'c' => 2, 'e' => 'len([]) = 0 dan pembagian dengan nol itu error. Data kosong wajib ditangani dulu — seperti bias data di Modul 3.'],
                ['q' => 'Data kota ["Jakarta", "BANDUNG"] ingin diseragamkan ke huruf kecil. Caranya?', 'o' => ['kota.upper()', 'hapus datanya', 'kota[0]', 'tambah spasi', '[k.lower() for k in kota]'], 'c' => 4, 'e' => 'lower() menyeragamkan kapitalisasi — langkah pembersihan data sebelum dianalisis.'],
                ['q' => 'Syarat "lulus jika nilai >= 80 DAN kehadiran >= 75%" memakai operator...', 'o' => ['or', 'and', 'not', 'xor', '+'], 'c' => 1, 'e' => 'Operator and berarti kedua syarat wajib benar sekaligus.'],
                ['q' => 'Nilai [50, 55, 100] rata-ratanya 68. Mengapa angka ini menipu?', 'o' => ['Salah hitung komputer', 'Rata-rata selalu 68', 'Data terlalu sedikit pasti salah', 'Satu nilai ekstrem (100) menarik rata-rata ke atas', 'Komputer sedang lelah'], 'c' => 3, 'e' => 'Nilai ekstrem (outlier) menarik rata-rata; analisis jujur melihat sebaran, bukan cuma rata-rata.'],
                ['q' => 'data = ["kucing", "anjing", "kucing"]. Cara menghitung kemunculan "kucing"?', 'o' => ['data.count("kucing") menghasilkan 2', 'len(data) menghasilkan 2', 'data[0] menghasilkan 2', 'sum(data) menghasilkan 2', 'data + "kucing" menghasilkan 2'], 'c' => 0, 'e' => 'Metode count menghitung kemunculan sebuah label — dasar statistik kategori.'],
                ['q' => 'Classifier "spam jika ada kata gratis" disebut berbasis aturan karena...', 'o' => ['Memakai jaringan saraf tiruan', 'Butuh kamera untuk bekerja', 'Keputusan mengikuti if/else buatan manusia, bukan belajar dari data', 'Tidak pernah salah', 'Berjalan di komputasi awan'], 'c' => 2, 'e' => 'Aturannya ditulis manusia; ML sejati (Modul 2) menemukan aturannya sendiri dari data.'],
                ['q' => 'Aturan buatan manusia paling tepat diganti machine learning saat...', 'o' => ['Datanya hanya 3 baris', 'Komputer lambat', 'Tidak ada internet', 'Polanya terlalu rumit ditulis manual tetapi contohnya banyak', 'Malas mengetik aturan'], 'c' => 3, 'e' => 'Inilah jembatan ke ML: aturan manual tidak sanggup (mis. kenali wajah), tetapi ratusan contoh bisa melatih model.'],
                ['q' => '[x for x in nilai if x >= 80] disebut...', 'o' => ['list comprehension: membuat sekaligus menyaring list dalam sebaris', 'perulangan yang rusak', 'dict rahasia', 'fungsi anonim', 'komentar yang panjang'], 'c' => 0, 'e' => 'List comprehension membangun list baru yang sudah tersaring dalam satu baris.'],
                ['q' => 'sorted([90, 70, 80]) menghasilkan...', 'o' => ['[90, 70, 80]', '90', '[70, 80, 90]', '[80, 70, 90]', '70'], 'c' => 2, 'e' => 'Fungsi sorted mengurutkan menaik: [70, 80, 90].'],
                ['q' => 'Dari 10 tebakan aturan, 7 benar. Akurasinya?', 'o' => ['17%', '10%', '7%', 'Tidak bisa dihitung', '70%'], 'c' => 4, 'e' => 'Akurasi = jumlah benar dibagi total = 7/10 = 70%.'],
                ['q' => 'Dataset latihan hanya berisi satu kelompok (ingat Modul 3). Akibatnya?', 'o' => ['Model berjalan lebih cepat', 'Model bias: gagal di kelompok yang minim data', 'Model menjadi adil', 'Model menjadi gratis', 'Tidak ada akibat apa pun'], 'c' => 1, 'e' => 'Persis pelajaran Modul 3: ketidakseimbangan data melahirkan bias, walau kodenya ditulis dengan Python.'],
            ],
        ];

        foreach ($quizzesPython as $slug => $items) {
            $module = Module::where('slug', $slug)->first();
            if (! $module) {
                continue;
            }
            foreach ($items as $item) {
                Quiz::firstOrCreate(
                    ['module_id' => $module->id, 'teacher_id' => null, 'question' => $item['q']],
                    ['options' => $item['o'], 'correct_index' => $item['c'], 'points' => 20, 'explanation' => $item['e']]
                );
            }
        }

        // Tempel 6 modul default (posisi 1-6) ke kelas yang belum punya susunan.
        $defaults = Module::whereNull('teacher_id')->orderBy('order')->get();
        foreach (\App\Models\Classroom::whereDoesntHave('modules')->get() as $kelas) {
            $pos = 1;
            foreach ($defaults as $dm) {
                $kelas->modules()->syncWithoutDetaching([$dm->id => ['position' => $pos++]]);
            }
            foreach (Module::where('teacher_id', $kelas->teacher_id)->orderBy('order')->get() as $cm) {
                $kelas->modules()->syncWithoutDetaching([$cm->id => ['position' => $pos++]]);
            }
        }
    }
}
