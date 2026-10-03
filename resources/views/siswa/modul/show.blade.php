<x-app-layout>
    <x-slot name="header">
        <a href="{{ route('siswa.dashboard') }}" class="text-sm font-semibold text-orange-500">← Kembali</a>
        <h2 class="font-extrabold text-xl text-slate-900 mt-1">{{ $module->title }}</h2>
        <p class="text-sm text-slate-500">{{ $module->description }} • +{{ $module->xp_reward }} XP • Lulus jika kuis ≥80</p>
    </x-slot>

    <div class="py-8 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        @if($module->slug === 'apa-itu-ai')
            <x-card title="Apa itu AI?" subtitle="AI = komputer yang belajar dari data. Machine Learning = cara belajarnya.">
                <div class="grid sm:grid-cols-3 gap-3" x-data="{ tab: 'ml' }">
                    <div class="sm:col-span-1 flex sm:flex-col gap-2">
                        <button @click="tab='ml'" :class="tab==='ml' ? 'bg-orange-500 text-white' : 'bg-white text-slate-600'" class="px-4 py-2 rounded-xl border border-slate-200 text-sm font-bold">Machine Learning</button>
                        <button @click="tab='contoh'" :class="tab==='contoh' ? 'bg-orange-500 text-white' : 'bg-white text-slate-600'" class="px-4 py-2 rounded-xl border border-slate-200 text-sm font-bold">Contoh Sehari-hari</button>
                        <button @click="tab='cara'" :class="tab==='cara' ? 'bg-orange-500 text-white' : 'bg-white text-slate-600'" class="px-4 py-2 rounded-xl border border-slate-200 text-sm font-bold">Cara Kerja</button>
                    </div>
                    <div class="sm:col-span-2 rounded-2xl bg-slate-50 border border-slate-200 p-5 text-sm text-slate-700 min-h-40">
                        <template x-if="tab==='ml'"><p><b>Machine Learning:</b> 1) Kumpulkan data → 2) Latih model → 3) Uji dengan data baru → 4) Perbaiki. Semakin beragam datanya, semakin pintar modelnya.</p></template>
                        <template x-if="tab==='contoh'"><p><b>Contoh:</b> filter spam, rekomendasi video, deteksi plagiarisme, chatbot belajar, pengenalan wajah untuk absensi.</p></template>
                        <template x-if="tab==='cara'"><p><b>Cara kerja:</b> gambar diubah jadi angka (fitur) → model mencari pola → menebak kelas paling mirip. Di Modul 2 kamu praktik langsung!</p></template>
                    </div>
                </div>
            </x-card>
        @endif

        @if($module->slug === 'latih-ai-mu')
            <x-card title="Latih AI-mu (100% di browser, privasi aman)" subtitle="Aktifkan kamera, arahkan ke benda, klik Uji. Gambar TIDAK dikirim ke server Laravel.">
                <div x-data="kkaKnN()" x-init="boot()" class="space-y-4">
                    <div class="rounded-2xl overflow-hidden border border-slate-200 bg-slate-900 aspect-video flex items-center justify-center relative">
                        <video x-ref="video" class="w-full h-full object-cover" playsinline muted></video>
                        <template x-if="!cameraOn"><p class="absolute text-white/80 text-sm px-6 text-center">Klik “Aktifkan Kamera” lalu arahkan kamera ke benda di sekitarmu.</p></template>
                        <span class="absolute top-3 left-3 kka-badge" x-text="status"></span>
                    </div>
                    <div class="rounded-2xl bg-slate-900 text-emerald-300 p-4 font-mono text-sm min-h-16" x-text="prediction || 'Halo! Aku AI. Aktifkan kamera lalu klik Uji — aku sebutkan benda yang kulihat.'"></div>
                    <div class="flex flex-wrap gap-2">
                        <button @click="toggleKamera()" class="kka-btn-primary !py-2 text-sm" x-text="cameraOn ? 'Matikan Kamera' : 'Aktifkan Kamera'"></button>
                        <button @click="uji()" class="kka-btn-secondary !py-2 text-sm" x-text="sibuk ? 'Menganalisis...' : 'Uji'"></button>
                    </div>
                    <div class="flex items-center gap-3 text-sm">
                        <span class="kka-badge">Uji meyakinkan: <span x-text="ujiCount"></span>/2</span>
                        <span class="text-xs text-slate-500" x-text="xpNote"></span>
                    </div>
                    <p class="text-[11px] text-slate-400">2x Uji meyakinkan (keyakinan ≥50%) otomatis +20 XP, sekali saja. Dekatkan benda ke kamera dan perbaiki cahaya agar hasil akurat.</p>
                </div>
            </x-card>
        @endif

        @if($module->slug === 'bias-data')
            <x-card title="Simulasi Bias Data" subtitle="Geser slider: lihat bagaimana dataset timpang merusak keadilan model.">
                <div x-data="{ a: 90, b: 10 }" class="space-y-4">
                    <label class="block text-sm font-semibold">Data Kelompok A: <span x-text="a"></span>% <input type="range" min="5" max="95" x-model="a" @input="b = 100 - a" class="w-full accent-orange-500"></label>
                    <label class="block text-sm font-semibold">Data Kelompok B: <span x-text="b"></span>% (otomatis)</label>
                    <div class="grid sm:grid-cols-2 gap-3">
                        <div class="rounded-2xl border border-slate-200 p-4 text-center"><p class="text-xs text-slate-500">Akurasi Kelompok A</p><p class="text-3xl font-extrabold text-emerald-500" x-text="Math.round(60 + a * 0.4) + '%'"></p></div>
                        <div class="rounded-2xl border border-slate-200 p-4 text-center"><p class="text-xs text-slate-500">Akurasi Kelompok B</p><p class="text-3xl font-extrabold text-orange-500" x-text="Math.round(60 + b * 0.4) + '%'"></p></div>
                    </div>
                    <p class="text-sm p-4 rounded-2xl border" :class="Math.abs(a-b) > 60 ? 'bg-orange-50 border-orange-200 text-orange-800' : 'bg-emerald-50 border-emerald-200 text-emerald-800'" x-text="Math.abs(a-b) > 60 ? 'Tidak adil! Selisih >60%. Tambah data kelompok minoritas agar model adil.' : 'Bagus! Dataset relatif seimbang, model cenderung adil.'"></p>
                </div>
            </x-card>
        @endif

        @if($module->slug === 'etika-hoaks-ai')
            <x-card title="Etika & Hoaks AI" subtitle="Studi kasus: kenali deepfake sebelum share.">
                <div class="grid sm:grid-cols-2 gap-3 text-sm">
                    <div class="rounded-2xl border border-slate-200 p-4"><p class="font-bold">Kasus 1: Foto viral</p><p class="text-slate-600 mt-1">Cek tangan/jari, teks aneh, bayangan tidak konsisten. Verifikasi silang ke 2 sumber resmi.</p></div>
                    <div class="rounded-2xl border border-slate-200 p-4"><p class="font-bold">Kasus 2: Suara youngsters</p><p class="text-slate-600 mt-1">AI voice clone bisa meniru guru/orang tua. Konfirmasi via kanal kedua sebelum transfer/percaya.</p></div>
                    <div class="rounded-2xl border border-slate-200 p-4"><p class="font-bold">Prinsip etis</p><p class="text-slate-600 mt-1">Izin • Transparan (beri label “dibuat AI”) • Tanggung jawab • Jaga privasi.</p></div>
                    <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4"><p class="font-bold text-emerald-800">Rumus CHECK</p><p class="text-emerald-700 mt-1">Cek sumber • Hindari judul bombastis • Eja tanggal • Cek gambar • Kroscek fakta.</p></div>
                </div>
            </x-card>
        @endif

        <x-card title="Kuis Modul (minimal 80 untuk lulus)" :subtitle="$progress && $progress->is_completed ? 'Status: LULUS • Skor '.$progress->quiz_score.' • +'.$progress->xp_earned.' XP' : 'Status: '.($progress ? 'skor '.$progress->quiz_score.' — belum lulus, coba lagi!' : 'belum dikerjakan')">
            <form method="POST" action="{{ route('siswa.modul.quiz', $module->slug) }}" class="space-y-4">
                @csrf
                @foreach($module->quizzes as $i => $q)
                    <div class="rounded-2xl border border-slate-200 p-4">
                        <p class="font-semibold text-slate-800 text-sm">{{ $i + 1 }}. {{ $q->question }}</p>
                        <div class="mt-2 space-y-1.5">
                            @foreach($q->options as $oi => $opt)
                                <label class="flex items-center gap-2 text-sm text-slate-700 hover:text-orange-600 cursor-pointer">
                                    <input type="radio" name="answers[{{ $i }}]" value="{{ $oi }}" class="accent-orange-500" required>
                                    {{ $opt }}
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach
                <x-button type="submit">Kumpulkan Kuis</x-button>
            </form>
        </x-card>
    </div>

    @if($module->slug === 'latih-ai-mu')
    @push('head')
        <script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs@4.9.0/dist/tf.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@tensorflow-models/mobilenet@2.1.1/dist/mobilenet.min.js"></script>
    @endpush
    @push('scripts')
    <script>
    function kkaKnN() {
        // Model disimpan di closure biasa (BUKAN reactive data):
        // objek tfjs yang lewat proxy Alpine merusak identity tensor.
        // Label Indonesia untuk 30+ benda umum (sisanya tampil Inggris).
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
        let rawNet = null;
        const snap = document.createElement('canvas');
        // Potong tengah jadi bujur sangkar (hindari distorsi gepeng yang
        // menurunkan akurasi), lalu kecilkan ke 224x224.
        function frameTensor(videoEl) {
            const S = 224;
            const vw = videoEl.videoWidth || 640, vh = videoEl.videoHeight || 480;
            const side = Math.min(vw, vh), sx = (vw - side) / 2, sy = (vh - side) / 2;
            snap.width = S; snap.height = S;
            const ctx = snap.getContext('2d', { willReadFrequently: true });
            ctx.drawImage(videoEl, sx, sy, side, side, 0, 0, S, S);
            const d = ctx.getImageData(0, 0, S, S).data;
            const n = S * S, vals = new Int32Array(n * 3);
            for (let i = 0; i < n; i++) {
                vals[i * 3] = d[i * 4]; vals[i * 3 + 1] = d[i * 4 + 1]; vals[i * 3 + 2] = d[i * 4 + 2];
            }
            return tf.tensor3d(vals, [S, S, 3], 'int32');
        }
        return {
            status: 'Belum dimuat', cameraOn: false, sibuk: false,
            prediction: '', ujiCount: {{ $progress->hands_on_count ?? 0 }}, xpNote: '',
            async boot() {
                if (!window.__kkaModulNet) {
                    window.__kkaModulNet = (async () => {
                        if (window.tf) { await tf.ready(); }
                        const net = await mobilenet.load();
                        const g = new Int32Array(224 * 224 * 3).fill(128);
                        const t0 = tf.tensor3d(g, [224, 224, 3], 'int32');
                        try { await net.classify(t0, 1); } finally { t0.dispose(); }
                        return net;
                    })();
                }
                try {
                    rawNet = await window.__kkaModulNet;
                    this.status = 'Model siap — aktifkan kamera';
                } catch(e) { this.status = 'Gagal load model (butuh internet sekali)'; }
            },
            async toggleKamera() {
                if (this.cameraOn) {
                    if (this.stream) this.stream.getTracks().forEach(t => t.stop());
                    this.$refs.video.srcObject = null;
                    this.cameraOn = false; this.prediction = '';
                    this.status = rawNet ? 'Model siap — aktifkan kamera' : 'Memuat model...';
                    return;
                }
                try {
                    this.stream = await navigator.mediaDevices.getUserMedia({ video: true });
                    this.$refs.video.srcObject = this.stream;
                    await this.$refs.video.play();
                    this.cameraOn = true; this.status = 'Kamera menyala — arahkan ke benda lalu klik Uji';
                } catch(e) { this.status = 'Kamera ditolak — izinkan akses kamera lalu coba lagi'; }
            },
            async uji() {
                if (!rawNet) { this.prediction = 'Model belum termuat, tunggu tulisan “Model siap” lalu coba lagi!'; return; }
                if (!this.cameraOn) { this.prediction = 'Aktifkan kamera dulu ya!'; return; }
                if (this.sibuk) return;
                const v = this.$refs.video;
                let tunggu = 0;
                while ((v.readyState < 2 || v.videoWidth === 0) && tunggu < 20) {
                    await new Promise(r => setTimeout(r, 200));
                    tunggu++;
                }
                if (v.videoWidth === 0) { this.prediction = 'Kamera belum menampilkan gambar — tunggu lalu klik Uji lagi!'; return; }
                this.sibuk = true; this.status = 'Menganalisis gambar...';
                try {
                    const t = frameTensor(v);
                    let preds;
                    try { preds = await rawNet.classify(t, 1); }
                    finally { t.dispose(); }
                    const top = preds[0];
                    const pct = Math.round((top.probability || 0) * 100);
                    if (pct >= 50) {
                        this.prediction = 'Hasil: ' + idLabel(top.className) + ' — ' + pct + '% yakin.';
                        await this.laporUji(pct);
                    } else {
                        this.prediction = 'Kurang yakin (terbaik: ' + idLabel(top.className) + ' — ' + pct + '%). Dekatkan benda ke kamera, perbaiki cahaya, lalu Uji lagi!';
                    }
                    this.status = 'Kamera menyala — arahkan ke benda lalu klik Uji';
                } catch (e) {
                    console.error(e);
                    this.prediction = 'Gagal menganalisis. Coba klik Uji lagi!';
                }
                this.sibuk = false;
            },
            // Lapor Uji meyakinkan ke server: dihitung + XP otomatis (anti-farm di backend).
            async laporUji(pct) {
                try {
                    const res = await fetch(@js(route('siswa.modul.handson', $module->slug)), {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({ confidence: pct }),
                    });
                    const data = await res.json();
                    this.ujiCount = data.count ?? this.ujiCount;
                    this.xpNote = data.message ?? '';
                } catch (e) { /* XP menyusul saat Uji berikutnya */ }
            },
        }
    }
    </script>
    @endpush
    @endif
</x-app-layout>
