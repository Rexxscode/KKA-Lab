<x-sidebar-layout>
    <x-slot name="header">
        <a href="{{ route('guru.quiz.index') }}" class="text-sm font-semibold text-orange-500">← Semua Modul</a>
        <h2 class="font-extrabold text-xl text-slate-900 mt-1">Soal: {{ $module->title }}</h2>
        <p class="text-sm text-slate-500">{{ $module->quizzes->count() }} soal • Poin per soal 20 • Lulus jika skor ≥80.</p>
    </x-slot>

    <x-card title="Tambah Soal Baru" subtitle="Untuk modul ini saja.">
        <form method="POST" action="{{ route('guru.quiz.module.store', $module) }}" class="grid sm:grid-cols-2 gap-2 sm:gap-3">
            @csrf
            <input name="question" required placeholder="Tulis pertanyaan..." class="sm:col-span-2 rounded-xl border-slate-200 focus:border-orange-500 focus:ring-orange-500" />
            <input name="options[]" required placeholder="Opsi A" class="rounded-xl border-slate-200 focus:border-orange-500 focus:ring-orange-500" />
            <input name="options[]" required placeholder="Opsi B" class="rounded-xl border-slate-200 focus:border-orange-500 focus:ring-orange-500" />
            <input name="options[]" placeholder="Opsi C (opsional)" class="rounded-xl border-slate-200 focus:border-orange-500 focus:ring-orange-500" />
            <input name="options[]" placeholder="Opsi D (opsional)" class="rounded-xl border-slate-200 focus:border-orange-500 focus:ring-orange-500" />
            <label class="text-xs font-semibold text-slate-600">Jawaban benar (0=A, 1=B, 2=C, 3=D)
                <input name="correct_index" type="number" min="0" max="4" value="0" required class="mt-1 w-full rounded-xl border-slate-200 focus:border-orange-500 focus:ring-orange-500" />
            </label>
            <div class="sm:col-span-2"><x-button type="submit">Simpan Soal</x-button></div>
        </form>
    </x-card>

    <h3 class="mt-6 mb-3 font-extrabold text-slate-900">Daftar Soal ({{ $module->quizzes->count() }})</h3>
    <div class="space-y-3">
        @forelse($module->quizzes as $idx => $q)
            <x-card :title="'Soal ' . ($idx + 1)" :subtitle="$q->question">
                <div class="space-y-1.5 text-sm">
                    @foreach($q->options as $oi => $opt)
                        <p class="{{ $oi == $q->correct_index ? 'font-bold text-emerald-600' : 'text-slate-600' }}">
                            {{ chr(65 + $oi) }}. {{ $opt }} @if($oi == $q->correct_index) ✓ @endif
                        </p>
                    @endforeach
                    @if($q->explanation)
                        <p class="text-xs text-slate-500 rounded-xl bg-slate-50 border border-slate-200 p-2">💡 {{ $q->explanation }}</p>
                    @endif
                </div>
                <div class="mt-3 flex flex-wrap gap-2">
                    <details class="relative">
                        <summary class="kka-btn-secondary !py-1.5 text-xs cursor-pointer list-none">Ubah</summary>
                        <form method="POST" action="{{ route('guru.quiz.update', $q) }}" class="mt-2 rounded-2xl border border-slate-200 p-3 grid sm:grid-cols-2 gap-2">
                            @csrf @method('PATCH')
                            <input name="question" value="{{ $q->question }}" required class="sm:col-span-2 rounded-xl border-slate-200 text-sm" />
                            @foreach($q->options as $opt)
                                <input name="options[]" value="{{ $opt }}" required class="rounded-xl border-slate-200 text-sm" />
                            @endforeach
                            <input name="correct_index" type="number" min="0" value="{{ $q->correct_index }}" required class="rounded-xl border-slate-200 text-sm" />
                            <div class="sm:col-span-2"><button class="kka-btn-primary !py-1.5 text-xs">Simpan Perubahan</button></div>
                        </form>
                    </details>
                    <form method="POST" action="{{ route('guru.quiz.destroy', $q) }}" onsubmit="return confirm('Hapus soal ini?')">
                        @csrf @method('DELETE')
                        <button class="kka-btn-secondary !py-1.5 text-xs !text-red-600">Hapus</button>
                    </form>
                </div>
            </x-card>
        @empty
            <x-card title="Belum ada soal" subtitle="Tambah soal pertama lewat formulir di atas." />
        @endforelse
    </div>
</x-sidebar-layout>
