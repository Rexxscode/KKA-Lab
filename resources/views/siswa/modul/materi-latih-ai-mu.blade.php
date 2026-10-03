<div class="prose max-w-none text-sm text-slate-700 space-y-4">
    <div>
        <h4 class="font-extrabold text-slate-900">1. Alur Kerja Machine Learning</h4>
        <p>Melatih AI selalu mengikuti 4 tahap: <b>(1) Kumpulkan data</b> — foto tiap benda yang ingin dikenali; <b>(2) Latih</b> — komputer mencari pola dari data; <b>(3) Uji</b> — coba model dengan data yang <b>belum pernah dilihat</b> saat latihan; <b>(4) Perbaiki</b> — tambah data dan ulangi bila hasil kurang. Praktik di bawah halaman ini mengikuti alur persis ini.</p>
    </div>
    <div>
        <h4 class="font-extrabold text-slate-900">2. Kelas, Contoh & Label</h4>
        <p><b>Kelas (class)</b> adalah kategori yang ingin dikenali — di sini bebas kamu tentukan, misalnya "Botol" vs "Buku". Tiap kelas butuh banyak <b>contoh</b> foto dari sudut, jarak, dan cahaya berbeda, masing-masing ber-<b>label</b> benar. Jumlah tiap kelas sebaiknya <b>seimbang</b> agar model tidak berat sebelah.</p>
    </div>
    <div>
        <h4 class="font-extrabold text-slate-900">3. Transfer Learning: Tidak Mulai dari Nol</h4>
        <p><b>MobileNet</b> adalah model yang sudah dilatih jutaan gambar dan paham ciri visual umum (tepi, tekstur, bentuk). Dengan <b>transfer learning</b>, kita memakai kepintarannya sebagai titik awal — kamu tinggal mengajarkan beda Botol vs Buku. Inilah alasan latihan di browser selesai dalam hitungan detik, bukan hari.</p>
    </div>
    <div>
        <h4 class="font-extrabold text-slate-900">4. KNN: Tetangga Terdekat</h4>
        <p><b>KNN (K-Nearest Neighbors)</b> bekerja sederhana: ia menyimpan ciri tiap contoh latihmu. Saat ada gambar baru, ia mencari contoh latih yang <b>paling mirip</b> lalu ikut kategorinya. Makin banyak dan beragam contohmu, makin tepat tetangga yang ia temukan.</p>
    </div>
    <div>
        <h4 class="font-extrabold text-slate-900">5. Overfitting & Evaluasi Jujur</h4>
        <p><b>Overfitting</b> = model hafal data latih tapi gagal di data baru (seperti hafal soal tapi tidak paham konsep). Obatnya: contoh bervariasi + uji dengan data baru. Nilai 60% artinya datamu perlu diperbaiki — tambah contoh, seimbangkan, latih ulang. Dan tenang: gambar webcam-mu <b>hanya diproses di browser</b>, tidak pernah dikirim ke server.</p>
    </div>
</div>
