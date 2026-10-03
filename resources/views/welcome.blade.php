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
<body style="background:#FAFAFA;font-family:'Plus Jakarta Sans',sans-serif" x-data="kkaLandingCam()" x-init="init()">
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
            <p class="mt-2 text-xs text-slate-400">Akun guru dibuatkan oleh admin — siswa bisa daftar gratis.</p>
            <div class="mt-6 grid grid-cols-3 gap-3 text-center">
                <div class="kka-card !p-4"><p class="text-2xl font-extrabold text-orange-500">4</p><p class="text-xs text-slate-500">Modul interaktif</p></div>
                <div class="kka-card !p-4"><p class="text-2xl font-extrabold text-emerald-500">600</p><p class="text-xs text-slate-500">Total XP</p></div>
                <div class="kka-card !p-4"><p class="text-2xl font-extrabold text-slate-900">PDF</p><p class="text-xs text-slate-500">Sertifikat otomatis</p></div>
            </div>
        </div>
        <x-card title="Coba AI Langsung — Kamera Asli" subtitle="Latih 3 kelas lewat webcam, lalu Uji / Pantau live. 100% di browser, gambar tidak dikirim ke server.">
            <div class="rounded-2xl overflow-hidden border border-slate-200 bg-slate-900 aspect-video flex items-center justify-center relative">
                <video x-ref="video" class="w-full h-full object-cover" playsinline muted></video>
                <template x-if="!cameraOn"><p class="absolute text-white/80 text-sm px-6 text-center">Klik “Aktifkan Kamera” dulu, lalu latih tiap kelas dengan contoh gambar.</p></template>
                <span class="absolute top-3 left-3 kka-badge" x-text="status"></span>
            </div>
            <div class="mt-3 rounded-2xl bg-slate-900 text-emerald-300 p-4 font-mono text-sm min-h-16" x-text="prediction || 'Halo! Aku AI. Tunjukkan gambar ke kamera, aku belajar mengenalinya.'"></div>
            <div class="mt-3 flex flex-wrap gap-2">
                <button class="kka-btn-primary !py-2 text-sm" @click="startCamera()">Aktifkan Kamera</button>
                <button class="kka-btn-secondary !py-2 text-sm" @click="addExample(0)">+ Kelas A 🐱 (<span x-text="counts[0]"></span>)</button>
                <button class="kka-btn-secondary !py-2 text-sm" @click="addExample(1)">+ Kelas B ♻️ (<span x-text="counts[1]"></span>)</button>
                <button class="kka-btn-secondary !py-2 text-sm" @click="addExample(2)">+ Kelas C ✋ (<span x-text="counts[2]"></span>)</button>
            </div>
            <div class="mt-2 flex flex-wrap gap-2">
                <button class="kka-btn-primary !py-2 text-sm" @click="predict()">Uji Sekali</button>
                <button class="kka-btn-secondary !py-2 text-sm" @click="toggleWatch()" x-text="watching ? 'Stop Pantau' : 'Pantau Live'"></button>
                <button class="kka-btn-secondary !py-2 text-sm" @click="reset()">Reset</button>
            </div>
            <p class="mt-2 text-[11px] text-slate-400">Butuh internet sekali untuk memuat model + izin kamera. Versi lengkap (simpan XP & badge) ada di Modul 2 setelah daftar.</p>
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
        <x-card title="Untuk Guru" subtitle="Akun dibuatkan admin. Buat kelas, generate kode unik, pantau progres & ekspor CSV nilai.">
            <x-button href="{{ route('login') }}" class="w-full">Masuk Portal Guru</x-button>
        </x-card>
        <x-card title="Sertifikat" subtitle="Otomatis terbit jika 4 modul lulus (skor ≥ 80). Siap cetak PDF via browser.">
            <span class="kka-badge">Syarat: 4/4 lulus • ≥80</span>
        </x-card>
    </section>

    <footer class="py-8 text-center text-xs text-slate-400">KKA Lab • Dibangun dengan Laravel Blade + Alpine.js + Tailwind + TensorFlow.js (client-side) • Untuk pembelajaran AI di sekolah</footer>
</div>
<script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs@4.22.0/dist/tf.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@tensorflow-models/mobilenet@2.1.1/dist/mobilenet.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@tensorflow-models/knn-classifier@1.2.6/dist/knn-classifier.min.js"></script>
<script>
function kkaLandingCam() {
    return {
        status: 'Memuat model...', cameraOn: false, counts: [0, 0, 0],
        prediction: '', net: null, classifier: null, watching: false, watchTimer: null,
        async init() {
            try {
                this.net = await mobilenet.load();
                this.classifier = knnClassifier.create();
                this.status = 'Model siap — aktifkan kamera';
            } catch (e) { this.status = 'Gagal memuat model (butuh internet sekali)'; }
        },
        async startCamera() {
            try {
                const s = await navigator.mediaDevices.getUserMedia({ video: true });
                this.$refs.video.srcObject = s;
                await this.$refs.video.play();
                this.cameraOn = true; this.status = 'Kamera aktif — latih tiap kelas!';
            } catch (e) { this.status = 'Kamera ditolak — izinkan akses kamera lalu coba lagi'; }
        },
        async addExample(cls) {
            if (!this.net) return;
            if (!this.cameraOn) { this.prediction = 'Aktifkan kamera dulu ya!'; return; }
            const img = tf.browser.fromPixels(this.$refs.video);
            const act = this.net.infer(img, true);
            this.classifier.addExample(act, cls);
            this.counts[cls]++; img.dispose(); act.dispose();
            this.prediction = 'Hebat! Kelas ' + ['A 🐱', 'B ♻️', 'C ✋'][cls] + ' sekarang punya ' + this.counts[cls] + ' contoh. Tambah lagi biar makin pintar!';
        },
        async predict() {
            if (!this.net || !this.classifier || this.classifier.getNumClasses() === 0) {
                this.prediction = 'Latih dulu minimal 2 kelas ya (klik + Kelas A/B/C beberapa kali).';
                return;
            }
            if (!this.cameraOn) { this.prediction = 'Aktifkan kamera dulu ya!'; return; }
            const img = tf.browser.fromPixels(this.$refs.video);
            const act = this.net.infer(img, true);
            const res = await this.classifier.predictClass(act);
            const label = ['Kelas A 🐱', 'Kelas B ♻️', 'Kelas C ✋'][res.label] ?? ('Kelas ' + res.label);
            this.prediction = 'Model menebak: ' + label + ' (' + Math.round((res.confidences[res.label] || 0) * 100) + '%). Semua dihitung di browser-mu!';
            img.dispose(); act.dispose();
        },
        toggleWatch() {
            if (this.watching) {
                this.watching = false; clearInterval(this.watchTimer); this.status = 'Kamera aktif — latih tiap kelas!';
            } else { this.watching = true; this.status = 'Memantau live...'; this.watchTimer = setInterval(() => this.predict(), 800); }
        },
        reset() {
            if (this.classifier) this.classifier = knnClassifier.create();
            this.counts = [0, 0, 0]; this.prediction = 'Model di-reset. Yuk latih ulang!';
            if (this.watching) this.toggleWatch();
        }
    }
}
</script>
</body>
</html>
