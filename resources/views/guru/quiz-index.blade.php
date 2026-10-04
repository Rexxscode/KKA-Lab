<x-sidebar-layout>
    <x-slot name="header">
        <h2 class="font-extrabold text-xl text-slate-900">Bank Soal (Guru bisa edit)</h2>
        <p class="text-sm text-slate-500">Tambah / ubah / hapus soal per modul. Skor lulus tetap ≥80.</p>
    </x-slot>

    <div class="py-8 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        <x-card title="Tambah Soal" subtitle="Poin per soal = 20.">
            <form method="POST" action="{{ route('guru.quiz.store') }}" class="grid sm:grid-cols-2 gap-3">
                @csrf
                <select name="module_id" required class="rounded-xl border-slate-200 focus:border-orange-500 focus:ring-orange-500">
                    @foreach($modules as $m)<option value="{{ $m->id }}">{{ $m->title }}</option>@endforeach
                </select>
                <input name="correct_index" type="number" min="0" max="4" required placeholder="Index jawaban benar (0-...)" class="rounded-xl border-slate-200 focus:border-orange-500 focus:ring-orange-500" />
                <input name="question" required placeholder="Pertanyaan..." class="sm:col-span-2 rounded-xl border-slate-200 focus:border-orange-500 focus:ring-orange-500" />
                <input name="options[]" required placeholder="Opsi 1 (benar jika index 0)" class="rounded-xl border-slate-200" />
                <input name="options[]" required placeholder="Opsi 2" class="rounded-xl border-slate-200" />
                <input name="options[]" placeholder="Opsi 3 (opsional)" class="rounded-xl border-slate-200" />
                <input name="options[]" placeholder="Opsi 4 (opsional)" class="rounded-xl border-slate-200" />
                <div class="sm:col-span-2"><x-button type="submit">Simpan Soal</x-button></div>
            </form>
        </x-card>

        @foreach($modules as $m)
            <x-card :title="$m->title" :subtitle="$m->quizzes->count().' soal'">
                <div class="space-y-3">
                    @foreach($m->quizzes as $q)
                        <details class="rounded-2xl border border-slate-200 p-4">
                            <summary class="font-semibold text-sm cursor-pointer">{{ $q->question }} <span class="text-emerald-600">(benar: {{ $q->options[$q->correct_index] ?? '-' }})</span></summary>
                            <form method="POST" action="{{ route('guru.quiz.update', $q) }}" class="mt-3 grid sm:grid-cols-2 gap-2">
                                @csrf @method('PATCH')
                                <input name="question" value="{{ $q->question }}" required class="sm:col-span-2 rounded-xl border-slate-200 text-sm" />
                                @foreach($q->options as $i => $opt)
                                    <input name="options[]" value="{{ $opt }}" required class="rounded-xl border-slate-200 text-sm" />
                                @endforeach
                                <input name="correct_index" type="number" min="0" value="{{ $q->correct_index }}" required class="rounded-xl border-slate-200 text-sm" />
                                <div class="sm:col-span-2 flex gap-2">
                                    <button class="kka-btn-primary !py-1.5 text-xs">Simpan</button>
                            </form>
                            <form method="POST" action="{{ route('guru.quiz.destroy', $q) }}" onsubmit="return confirm('Hapus soal ini?')">
                                @csrf @method('DELETE')
                                <button class="kka-btn-secondary !py-1.5 text-xs !text-red-600">Hapus</button>
                            </form>
                                </div>
                        </details>
                    @endforeach
                </div>
            </x-card>
        @endforeach
    </div>
</x-sidebar-layout>
