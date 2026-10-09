<x-sidebar-layout>
    <x-slot name="header">
        <a href="{{ route('siswa.dashboard') }}" class="text-sm font-semibold text-orange-500">← Dasbor</a>
        <h2 class="font-extrabold text-xl text-slate-900 dark:text-white mt-1">Model Global</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400">Karya siswa pilihan guru, bisa dicoba semua orang langsung di browser.</p>
    </x-slot>

    @vite('resources/js/ai/trainer.js')
    <div class="max-w-3xl" x-data="kkaGlobal()" x-init="boot()">
        @if($global)
            <x-card :title="$global->name" :subtitle="'v' . $global->version . ' • Label: ' . implode(', ', $global->labels) . ($global->accuracy !== null ? ' • Akurasi uji guru: ' . $global->accuracy . '%' : '')">
                <div class="rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-700 bg-slate-900 aspect-video flex items-center justify-center relative">
                    <video x-ref="video" class="w-full h-full object-cover" playsinline muted></video>
                    <template x-if="!cameraOn"><p class="absolute text-white/80 text-sm px-6 text-center">Aktifkan kamera, arahkan ke benda, tekan Uji.</p></template>
                </div>
                <div class="mt-3 flex flex-wrap gap-2">
                    <button type="button" @click="toggleKamera()" class="kka-btn-primary !py-2 text-sm" x-text="cameraOn ? 'Matikan Kamera' : 'Aktifkan Kamera'"></button>
                    <button type="button" @click="uji()" :disabled="!siap" class="kka-btn-secondary !py-2 text-sm disabled:opacity-40">Uji Sekarang</button>
                </div>
                <p class="mt-2 text-sm font-bold text-slate-800 dark:text-slate-100" x-text="hasil" role="status"></p>
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400" x-text="status" role="status"></p>
            </x-card>
        @else
            <x-card title="Belum ada model global" subtitle="Guru belum menyetujui model siswa mana pun. Jadilah yang pertama lewat halaman Latih Model!">
                <a href="{{ route('siswa.latih.index') }}" class="kka-btn-primary !py-2 text-sm">Ke Halaman Latih</a>
            </x-card>
        @endif
    </div>

    @if($global)
    <script>
    function kkaGlobal() {
        return {
            cameraOn: false, siap: false, hasil: '', status: 'Memuat model global...',
            pelatih: null,
            dataset: @js($global->dataset),
            labels: @js($global->labels),
            async boot() {
                const lib = window.KkaTrainer;
                if (!lib) { this.status = 'Modul gagal dimuat. Muat ulang halaman.'; return; }
                this.pelatih = new lib.Pelatih();
                try {
                    await this.pelatih.load();
                    this.pelatih.restore(this.dataset, this.labels);
                    this.siap = true;
                    this.status = 'Model "' + @js($global->name) + '" siap — aktifkan kamera!';
                } catch (e) { this.status = 'Gagal memuat model. Muat ulang halaman.'; }
            },
            async toggleKamera() {
                if (this.cameraOn) { this.pelatih.hentikanKamera(this.$refs.video); this.cameraOn = false; return; }
                try { await this.pelatih.mulaiKamera(this.$refs.video); this.cameraOn = true; }
                catch (e) { this.status = 'Kamera ditolak — izinkan akses kamera lalu coba lagi.'; }
            },
            async uji() {
                if (!this.siap || !this.cameraOn) return;
                try {
                    const hasil = await this.pelatih.uji(this.$refs.video, { minConfidence: 0.6 });
                    if (!hasil) { this.hasil = 'Model kosong (butuh 2 label).'; return; }
                    if (hasil.butuhData) { this.hasil = hasil.alasan; return; }
                    const persen = Math.round((hasil.confidence || 0) * 100);
                    if (hasil.tidakYakin) {
                        this.hasil = 'Tidak yakin (' + persen + '%) — coba dekatkan benda / perbaiki cahaya.';
                        return;
                    }
                    this.hasil = 'Terdeteksi: "' + (this.labels[Number(hasil.label)] || '?') + '" (' + persen + '%)';
                } catch (e) { this.hasil = 'Gagal menguji. Coba lagi.'; }
            },
        };
    }
    </script>
    @endif
</x-sidebar-layout>
