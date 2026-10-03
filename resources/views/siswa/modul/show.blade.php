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
            <x-card title="Latih AI-mu (100% di browser, privasi aman)" subtitle="1) Kumpulkan contoh Kelas A & B → 2) Latih Model → 3) Uji/Pantau real-time. Tanpa API luar, gambar TIDAK dikirim ke server.">
                <div x-data="kkaKnN()" x-init="boot()" class="space-y-4">
                    <div class="rounded-2xl overflow-hidden border border-slate-200 bg-slate-900 aspect-video flex items-center justify-center relative">
                        <video x-ref="video" class="w-full h-full object-cover" playsinline muted></video>
                        <template x-if="!cameraOn"><p class="absolute text-white/80 text-sm px-6 text-center">Klik “Aktifkan Kamera” untuk ambil contoh, atau pakai Dataset Contoh di bawah.</p></template>
                        <span class="absolute top-3 left-3 kka-badge" x-text="status"></span>
                    </div>
                    <div class="rounded-2xl bg-slate-900 text-emerald-300 p-4 font-mono text-sm min-h-16" x-text="prediction || 'Halo! Aku AI yang akan kamu latih sendiri. Mulai dari Aktifkan Kamera ya!'"></div>
                    <div class="flex flex-wrap gap-2">
                        <button @click="toggleKamera()" class="kka-btn-primary !py-2 text-sm" x-text="cameraOn ? 'Matikan Kamera' : 'Aktifkan Kamera'"></button>
                        <button @click="ambilContoh(0)" class="kka-btn-secondary !py-2 text-sm">+ Contoh A (<span x-text="counts[0]"></span>)</button>
                        <button @click="ambilContoh(1)" class="kka-btn-secondary !py-2 text-sm">+ Contoh B (<span x-text="counts[1]"></span>)</button>
                        <button @click="latihModel()" class="kka-btn-primary !py-2 text-sm">Latih Model</button>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <button @click="uji()" :disabled="!terlatih" class="kka-btn-secondary !py-2 text-sm disabled:opacity-40" x-text="sibuk ? 'Menganalisis...' : 'Uji Sekali'"></button>
                        <button @click="pantau()" :disabled="!terlatih" class="kka-btn-secondary !py-2 text-sm disabled:opacity-40" x-text="live ? 'Stop Pantau' : 'Pantau Live'"></button>
                        <button @click="reset()" class="kka-btn-secondary !py-2 text-sm">Reset</button>
                        <button @click="muatContoh()" class="kka-btn-secondary !py-2 text-sm">Muat Dataset Contoh</button>
                    </div>
                    <div class="flex items-center gap-3 text-sm">
                        <span class="kka-badge">Uji meyakinkan: <span x-text="ujiCount"></span>/2</span>
                        <span class="text-xs text-slate-500" x-text="xpNote"></span>
                    </div>
                    <div class="rounded-2xl border border-orange-200 bg-orange-50 p-4">
                        <div class="flex items-center gap-2">
                            <p class="text-sm font-bold text-slate-800">Penjelasan Tutor AI (teks)</p>
                            <button @click="jelaskan()" :disabled="jelasSibuk" class="kka-btn-secondary !py-1.5 text-xs disabled:opacity-50" x-text="jelasSibuk ? 'Meminta...' : 'Jelaskan Hasilku'"></button>
                        </div>
                        <p class="mt-2 text-sm text-slate-700 whitespace-pre-wrap" x-show="penjelasan" x-text="penjelasan"></p>
                    </div>
                    <p class="text-[11px] text-slate-400">2x Uji meyakinkan (keyakinan ≥50%) otomatis +20 XP, sekali saja. Dataset contoh berlabel simulasi (pola garis vs lingkaran).</p>
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
        <script src="https://cdn.jsdelivr.net/npm/@tensorflow-models/knn-classifier@1.2.6/dist/knn-classifier.min.js"></script>
    @endpush
    @push('scripts')
    <script>
    function kkaKnN() {
        // Model & classifier di closure biasa (BUKAN reactive data):
        // objek tfjs yang lewat proxy Alpine merusak identity tensor.
        // 100% client-side: MobileNet (ekstraksi fitur) + KNN (klasifikasi).
        // Nol API luar untuk gambar; Gemini hanya untuk penjelasan teks.
        const MIN_SAMPEL = 5;
        let rawNet = null;
        let rawKnn = null;
        const snap = document.createElement('canvas');
        const pola = document.createElement('canvas');
        function tensorDariKanvas(c) {
            const S = 224;
            const ctx = c.getContext('2d', { willReadFrequently: true });
            const d = ctx.getImageData(0, 0, S, S).data;
            const n = S * S, vals = new Int32Array(n * 3);
            for (let i = 0; i < n; i++) {
                vals[i * 3] = d[i * 4]; vals[i * 3 + 1] = d[i * 4 + 1]; vals[i * 3 + 2] = d[i * 4 + 2];
            }
            return tf.tensor3d(vals, [S, S, 3], 'int32');
        }
        // Potong tengah jadi bujur sangkar (hindari distorsi yang
        // menurunkan akurasi), kecilkan ke 224x224.
        function frameTensor(videoEl) {
            const S = 224;
            const vw = videoEl.videoWidth || 640, vh = videoEl.videoHeight || 480;
            const side = Math.min(vw, vh), sx = (vw - side) / 2, sy = (vh - side) / 2;
            snap.width = S; snap.height = S;
            snap.getContext('2d', { willReadFrequently: true }).drawImage(videoEl, sx, sy, side, side, 0, 0, S, S);
            return tensorDariKanvas(snap);
        }
        // Pola sintetis untuk fallback tanpa kamera (SIMULASI, bukan foto asli):
        // Kelas A = garis vertikal, Kelas B = lingkaran, dengan jitter acak.
        function polaTensor(cls, varian) {
            const S = 224;
            pola.width = S; pola.height = S;
            const ctx = pola.getContext('2d');
            ctx.fillStyle = '#111827'; ctx.fillRect(0, 0, S, S);
            const rnd = (a, b) => a + Math.random() * (b - a);
            if (cls === 0) {
                for (let x = 0; x < 7; x++) {
                    ctx.fillStyle = x % 2 ? '#F97316' : '#FBBF24';
                    ctx.fillRect(x * 32 + rnd(-6, 6) + varian * 3, 0, rnd(10, 18), S);
                }
            } else {
                for (let gy = 0; gy < 4; gy++) for (let gx = 0; gx < 4; gx++) {
                    ctx.fillStyle = (gx + gy) % 2 ? '#10B981' : '#6EE7B7';
                    ctx.beginPath();
                    ctx.arc(gx * 56 + 28 + rnd(-8, 8) + varian * 2, gy * 56 + 28 + rnd(-8, 8), rnd(12, 20), 0, 7);
                    ctx.fill();
                }
            }
            return tensorDariKanvas(pola);
        }
        function tambahContoh(imgTensor, cls) {
            const act = rawNet.infer(imgTensor, true);
            rawKnn.addExample(act, cls);
            imgTensor.dispose(); act.dispose();
        }
        return {
            status: 'Belum dimuat', cameraOn: false, sibuk: false,
            counts: [0, 0], terlatih: false, live: false, timer: null,
            prediction: '', ujiCount: {{ $progress->hands_on_count ?? 0 }}, xpNote: '',
            penjelasan: '', jelasSibuk: false,
            async boot() {
                if (!window.__kkaModulNet) {
                    window.__kkaModulNet = (async () => {
                        if (window.tf) { await tf.ready(); }
                        const net = await mobilenet.load();
                        // Tes mandiri jalur penuh (embedding + KNN + prediksi)
                        const knn = knnClassifier.create();
                        const g = new Int32Array(224 * 224 * 3).fill(128);
                        const t0 = tf.tensor3d(g, [224, 224, 3], 'int32');
                        try {
                            const a0 = net.infer(t0, true);
                            knn.addExample(a0, 0); knn.addExample(a0, 1);
                            await knn.predictClass(a0);
                            a0.dispose();
                        } finally { t0.dispose(); }
                        return { net, knn: knnClassifier.create() };
                    })();
                }
                try {
                    const m = await window.__kkaModulNet;
                    rawNet = m.net; rawKnn = m.knn;
                    this.status = 'Model siap — kumpulkan contoh Kelas A & B';
                } catch(e) { this.status = 'Gagal load model (butuh internet sekali)'; }
            },
            async toggleKamera() {
                if (this.cameraOn) {
                    if (this.stream) this.stream.getTracks().forEach(t => t.stop());
                    if (this.timer) { clearInterval(this.timer); this.timer = null; this.live = false; }
                    this.$refs.video.srcObject = null;
                    this.cameraOn = false; this.prediction = '';
                    this.status = rawNet ? 'Model siap — kumpulkan contoh' : 'Memuat model...';
                    return;
                }
                try {
                    this.stream = await navigator.mediaDevices.getUserMedia({ video: true });
                    this.$refs.video.srcObject = this.stream;
                    await this.$refs.video.play();
                    this.cameraOn = true; this.status = 'Kamera menyala — ambil contoh Kelas A & B';
                } catch(e) { this.status = 'Kamera ditolak — pakai tombol Dataset Contoh di bawah'; }
            },
            // 1. KUMPULKAN SAMPEL dari webcam (Kelas A / Kelas B)
            async ambilContoh(cls) {
                if (!rawNet || !this.cameraOn) { this.prediction = 'Aktifkan kamera dulu ya!'; return; }
                const img = frameTensor(this.$refs.video);
                tambahContoh(img, cls);
                this.counts[cls]++;
                this.prediction = 'Contoh Kelas ' + ['A','B'][cls] + ' tersimpan (' + this.counts[cls] + '). Tambah lagi biar model makin pintar!';
            },
            // Fallback tanpa kamera: 10 pola sintetis per kelas (SIMULASI).
            muatContoh() {
                if (!rawNet) { this.prediction = 'Tunggu model siap dulu ya!'; return; }
                for (let i = 0; i < 10; i++) { tambahContoh(polaTensor(0, i), 0); tambahContoh(polaTensor(1, i), 1); }
                this.counts[0] += 10; this.counts[1] += 10;
                this.prediction = 'Dataset contoh (simulasi pola) dimuat: A=' + this.counts[0] + ', B=' + this.counts[1] + '. Klik Latih Model!';
            },
            // 2. LOCAL TRAINING: validasi + cek konsistensi (~2-3 detik).
            // KNN itu instance-based: "latihan" = memastikan tiap kelas
            // punya cukup contoh lalu menguji konsistensi model.
            async latihModel() {
                if (!rawNet) { this.prediction = 'Tunggu model siap dulu ya!'; return; }
                const kurang = [];
                if (this.counts[0] < MIN_SAMPEL) kurang.push('Kelas A kurang ' + (MIN_SAMPEL - this.counts[0]) + ' contoh');
                if (this.counts[1] < MIN_SAMPEL) kurang.push('Kelas B kurang ' + (MIN_SAMPEL - this.counts[1]) + ' contoh');
                if (kurang.length) { this.prediction = 'Belum bisa dilatih: ' + kurang.join(', ') + '.'; return; }
                this.status = 'Melatih model (cek konsistensi)...';
                await new Promise(r => setTimeout(r, 300));
                try {
                    const t = this.cameraOn ? frameTensor(this.$refs.video) : polaTensor(0, 99);
                    const act = rawNet.infer(t, true);
                    const res = await rawKnn.predictClass(act);
                    const pct = Math.round((res.confidences[res.label] || 0) * 100);
                    t.dispose(); act.dispose();
                    this.terlatih = true;
                    this.prediction = 'Hebat! Model AI-mu berhasil dilatih! Cek konsistensi: ' + ['Kelas A','Kelas B'][res.label] + ' (' + pct + '%). Sekarang Uji / Pantau Live!';
                    this.status = 'Model terlatih — silakan Uji!';
                } catch (e) {
                    console.error(e);
                    this.prediction = 'Latihan gagal, coba klik Latih Model lagi!';
                    this.status = 'Model siap — kumpulkan contoh';
                }
            },
            // 3. TESTING REAL-TIME di browser (0 latency, tanpa API luar)
            async intiUji() {
                const t = frameTensor(this.$refs.video);
                let res;
                try {
                    const act = rawNet.infer(t, true);
                    try { res = await rawKnn.predictClass(act); }
                    finally { act.dispose(); }
                } finally { t.dispose(); }
                return res;
            },
            async uji() {
                if (!rawNet) { this.prediction = 'Model belum termuat, tunggu tulisan “Model siap”!'; return; }
                if (!this.terlatih) { this.prediction = 'Latih model dulu (minimal 5 contoh per kelas)!'; return; }
                if (!this.cameraOn) { this.prediction = 'Aktifkan kamera dulu ya!'; return; }
                if (this.sibuk) return;
                const v = this.$refs.video;
                let tunggu = 0;
                while ((v.readyState < 2 || v.videoWidth === 0) && tunggu < 20) {
                    await new Promise(r => setTimeout(r, 200));
                    tunggu++;
                }
                if (v.videoWidth === 0) { this.prediction = 'Kamera belum menampilkan gambar — tunggu lalu Uji lagi!'; return; }
                this.sibuk = true;
                try {
                    const res = await this.intiUji();
                    const nama = ['Kelas A', 'Kelas B'][res.label] || ('Kelas ' + res.label);
                    const pct = Math.round((res.confidences[res.label] || 0) * 100);
                    if (pct >= 50) {
                        this.prediction = 'Hasil: ' + nama + ' — ' + pct + '% yakin.';
                        await this.laporUji(pct);
                    } else {
                        this.prediction = 'Kurang yakin (terbaik: ' + nama + ' — ' + pct + '%). Tambah contoh tiap kelas lalu Latih ulang!';
                    }
                } catch (e) {
                    console.error(e);
                    this.prediction = 'Gagal menganalisis. Coba klik Uji lagi!';
                }
                this.sibuk = false;
            },
            pantau() {
                if (!this.terlatih || !this.cameraOn || this.sibuk) return;
                if (this.live) {
                    clearInterval(this.timer); this.timer = null; this.live = false;
                    this.status = 'Model terlatih — silakan Uji!';
                    return;
                }
                this.live = true; this.status = 'Memantau live... (klik Stop untuk berhenti)';
                this.timer = setInterval(async () => {
                    if (this.sibuk) return;
                    this.sibuk = true;
                    try {
                        const res = await this.intiUji();
                        const nama = ['Kelas A', 'Kelas B'][res.label] || ('Kelas ' + res.label);
                        const pct = Math.round((res.confidences[res.label] || 0) * 100);
                        this.prediction = 'Live: ' + nama + ' — ' + pct + '%';
                        if (pct >= 50) await this.laporUji(pct);
                    } catch (e) { /* lewati frame gagal */ }
                    this.sibuk = false;
                }, 800);
            },
            reset() {
                rawKnn = knnClassifier.create();
                if (this.timer) { clearInterval(this.timer); this.timer = null; this.live = false; }
                this.counts = [0, 0]; this.terlatih = false;
                this.prediction = 'Model di-reset. Kumpulkan contoh lalu Latih ulang!';
                this.status = 'Model siap — kumpulkan contoh Kelas A & B';
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
            // Gemini HANYA untuk penjelasan teks (bukan klasifikasi gambar).
            async jelaskan() {
                if (this.jelasSibuk) return;
                this.jelasSibuk = true;
                this.penjelasan = 'Meminta penjelasan ke Tutor AI...';
                try {
                    const res = await fetch(@js(route('ai.chat')), {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            module: 'latih-ai-mu',
                            message: 'Aku baru selesai praktikum Modul 2: kukumpulkan ' + this.counts[0] + ' contoh Kelas A dan ' + this.counts[1] + ' contoh Kelas B, lalu model KNN-ku menebak "' + this.prediction + '". Jelaskan singkat kenapa hasilku bisa akurat atau salah, dan beri 1 tips memperbaikinya.',
                        }),
                    });
                    const data = await res.json();
                    this.penjelasan = data.reply || 'Tidak ada jawaban.';
                } catch (e) { this.penjelasan = 'Gagal menghubungi Tutor AI. Coba lagi ya!'; }
                this.jelasSibuk = false;
            },
        }
    }
    </script>
    @endpush
    @endif
</x-app-layout>
