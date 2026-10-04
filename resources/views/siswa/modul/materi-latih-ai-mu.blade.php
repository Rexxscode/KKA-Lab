<div class="prose max-w-none text-sm text-slate-700 space-y-4">
    <div>
        <h4 class="font-extrabold text-slate-900">1. Alur Kerja Machine Learning</h4>
        <p>Melatih AI selalu mengikuti 4 tahap: <b>(1) Kumpulkan data</b>; <b>(2) Latih</b> — komputer mencari pola; <b>(3) Uji</b> — coba dengan data yang <b>belum pernah dilihat</b> saat latihan; <b>(4) Perbaiki</b> — tambah data dan ulangi bila hasil kurang. Praktik di bawah halaman ini mengikuti alur persis ini.</p>
        <p class="mt-2 rounded-xl bg-emerald-50 border border-emerald-200 p-3"><b>Contoh alur:</b> (1) foto 20 botol + 20 buku → latih → uji dengan botol baru → tambah foto bila salah; (2) 100 soal latihan → ikut try out sebagai ujian → pelajari yang salah; (3) resep dicoba → dicicipi orang lain → diperbaiki bumbunya.</p>
    </div>
    <div>
        <h4 class="font-extrabold text-slate-900">2. Kelas, Contoh & Label</h4>
        <p><b>Kelas (class)</b> adalah kategori yang ingin dikenali — di sini bebas kamu tentukan. Tiap kelas butuh banyak <b>contoh</b> dari sudut, jarak, dan cahaya berbeda, masing-masing ber-<b>label</b> benar. Jumlah tiap kelas sebaiknya <b>seimbang</b> agar model tidak berat sebelah.</p>
        <p class="mt-2 rounded-xl bg-emerald-50 border border-emerald-200 p-3"><b>Contoh pasangan kelas:</b> (1) "Botol" vs "Buku"; (2) "Tangan terbuka" vs "Tangan mengepal"; (3) "Masker dipakai" vs "Masker tidak dipakai". <b>Contoh variasi foto:</b> dekat, jauh, dan miring — pagi, siang, dan lampu redup.</p>
    </div>
    <div>
        <h4 class="font-extrabold text-slate-900">3. Transfer Learning: Tidak Mulai dari Nol</h4>
        <p><b>MobileNet</b> adalah model yang sudah dilatih jutaan gambar dan paham ciri visual umum (tepi, tekstur, bentuk). Dengan <b>transfer learning</b>, kita memakai kepintarannya sebagai titik awal. Inilah alasan latihan di browser selesai dalam hitungan detik, bukan hari.</p>
        <p class="mt-2 rounded-xl bg-emerald-50 border border-emerald-200 p-3"><b>Contoh:</b> (1) koki magang yang sudah bisa memotong — tinggal diajari resep restoran; (2) supir mobil yang belajar menyetir truk — dasarnya sudah ada; (3) kamu yang bisa Bahasa Indonesia lalu belajar bahasa pemrograman — logikanya menular.</p>
    </div>
    <div>
        <h4 class="font-extrabold text-slate-900">4. KNN: Tetangga Terdekat</h4>
        <p><b>KNN (K-Nearest Neighbors)</b> menyimpan ciri tiap contoh latihmu. Saat ada gambar baru, ia mencari contoh latih yang <b>paling mirip</b> lalu ikut kategorinya. Makin banyak dan beragam contohmu, makin tepat tetangga yang ia temukan.</p>
        <p class="mt-2 rounded-xl bg-emerald-50 border border-emerald-200 p-3"><b>Contoh:</b> (1) anak baru langsung akrab dengan teman sebangku yang paling mirip hobinya; (2) toko menyarankan baju mirip yang pernah kamu beli; (3) aplikasi musik memutar lagu mirip yang sering kamu dengar.</p>
    </div>
    <div>
        <h4 class="font-extrabold text-slate-900">5. Overfitting & Evaluasi Jujur</h4>
        <p><b>Overfitting</b> = model hafal data latih tapi gagal di data baru (seperti hafal soal tapi tidak paham konsep). Obatnya: contoh bervariasi + uji dengan data baru. Dan tenang: gambar webcam-mu <b>hanya diproses di browser</b>, tidak pernah dikirim ke server.</p>
        <p class="mt-2 rounded-xl bg-emerald-50 border border-emerald-200 p-3"><b>Contoh overfitting:</b> (1) hafal 10 soal latihan, gagal saat angkanya diganti; (2) model kenal botolmu tapi tidak kenal botol temanmu; (3) hafal rute ke sekolah tapi tersesat saat jalan ditutup. <b>Contoh evaluasi jujur:</b> nilai 60% artinya tambah contoh lalu latih ulang.</p>
    </div>
</div>
