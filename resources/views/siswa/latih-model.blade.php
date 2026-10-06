<x-sidebar-layout>
    <x-slot name="header">
        <a href="{{ route('siswa.bahan.show', $module->slug) }}" class="text-sm font-semibold text-orange-500">← Modul 2</a>
        <h2 class="font-extrabold text-xl text-slate-900 dark:text-white mt-1">Latih Modelmu Sendiri</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400">Seperti Teachable Machine: beri label, kumpulkan contoh webcam, latih, uji — 100% di browser, tanpa upload.</p>
    </x-slot>

    @vite('resources/js/ai/trainer.js')
    <div class="max-w-4xl space-y-4" x-data="kkaLatih()" x-init="boot()">
        <x-card title="Langkah 1 — Label (2 sampai 5)" subtitle="Nama benda yang mau dikenali, mis. Botol dan Buku.">
            <div class="flex gap-2">
                <input x-model="labelBaru" @keydown.enter.prevent="tambahLabel()" maxlength="30" placeholder="Nama label baru..." class="flex-1 rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 text-sm focus:border-orange-500 focus:ring-orange-500" />
                <x-button type="button" @click="tambahLabel()">Tambah</x-button>
            </div>
            <p x-show="galatLabel" x-text="galatLabel" class="mt-2 text-xs font-bold text-red-600" role="alert"></p>
            <div class="mt-3 flex flex-wrap gap-2">
                <template x-for="(label, i) in labels" :key="label">
                    <span class="kka-badge !py-1.5">
                        <span x-text="label + ' (' + (jumlah[i] || 0) + ')'"></span>
                        <button type="button" @click="hapusLabel(i)" aria-label="Hapus label" class="font-extrabold hover:text-red-600">×</button>
                    </span>
                </template>
                <p x-show="!labels.length" class="text-sm text-slate-500 dark:text-slate-400">Belum ada label.</p>
            </div>
        </x-card>

        <x-card title="Langkah 2 — Kumpulkan Contoh (min. 20 per label)" subtitle="Aktifkan kamera, arahkan ke benda, tekan +1 berulang (atau +10). Variasikan sudut & cahaya!">
            <p class="text-xs font-bold" :class="modelSiap ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-500 dark:text-slate-400'" x-text="statusModel" role="status"></p>
            <div class="mt-3 rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-700 bg-slate-900 aspect-video flex items-center justify-center relative">
                <video x-ref="video" class="w-full h-full object-cover" playsinline muted></video>
                <template x-if="!cameraOn"><p class="absolute text-white/80 text-sm px-6 text-center">Aktifkan kamera untuk mulai mengumpulkan contoh.</p></template>
            </div>
            <div class="mt-3 flex flex-wrap gap-2">
                <button type="button" @click="toggleKamera()" class="kka-btn-primary !py-2 text-sm" x-text="cameraOn ? 'Matikan Kamera' : 'Aktifkan Kamera'"></button>
            </div>
            <div class="mt-3 space-y-2">
                <template x-for="(label, i) in labels" :key="'c-'+label">
                    <div class="flex items-center gap-2 rounded-2xl border border-slate-200 dark:border-slate-700 px-4 py-2">
                        <span class="flex-1 text-sm font-bold text-slate-800 dark:text-slate-100" x-text="label + ' — ' + (jumlah[i] || 0) + '/20'"></span>
                        <button type="button" @click="ambil(i, 1)" :disabled="!bisaLatih()" class="kka-btn-secondary !py-1.5 text-xs disabled:opacity-40">+1</button>
                        <button type="button" @click="ambil(i, 10)" :disabled="!bisaLatih()" class="kka-btn-secondary !py-1.5 text-xs disabled:opacity-40">+10</button>
                    </div>
                </template>
            </div>
        </x-card>

        <x-card title="Langkah 3 — Uji Beruntun (5× benar)" subtitle="Pilih label yang sedang di depan kamera, tekan Uji, ulangi sampai 5 benar beruntun.">
            <div class="flex flex-col sm:flex-row gap-2">
                <select x-model.number="ujiLabel" class="rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 text-sm">
                    <template x-for="(label, i) in labels" :key="'u-'+label">
                        <option :value="i" x-text="label"></option>
                    </template>
                </select>
                <x-button type="button" @click="uji()">Uji Sekarang</x-button>
            </div>
            <p class="mt-2 text-sm font-bold" x-text="hasilUji" role="status"></p>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Beruntun benar: <b x-text="streak"></b>/5</p>
        </x-card>

        <x-card title="Langkah 4 — Kirim ke Guru" subtitle="Gurumu memeriksa dulu; yang disetujui tayang sebagai Model Global untuk semua.">
            <ul class="space-y-1 text-sm">
                <template x-for="s in syaratList" :key="s.teks">
                    <li class="flex items-center gap-2" :class="s.ok ? 'text-emerald-700 dark:text-emerald-400 font-bold' : 'text-slate-500 dark:text-slate-400'">
                        <span x-text="s.ok ? '✓' : '○'"></span><span x-text="s.teks"></span>
                    </li>
                </template>
            </ul>
            <div class="mt-3 flex flex-col sm:flex-row gap-2">
                <input x-model="namaModel" maxlength="60" placeholder="Nama modelmu, mis. Deteksi Botol vs Buku" class="flex-1 rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 text-sm focus:border-orange-500 focus:ring-orange-500" />
            </div>
            <div class="mt-2 flex flex-wrap gap-2">
                <button type="button" @click="kirim()" :disabled="mengirim" class="kka-btn-primary !py-2 text-sm disabled:opacity-50" x-text="mengirim ? 'Mengirim...' : 'Kirim ke Guru'"></button>
                <button type="button" @click="simpanDraf(true)" class="kka-btn-secondary !py-2 text-sm">Simpan Draf</button>
                <button type="button" @click="resetSemua()" class="kka-btn-secondary !py-2 text-sm !text-red-600">Ulangi dari Nol</button>
            </div>
            <p class="mt-2 text-xs font-semibold" :class="pesanOk ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-500 dark:text-slate-400'" x-text="pesanDraf" role="status"></p>
        </x-card>
    </div>

    <script>
    function kkaLatih() {
        return {
            labels: [], jumlah: {}, labelBaru: '', galatLabel: '',
            cameraOn: false, modelSiap: false, statusModel: 'Memuat backbone MobileNet...',
            ujiLabel: 0, hasilUji: '', streak: 0, pesanDraf: '', pesanOk: false,
            namaModel: '', mengirim: false,
            pelatih: null, kunciDraf: 'kka-draf-{{ auth()->id() }}',
            async boot() {
                const lib = window.KkaTrainer;
                if (!lib) { this.statusModel = 'Modul latih gagal dimuat. Muat ulang halaman.'; return; }
                this.pelatih = new lib.Pelatih();
                try {
                    await this.pelatih.load();
                    this.modelSiap = true;
                    this.statusModel = 'Model siap — aktifkan kamera!';
                } catch (e) {
                    this.statusModel = 'Gagal memuat model (butuh internet sekali). Muat ulang halaman.';
                    return;
                }
                try {
                    const draf = await lib.bacaDraf(this.kunciDraf);
                    if (draf && draf.labels && draf.dataset) {
                        this.labels = draf.labels;
                        this.pelatih.restore(draf.dataset, draf.labels);
                        this.segarkanJumlah();
                        this.pesanDraf = 'Draf terakhir dimuat kembali.';
                    }
                } catch (e) { /* draf opsional */ }
            },
            tambahLabel() {
                const lib = window.KkaTrainer;
                const r = lib.validasiLabel([...this.labels, this.labelBaru]);
                if (!r.ok) { this.galatLabel = r.galat; return; }
                this.galatLabel = '';
                this.labels = r.labels;
                this.labelBaru = '';
                this.segarkanJumlah();
                this.simpanDraf(false);
            },
            hapusLabel(i) {
                this.labels.splice(i, 1);
                this.pelatih.reset();
                this.streak = 0;
                this.segarkanJumlah();
            },
            segarkanJumlah() {
                const c = this.pelatih ? this.pelatih.jumlahPerKelas() : {};
                const j = {};
                this.labels.forEach((nama, i) => { j[i] = c[i] || 0; });
                this.jumlah = j;
            },
            bisaLatih() { return this.modelSiap && this.cameraOn && this.labels.length >= 2; },
            async toggleKamera() {
                if (this.cameraOn) {
                    this.pelatih.hentikanKamera(this.$refs.video);
                    this.cameraOn = false;
                    return;
                }
                try {
                    await this.pelatih.mulaiKamera(this.$refs.video);
                    this.cameraOn = true;
                } catch (e) {
                    this.statusModel = 'Kamera ditolak — izinkan akses kamera lalu coba lagi.';
                }
            },
            async ambil(i, n) {
                for (let k = 0; k < n; k++) {
                    try { this.pelatih.tambahContoh(this.$refs.video, i); }
                    catch (e) { this.statusModel = 'Gagal mengambil contoh. Coba lagi.'; break; }
                    if (k % 5 === 4) await new Promise((r) => setTimeout(r, 120));
                }
                this.segarkanJumlah();
                this.streak = 0;
                this.simpanDraf(false);
            },
            async uji() {
                if (!this.bisaLatih()) { this.hasilUji = 'Aktifkan kamera dan pastikan minimal 2 label.'; return; }
                const nama = this.labels[this.ujiLabel];
                if ((this.jumlah[this.ujiLabel] || 0) === 0) { this.hasilUji = 'Label "' + nama + '" belum punya contoh.'; return; }
                try {
                    const hasil = await this.pelatih.uji(this.$refs.video);
                    if (!hasil) { this.hasilUji = 'Belum ada data latih.'; return; }
                    if (Number(hasil.label) === this.ujiLabel) {
                        this.streak++;
                        this.hasilUji = 'Benar! "' + nama + '" dikenali. (' + this.streak + '/5)';
                    } else {
                        this.streak = 0;
                        this.hasilUji = 'Kurang tepat — terdeteksi "' + (this.labels[Number(hasil.label)] || '?') + '". Tambah contoh ' + nama + ' yang bervariasi!';
                    }
                } catch (e) { this.hasilUji = 'Gagal menguji. Coba lagi.'; }
            },
            get syaratList() {
                const lib = window.KkaTrainer;
                if (!lib) return [];
                const jumlahNama = {};
                this.labels.forEach((nama, i) => { jumlahNama[nama] = this.jumlah[i] || 0; });
                const cek = lib.cekSyarat(jumlahNama, this.streak);
                const semua = [];
                this.labels.forEach((nama) => {
                    const n = jumlahNama[nama];
                    semua.push({ teks: nama + ': ' + n + '/20 contoh', ok: n >= 20 });
                });
                semua.push({ teks: 'Uji beruntun: ' + this.streak + '/5', ok: this.streak >= 5 });
                if (!cek.ok && !this.labels.length) semua.push({ teks: 'Buat minimal 2 label dulu', ok: false });
                return semua;
            },
            async simpanDraf(manual) {
                const lib = window.KkaTrainer;
                if (!lib || !this.labels.length) { if (manual) this.pesanDraf = 'Belum ada yang disimpan.'; return; }
                try {
                    await lib.tulisDraf(this.kunciDraf, {
                        labels: this.labels,
                        dataset: this.pelatih.serialize(this.labels),
                        saat: new Date().toISOString(),
                    });
                    this.pesanDraf = 'Draf tersimpan di perangkat ini.';
                } catch (e) { this.pesanDraf = 'Gagal menyimpan draf di perangkat ini.'; }
            },
            async kirim() {
                const lib = window.KkaTrainer;
                if (this.mengirim) return;
                if (!this.namaModel.trim()) { this.pesanOk = false; this.pesanDraf = 'Isi dulu nama modelmu!'; return; }
                const jumlahNama = {};
                this.labels.forEach((nama, i) => { jumlahNama[nama] = this.jumlah[i] || 0; });
                const cek = lib.cekSyarat(jumlahNama, this.streak);
                if (!cek.ok) { this.pesanOk = false; this.pesanDraf = 'Belum memenuhi syarat: ' + cek.alasan.join(' '); return; }
                this.mengirim = true;
                this.pesanOk = false;
                this.pesanDraf = 'Mengirim dataset ke guru...';
                try {
                    const res = await fetch(@js(route('siswa.latih.publish')), {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            name: this.namaModel.trim(),
                            labels: this.labels,
                            dataset: this.pelatih.serialize(this.labels),
                        }),
                    });
                    const data = await res.json();
                    if (!res.ok) {
                        const galat = data.errors ? Object.values(data.errors).flat().join(' ') : (data.message || 'Gagal mengirim.');
                        throw new Error(galat);
                    }
                    this.pesanOk = true;
                    this.pesanDraf = data.message;
                    this.namaModel = '';
                } catch (e) {
                    this.pesanOk = false;
                    this.pesanDraf = String((e && e.message) || e);
                }
                this.mengirim = false;
            },
            resetSemua() {
                if (!confirm('Hapus semua label, contoh, dan draf?')) return;
                this.labels = []; this.jumlah = {}; this.streak = 0;
                this.pelatih.reset();
                window.KkaTrainer.tulisDraf(this.kunciDraf, null).catch(() => {});
                this.pesanDraf = 'Draf dihapus.';
            },
        };
    }
    </script>
</x-sidebar-layout>
