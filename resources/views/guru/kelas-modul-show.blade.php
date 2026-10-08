<x-sidebar-layout>
    <x-slot name="header">
        <a href="{{ route('guru.kelas.show', $classroom) }}" class="text-sm font-semibold text-orange-500">← {{ $classroom->name }} ({{ $classroom->code }})</a>
        <h2 class="font-extrabold text-xl text-slate-900 dark:text-white mt-1">{{ $module->title }}</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400">
            {{ $module->isDefault() ? 'Modul default' : 'Modul buatan ' . ($module->teacher->name ?? 'guru') }} • {{ $soal->count() }} soal • +{{ $module->xp_reward }} XP
            <span class="kka-badge ml-1">Lihat saja</span>
        </p>
    </x-slot>

    <x-card title="Bahan Ajar" subtitle="tampilan baca-saja">
        @if($module->materi)
            @include('siswa.modul.materi-custom', ['materi' => $module->materi])
        @else
            <p class="text-sm text-slate-600 dark:text-slate-300">{{ $module->description ?? 'Belum ada materi.' }}</p>
        @endif
    </x-card>

    <h3 class="mt-6 mb-3 font-extrabold text-slate-900 dark:text-white">Bank Soal ({{ $soal->count() }})</h3>
    <div class="space-y-2">
        @forelse($soal as $idx => $q)
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
            </details>
        @empty
            <x-card title="Belum ada soal" subtitle="Modul ini belum memiliki bank soal." />
        @endforelse
    </div>
</x-sidebar-layout>
