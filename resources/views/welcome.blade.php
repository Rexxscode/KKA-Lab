<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>KKA Lab — Laboratorium Koding & Kecerdasan Artifisial</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body style="background:#FAFAFA;font-family:'Plus Jakarta Sans',sans-serif" x-data="kkaLandingCam()" x-init="boot()">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <nav class="flex items-center justify-between py-5">
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center justify-center w-10 h-10 rounded-2xl bg-orange-500 text-white font-extrabold text-xl">K</span>
            <div><p class="font-extrabold text-slate-900 leading-none">KKA <span class="text-orange-500">Lab</span></p><p class="text-xs text-slate-500">Koding & Kecerdasan Artifisial</p></div>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('login') }}" class="text-sm font-semibold text-slate-600 hover:text-orange-600">Masuk</a>
            <a href="{{ route('register') }}" class="kka-btn-primary !px-4 !py-2 text-sm">Mulai Gratis</a>
        </div>
    </nav>

    <section class="grid lg:grid-cols-2 gap-8 items-center py-10">
        <div>
            <span class="kka-badge">Untuk SMA/SMK • Kurikulum KKA • 100% Praktik di Browser</span>
            <h1 class="mt-4 text-4xl sm:text-5xl font-extrabold text-slate-900 leading-tight">Belajar AI & Koding <span class="text-orange-500">hands-on</span> di browser.</h1>
            <p class="mt-4 text-slate-600">4 modul interaktif: apa itu AI → latih model webcam → bias data → etika & hoaks. Privasi terjaga: gambar tidak dikirim ke server. Kuis minimal <b>80</b> untuk lulus, kumpulkan XP & badge, cetak sertifikat otomatis.</p>
            <div class="mt-6 flex flex-wrap gap-3">
                <x-button href="{{ route('register') }}">Masuk sebagai Siswa</x-button>
                <x-button variant="secondary" href="{{ route('login') }}">Portal Guru</x-button>
            </div>
            <div class="mt-6 grid grid-cols-3 gap-3 text-center">
                <div class="kka-card !p-4"><p class="text-2xl font-extrabold text-orange-500">4</p><p class="text-xs text-slate-500">Modul interaktif</p></div>
                <div class="kka-card !p-4"><p class="text-2xl font-extrabold text-emerald-500">600</p><p class="text-xs text-slate-500">Total XP</p></div>
                <div class="kka-card !p-4"><p class="text-2xl font-extrabold text-slate-900">PDF</p><p class="text-xs text-slate-500">Sertifikat otomatis</p></div>
            </div>
        </div>
        <x-card title="Coba AI Langsung — Kamera Asli" subtitle="Aktifkan kamera, arahkan ke benda, klik Uji. AI menebak benda yang terlihat — 100% di browser.">
            <div class="rounded-2xl overflow-hidden border border-slate-200 bg-slate-900 aspect-video flex items-center justify-center relative">
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
            <p class="mt-2 text-[11px] text-slate-400">Butuh internet sekali untuk memuat model + izin kamera. Versi latihan sendiri (KNN) ada di Modul 2 setelah daftar.</p>
        </x-card>
    </section>

    <section class="py-8">
        <h2 class="text-2xl font-extrabold text-slate-900">4 Modul KKA</h2>
        <p class="text-slate-500 text-sm mt-1">Urut dari konsep → praktik → kritis → etis.</p>
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
                <x-card title="Modul 2: Latih AI-mu" subtitle="Klasifikasi gambar via webcam (KNN lokal) + mode tanpa kamera." />
                <x-card title="Modul 3: Bias Data" subtitle="Simulasi dataset tidak seimbang dengan Alpine.js." />
                <x-card title="Modul 4: Etika & Hoaks AI" subtitle="Studi kasus deepfake & kuis literasi digital." />
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
        <x-card title="Sertifikat" subtitle="Otomatis terbit jika 4 modul lulus (skor ≥ 80). Siap cetak PDF via browser.">
            <span class="kka-badge">Syarat: 4/4 lulus • ≥80</span>
        </x-card>
    </section>

    <footer class="py-8 text-center text-xs text-slate-400">KKA Lab • Dibangun dengan Laravel Blade + Alpine.js + Tailwind + TensorFlow.js (client-side) • Untuk pembelajaran AI di sekolah</footer>
</div>
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


