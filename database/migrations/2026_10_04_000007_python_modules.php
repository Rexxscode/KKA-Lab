<?php

use App\Models\Badge;
use App\Models\Module;
use App\Models\Quiz;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $modules = [
            [
                'title' => 'Modul 5: Python Dasar untuk AI',
                'slug' => 'python-dasar-ai',
                'order' => 5,
                'xp_reward' => 150,
                'description' => 'Python asli di browser: print, variabel, tipe data, list, loop, fungsi, dan dict sebagai fondasi AI.',
            ],
            [
                'title' => 'Modul 6: Data & Logika AI dengan Python',
                'slug' => 'data-logika-ai',
                'order' => 6,
                'xp_reward' => 200,
                'description' => 'Olah data nyata: rata-rata, filter, dan mini-classifier berbasis aturan sebagai jembatan ke machine learning.',
            ],
        ];

        foreach ($modules as $m) {
            $module = Module::firstOrCreate(['slug' => $m['slug']], $m);
            Badge::firstOrCreate(
                ['module_id' => $module->id],
                ['name' => 'Badge ' . $module->title, 'icon_path' => null]
            );
        }

        foreach ($this->soal() as $slug => $items) {
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

        // Tempel posisi akhir ke semua kelas existing yang belum punya.
        $baru = Module::whereIn('slug', ['python-dasar-ai', 'data-logika-ai'])->orderBy('order')->get();
        foreach (\App\Models\Classroom::all() as $kelas) {
            foreach ($baru as $dm) {
                if (! $kelas->modules()->where('modules.id', $dm->id)->exists()) {
                    $pos = (int) ($kelas->modules()->max('classroom_module.position') ?? 0) + 1;
                    $kelas->modules()->attach($dm->id, ['position' => $pos]);
                }
            }
        }
    }

    public function down(): void
    {
        $ids = Module::whereIn('slug', ['python-dasar-ai', 'data-logika-ai'])->pluck('id');
        DB::table('classroom_module')->whereIn('module_id', $ids)->delete();
        Quiz::whereIn('module_id', $ids)->whereNull('teacher_id')->delete();
        Badge::whereIn('module_id', $ids)->delete();
        Module::whereIn('id', $ids)->delete();
    }

    /**
     * 30 soal baru (15 per modul), jawaban benar disebar acak A–E.
     */
    private function soal(): array
    {
        return [
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
    }
};
