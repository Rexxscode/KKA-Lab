<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>KKA Lab — Laboratorium Koding & Kecerdasan Artifisial</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script>
    try { var kkaT = localStorage.getItem('kka-theme'); if (kkaT === 'dark' || (!kkaT && matchMedia('(prefers-color-scheme: dark)').matches)) document.documentElement.classList.add('dark'); } catch (e) {}
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body style="background:var(--color-page);font-family:'Plus Jakarta Sans',sans-serif" x-data="kkaLandingCam()" x-init="boot()">
    <style>
        @keyframes kka-nav-masuk {
            from { opacity: 0; transform: translateY(-16px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .kka-nav-animasi { animation: kka-nav-masuk 500ms ease-out both; }
        .kka-nav-logo { transition: transform 200ms ease-out; }
        .kka-nav-logo:hover { transform: scale(1.04) rotate(-2deg); }
    </style>
    <nav x-data="{ sudahScroll: false }" @scroll.window="sudahScroll = window.scrollY > 24"
         class="kka-nav-animasi sticky top-0 z-40 transition-all duration-300"
         :class="sudahScroll ? 'bg-white/90 dark:bg-slate-900/90 backdrop-blur border-b border-slate-200 dark:border-slate-800 shadow-sm py-3' : 'bg-transparent border-b border-transparent py-5'">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between">
            <a href="{{ route('home') }}" class="kka-nav-logo flex items-center gap-2" aria-label="KKA Lab - Beranda">
                <span class="inline-flex items-center justify-center w-10 h-10 rounded-2xl bg-orange-500 text-white font-extrabold text-xl">K</span>
                <span><span class="block font-extrabold text-slate-900 dark:text-white dark:text-white leading-none">KKA <span class="text-orange-500">Lab</span></span><span class="block text-xs text-slate-500 dark:text-slate-400 dark:text-slate-400">Koding & Kecerdasan Artifisial</span></span>
            </a>
            <div class="flex items-center gap-2">
                <x-theme-toggle />
                <a href="{{ route('login') }}" class="kka-btn-primary !px-5 !py-2 text-sm hover:-translate-y-0.5">Masuk</a>
            </div>
        </div>
    </nav>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

    <section class="grid lg:grid-cols-2 gap-8 items-center py-10">
        <div>
            <span class="kka-badge">Untuk SMA/SMK • Kurikulum KKA • 100% Praktik di Browser</span>
            <h1 class="mt-4 text-4xl sm:text-5xl font-extrabold text-slate-900 dark:text-white dark:text-white leading-tight">Belajar AI & Koding <span class="text-orange-500">hands-on</span> di browser.</h1>
            <p class="mt-4 text-slate-600 dark:text-slate-300">6 modul interaktif: konsep AI → latih model webcam → bias data → etika & hoaks → Python dasar → data & logika AI. Privasi terjaga: gambar tidak dikirim ke server. Kuis minimal <b>80</b> untuk lulus, kumpulkan XP & badge, cetak sertifikat otomatis.</p>
            <div class="mt-6 flex flex-wrap gap-3">
                <x-button href="{{ route('login') }}">Masuk sebagai Siswa</x-button>
                <x-button variant="secondary" href="{{ route('login') }}">Portal Guru</x-button>
            </div>
            <div class="mt-6 grid grid-cols-3 gap-3 text-center">
                <div class="kka-card !p-4"><p class="kka-stat text-orange-600 dark:text-orange-400">6</p><p class="text-xs text-slate-500 dark:text-slate-400">Modul interaktif</p></div>
                <div class="kka-card !p-4"><p class="kka-stat text-emerald-600 dark:text-emerald-400">950</p><p class="text-xs text-slate-500 dark:text-slate-400">Total XP</p></div>
                <div class="kka-card !p-4"><p class="kka-stat text-slate-900 dark:text-white">90</p><p class="text-xs text-slate-500 dark:text-slate-400">Soal berpembahasan</p></div>
            </div>
        </div>
        <x-card title="Coba AI Langsung — Kamera Asli" subtitle="Aktifkan kamera, arahkan ke benda, klik Uji. AI menebak benda yang terlihat — 100% di browser.">
            <div class="rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-700 bg-slate-900 aspect-video flex items-center justify-center relative">
                <video x-ref="video" class="w-full h-full object-cover" playsinline muted></video>
                <template x-if="!cameraOn"><p class="absolute text-white/80 text-sm px-6 text-center">Klik “Aktifkan Kamera” lalu arahkan kamera ke benda di sekitarmu.</p></template>
                <span class="absolute top-3 left-3 kka-badge" x-text="status"></span>
            </div>
            <div class="mt-3 rounded-2xl bg-slate-900 text-emerald-300 p-4 font-mono text-sm min-h-16">
                <template x-if="!hasil.length"><p>Halo! Aku AI. Aktifkan kamera lalu klik Uji — aku sebutkan benda yang kulihat.</p></template>
                <template x-for="(h, i) in hasil" :key="i"><p x-text="(i+1) + '. ' + h"></p></template>
            </div>
            <div class="mt-3 flex flex-wrap gap-2">
                <button class="kka-btn-primary !py-2 text-sm" @click="toggleKamera()" x-text="cameraOn ? 'Matikan Kamera' : 'Aktifkan Kamera'"></button>
                <button class="kka-btn-secondary !py-2 text-sm" @click="uji()" x-text="sibuk ? 'Menganalisis...' : 'Uji'"></button>
            </div>
            <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">Butuh internet sekali untuk memuat model + izin kamera. Versi deteksi objek ada di Modul 2 setelah daftar.</p>
        </x-card>
    </section>

    <section class="py-8">
        <h2 class="text-2xl font-extrabold text-slate-900 dark:text-white">{{ $modules->count() ?: 6 }} Modul KKA</h2>
        <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">Dari konsep → praktik kamera → data kritis → etika → Python untuk AI.</p>
        <div class="mt-5 grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @forelse($modules as $m)
                <x-card :title="$m->title" :subtitle="$m->description">
                    <div class="flex items-center justify-between">
                        <span class="kka-badge">+{{ $m->xp_reward }} XP</span>
                        <a href="{{ route('login') }}" class="text-sm font-bold text-orange-500 hover:text-orange-600">Mulai →</a>
                    </div>
                </x-card>
            @empty
                <x-card title="Modul 1: Apa itu AI?" subtitle="Konsep dasar AI & ML dengan visual interaktif + kuis." />
                <x-card title="Modul 2: Latih AI-mu" subtitle="Deteksi objek real-time via webcam (COCO-SSD pretrained) + bounding box." />
                <x-card title="Modul 3: Bias Data" subtitle="Simulasi dataset tidak seimbang dengan Alpine.js." />
                <x-card title="Modul 4: Etika & Hoaks AI" subtitle="Studi kasus deepfake & kuis literasi digital." />
                <x-card title="Modul 5: Python Dasar untuk AI" subtitle="Python asli di browser: print, list, loop, fungsi, dict." />
                <x-card title="Modul 6: Data & Logika AI" subtitle="Rata-rata, filter data, dan mini-classifier aturan." />
            @endforelse
        </div>
    </section>

    <section class="py-8 grid lg:grid-cols-3 gap-4">
        <x-card title="Untuk Siswa" subtitle="Gabung kelas dengan 6 digit kode, kerjakan modul, kumpulkan badge.">
            <x-button href="{{ route('register') }}" class="w-full">Daftar sebagai Siswa</x-button>
        </x-card>
        <x-card title="Untuk Guru" subtitle="Buat kelas, generate kode unik, pantau progres & ekspor CSV nilai.">
            <x-button href="{{ route('login') }}" class="w-full">Masuk Portal Guru</x-button>
        </x-card>
        <x-card title="Sertifikat" subtitle="Otomatis terbit jika 6 modul lulus (skor ≥ 80). Siap cetak PDF via browser.">
            <span class="kka-badge">Syarat: 6/6 lulus • ≥80</span>
        </x-card>
    </section>

    <section class="py-8">
        <h2 class="text-2xl font-extrabold text-slate-900 dark:text-white">Cara mulai</h2>
        <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">Empat langkah, tanpa instal apa pun. Semua jalan di browser HP atau laptop.</p>
        <div class="mt-5 grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <x-card title="1. Daftar" subtitle="Buat akun siswa gratis dengan email."><p class="text-xs text-slate-500 dark:text-slate-400">Satu menit, langsung masuk dasbor.</p></x-card>
            <x-card title="2. Gabung kelas" subtitle="Minta kode 6 digit ke gurumu."><p class="text-xs text-slate-500 dark:text-slate-400">Urutan modul dan soal mengikuti kelasmu.</p></x-card>
            <x-card title="3. Kerjakan berurutan" subtitle="Baca bahan, praktik, lalu kuis."><p class="text-xs text-slate-500 dark:text-slate-400">Soal dan opsi diacak setiap percobaan.</p></x-card>
            <x-card title="4. Lulus & cetak" subtitle="Skor ≥80 di 6 modul."><p class="text-xs text-slate-500 dark:text-slate-400">Sertifikat PDF terbit otomatis.</p></x-card>
        </div>
    </section>

    <section class="py-4">
        <div class="rounded-3xl bg-slate-900 p-8 sm:p-10 text-white overflow-hidden">
            <div class="flex flex-wrap items-end justify-between gap-6">
                <div class="max-w-xl">
                    <p class="text-xs font-bold tracking-widest text-emerald-400">6 MODUL • 90 SOAL • 950 XP</p>
                    <h2 class="mt-2 text-2xl sm:text-3xl font-extrabold">Selesaikan 6 modul, bawa pulang sertifikat.</h2>
                    <p class="mt-2 text-sm sm:text-base text-slate-300">Mulai dari konsep AI, latih model pakai webcam, sampai nulis Python asli di browser. Kuis lulus di skor 80.</p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('register') }}" class="inline-block px-7 py-3 rounded-2xl bg-orange-500 hover:bg-orange-600 text-white font-extrabold text-sm transition">Daftar gratis</a>
                    <a href="{{ route('login') }}" class="inline-block px-7 py-3 rounded-2xl border border-slate-600 text-slate-200 font-bold text-sm hover:border-slate-400 transition">Masuk</a>
                </div>
            </div>
        </div>
    </section>

    <section class="py-8">
        <h2 class="text-2xl font-extrabold text-slate-900 dark:text-white text-center">Kontak Kami</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400 text-center mt-1">Punya pertanyaan, saran, atau ingin bekerja sama? Hubungi tim KKA Lab.</p>
        <div class="mt-5 grid sm:grid-cols-3 gap-4">
            <x-card title="Email" subtitle="halo@kkalab.sch.id"><p class="text-xs text-slate-500 dark:text-slate-400 flex items-center gap-1.5"><x-icon name="mail" class="w-4 h-4 text-orange-500" />Kami balas dalam 1–2 hari kerja.</p></x-card>
            <x-card title="Lokasi" subtitle="Indonesia"><p class="text-xs text-slate-500 dark:text-slate-400 flex items-center gap-1.5"><x-icon name="pin" class="w-4 h-4 text-orange-500" />Untuk SMA/SMK di seluruh Indonesia.</p></x-card>
            <x-card title="Respon" subtitle="Cepat & ramah"><p class="text-xs text-slate-500 dark:text-slate-400 flex items-center gap-1.5"><x-icon name="chat" class="w-4 h-4 text-orange-500" />Siswa, guru, dan sekolah dipersilakan bertanya.</p></x-card>
        </div>
    </section>
</div>

<footer class="border-t border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 grid gap-8 sm:grid-cols-2 lg:grid-cols-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-orange-500 text-white font-extrabold">K</span>
                <p class="font-extrabold text-slate-900 dark:text-white">KKA <span class="text-orange-500">Lab</span></p>
            </div>
            <p class="mt-3 text-xs text-slate-500 dark:text-slate-400">Laboratorium Koding & Kecerdasan Artifisial untuk pembelajaran AI di sekolah.</p>
        </div>
        <div>
            <p class="font-bold text-sm text-slate-900 dark:text-white">Modul</p>
            <ul class="mt-3 space-y-2 text-xs text-slate-500 dark:text-slate-400">
                <li><a href="{{ route('login') }}" class="hover:text-orange-600">Apa itu AI?</a></li>
                <li><a href="{{ route('login') }}" class="hover:text-orange-600">Latih AI-mu</a></li>
                <li><a href="{{ route('login') }}" class="hover:text-orange-600">Bias Data</a></li>
                <li><a href="{{ route('login') }}" class="hover:text-orange-600">Etika & Hoaks AI</a></li>
                <li><a href="{{ route('login') }}" class="hover:text-orange-600">Python Dasar untuk AI</a></li>
                <li><a href="{{ route('login') }}" class="hover:text-orange-600">Data & Logika AI</a></li>
            </ul>
        </div>
        <div>
            <p class="font-bold text-sm text-slate-900 dark:text-white">Akun</p>
            <ul class="mt-3 space-y-2 text-xs text-slate-500 dark:text-slate-400">
                <li><a href="{{ route('login') }}" class="hover:text-orange-600">Masuk</a></li>
                <li><a href="{{ route('register') }}" class="hover:text-orange-600">Daftar sebagai Siswa</a></li>
                <li><a href="{{ route('login') }}" class="hover:text-orange-600">Portal Guru</a></li>
            </ul>
        </div>
        <div>
            <p class="font-bold text-sm text-slate-900 dark:text-white">Kontak</p>
            <ul class="mt-3 space-y-2 text-xs text-slate-500 dark:text-slate-400">
                <li>halo@kkalab.sch.id</li>
                <li>Indonesia</li>
            </ul>
        </div>
    </div>
    <div class="border-t border-slate-100 dark:border-slate-800">
        <p class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 text-center text-xs text-slate-500 dark:text-slate-400">KKA Lab • Untuk pembelajaran AI di sekolah</p>
    </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs@4.9.0/dist/tf.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@tensorflow-models/mobilenet@2.1.1/dist/mobilenet.min.js"></script>
<script>
function kkaLandingCam() {
    const ID = [
        ['computer mouse', 'Tetikus Komputer'], ['mouse', 'Tikus'], ['keyboard', 'Kibor'], ['laptop', 'Laptop'],
        ['monitor', 'Monitor'], ['screen', 'Layar'], ['cellular telephone', 'HP'], ['cell phone', 'HP'], ['iphone', 'HP'],
        ['book', 'Buku'], ['notebook', 'Buku Catatan'], ['pencil', 'Pensil'], ['pen', 'Pulpen'], ['chair', 'Kursi'],
        ['table', 'Meja'], ['desk', 'Meja'], ['bottle', 'Botol'], ['water bottle', 'Botol Air'], ['cup', 'Cangkir'],
        ['mug', 'Mug'], ['cat', 'Kucing'], ['dog', 'Anjing'], ['bird', 'Burung'], ['fish', 'Ikan'],
        ['person', 'Orang'], ['face', 'Wajah'], ['hand', 'Tangan'], ['eye', 'Mata'], ['glasses', 'Kacamata'], ['hat', 'Topi'],
        ['shirt', 'Kaus'], ['shoe', 'Sepatu'], ['bag', 'Tas'], ['backpack', 'Ransel'], ['umbrella', 'Payung'],
        ['clock', 'Jam'], ['watch', 'Jam Tangan'], ['banana', 'Pisang'], ['apple', 'Apel'], ['orange', 'Jeruk'],
        ['car', 'Mobil'], ['bicycle', 'Sepeda'], ['motor', 'Motor'], ['bus', 'Bus'], ['truck', 'Truk'],
        ['tree', 'Pohon'], ['flower', 'Bunga'], ['ball', 'Bola'], ['guitar', 'Gitar'], ['television', 'TV'],
        ['remote', 'Remot'], ['sofa', 'Sofa'], ['bed', 'Kasur'], ['lamp', 'Lampu'], ['door', 'Pintu'], ['window', 'Jendela'],
    ];
    function idLabel(name) {
        const n = name.toLowerCase();
        for (const [en, id] of ID) { if (n.includes(en)) return id + ' (' + name.split(',')[0].trim() + ')'; }
        return name.split(',')[0].trim();
    }
    // Model + canvas disimpan di variabel closure biasa (BUKAN reactive
    // data): objek yang lewat proxy reaktif Alpine merusak identity
    // tensor (WeakMap engine tfjs) sehingga inferensi selalu gagal.
    let rawNet = null;
    let rawSnap = null;
    return {
        status: 'Memuat model...', cameraOn: false, hasil: [], stream: null, sibuk: false,
        // "boot" (bukan "init"): Alpine otomatis memanggil method bernama
        // init(), jadi penamaan init + x-init menyebabkan double-init.
        async boot() {
            // Singleton: inisialisasi Alpine yang kedua memakai ulang
            // promise model yang sama, tidak me-load ulang.
            if (!window.__kkaReady) {
                window.__kkaReady = (async () => {
                    if (window.tf) { await tf.ready(); }
                    const net = await mobilenet.load();
                    // Tes mandiri: pastikan seluruh jalur graph benar-benar jalan
                    const g = new Int32Array(224 * 224 * 3).fill(128);
                    const t0 = tf.tensor3d(g, [224, 224, 3], 'int32');
                    try { await net.classify(t0, 1); } finally { t0.dispose(); }
                    return net;
                })();
            }
            try {
                rawNet = await window.__kkaReady;
                rawSnap = document.createElement('canvas');
                this.status = 'Model siap — aktifkan kamera';
            } catch (e) {
                console.error(e);
                window.__kkaReady = null;
                this.status = 'Model belum siap (' + String((e && e.message) || e).slice(0, 80) + '). Muat ulang halaman ya!';
            }
        },
        // Klasifikasi dengan retry: kegagalan transien engine sembuh
        // dengan percobaan ulang memakai tensor baru.
        async classifyRetry(makeTensor, topK) {
            let lastErr = null;
            for (let a = 0; a < 3; a++) {
                const t = makeTensor();
                try {
                    const preds = await rawNet.classify(t, topK);
                    t.dispose();
                    return preds;
                } catch (e) { lastErr = e; try { t.dispose(); } catch (_) {} }
                await new Promise(r => setTimeout(r, 400));
            }
            throw lastErr;
        },
        async toggleKamera() {
            if (this.cameraOn) {
                if (this.stream) this.stream.getTracks().forEach(t => t.stop());
                this.$refs.video.srcObject = null;
                this.cameraOn = false; this.hasil = [];
                this.status = rawNet ? 'Model siap — aktifkan kamera' : 'Memuat model...';
                return;
            }
            try {
                this.stream = await navigator.mediaDevices.getUserMedia({ video: true });
                this.$refs.video.srcObject = this.stream;
                await this.$refs.video.play();
                this.cameraOn = true; this.status = 'Kamera menyala — arahkan ke benda lalu klik Uji';
            } catch (e) { this.status = 'Kamera ditolak — izinkan akses kamera lalu coba lagi'; }
        },
        async uji() {
            if (!rawNet) { this.hasil = ['Model belum termuat, tunggu tulisan “Model siap” lalu coba lagi!']; return; }
            if (!this.cameraOn) { this.hasil = ['Aktifkan kamera dulu ya!']; return; }
            if (this.sibuk) return;
            const v = this.$refs.video;
            let tunggu = 0;
            while ((v.readyState < 2 || v.videoWidth === 0) && tunggu < 20) {
                await new Promise(r => setTimeout(r, 200));
                tunggu++;
            }
            if (v.videoWidth === 0) {
                this.hasil = ['Kamera belum menampilkan gambar — tunggu video muncul lalu klik Uji lagi!'];
                return;
            }
            this.sibuk = true; this.status = 'Menganalisis gambar...';
            try {
                // Potong tengah jadi bujur sangkar (hindari distorsi gepeng
                // yang menurunkan akurasi), kecilkan ke 224x224, baca piksel
                // manual menjadi tensor CPU.
                const vw = v.videoWidth, vh = v.videoHeight, S = 224;
                const side = Math.min(vw, vh), sx = (vw - side) / 2, sy = (vh - side) / 2;
                rawSnap.width = S; rawSnap.height = S;
                const ctx = rawSnap.getContext('2d', { willReadFrequently: true });
                ctx.drawImage(v, sx, sy, side, side, 0, 0, S, S);
                const d = ctx.getImageData(0, 0, S, S).data;
                const n = S * S, vals = new Int32Array(n * 3);
                for (let i = 0; i < n; i++) {
                    vals[i * 3] = d[i * 4]; vals[i * 3 + 1] = d[i * 4 + 1]; vals[i * 3 + 2] = d[i * 4 + 2];
                }
                const preds = await this.classifyRetry(() => tf.tensor3d(vals, [S, S, 3], 'int32'), 1);
                const top = preds[0];
                const pct = Math.round((top.probability || 0) * 100);
                if (pct >= 50) {
                    this.hasil = ['Hasil: ' + idLabel(top.className) + ' — ' + pct + '% yakin.'];
                } else {
                    this.hasil = ['Kurang yakin (terbaik: ' + idLabel(top.className) + ' — ' + pct + '%). Dekatkan benda ke kamera, perbaiki cahaya, lalu Uji lagi!'];
                }
                this.status = 'Kamera menyala — arahkan ke benda lalu klik Uji';
            } catch (e) {
                console.error(e);
                this.hasil = ['Gagal menganalisis (' + String((e && e.message) || e).slice(0, 100) + '). Coba klik Uji lagi!'];
            }
            this.sibuk = false;
        }
    }
}
</script>
</body>
</html>


