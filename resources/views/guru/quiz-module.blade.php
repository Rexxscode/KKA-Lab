<x-sidebar-layout>
    <x-slot name="header">
        <h2 class="font-extrabold text-xl text-slate-900 dark:text-white">Soal: {{ $module->title }}</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400">{{ $module->quizzes->count() }} soal • Poin per soal 20 • Lulus jika skor ≥80.</p>
    </x-slot>

    <x-card title="Tambah Soal Baru" subtitle="Untuk modul ini saja.">
        @if($module->isDefault() && ! auth()->user()->isAdmin())
            <p class="text-xs rounded-xl bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/30 text-emerald-700 dark:text-emerald-400 p-2.5 mb-3">Ini soal default admin. Silakan edit langsung — perubahan otomatis tersimpan sebagai versi kelas-kelasmu, soal admin tetap aman.</p>
        @endif
        <form method="POST" action="{{ route('guru.quiz.module.store', $module) }}" class="space-y-2.5">
            @csrf
            <label for="soal-baru" class="text-xs font-bold text-slate-600 dark:text-slate-300">Pertanyaan baru</label>
            <input id="soal-baru" name="question" required placeholder="Tulis pertanyaan..." class="w-full rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 text-sm focus:border-orange-500 focus:ring-orange-500" />
            @foreach(['A', 'B', 'C', 'D', 'E'] as $huruf)
                <label for="opsi-baru-{{ $huruf }}" class="flex items-center gap-2.5 text-sm">
                    <span class="inline-flex items-center justify-center w-7 h-7 shrink-0 rounded-lg bg-slate-100 dark:bg-slate-700 font-extrabold text-slate-600 dark:text-slate-300" aria-hidden="true">{{ $huruf }}</span>
                    <input id="opsi-baru-{{ $huruf }}" name="options[]" required placeholder="Opsi {{ $huruf }} (wajib diisi)" class="flex-1 rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 text-sm focus:border-orange-500 focus:ring-orange-500" />
                </label>
            @endforeach
            <label for="jawaban-baru" class="flex items-center gap-2.5 text-sm font-semibold text-slate-700 dark:text-slate-200">Jawaban benar:
                <select id="jawaban-baru" name="correct_letter" required class="rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 text-sm focus:border-orange-500 focus:ring-orange-500">
                    @foreach(['A', 'B', 'C', 'D', 'E'] as $huruf)
                        <option value="{{ $huruf }}">{{ $huruf }}</option>
                    @endforeach
                </select>
            </label>
            <div><x-button type="submit">Simpan Soal</x-button></div>
        </form>
    </x-card>

    <h3 class="mt-6 mb-3 font-extrabold text-slate-900 dark:text-white">Daftar Soal ({{ $module->quizzes->count() }})</h3>
    <div x-data="{ cari: '' }">
        <label for="cari-soal" class="sr-only">Cari soal</label>
        <input id="cari-soal" x-model="cari" type="search" placeholder="Cari teks soal..." class="w-full sm:max-w-sm rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 text-sm focus:border-orange-500 focus:ring-orange-500 mb-3" />
    <div class="space-y-3">
        @forelse($module->quizzes as $idx => $q)
            <div x-show="$el.dataset.cari.toLowerCase().includes(cari.toLowerCase())" data-cari="{{ strtolower($q->question) }}">
            <x-card :title="'Soal ' . ($idx + 1)" :subtitle="$q->question">
                <div class="space-y-1.5 text-sm">
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
            </x-card>
            </div>
        @empty
            <x-card title="Belum ada soal" subtitle="Tambah soal pertama lewat formulir di atas." />
        @endforelse
    </div>
    </div>
</x-sidebar-layout>
