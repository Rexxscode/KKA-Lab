<x-sidebar-layout>
    @php
        $milikSaya = auth()->user()->isAdmin() ? $module->quizzes->whereNull('teacher_id')->count() : $module->quizzes->where('teacher_id', auth()->id())->count();
        $targetBank = auth()->user()->isAdmin() ? \App\Services\StudentModuleService::BANK_DEFAULT_TARGET : \App\Services\StudentModuleService::BANK_GURU_MAX;
        $minBank = \App\Services\StudentModuleService::BANK_GURU_MIN;
    @endphp
    <x-slot name="header">
        <h2 class="font-extrabold text-xl text-slate-900 dark:text-white">Soal: {{ $module->title }}</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400">Bank saya: <b>{{ $milikSaya }}/{{ $targetBank }}</b> • Poin per soal 20 • Lulus jika skor ≥80.</p>
        @if(! auth()->user()->isAdmin() && $milikSaya < $minBank)
            <p class="mt-2 text-xs font-bold rounded-xl bg-orange-50 dark:bg-orange-500/10 border border-orange-200 dark:border-orange-500/30 text-orange-700 dark:text-orange-300 p-2.5">Bank belum lengkap — latihan siswa terkunci sampai bank mencapai {{ $minBank }} soal. Tambah {{ $minBank - $milikSaya }} soal lagi!</p>
        @endif
    </x-slot>

    <x-card title="Tambah Banyak Soal Sekaligus" subtitle="Isi beberapa soal lalu simpan sekali — maks 30 per submit, bank guru wajib 25–30.">
        @if($module->isDefault() && ! auth()->user()->isAdmin())
            <p class="text-xs rounded-xl bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/30 text-emerald-700 dark:text-emerald-400 p-2.5 mb-3">Ini soal default admin. Silakan tambah/edit langsung — tersimpan otomatis sebagai versi kelas-kelasmu, soal admin tetap aman.</p>
        @endif
        <form method="POST" action="{{ route('guru.quiz.module.bulk', $module) }}" x-data="{ jumlah: 1, maks: 30 }" class="space-y-3">
            @csrf
            <template x-for="i in jumlah" :key="i">
                <div class="rounded-2xl border border-slate-200 dark:border-slate-700 p-3 sm:p-4 space-y-2.5">
                    <div class="flex items-center justify-between gap-2">
                        <p class="text-xs font-extrabold text-slate-600 dark:text-slate-300" x-text="'Soal baru #' + i"></p>
                        <button type="button" @click="if (jumlah > 1) jumlah--" x-show="jumlah > 1" class="text-xs font-bold text-red-600 hover:text-red-700">Hapus blok</button>
                    </div>
                    <label class="block text-xs font-bold text-slate-600 dark:text-slate-300">Pertanyaan
                        <input :name="`soal[${i-1}][question]`" required placeholder="Tulis pertanyaan..." class="mt-1 w-full rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 text-sm focus:border-orange-500 focus:ring-orange-500" />
                    </label>
                    <template x-for="(huruf, oi) in ['A','B','C','D','E']" :key="huruf">
                        <label class="flex items-center gap-2.5 text-sm">
                            <span class="inline-flex items-center justify-center w-7 h-7 shrink-0 rounded-lg bg-slate-100 dark:bg-slate-700 font-extrabold text-slate-600 dark:text-slate-300" x-text="huruf"></span>
                            <input :name="`soal[${i-1}][options][${oi}]`" required :placeholder="`Opsi ${huruf} (wajib diisi)`" class="flex-1 min-w-0 rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 text-sm focus:border-orange-500 focus:ring-orange-500" />
                        </label>
                    </template>
                    <div class="grid sm:grid-cols-2 gap-2.5">
                        <label class="flex items-center gap-2.5 text-sm font-semibold text-slate-700 dark:text-slate-200">Jawaban benar:
                            <select :name="`soal[${i-1}][correct_letter]`" required class="rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 text-sm focus:border-orange-500 focus:ring-orange-500">
                                <option value="A">A</option><option value="B">B</option><option value="C">C</option><option value="D">D</option><option value="E">E</option>
                            </select>
                        </label>
                        <label class="block text-xs font-bold text-slate-600 dark:text-slate-300">Pembahasan (opsional)
                            <input :name="`soal[${i-1}][explanation]`" placeholder="Kenapa jawaban itu benar..." class="mt-1 w-full rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 text-sm focus:border-orange-500 focus:ring-orange-500" />
                        </label>
                    </div>
                </div>
            </template>
            <div class="flex flex-col sm:flex-row gap-2">
                <button type="button" @click="if (jumlah < maks) jumlah++" :disabled="jumlah >= maks" class="kka-btn-secondary !py-2 text-sm text-center disabled:opacity-40">+ Tambah Soal Lagi (<span x-text="jumlah"></span>/30)</button>
                <x-button type="submit" class="text-center">Simpan Semua Soal</x-button>
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400">Tips: blok kosong yang tidak diisi otomatis dilewati. Bank guru wajib 25–30 soal agar latihan terbuka untuk siswa (tiap latihan keluar 15 acak).</p>
        </form>
    </x-card>

    <h3 class="mt-6 mb-3 font-extrabold text-slate-900 dark:text-white">Daftar Soal ({{ $module->quizzes->count() }})</h3>
    <div x-data="{ cari: '' }">
        <label for="cari-soal" class="sr-only">Cari soal</label>
        <input id="cari-soal" x-model="cari" type="search" placeholder="Cari teks soal..." class="w-full sm:max-w-sm rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 text-sm focus:border-orange-500 focus:ring-orange-500 mb-3" />
    <p class="text-xs text-slate-500 dark:text-slate-400 mb-2">Klik baris soal untuk membuka opsi, pembahasan, dan tombol Ubah/Hapus.</p>
    <div class="space-y-2">
        @forelse($module->quizzes as $idx => $q)
            <div x-show="$el.dataset.cari.toLowerCase().includes(cari.toLowerCase())" data-cari="{{ strtolower($q->question) }}">
            <details class="kka-card !p-3 sm:!p-4">
                <summary class="flex items-center gap-3 cursor-pointer list-none">
                    <span class="inline-flex items-center justify-center w-7 h-7 shrink-0 rounded-lg bg-slate-100 dark:bg-slate-700 font-extrabold text-xs text-slate-600 dark:text-slate-300">{{ $idx + 1 }}</span>
                    <span class="flex-1 min-w-0">
                        <span class="block font-bold text-sm text-slate-800 dark:text-slate-100 truncate">{{ $q->question }}</span>
                        <span class="block text-xs text-slate-500 dark:text-slate-400 mt-0.5">Jawaban: <b class="text-emerald-600 dark:text-emerald-400">{{ chr(65 + $q->correct_index) }}</b>{{ $q->explanation ? ' • ada pembahasan' : '' }}</span>
                    </span>
                    <span class="kka-badge shrink-0 hidden sm:inline-flex">{{ chr(65 + $q->correct_index) }} ✓</span>
                </summary>
                <div class="mt-3 pt-3 border-t border-slate-100 dark:border-slate-800 space-y-1.5 text-sm">
                    @foreach($q->options as $oi => $opt)
                        <p class="{{ $oi == $q->correct_index ? 'font-bold text-emerald-600 dark:text-emerald-400' : 'text-slate-600 dark:text-slate-300' }}">
                            {{ chr(65 + $oi) }}. {{ $opt }} @if($oi == $q->correct_index) ✓ @endif
                        </p>
                    @endforeach
                    @if($q->explanation)
                        <p class="text-xs text-slate-500 dark:text-slate-400 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 p-2"><b>Pembahasan:</b> {{ $q->explanation }}</p>
                    @endif
                </div>
                <div class="mt-3 flex flex-wrap gap-2">
                    <details class="relative">
                        <summary class="kka-btn-secondary !py-1.5 text-xs cursor-pointer list-none">Ubah</summary>
                        <form method="POST" action="{{ route('guru.quiz.update', $q) }}" class="mt-2 rounded-2xl border border-slate-200 dark:border-slate-700 p-3 sm:p-4 space-y-2.5">
                            @csrf @method('PATCH')
                            <input name="question" value="{{ $q->question }}" required class="w-full rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 text-sm" />
                            @foreach($q->options as $oi => $opt)
                                <label class="flex items-center gap-2.5 text-sm">
                                    <span class="inline-flex items-center justify-center w-7 h-7 shrink-0 rounded-lg {{ $oi == $q->correct_index ? 'bg-emerald-100 dark:bg-emerald-500/15 text-emerald-700 dark:text-emerald-400' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300' }} font-extrabold">{{ chr(65 + $oi) }}</span>
                                    <input name="options[]" value="{{ $opt }}" required class="flex-1 rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 text-sm" />
                                </label>
                            @endforeach
                            @for($k = count($q->options); $k < 5; $k++)
                                <label class="flex items-center gap-2.5 text-sm">
                                    <span class="inline-flex items-center justify-center w-7 h-7 shrink-0 rounded-lg bg-orange-100 dark:bg-orange-500/15 text-orange-700 dark:text-orange-400 font-extrabold">{{ chr(65 + $k) }}</span>
                                    <input name="options[]" value="" required placeholder="Opsi {{ chr(65 + $k) }} (wajib diisi)" class="flex-1 rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 text-sm !border-orange-300" />
                                </label>
                            @endfor
                            <label class="flex items-center gap-2.5 text-sm font-semibold text-slate-700 dark:text-slate-200">Jawaban benar:
                                <select name="correct_letter" required class="rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 text-sm focus:border-orange-500 focus:ring-orange-500">
                                    @foreach(['A', 'B', 'C', 'D', 'E'] as $huruf)
                                        <option value="{{ $huruf }}" @selected(ord($huruf) - 65 == $q->correct_index)>{{ $huruf }}</option>
                                    @endforeach
                                </select>
                            </label>
                            <div><button class="kka-btn-primary !py-1.5 text-xs">Simpan Perubahan</button></div>
                        </form>
                    </details>
                    <form method="POST" action="{{ route('guru.quiz.destroy', $q) }}" onsubmit="return confirm('Hapus soal ini?')">
                        @csrf @method('DELETE')
                        <button class="kka-btn-secondary !py-1.5 text-xs !text-red-600">Hapus</button>
                    </form>
                </div>
            </details>
            </div>
        @empty
            <x-card title="Belum ada soal" subtitle="Tambah soal pertama lewat formulir di atas." />
        @endforelse
    </div>
    </div>
</x-sidebar-layout>
