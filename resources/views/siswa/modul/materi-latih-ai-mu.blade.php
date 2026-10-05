<div class="prose max-w-none text-sm text-slate-700 dark:text-slate-200 space-y-4">
    <div>
        <h4 class="font-extrabold text-slate-900 dark:text-white">1. Klasifikasi vs Deteksi Objek</h4>
        <p><b>Klasifikasi gambar</b> menjawab satu pertanyaan untuk seluruh foto: "ini gambar apa?". <b>Deteksi objek (object detection)</b> menjawab lebih lengkap: "benda apa saja yang ada, <b>di mana letaknya</b>, dan seberapa yakin?". Letak ditandai <b>bounding box</b> — kotak di sekeliling tiap benda yang ditemukan. Praktik di bawah halaman ini memakai deteksi objek sungguhan.</p>
        <div class="mt-2 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/30 p-3">
            <p class="font-bold">Contoh:</p>
            <ul class="list-disc ps-5 mt-1 space-y-1">
                <li>Klasifikasi: foto kelas dijawab "ruang kelas".</li>
                <li>Deteksi: foto yang sama dijawab "3 kursi, 1 meja, 2 orang" + kotak lokasinya.</li>
                <li>Parkir mal memakai deteksi untuk menghitung mobil kosong.</li>
            </ul>
        </div>
    </div>
    <div>
        <h4 class="font-extrabold text-slate-900 dark:text-white">2. Model Pretrained: Tidak Mulai dari Nol</h4>
        <p>Modul ini memakai <b>COCO-SSD</b>, model yang <b>sudah dilatih sebelumnya (pretrained)</b> dengan ratusan ribu foto. Kamu tidak perlu mengumpulkan contoh atau melatih apa pun — cukup arahkan kamera. Ini disebut <b>transfer learning</b>: memakai kepintaran model yang sudah jadi.</p>
        <div class="mt-2 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/30 p-3">
            <p class="font-bold">Contoh:</p>
            <ul class="list-disc ps-5 mt-1 space-y-1">
                <li>Bisa naik sepeda → lebih cepat belajar naik motor.</li>
                <li>Model sudah kenal jutaan foto → langsung kenal botolmu.</li>
                <li>Satpam baru yang langsung hafal denah dari senior.</li>
            </ul>
        </div>
    </div>
    <div>
        <h4 class="font-extrabold text-slate-900 dark:text-white">3. Bounding Box, Label & Confidence</h4>
        <p>Setiap temuan punya tiga info: <b>kotak (bounding box)</b> = posisi benda; <b>label</b> = nama benda dalam bahasa Indonesia; <b>confidence</b> = persen keyakinan model. Geser <b>ambang confidence</b>: temuan di bawah ambang disembunyikan agar kotak tidak bergoyang liar.</p>
        <div class="mt-2 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/30 p-3">
            <p class="font-bold">Contoh:</p>
            <ul class="list-disc ps-5 mt-1 space-y-1">
                <li>"Botol 91%" = model sangat yakin melihat botol di kotak itu.</li>
                <li>"Buku 34%" disembunyikan bila ambangmu 50%.</li>
                <li>Dua kotak menumpuk di benda yang sama = kandidat ganda, naikkan ambang.</li>
            </ul>
        </div>
    </div>
    <div>
        <h4 class="font-extrabold text-slate-900 dark:text-white">4. Batasan Kelas: Hanya 80 Benda Umum</h4>
        <p>COCO-SSD hanya mengenali <b>80 kelas umum</b> seperti botol, buku, laptop, HP, kursi, kucing, dan anjing. Ia <b>tidak bisa</b> mengenali benda custom ("botol bekal Biru milikku") atau tulisan di buku. Benda di luar daftar akan diabaikan atau salah tebak — itu batas model, bukan salahmu.</p>
        <div class="mt-2 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/30 p-3">
            <p class="font-bold">Contoh yang dikenali:</p>
            <ul class="list-disc ps-5 mt-1 space-y-1">
                <li>Botol, cangkir, buku, laptop, kibor, tetikus, HP, jam.</li>
                <li>Kursi, sofa, ransel, payung, gunting, vas.</li>
                <li>Orang, kucing, anjing, burung.</li>
            </ul>
        </div>
    </div>
    <div>
        <h4 class="font-extrabold text-slate-900 dark:text-white">5. Privasi & Evaluasi Jujur</h4>
        <p>Cahaya redup, benda jauh, atau kamera goyang membuat confidence anjlok — perbaiki posisi dulu sebelum menyalahkan model. Dan tenang: frame webcam-mu <b>hanya diproses di browser</b>, tidak dikirim ke server maupun ke Tutor AI.</p>
        <div class="mt-2 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/30 p-3">
            <p class="font-bold">Contoh:</p>
            <ul class="list-disc ps-5 mt-1 space-y-1">
                <li>Botol 91% di meja terang → 45% saat lampu dimatikan.</li>
                <li>Buku miring jauh → tidak terdeteksi; dekatkan ke kamera.</li>
                <li>Kotak bergoyang → naikkan ambang ke 70%.</li>
            </ul>
        </div>
    </div>
</div>
