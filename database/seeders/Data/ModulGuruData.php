<?php

namespace Database\Seeders\Data;

/**
 * Bank 14 modul custom milik guru + soalnya.
 * Struktur tiap modul: title, slug, description, materi, xp_reward, quizzes[].
 * Tiap soal: q, o[5 opsi], c (index benar), e (pembahasan).
 */
class ModulGuruData
{
    public static function daftar(): array
    {
        return [
            [
                'title' => 'Jaringan Komputer Dasar', 'slug' => 'jaringan-komputer-dasar', 'xp_reward' => 100,
                'description' => 'LAN, IP address, dan cara internet menyambungkan komputer.',
                'materi' => "## Pengertian Jaringan\nJaringan komputer menghubungkan dua komputer atau lebih agar bisa bertukar data. Contoh paling dekat: hotspot HP ke laptop.\n\n[PRAKTIK] Buka pengaturan WiFi di HP-mu, catat nama SSID dan kekuatan sinyal di kelas vs di kantin. Bandingkan hasilnya dengan teman sebangku.\n\n## Alamat IP\nSetiap perangkat punya alamat IP sebagai identitas, mirip nomor rumah. IPv4 berbentuk empat angka 0-255, misalnya 192.168.1.10.\n\n[TUGAS] Cari alamat IP laptop sekolah lewat pengaturan jaringan, tulis beserta penjelasan tiap bagiannya dengan bahasamu sendiri.",
                'quizzes' => [
                    ['q' => 'Fungsi utama jaringan komputer?', 'o' => ['Menghubungkan perangkat agar bertukar data', 'Mempercepat baterai', 'Mendinginkan prosesor', 'Mengganti kabel', 'Menambah memori'], 'c' => 0, 'e' => 'Inti jaringan adalah konektivitas dan pertukaran data antarperangkat.'],
                    ['q' => 'Contoh alamat IPv4 yang valid?', 'o' => ['192.168.1.10', '999.1.1.1', '192.168.1', 'GG.00.11.AA', '1234'], 'c' => 0, 'e' => 'IPv4 = empat kelompok angka 0-255 dipisah titik.'],
                ],
            ],
            [
                'title' => 'Keamanan Digital Dasar', 'slug' => 'keamanan-digital-dasar', 'xp_reward' => 100,
                'description' => 'Password kuat, phishing, dan verifikasi dua langkah.',
                'materi' => "## Password Kuat\nPassword kuat itu panjang dan unik tiap akun. Rumus mudah: 3 kata acak + angka + simbol, misalnya kuda-terbang-27!.\n\n[PRAKTIK] Nilai password lamamu sendiri: apakah 8+ karakter, ada huruf besar-kecil, angka, dan simbol? Catat skornya tanpa menulis password aslinya.\n\n## Waspada Phishing\nPhising meniru tampilan resmi untuk mencuri akun. Cek pengirim, ejaan tautan, dan urgensi palsu sebelum klik.\n\n[TUGAS] Tulis 3 ciri pesan phishing dari contoh yang pernah kamu terima, lalu jelaskan cara memastikannya aman.",
                'quizzes' => [
                    ['q' => 'Password paling kuat?', 'o' => ['12345678', 'namasaya', 'kuda-terbang-27!', 'password', 'qwerty'], 'c' => 2, 'e' => 'Panjang + kombinasi huruf besar-kecil, angka, simbol paling sulit ditebak.'],
                    ['q' => 'Langkah tepat saat menerima tautan mencurigakan?', 'o' => ['Langsung klik', 'Teruskan ke semua grup', 'Cek pengirim dan ejaan tautan dulu', 'Masukkan password agar cepat', 'Abaikan antivirus'], 'c' => 2, 'e' => 'Verifikasi pengirim dan tautan sebelum klik apa pun.'],
                ],
            ],
            [
                'title' => 'Logika Algoritma Sehari-hari', 'slug' => 'logika-algoritma', 'xp_reward' => 120,
                'description' => 'Urutkan langkah, percabangan, dan pengulangan tanpa komputer.',
                'materi' => "## Algoritma Itu Resep\nAlgoritma = urutan langkah pasti untuk mencapai tujuan. Membuat mi instan pun algoritma: rebus air, masukkan mi, tunggu 3 menit, tiriskan, campur bumbu.\n\n[PRAKTIK] Tulis algoritma bangun pagi sampai berangkat sekolah dalam 8 langkah bernomor. Tukarkan dengan teman: bisakah ia mengikutinya tanpa bertanya?\n\n## Percabangan dan Pengulangan\nJika hujan maka bawa payung (if-else). Selama piring kotor, cuci satu per satu (while).\n\n[TUGAS] Buat flowchart mini di kertas untuk keputusan jajan di kantin dengan budget Rp10.000, lalu foto dan jelaskan cabangnya.",
                'quizzes' => [
                    ['q' => 'Ciri algoritma yang baik?', 'o' => ['Langkah pasti, berurutan, dan berhenti', 'Sebanyak mungkin langkah', 'Memakai bahasa Inggris', 'Tanpa tujuan jelas', 'Berputar selamanya'], 'c' => 0, 'e' => 'Algoritma harus jelas langkahnya dan berakhir dengan hasil.'],
                    ['q' => '"Selama nilai belum tuntas, ulangi belajar" adalah...', 'o' => ['Percabangan', 'Pengulangan (looping)', 'Variabel', 'Fungsi', 'Komentar'], 'c' => 1, 'e' => 'Kata "selama" menandakan pengulangan sampai syarat terpenuhi.'],
                ],
            ],
            [
                'title' => 'Desain Slide Presentasi', 'slug' => 'desain-slide-presentasi', 'xp_reward' => 80,
                'description' => 'Satu pesan per slide, kontras, dan visual pendukung.',
                'materi' => "## Satu Slide Satu Pesan\nAudiens membaca 3 detik pertama. Satu slide = satu ide, maksimal 6 baris teks.\n\n[PRAKTIK] Ambil satu slide tugasmu yang paling penuh, potong isinya jadi setengah tanpa menghilangkan pesan utamanya.\n\n## Kontras dan Visual\nJudul besar-gelap di latar terang (atau sebaliknya). Satu gambar relevan mengalahkan 100 kata.\n\n[TUGAS] Desain ulang 3 slide materi favoritmu dengan aturan 1 pesan + 1 visual per slide.",
                'quizzes' => [
                    ['q' => 'Aturan emas isi slide?', 'o' => ['Satu slide satu pesan utama', 'Semua materi dalam satu slide', 'Animasi sebanyak mungkin', 'Huruf sekecil mungkin', 'Tanpa judul'], 'c' => 0, 'e' => 'Satu pesan per slide membuat audiens fokus dan ingat.'],
                    ['q' => 'Kombinasi teks-latar yang terbaca?', 'o' => ['Kuning di atas putih', 'Abu muda di atas abu tua', 'Teks gelap di latar terang', 'Merah di atas hijau', 'Putih di atas kuning'], 'c' => 2, 'e' => 'Kontras tinggi (gelap-terang) adalah syarat keterbacaan.'],
                ],
            ],
            [
                'title' => 'Spreadsheet untuk Analisis Nilai', 'slug' => 'spreadsheet-analisis-nilai', 'xp_reward' => 100,
                'description' => 'SUM, AVERAGE, IF, dan diagram batang dari data kelas.',
                'materi' => "## Rumus Dasar\n=SUM() menjumlah, =AVERAGE() merata-rata, =IF() memberi label lulus/remidi otomatis.\n\n[PRAKTIK] Buat tabel 5 teman (nama + 3 nilai), hitung rata-rata tiap orang dengan AVERAGE, lalu beri label lulus (≥80) dengan IF.\n\n## Visualisasi\nDiagram batang memudahkan melihat siapa di atas rata-rata kelas.\n\n[TUGAS] Buat diagram batang dari tabelmu dan tulis 2 kalimat kesimpulan: siapa tertinggi dan berapa selisihnya dari rata-rata.",
                'quizzes' => [
                    ['q' => 'Rumus rata-rata nilai A1 sampai A5?', 'o' => ['=SUM(A1:A5)', '=AVERAGE(A1:A5)', '=IF(A1:A5)', '=MAX(A1:A5)', '=RATA(A1-A5)'], 'c' => 1, 'e' => 'AVERAGE menghitung rata-rata rentang sel.'],
                    ['q' => '=IF(B2>=80,"Lulus","Remidi") dengan B2=75 menghasilkan...', 'o' => ['Lulus', 'Remidi', 'Error', '80', 'Kosong'], 'c' => 1, 'e' => '75 tidak memenuhi >= 80, jadi cabang kedua: Remidi.'],
                ],
            ],
            [
                'title' => 'Berpikir Komputasional', 'slug' => 'berpikir-komputasional', 'xp_reward' => 120,
                'description' => 'Dekomposisi, pola, abstraksi, dan desain algoritma.',
                'materi' => "## Empat Pilar\n1) Dekomposisi: pecah masalah besar. 2) Pola: cari kesamaan. 3) Abstraksi: abaikan detail tak penting. 4) Algoritma: susun langkah.\n\n[PRAKTIK] Pecah masalah antre kantin yang panjang jadi 4 bagian kecil, lalu tulis solusi tiap bagian dalam 1 kalimat.\n\n## Abstraksi Sehari-hari\nPeta bus hanya menampilkan rute dan halte — jalan kecil dan warung diabaikan karena tak relevan.\n\n[TUGAS] Jelaskan dengan bahasamu: detail apa yang diabstraksikan dari peta kelasmu, dan mengapa itu membantu?",
                'quizzes' => [
                    ['q' => 'Dekomposisi artinya...', 'o' => ['Memecah masalah besar jadi bagian kecil', 'Menghias tampilan', 'Menyalin jawaban', 'Menambah masalah baru', 'Menghapus data'], 'c' => 0, 'e' => 'Masalah besar jadi ringan bila dipecah jadi bagian-bagian kecil.'],
                    ['q' => 'Contoh abstraksi?', 'o' => ['Peta bus yang hanya menampilkan rute dan halte', 'Foto resolusi penuh', 'Rekaman tanpa edit', 'Salinan persis buku', 'Arsip semua chat'], 'c' => 0, 'e' => 'Abstraksi menyisakan info relevan dan membuang detail yang mengganggu.'],
                ],
            ],
            [
                'title' => 'HTML: Struktur Halaman Web', 'slug' => 'html-struktur-web', 'xp_reward' => 100,
                'description' => 'Tag heading, paragraf, link, gambar, dan list.',
                'materi' => "## Anatomi Tag\nTag berpasangan: pembuka, isi, penutup. Contoh: <h1>Judul</h1>, <p>paragraf</p>.\n\n[PRAKTIK] Tulis halaman biodata mini: 1 heading nama, 1 paragraf hobi, 1 link ke situs favorit, 1 daftar 3 makanan favorit.\n\n## Atribut\nAtribut memberi info tambahan: href di link, src dan alt di gambar. Alt text penting untuk aksesibilitas.\n\n[TUGAS] Tambahkan 1 gambar (src bebas) lengkap dengan alt text deskriptif ke biodatamu, lalu jelaskan fungsi tiap atributnya.",
                'quizzes' => [
                    ['q' => 'Tag untuk judul terbesar?', 'o' => ['<p>', '<h1>', '<a>', '<img>', '<title>'], 'c' => 1, 'e' => 'h1 adalah heading level tertinggi untuk judul utama.'],
                    ['q' => 'Fungsi atribut alt pada gambar?', 'o' => ['Memperbesar gambar', 'Teks pengganti bila gambar gagal tampil / dibaca pembaca layar', 'Mengganti warna', 'Mempercepat loading', 'Menambah border'], 'c' => 1, 'e' => 'Alt menjamin makna gambar tetap tersampaikan ke semua pengguna.'],
                ],
            ],
            [
                'title' => 'CSS: Warna dan Tata Letak', 'slug' => 'css-warna-tata-letak', 'xp_reward' => 100,
                'description' => 'Selector, box model, flexbox baris-kolom.',
                'materi' => "## Selector dan Properti\nSelector memilih elemen, properti mengatur tampilan: h1 { color: orange; }.\n\n[PRAKTIK] Ubah biodata HTML-mu: judul oranye rata tengah, paragraf abu gelap, link tebal tanpa garis bawah.\n\n## Box Model dan Flexbox\nTiap elemen punya content-padding-border-margin. Flexbox mengatur barisan: display:flex + justify-content.\n\n[TUGAS] Buat 3 kartu hobi sejajar horizontal dengan flexbox, masing-masing berisi emoji, judul, dan 1 kalimat.",
                'quizzes' => [
                    ['q' => 'Properti pengatur warna teks?', 'o' => ['background', 'margin', 'color', 'padding', 'border'], 'c' => 2, 'e' => 'color mengatur warna teks elemen.'],
                    ['q' => 'Agar 3 kartu sejajar horizontal, properti wadah yang tepat?', 'o' => ['display: block', 'display: flex', 'float: none', 'position: static', 'display: inline'], 'c' => 1, 'e' => 'Flexbox pada wadah menyusun anak-anaknya dalam baris/kolom.'],
                ],
            ],
            [
                'title' => 'Literasi Data: Grafik Jujur vs Menyesatkan', 'slug' => 'literasi-data-grafik', 'xp_reward' => 120,
                'description' => 'Sumbu terpotong, skala ganda, dan cherry-picking.',
                'materi' => "## Sumbu Terpotong\nGrafik batang yang sumbunya mulai dari 50 membuat selisih kecil terlihat raksasa. Selalu cek titik awal sumbu.\n\n[PRAKTIK] Gambar 2 diagram dari data sama (70 vs 75): sumbu mulai 0 dan sumbu mulai 60. Bandingkan kesan yang timbul.\n\n## Cherry-Picking\nMemilih rentang waktu yang menguntungkan, mis. hanya menampilkan bulan naiknya penjualan.\n\n[TUGAS] Cari 1 grafik di media sosial, tulis 3 hal yang harus dicek sebelum mempercayainya (sumbu, sumber, rentang).",
                'quizzes' => [
                    ['q' => 'Mengapa sumbu yang mulai dari 50 menyesatkan?', 'o' => ['Membuat selisih kecil tampak besar', 'Membuat grafik lebih indah', 'Mempercepat loading', 'Menambah data', 'Tidak ada efek'], 'c' => 0, 'e' => 'Skala terpotong memperbesar perbedaan visual yang sebenarnya kecil.'],
                    ['q' => 'Cherry-picking data artinya...', 'o' => ['Memilih data yang mendukung klaim sambil menyembunyikan sisanya', 'Memetik buah ceri', 'Mengacak data', 'Menghapus semua data', 'Memilih warna grafik'], 'c' => 0, 'e' => 'Hanya menampilkan potongan yang menguntungkan adalah manipulasi klasik.'],
                ],
            ],
            [
                'title' => 'Etika Plagiarisme dan Sitasi', 'slug' => 'etika-plagiarisme-sitasi', 'xp_reward' => 80,
                'description' => 'Kutip, parafrase, dan daftar pustaka sederhana.',
                'materi' => "## Kutip vs Parafrase\nKutip: salin persis + tanda kutip + sumber. Parafrase: tulis ulang dengan bahasamu + tetap cantumkan sumber.\n\n[PRAKTIK] Ambil 1 paragraf dari buku paketmu, buat versi parafrasenya dalam 2 kalimat, lalu tulis sitasi sederhananya.\n\n## Daftar Pustaka Mini\nFormat sederhana: Nama. (Tahun). Judul. Penerbit. Konsisten di seluruh tugas.\n\n[TUGAS] Buat daftar pustaka 3 sumber (buku, web, video) untuk makalah imajinasimu dengan format yang konsisten.",
                'quizzes' => [
                    ['q' => 'Menyalin teks tanpa menyebut sumber disebut...', 'o' => ['Parafrase', 'Sitasi', 'Plagiarisme', 'Referensi', 'Abstraksi'], 'c' => 2, 'e' => 'Mengakui karya orang sebagai milik sendiri adalah plagiarisme.'],
                    ['q' => 'Parafrase yang benar...', 'o' => ['Mengganti 1-2 kata saja', 'Menulis ulang dengan bahasa sendiri + tetap cantum sumber', 'Menghapus nama penulis', 'Menyalin tanpa tanda kutip', 'Menerjemahkan lalu mengaku karya sendiri'], 'c' => 1, 'e' => 'Parafrase = ide sama, kalimat baru, sumber tetap dicantumkan.'],
                ],
            ],
            [
                'title' => 'Mikrokontroler dan Sensor Sederhana', 'slug' => 'mikrokontroler-sensor', 'xp_reward' => 120,
                'description' => 'Input-proses-output, sensor cahaya, dan logika lampu otomatis.',
                'materi' => "## Pola IPO\nSemua alat pintar mengikuti Input-Proses-Output: sensor membaca, mikrokontroler memutuskan, aktuator bertindak.\n\n[PRAKTIK] Petakan 3 alat di rumahmu (kipas remote, lampu sensor, dispenser) ke tabel Input-Proses-Output.\n\n## Logika Lampu Otomatis\nJIKA cahaya < ambang MAKA nyalakan lampu. Ambang menentukan sensitivitasnya.\n\n[TUGAS] Tulis pseudocode lampu taman otomatis + 1 kondisi darurat (mis. siang hari lampu wajib mati apa pun yang terjadi).",
                'quizzes' => [
                    ['q' => 'Urutan kerja alat pintar?', 'o' => ['Output-Input-Proses', 'Input-Proses-Output', 'Proses-Output-Input', 'Acak', 'Output saja'], 'c' => 1, 'e' => 'Sensor (input) → keputusan (proses) → aksi (output).'],
                    ['q' => 'Sensor cahaya mendeteksi...', 'o' => ['Suhu ruangan', 'Tingkat terang-gelap lingkungan', 'Kelembapan', 'Suara', 'Tekanan udara'], 'c' => 1, 'e' => 'Sensor cahaya (LDR) mengukur intensitas cahaya sekitar.'],
                ],
            ],
            [
                'title' => 'Fungsi dan Modularitas Kode', 'slug' => 'fungsi-modularitas', 'xp_reward' => 100,
                'description' => 'Parameter, return value, dan kapan memecah fungsi.',
                'materi' => "## Mengapa Fungsi?\nTulis sekali, pakai berkali-kali. Perbaikan cukup di satu tempat.\n\n[PRAKTIK] Tulis fungsi sapa(nama) yang mengembalikan 'Halo, {nama}!' lalu panggil untuk 3 nama berbeda.\n\n## Parameter vs Return\nParameter = masukan fungsi. Return = keluaran untuk dipakai kode lain. print hanya menampilkan.\n\n[TUGAS] Pecah program hitung rata-rata kelas menjadi 2 fungsi: input_nilai() dan hitung_rata(list). Jelaskan pembagiannya.",
                'quizzes' => [
                    ['q' => 'Keuntungan utama fungsi?', 'o' => ['Kode bisa dipakai ulang dan mudah diperbaiki', 'Program lebih lambat', 'Wajib internet', 'Menghapus variabel', 'Tanpa manfaat'], 'c' => 0, 'e' => 'Reusabilitas dan kemudahan perawatan adalah alasan utama fungsi.'],
                    ['q' => 'Bedanya print dan return?', 'o' => ['Sama saja', 'print menampilkan ke layar, return mengirim nilai ke pemanggil', 'return menghapus fungsi', 'print lebih cepat', 'return hanya untuk angka'], 'c' => 1, 'e' => 'Nilai return bisa dipakai kode lain; print hanya tampil.'],
                ],
            ],
            [
                'title' => 'Pengenalan Pola Tanpa Komputer', 'slug' => 'pengenalan-pola-unplugged', 'xp_reward' => 80,
                'description' => 'Aktivitas unplugged: klasifikasi kartu dan pohon keputusan.',
                'materi' => "## Main Kartu Klasifikasi\nSiapkan 20 kartu gambar (hewan/buah/kendaraan). Kelompokkan dengan aturan yang kamu buat sendiri, lalu uji ke teman: bisakah ia menebak aturannya?\n\n[PRAKTIK] Buat 3 kelompok dari benda di tasku dengan 2 ciri berbeda (mis. warna dan fungsi). Tulis aturannya.\n\n## Pohon Keputusan\nSerangkaian pertanyaan ya/tidak yang mengerucut ke jawaban, seperti permainan tebak hewan.\n\n[TUGAS] Buat pohon keputusan 3 tingkat untuk menebak 4 hewan favoritmu.",
                'quizzes' => [
                    ['q' => 'Kegiatan unplugged bertujuan...', 'o' => ['Menghindari komputer rusak', 'Memahami konsep komputasi tanpa perangkat', 'Menghemat listrik', 'Bermain saja', 'Tidur di kelas'], 'c' => 1, 'e' => 'Unplugged mengajarkan konsep inti (klasifikasi, algoritma) lewat aktivitas fisik.'],
                    ['q' => 'Pohon keputusan bekerja dengan...', 'o' => ['Tebakan acak', 'Rangkaian pertanyaan ya/tidak yang mengerucut', 'Sulap', 'Undian', 'Menyalin jawaban'], 'c' => 1, 'e' => 'Tiap jawaban mempersempit kemungkinan sampai satu kesimpulan.'],
                ],
            ],
            [
                'title' => 'Kolaborasi Proyek dengan Git Sederhana', 'slug' => 'kolaborasi-git', 'xp_reward' => 100,
                'description' => 'Commit, pesan yang jelas, dan alur kerja kelompok.',
                'materi' => "## Commit Itu Titik Simpan\nTiap commit = foto progres + pesan jelas: 'Tambah bab 2 makalah', bukan 'update'.\n\n[PRAKTIK] Buat 3 commit imajiner untuk proyek kelompokmu minggu ini beserta pesannya masing-masing.\n\n## Bagi Tugas Tanpa Tabrakan\nSatu orang satu bagian, satukan di akhir, catat siapa mengerjakan apa.\n\n[TUGAS] Susun pembagian tugas 4 orang untuk video presentasi kelas beserta tenggat tiap bagian.",
                'quizzes' => [
                    ['q' => 'Pesan commit yang baik?', 'o' => ['update', 'asdfgh', 'Tambah bab 2 makalah', '...', 'final final v2'], 'c' => 2, 'e' => 'Pesan menjelaskan apa yang berubah agar riwayat mudah dibaca.'],
                    ['q' => 'Agar kerja kelompok tidak tabrakan...', 'o' => ['Semua edit file yang sama bersamaan', 'Bagi bagian jelas + satukan berkala', 'Sembunyikan progres', 'Kerjakan H-1 semua', 'Satu orang mengerjakan semua'], 'c' => 1, 'e' => 'Pembagian jelas dan integrasi rutin mencegah konflik.'],
                ],
            ],
        ];
    }
}
