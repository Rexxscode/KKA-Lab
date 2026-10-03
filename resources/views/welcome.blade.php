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
    return {
        status: 'Memuat model...', cameraOn: false, hasil: [], net: null, stream: null, sibuk: false,
        async init() {
            try {
                if (window.tf) {
                    try { await tf.setBackend('webgl'); } catch (_) { await tf.setBackend('cpu'); }
                    await tf.ready();
                }
                this.net = await mobilenet.load();
                this.status = 'Model siap — aktifkan kamera';
            } catch (e) {
                console.error(e);
                this.status = 'Gagal memuat model (butuh internet). Muat ulang halaman ya!';
            }
        },
        async toggleKamera() {
            if (this.cameraOn) {
                if (this.stream) this.stream.getTracks().forEach(t => t.stop());
                this.$refs.video.srcObject = null;
                this.cameraOn = false; this.hasil = [];
                this.status = this.net ? 'Model siap — aktifkan kamera' : 'Memuat model...';
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
            if (!this.net) { this.hasil = ['Model belum termuat, tunggu tulisan “Model siap” lalu coba lagi!']; return; }
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
                const preds = await this.net.classify(v, 3);
                this.hasil = preds.map(p => idLabel(p.className) + ' — ' + Math.round(p.probability * 100) + '%');
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
