<x-sidebar-layout>
    <x-slot name="header">
        <h2 class="font-extrabold text-xl text-slate-900">Modul Saya</h2>
        <p class="text-sm text-slate-500">Buat modul sendiri untuk kelasmu. Modul default milik admin tidak bisa diubah.</p>
    </x-slot>

    <x-card title="Buat Modul Baru" subtitle="Otomatis ditempel ke semua kelasmu (posisi akhir).">
        <form method="POST" action="{{ route('guru.modul.store') }}" class="grid sm:grid-cols-2 gap-2 sm:gap-3">
            @csrf
            <input name="title" required maxlength="150" placeholder="Judul modul, cth: Jaringan Komputer Dasar" class="sm:col-span-2 rounded-xl border-slate-200 focus:border-orange-500 focus:ring-orange-500" />
            <input name="description" maxlength="500" placeholder="Deskripsi singkat (opsional)" class="rounded-xl border-slate-200 focus:border-orange-500 focus:ring-orange-500" />
            <label class="text-xs font-semibold text-slate-600">Hadiah XP lulus (isi 0 bila tanpa XP)
                <input name="xp_reward" type="number" min="0" max="1000" value="0" class="mt-1 w-full rounded-xl border-slate-200 focus:border-orange-500 focus:ring-orange-500" />
            </label>
            <label class="sm:col-span-2 text-xs font-semibold text-slate-600" for="materi-baru">Bahan ajar (wajib diisi — dibaca siswa sebelum kuis)
                <textarea id="materi-baru" name="materi" required rows="5" placeholder="Tulis materi bacaan: penjelasan konsep + contoh-contoh. Baris baru = paragraf baru." class="mt-1 w-full rounded-xl border-slate-200 text-sm focus:border-orange-500 focus:ring-orange-500"></textarea>
            </label>
            <div class="sm:col-span-2"><x-button type="submit">Buat Modul</x-button></div>
        </form>
    </x-card>

    <x-card title="Template Blok Praktik" subtitle="Klik untuk menempelkan kerangka siap isi ke kolom bahan ajar di atas. Penanda: ## judul, [PRAKTIK], [TUGAS], - [ ] checklist." class="mt-4">
        <div class="grid sm:grid-cols-3 gap-2" x-data="kkaTemplate()">
            <div class="rounded-2xl border border-slate-200 p-3">
                <p class="font-extrabold text-sm text-slate-800 flex items-center gap-1.5"><x-icon name="flask" class="w-4 h-4 text-orange-500" />Praktik Langkah</p>
                <p class="text-xs text-slate-500 mt-1">Langkah bernomor + kotak praktik.</p>
                <button type="button" @click="tempel('praktik')" class="kka-btn-secondary !py-1.5 text-xs mt-2 w-full">Tempel Template</button>
            </div>
            <div class="rounded-2xl border border-slate-200 p-3">
                <p class="font-extrabold text-sm text-slate-800 flex items-center gap-1.5"><x-icon name="check" class="w-4 h-4 text-emerald-600" />Tugas + Checklist</p>
                <p class="text-xs text-slate-500 mt-1">Tugas mandiri + daftar periksa.</p>
                <button type="button" @click="tempel('tugas')" class="kka-btn-secondary !py-1.5 text-xs mt-2 w-full">Tempel Template</button>
            </div>
            <div class="rounded-2xl border border-slate-200 p-3">
                <p class="font-extrabold text-sm text-slate-800 flex items-center gap-1.5"><x-icon name="chat" class="w-4 h-4 text-orange-500" />Diskusi + Refleksi</p>
                <p class="text-xs text-slate-500 mt-1">Pertanyaan diskusi + refleksi diri.</p>
                <button type="button" @click="tempel('diskusi')" class="kka-btn-secondary !py-1.5 text-xs mt-2 w-full">Tempel Template</button>
            </div>
            <p x-show="pesan" x-text="pesan" class="sm:col-span-3 text-xs font-bold text-emerald-700" role="status"></p>
            <template x-for="(isi, kunci) in contoh" :key="kunci"><span class="hidden"></span></template>
        </div>
    </x-card>

    @push('scripts')
    <script>
    function kkaTemplate() {
        return {
            pesan: '',
            contoh: {
                praktik: '## Tujuan Pembelajaran\nSetelah membaca bagian ini, siswa mampu menjelaskan konsep dengan contoh sendiri.\n\n## Penjelasan Konsep\nTulis penjelasan singkat di sini (3-5 kalimat). Tambahkan 1 contoh dari kehidupan sehari-hari.\n\n[PRAKTIK] Langkah 1: siapkan ... Langkah 2: coba ... Langkah 3: catat hasilnya dan bandingkan dengan teman.\n\n- [ ] Aku bisa menjelaskan ulang konsep ini tanpa melihat catatan\n- [ ] Aku sudah mencoba langkah praktik sampai selesai\n- [ ] Aku mencatat 1 hal yang masih membingungkan',
                tugas: '## Tugas Mandiri\nKerjakan tugas berikut setelah membaca penjelasan di atas.\n\n[TUGAS] Buat 1 contoh penerapan konsep ini di sekolah, foto/gambar bila perlu, lalu tulis penjelasan 3 kalimat.\n\n- [ ] Tugas ditulis dengan bahasa sendiri (bukan salin-tempel)\n- [ ] Ada contoh nyata dari lingkungan sekitar\n- [ ] Siap dipresentasikan 1 menit di kelas',
                diskusi: '## Bahan Diskusi\nBacalah pernyataan berikut, lalu diskusikan berpasangan.\n\n[PRAKTIK] Pernyataan: "Teknologi ini selalu menguntungkan." Setuju atau tidak? Tulis 1 alasan setuju dan 1 alasan tidak setuju.\n\n[TUGAS] Tulis refleksi 2 kalimat: apa hal baru yang kamu pelajari dan apa yang masih ingin kamu tanyakan?\n\n- [ ] Aku berani menyampaikan pendapatku\n- [ ] Aku mendengarkan pendapat teman sebelum menyimpulkan',
            },
            tempel(kunci) {
                const area = document.getElementById('materi-baru');
                if (!area) return;
                const sisip = this.contoh[kunci] + '\n\n';
                area.value = area.value ? area.value + '\n' + sisip : sisip;
                area.focus();
                this.pesan = 'Template ditempel ke kolom bahan ajar. Ganti isi contoh dengan materimu ya!';
            }
        }
    }
    </script>
    @endpush

    <h3 class="mt-6 mb-3 font-extrabold text-slate-900">Modul Milikmu ({{ $milik->count() }})</h3>
    <div class="grid sm:grid-cols-2 xl:grid-cols-3 gap-3 sm:gap-4">
        @forelse($milik as $m)
            <x-card :title="$m->title" :subtitle="$m->soal . ' soal • +' . $m->xp_reward . ' XP'">
                <p class="text-xs text-slate-500 line-clamp-2">{{ $m->description }}</p>
                <div class="flex flex-wrap gap-2 mt-3">
                    <a href="{{ route('guru.quiz.module', $m) }}" class="kka-btn-primary !py-1.5 text-xs inline-flex items-center gap-1"><x-icon name="pencil" class="w-3.5 h-3.5" />Kelola Soal</a>
                    <details class="relative">
                        <summary class="kka-btn-secondary !py-1.5 text-xs cursor-pointer list-none">Ubah</summary>
                        <form method="POST" action="{{ route('guru.modul.update', $m) }}" class="mt-2 rounded-2xl border border-slate-200 p-3 space-y-2">
                            @csrf @method('PATCH')
                            <input name="title" value="{{ $m->title }}" required maxlength="150" class="w-full rounded-xl border-slate-200 text-sm" />
                            <input name="description" value="{{ $m->description }}" maxlength="500" placeholder="Deskripsi" class="w-full rounded-xl border-slate-200 text-sm" />
                            <textarea name="materi" rows="4" placeholder="Bahan ajar {{ $m->isDefault() ? '(kosongkan = pakai materi bawaan)' : '(wajib diisi)' }}" @if(! $m->isDefault()) required @endif class="w-full rounded-xl border-slate-200 text-sm">{{ $m->materi }}</textarea>
                            <input name="xp_reward" type="number" min="0" max="1000" value="{{ $m->xp_reward }}" class="w-full rounded-xl border-slate-200 text-sm" />
                            <button class="kka-btn-primary !py-1.5 text-xs w-full">Simpan</button>
                        </form>
                    </details>
                    <form method="POST" action="{{ route('guru.modul.destroy', $m) }}" onsubmit="return confirm('Hapus modul ini beserta soalnya?')">
                        @csrf @method('DELETE')
                        <button class="kka-btn-secondary !py-1.5 text-xs !text-red-600">Hapus</button>
                    </form>
                    <form method="POST" action="{{ route('guru.modul.duplikat', $m) }}">
                        @csrf
                        <button class="kka-btn-secondary !py-1.5 text-xs">Duplikat</button>
                    </form>
                </div>
            </x-card>
        @empty
            <x-card title="Belum ada modul sendiri" subtitle="Buat modul pertamamu lewat formulir di atas." />
        @endforelse
    </div>

    @if($default->count())
        <h3 class="mt-6 mb-3 font-extrabold text-slate-900">Modul Default (milik admin, baca saja)</h3>
        <div class="grid sm:grid-cols-2 xl:grid-cols-4 gap-3 sm:gap-4">
            @foreach($default as $m)
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                    <p class="font-bold text-sm">{{ $m->title }}</p>
                    <p class="text-xs text-slate-500 mt-1">{{ $m->soal }} soal</p>
                    <a href="{{ route('guru.quiz.module', $m) }}" class="text-xs font-bold text-orange-500 hover:text-orange-600">Lihat soal →</a>
                    <form method="POST" action="{{ route('guru.modul.duplikat', $m) }}" class="mt-2">
                        @csrf
                        <button class="kka-btn-secondary !py-1 text-xs w-full">Jadikan milikku</button>
                    </form>
                </div>
            @endforeach
        </div>
    @endif
</x-sidebar-layout>
