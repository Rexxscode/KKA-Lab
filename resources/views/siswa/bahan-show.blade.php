<x-sidebar-layout>
    <x-slot name="header">
        <h2 class="font-extrabold text-xl text-slate-900 dark:text-white">{{ $module->title }}</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400">{{ $module->description }} • +{{ $module->xp_reward }} XP • Lulus jika kuis ≥80</p>
    </x-slot>

    @if(auth()->user()->isAdmin())
        <div class="mb-4 rounded-2xl border border-orange-200 dark:border-orange-500/30 bg-orange-50 dark:bg-orange-500/10 px-4 py-3 flex flex-wrap items-center gap-2 text-sm" role="status">
            <span class="font-bold text-orange-800 dark:text-orange-300 min-w-0 flex-1 basis-48">Mode pratinjau admin — progres tidak disimpan. Kembali ke Pratinjau Siswa untuk melihat data siswa.</span>
            <a href="{{ route('admin.pratinjau.index') }}" class="kka-btn-primary !py-1.5 text-xs shrink-0">Buka Pratinjau →</a>
        </div>
    @endif

    <div class="space-y-4 sm:space-y-6" x-data="{ sampaiAkhir: false }">
        <x-card title="Tahap 1 — Bahan Ajar (wajib dibaca dulu)" subtitle="Scroll sampai akhir bacaan untuk membuka kunci kuis.">
            <div class="rounded-2xl border border-slate-200 dark:border-slate-700 bg-slate-50/60 dark:bg-slate-800/50 p-5 max-h-[46vh] overflow-y-auto" @scroll="if ($el.scrollTop + $el.clientHeight >= $el.scrollHeight - 24) sampaiAkhir = true">
                @if(view()->exists('siswa.modul.materi-' . $module->slug))
                    @include('siswa.modul.materi-' . $module->slug)
                @elseif($module->materi)
                    @include('siswa.modul.materi-custom', ['materi' => $module->materi])
                @else
                    <div class="prose max-w-none text-sm text-slate-700 dark:text-slate-200 space-y-4">
                        <p>{{ $module->description ?? 'Pelajari materi modul ini dengan saksama sebelum mengerjakan kuis.' }}</p>
                        <p>Kuis modul ini terdiri dari soal pilihan ganda buatan gurumu. Baca petunjuk di atas, kerjakan berurutan, dan capai skor minimal 80 untuk lulus.</p>
                    </div>
                @endif
            </div>
            <p class="mt-2 text-xs font-semibold" :class="sampaiAkhir ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-400'" x-text="sampaiAkhir ? '✓ Kamu sudah sampai akhir bacaan.' : 'Scroll kotak bacaan sampai bawah ya...'"></p>
            <form method="POST" action="{{ route('siswa.bahan.read', $module->slug) }}" class="mt-3">
                @csrf
                <button type="submit" :disabled="!sampaiAkhir" :class="sampaiAkhir ? 'kka-btn-primary' : 'kka-btn-primary opacity-40'" class="text-sm">Saya Sudah Selesai Membaca & Siap Mengerjakan Kuis →</button>
            </form>
        </x-card>
        @if($module->slug === 'apa-itu-ai')
            <x-card title="Apa itu AI?" subtitle="AI = komputer yang belajar dari data. Machine Learning = cara belajarnya.">
                <div class="grid sm:grid-cols-3 gap-3" x-data="{ tab: 'ml' }">
                    <div class="sm:col-span-1 flex sm:flex-col gap-2">
                        <button @click="tab='ml'" :class="tab==='ml' ? 'bg-orange-500 text-white' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300'" class="px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 text-sm font-bold">Machine Learning</button>
                        <button @click="tab='contoh'" :class="tab==='contoh' ? 'bg-orange-500 text-white' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300'" class="px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 text-sm font-bold">Contoh Sehari-hari</button>
                        <button @click="tab='cara'" :class="tab==='cara' ? 'bg-orange-500 text-white' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300'" class="px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 text-sm font-bold">Cara Kerja</button>
                    </div>
                    <div class="sm:col-span-2 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 p-5 text-sm text-slate-700 dark:text-slate-200 min-h-40">
                        <template x-if="tab==='ml'"><p><b>Machine Learning:</b> 1) Kumpulkan data → 2) Latih model → 3) Uji dengan data baru → 4) Perbaiki. Semakin beragam datanya, semakin pintar modelnya.</p></template>
                        <template x-if="tab==='contoh'"><p><b>Contoh:</b> filter spam, rekomendasi video, deteksi plagiarisme, chatbot belajar, pengenalan wajah untuk absensi.</p></template>
                        <template x-if="tab==='cara'"><p><b>Cara kerja:</b> gambar diubah jadi angka (fitur) → model mencari pola → menebak kelas paling mirip. Di Modul 2 kamu praktik langsung!</p></template>
                    </div>
                </div>
            </x-card>
        @endif

        @if(in_array($module->slug, ['python-dasar-ai', 'data-logika-ai']))
            <x-card title="Tahap 2 — Praktik Python Asli (di browser)" subtitle="Python beneran via Pyodide (WASM), bukan simulasi. Klik Jalankan — unduhan ±10MB hanya sekali, lalu bisa luring. Otomatis berhenti bila program jalan lebih dari 10 detik.">
                <div x-data="kkaPython(@js($module->slug))" class="space-y-3">
                    <div class="flex flex-wrap gap-2">
                        @if($module->slug === 'python-dasar-ai')
                            <button type="button" @click="isiContoh(0)" class="kka-btn-secondary !py-1.5 text-xs">Contoh 1: Halo AI</button>
                            <button type="button" @click="isiContoh(1)" class="kka-btn-secondary !py-1.5 text-xs">Contoh 2: Variabel</button>
                            <button type="button" @click="isiContoh(2)" class="kka-btn-secondary !py-1.5 text-xs">Contoh 3: List & Loop</button>
                            <button type="button" @click="isiContoh(3)" class="kka-btn-secondary !py-1.5 text-xs">Contoh 4: Fungsi & Dict</button>
                        @else
                            <button type="button" @click="isiContoh(0)" class="kka-btn-secondary !py-1.5 text-xs">Contoh 1: Rata-rata</button>
                            <button type="button" @click="isiContoh(1)" class="kka-btn-secondary !py-1.5 text-xs">Contoh 2: Filter</button>
                            <button type="button" @click="isiContoh(2)" class="kka-btn-secondary !py-1.5 text-xs">Contoh 3: Mini-Classifier</button>
                            <button type="button" @click="isiContoh(3)" class="kka-btn-secondary !py-1.5 text-xs">Contoh 4: Akurasi</button>
                        @endif
                    </div>
                    <label for="editor-python" class="block text-xs font-bold text-slate-600 dark:text-slate-300">Kode Python-mu
                        <textarea id="editor-python" x-model="kode" rows="8" spellcheck="false" class="mt-1 w-full rounded-2xl border-slate-200 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 font-mono text-[13px] focus:border-orange-500 focus:ring-orange-500" placeholder='print("Halo AI")'></textarea>
                    </label>
                    <div class="flex flex-wrap items-center gap-2">
                        <button type="button" @click="jalankan()" :disabled="sibuk" class="kka-btn-primary !py-2 text-sm disabled:opacity-50" x-text="sibuk ? 'Menjalankan...' : '▶ Jalankan'"></button>
                        <button type="button" @click="hentikan()" :disabled="!sibuk" class="kka-btn-secondary !py-2 text-sm disabled:opacity-40">⏹ Hentikan</button>
                        <span class="kka-badge" x-text="status" role="status"></span>
                    </div>
                    <div x-show="memuat" class="space-y-2" aria-hidden="true">
                        <div class="kka-skeleton h-4 w-2/3"></div>
                        <div class="kka-skeleton h-20 w-full"></div>
                        <p class="text-xs font-semibold text-slate-500 dark:text-slate-400">Mengunduh Python (Pyodide ±10MB, sekali saja — butuh internet)...</p>
                    </div>
                    <pre x-show="keluaran !== ''" x-text="keluaran" class="rounded-2xl bg-slate-900 text-emerald-300 p-4 font-mono text-[13px] whitespace-pre-wrap min-h-16 overflow-x-auto" aria-label="Hasil program"></pre>
                    <p class="kka-note">Tips: ubah contohnya lalu Jalankan lagi. Modul Python tidak memakai kamera. Kode berjalan lokal di perangkatmu.</p>
                </div>
            </x-card>
        @endif

        @if($module->slug === 'bias-data')
            <x-card title="Simulasi Bias Data" subtitle="Geser slider: lihat bagaimana dataset timpang merusak keadilan model.">
                <div x-data="{ a: 90, b: 10 }" class="space-y-4">
                    <label class="block text-sm font-semibold">Data Kelompok A: <span x-text="a"></span>% <input type="range" min="5" max="95" x-model="a" @input="b = 100 - a" class="w-full accent-orange-500"></label>
                    <label class="block text-sm font-semibold">Data Kelompok B: <span x-text="b"></span>% (otomatis)</label>
                    <div class="grid sm:grid-cols-2 gap-3">
                        <div class="rounded-2xl border border-slate-200 dark:border-slate-700 p-4 text-center"><p class="text-xs text-slate-500 dark:text-slate-400">Akurasi Kelompok A</p><p class="text-3xl font-extrabold text-emerald-500" x-text="Math.round(60 + a * 0.4) + '%'"></p></div>
                        <div class="rounded-2xl border border-slate-200 dark:border-slate-700 p-4 text-center"><p class="text-xs text-slate-500 dark:text-slate-400">Akurasi Kelompok B</p><p class="text-3xl font-extrabold text-orange-500" x-text="Math.round(60 + b * 0.4) + '%'"></p></div>
                    </div>
                    <p class="text-sm p-4 rounded-2xl border" :class="Math.abs(a-b) > 60 ? 'bg-orange-50 dark:bg-orange-500/10 border-orange-200 dark:border-orange-500/30 text-orange-800 dark:text-orange-300' : 'bg-emerald-50 dark:bg-emerald-500/10 border-emerald-200 dark:border-emerald-500/30 text-emerald-800 dark:text-emerald-300'" x-text="Math.abs(a-b) > 60 ? 'Tidak adil! Selisih >60%. Tambah data kelompok minoritas agar model adil.' : 'Bagus! Dataset relatif seimbang, model cenderung adil.'"></p>
                </div>
            </x-card>
        @endif

        @if($module->slug === 'etika-hoaks-ai')
            <x-card title="Etika & Hoaks AI" subtitle="Studi kasus: kenali deepfake sebelum share.">
                <div class="grid sm:grid-cols-2 gap-3 text-sm">
                    <div class="rounded-2xl border border-slate-200 dark:border-slate-700 p-4"><p class="font-bold">Kasus 1: Foto viral</p><p class="text-slate-600 dark:text-slate-300 mt-1">Cek tangan/jari, teks aneh, bayangan tidak konsisten. Verifikasi silang ke 2 sumber resmi.</p></div>
                    <div class="rounded-2xl border border-slate-200 dark:border-slate-700 p-4"><p class="font-bold">Kasus 2: Suara youngsters</p><p class="text-slate-600 dark:text-slate-300 mt-1">AI voice clone bisa meniru guru/orang tua. Konfirmasi via kanal kedua sebelum transfer/percaya.</p></div>
                    <div class="rounded-2xl border border-slate-200 dark:border-slate-700 p-4"><p class="font-bold">Prinsip etis</p><p class="text-slate-600 dark:text-slate-300 mt-1">Izin • Transparan (beri label “dibuat AI”) • Tanggung jawab • Jaga privasi.</p></div>
                    <div class="rounded-2xl border border-emerald-200 dark:border-emerald-500/30 bg-emerald-50 dark:bg-emerald-500/10 p-4"><p class="font-bold text-emerald-800 dark:text-emerald-300">Rumus CHECK</p><p class="text-emerald-700 dark:text-emerald-400 mt-1">Cek sumber • Hindari judul bombastis • Eja tanggal • Cek gambar • Kroscek fakta.</p></div>
                </div>
            </x-card>
        @endif

    </div>

    @if(in_array($module->slug, ['python-dasar-ai', 'data-logika-ai']))
    @push('scripts')
    <script>
    function kkaPython(slug) {
        // Python asli via Pyodide (WASM) — lazy-load saat Jalankan pertama.
        // Anti-hang: worker dihentikan paksa bila lewat 10 detik.
        const CONTOH = {
            'python-dasar-ai': [
                'print("Halo AI")\nprint("Aku siap belajar Python!")',
                'nama = "Sinta"\numur = 17\nprint("Nama:", nama)\nprint("Umur:", umur)\nprint("Tipe umur:", type(umur))',
                'nilai = [70, 80, 90]\nprint("Isi:", nilai)\nprint("Banyak data:", len(nilai))\nfor n in nilai:\n    print("Nilai:", n)',
                'def sapa(nama):\n    return "Halo, " + nama + "!"\n\nprint(sapa("Budi"))\nsiswa = {"nama": "Budi", "nilai": 85}\nprint("Nilai Budi:", siswa["nilai"])',
            ],
            'data-logika-ai': [
                'nilai = [70, 80, 90]\nrata = sum(nilai) / len(nilai)\nprint("Rata-rata:", rata)',
                'nilai = [70, 85, 60, 90]\nlulus = [x for x in nilai if x >= 80]\nprint("Yang lulus:", lulus)',
                'def label(n):\n    return "lulus" if n >= 80 else "remidi"\n\ndata = [85, 60, 92, 75]\nfor n in data:\n    print(n, "->", label(n))',
                'tebakan = ["lulus", "remidi", "lulus", "lulus"]\nbenar = ["lulus", "lulus", "lulus", "remidi"]\ncocok = sum(1 for a, b in zip(tebakan, benar) if a == b)\nprint("Akurasi:", cocok / len(benar) * 100, "%")',
            ],
        };
        const DILARANG = ['import os', 'import sys', 'import subprocess', 'import socket', 'open('];
        const awal = (CONTOH[slug] && CONTOH[slug][0]) || 'print("Halo AI")';
        return {
            kode: awal,
            keluaran: '',
            status: 'Siap — tulis kodemu lalu Jalankan',
            sibuk: false,
            memuat: false,
            worker: null,
            timerHang: null,
            contoh() {
                return CONTOH[slug] || [];
            },
            isiContoh(i) {
                const daftar = this.contoh();
                if (daftar[i] !== undefined) {
                    this.kode = daftar[i];
                    this.keluaran = '';
                    this.status = 'Contoh dimuat — tekan Jalankan!';
                }
            },
            jalankan() {
                if (this.sibuk) return;
                if (this.kode.trim() === '') {
                    this.status = 'Tulis dulu kodemu (atau klik salah satu Contoh)!';
                    return;
                }
                if (this.kode.length > 2000) {
                    this.status = 'Kodenya kepanjangan (maks 2000 karakter). Pendekkan dulu ya!';
                    return;
                }
                const kena = DILARANG.find(p => this.kode.includes(p));
                if (kena) {
                    this.status = 'Demi keamanan, "' + kena + '" tidak diizinkan di latihan ini.';
                    return;
                }
                this.sibuk = true;
                this.memuat = true;
                this.keluaran = '';
                this.status = 'Menyiapkan Python... (unduhan ±10MB hanya saat pertama)';
                try {
                    this.worker = new Worker(@js(asset('js/pyodide-worker.js')));
                } catch (e) {
                    this.sibuk = false; this.memuat = false;
                    this.status = 'Browser tidak mendukung Web Worker. Coba Chrome/Edge/Firefox terbaru.';
                    return;
                }
                const vm = this;
                this.worker.onmessage = function (e) {
                    const m = e.data || {};
                    if (m.type === 'status' && m.data === 'mengeksekusi') {
                        vm.memuat = false;
                        vm.status = 'Menjalankan kodemu... (maks 10 detik)';
                    }
                    if (m.type === 'selesai') {
                        vm.bersih();
                        if (m.data && m.data.ok) {
                            vm.keluaran = m.data.output === '' ? '(Program selesai tanpa keluaran. Coba tambah print(...).)' : m.data.output;
                            vm.status = 'Selesai ✓ — ubah kodenya dan Jalankan lagi!';
                        } else {
                            vm.keluaran = (m.data && m.data.output ? m.data.output + '\n' : '') + 'ERROR: ' + ((m.data && m.data.error) || 'tidak diketahui');
                            vm.status = (m.data && m.data.offline) ? 'Gagal memuat (butuh internet sekali) — coba lagi!' : 'Program error — baca pesan merah di atas, perbaiki, Jalankan lagi!';
                        }
                    }
                };
                this.worker.onerror = function () {
                    vm.bersih();
                    vm.keluaran = '';
                    vm.status = 'Gagal memuat Python (butuh internet sekali untuk ±10MB). Periksa koneksi lalu Jalankan lagi.';
                };
                this.worker.postMessage({ cmd: 'jalankan', code: this.kode });
                // Anti-hang wajib: terminate bila lewat 10 detik.
                this.timerHang = setTimeout(function () {
                    if (!vm.sibuk) return;
                    vm.bersih();
                    vm.keluaran = (vm.keluaran ? vm.keluaran + '\n' : '') + 'WAKTU HABIS (10 detik) — program dihentikan. Mungkin ada while True tanpa henti. Perbaiki lalu Jalankan lagi!';
                    vm.status = 'Dihentikan paksa (timeout 10 detik)';
                }, 10000);
            },
            hentikan() {
                if (!this.sibuk) return;
                this.bersih();
                this.status = 'Dihentikan. Tekan Jalankan untuk mencoba lagi.';
            },
            bersih() {
                if (this.timerHang) { clearTimeout(this.timerHang); this.timerHang = null; }
                if (this.worker) { try { this.worker.terminate(); } catch (e) {} this.worker = null; }
                this.sibuk = false;
                this.memuat = false;
            },
        }
    }
    </script>
    @endpush
    @endif
</x-sidebar-layout>
