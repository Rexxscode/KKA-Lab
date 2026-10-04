<div class="prose max-w-none text-sm text-slate-700 space-y-4">
    <div>
        <h4 class="font-extrabold text-slate-900">1. Alur Kerja Machine Learning</h4>
        <p>Melatih AI selalu mengikuti 4 tahap: <b>(1) Kumpulkan data</b>; <b>(2) Latih</b>; <b>(3) Uji</b> dengan data baru; <b>(4) Perbaiki</b> bila hasil kurang. Praktik di bawah halaman ini mengikuti alur persis ini.</p>
        <div class="mt-2 rounded-xl bg-emerald-50 border border-emerald-200 p-3">
            <p class="font-bold">Contoh:</p>
            <ul class="list-disc ps-5 mt-1 space-y-1">
                <li>Foto 20 botol + 20 buku → latih → uji botol baru.</li>
                <li>Belajar bab 1 → ikut ulangan → pelajari yang salah.</li>
                <li>Masak resep baru → minta keluarga mencicipi → perbaiki rasa.</li>
            </ul>
        </div>
    </div>
    <div>
        <h4 class="font-extrabold text-slate-900">2. Kelas, Contoh & Label</h4>
        <p><b>Kelas (class)</b> adalah kategori yang ingin dikenali — di sini bebas kamu tentukan. Tiap kelas butuh banyak <b>contoh</b> foto yang berbeda-beda, masing-masing ber-<b>label</b> benar dan jumlahnya <b>seimbang</b>.</p>
        <div class="mt-2 rounded-xl bg-emerald-50 border border-emerald-200 p-3">
            <p class="font-bold">Contoh pasangan kelas:</p>
            <ul class="list-disc ps-5 mt-1 space-y-1">
                <li>"Botol" vs "Buku".</li>
                <li>"Kucing" vs "Anjing".</li>
                <li>"Tangan terbuka" vs "Tangan mengepal".</li>
            </ul>
        </div>
    </div>
    <div>
        <h4 class="font-extrabold text-slate-900">3. Transfer Learning: Tidak Mulai dari Nol</h4>
        <p><b>MobileNet</b> adalah model yang sudah pintar mengenali gambar umum. Dengan <b>transfer learning</b>, kita memakai kepintarannya sebagai titik awal. Inilah alasan latihan di browser selesai dalam hitungan detik.</p>
        <div class="mt-2 rounded-xl bg-emerald-50 border border-emerald-200 p-3">
            <p class="font-bold">Contoh:</p>
            <ul class="list-disc ps-5 mt-1 space-y-1">
                <li>Bisa naik sepeda → lebih cepat belajar naik motor.</li>
                <li>Bisa Bahasa Indonesia → lebih mudah belajar Bahasa Inggris.</li>
                <li>Tukang cukur senior → cepat mengajari pegawai baru.</li>
            </ul>
        </div>
    </div>
    <div>
        <h4 class="font-extrabold text-slate-900">4. KNN: Tetangga Terdekat</h4>
        <p><b>KNN (K-Nearest Neighbors)</b> menyimpan ciri tiap contoh latihmu. Saat ada gambar baru, ia mencari contoh yang <b>paling mirip</b> lalu ikut kategorinya.</p>
        <div class="mt-2 rounded-xl bg-emerald-50 border border-emerald-200 p-3">
            <p class="font-bold">Contoh:</p>
            <ul class="list-disc ps-5 mt-1 space-y-1">
                <li>Anak baru duduk dengan teman yang hobinya mirip.</li>
                <li>Toko menyarankan baju mirip yang pernah kamu beli.</li>
                <li>YouTube menyarankan video mirip yang kamu tonton.</li>
            </ul>
        </div>
    </div>
    <div>
        <h4 class="font-extrabold text-slate-900">5. Overfitting & Evaluasi Jujur</h4>
        <p><b>Overfitting</b> = hafal data latih tapi gagal di data baru. Obatnya: contoh bervariasi + uji dengan data baru. Dan tenang: gambar webcam-mu <b>hanya diproses di browser</b>.</p>
        <div class="mt-2 rounded-xl bg-emerald-50 border border-emerald-200 p-3">
            <p class="font-bold">Contoh:</p>
            <ul class="list-disc ps-5 mt-1 space-y-1">
                <li>Hafal 10 soal, gagal saat angkanya diganti.</li>
                <li>Kenal botolmu, tapi tidak kenal botol temanmu.</li>
                <li>Hafal jalan ke sekolah, tersesat saat jalan ditutup.</li>
            </ul>
        </div>
    </div>
</div>
