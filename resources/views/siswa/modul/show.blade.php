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
            <x-card title="Latih AI-mu (100% di browser, privasi aman)" subtitle="KNN + MobileNet lokal. Gambar TIDAK dikirim ke server Laravel.">
                <div x-data="kkaKnN()" x-init="boot()" class="space-y-4">
                    <div class="rounded-2xl overflow-hidden border border-slate-200 bg-slate-900 aspect-video flex items-center justify-center relative">
                        <video x-ref="video" class="w-full h-full object-cover" playsinline muted></video>
                        <template x-if="!cameraOn"><p class="absolute text-white/80 text-sm px-6 text-center">Kamera belum aktif. Klik “Aktifkan Kamera” atau pakai Mode Tanpa Kamera di bawah.</p></template>
                        <span class="absolute top-3 left-3 kka-badge" x-text="status"></span>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <button @click="startCamera()" class="kka-btn-primary !py-2 text-sm">Aktifkan Kamera</button>
                        <button @click="addExample(0)" class="kka-btn-secondary !py-2 text-sm">+ Contoh Kelas A 🐱 (<span x-text="counts[0]"></span>)</button>
                        <button @click="addExample(1)" class="kka-btn-secondary !py-2 text-sm">+ Contoh Kelas B 🗑️ (<span x-text="counts[1]"></span>)</button>
                        <button @click="addExample(2)" class="kka-btn-secondary !py-2 text-sm">+ Contoh Kelas C ✋ (<span x-text="counts[2]"></span>)</button>
                        <button @click="predict()" class="kka-btn-primary !py-2 text-sm">Uji Sekarang</button>
                        <button @click="reset()" class="kka-btn-secondary !py-2 text-sm">Reset</button>
                    </div>
                    <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-semibold text-emerald-800" x-show="prediction" x-text="prediction"></div>
                    <div class="rounded-2xl border border-slate-200 p-4">
                        <p class="text-sm font-bold text-slate-800">Mode tanpa kamera (dataset contoh)</p>
                        <p class="text-xs text-slate-500">Klik untuk simulasi jika tidak ada webcam / izin ditolak.</p>
                        <div class="mt-2 flex flex-wrap gap-2">
                            <button @click="simulate(0)" class="kka-btn-secondary !py-1.5 text-xs">Simulasi A ×5</button>
                            <button @click="simulate(1)" class="kka-btn-secondary !py-1.5 text-xs">Simulasi B ×5</button>
                            <button @click="simulate(2)" class="kka-btn-secondary !py-1.5 text-xs">Simulasi C ×5</button>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('siswa.modul.handson', $module->slug) }}">
                        @csrf
                        <x-button type="submit">Saya sudah melatih model → Klaim +25 XP</x-button>
                    </form>
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
        let rawNet = null;
        let rawClassifier = null;
        const snap = document.createElement('canvas');
        function frameTensor(videoEl) {
            const S = 224;
            const vw = videoEl.videoWidth || 640, vh = videoEl.videoHeight || 480;
            snap.width = S; snap.height = S;
            const ctx = snap.getContext('2d', { willReadFrequently: true });
            ctx.drawImage(videoEl, 0, 0, vw, vh, 0, 0, S, S);
            const d = ctx.getImageData(0, 0, S, S).data;
            const n = S * S, vals = new Int32Array(n * 3);
            for (let i = 0; i < n; i++) {
                vals[i * 3] = d[i * 4]; vals[i * 3 + 1] = d[i * 4 + 1]; vals[i * 3 + 2] = d[i * 4 + 2];
            }
            return tf.tensor3d(vals, [S, S, 3], 'int32');
        }
        return {
            status: 'Belum dimuat', cameraOn: false, counts: [0,0,0],
            prediction: '',
            async boot() {
                if (!window.__kkaModulNet) {
                    window.__kkaModulNet = (async () => {
                        if (window.tf) { await tf.ready(); }
                        this.status = 'Memuat MobileNet...';
                        const net = await mobilenet.load();
                        const classifier = knnClassifier.create();
                        return { net, classifier };
                    })();
                }
                try {
                    const m = await window.__kkaModulNet;
                    rawNet = m.net; rawClassifier = m.classifier;
                    this.status = 'Siap dilatih (KNN lokal)';
                } catch(e) { this.status = 'Gagal load TF.js (butuh internet sekali)'; }
            },
            async startCamera() {
                try {
                    const s = await navigator.mediaDevices.getUserMedia({ video: true });
                    this.$refs.video.srcObject = s;
                    await this.$refs.video.play();
                    this.cameraOn = true; this.status = 'Kamera aktif';
                } catch(e) { this.status = 'Kamera ditolak — pakai Mode Tanpa Kamera'; }
            },
            async addExample(cls) {
                if (!rawNet) return;
                let img;
                if (this.cameraOn) { img = frameTensor(this.$refs.video); }
                else { img = tf.randomNormal([224,224,3]); }
                const act = rawNet.infer(img, true);
                rawClassifier.addExample(act, cls);
                this.counts[cls]++; img.dispose(); act.dispose();
                this.prediction = 'Hebat! Model AI-mu berhasil dilatih! Kelas ' + ['A','B','C'][cls] + ' sekarang punya ' + this.counts[cls] + ' contoh.';
            },
            simulate(cls) { for (let i=0;i<5;i++) this.addExample(cls); },
            async predict() {
                if (!rawNet || rawClassifier.getNumClasses() === 0) { this.prediction = 'Latih dulu minimal 2 kelas, masing-masing beberapa contoh ya!'; return; }
                let img = this.cameraOn ? frameTensor(this.$refs.video) : tf.randomNormal([224,224,3]);
                const act = rawNet.infer(img, true);
                const res = await rawClassifier.predictClass(act);
                const label = ['Kelas A 🐱','Kelas B 🗑️','Kelas C ✋'][res.label] || ('Kelas ' + res.label);
                this.prediction = 'Model menebak: ' + label + ' (' + Math.round((res.confidences[res.label]||0)*100) + '%). Semua di browser-mu!';
                img.dispose(); act.dispose();
            },
            reset() { rawClassifier = knnClassifier.create(); this.counts=[0,0,0]; this.prediction='Model di-reset. Yuk latih ulang!'; }
        }
    }
    </script>
    @endpush
    @endif
</x-app-layout>
