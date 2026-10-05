<x-sidebar-layout>
    <x-slot name="header">
        <a href="{{ route('siswa.dashboard') }}" class="text-sm font-semibold text-orange-500">← Dasbor</a>
        <h2 class="font-extrabold text-xl text-slate-900 dark:text-white mt-1">Papan Peringkat{{ $kelas ? ' — ' . $kelas->name : '' }}</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400">20 teratas berdasar total XP. Kejar terus!</p>
    </x-slot>

    <div class="py-8 max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <x-card title="Peringkat Kelas" subtitle="Diperbarui setiap ada XP masuk.">
            @if($kelas)
                <div class="space-y-2">
                    @forelse($papan as $i => $s)
                        <div class="flex items-center gap-3 rounded-xl border px-4 py-2 text-sm {{ $s->id === auth()->id() ? 'border-orange-300 dark:border-orange-500/40 bg-orange-50 dark:bg-orange-500/10' : 'border-slate-200 dark:border-slate-700' }}">
                            <span class="inline-flex items-center justify-center w-7 h-7 rounded-full text-xs font-extrabold {{ $i === 0 ? 'bg-amber-400 text-white' : ($i < 3 ? 'bg-slate-200 dark:bg-slate-600 text-slate-700 dark:text-slate-200' : 'bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400') }}">{{ $i + 1 }}</span>
                            <span class="flex-1 font-bold text-slate-800 dark:text-slate-100">{{ $s->name }}{{ $s->id === auth()->id() ? ' (kamu)' : '' }}</span>
                            <span class="font-extrabold text-emerald-600 dark:text-emerald-400">{{ (int) $s->xp }} XP</span>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500 dark:text-slate-400">Belum ada peringkat. Jadi yang pertama kumpulkan XP!</p>
                    @endforelse
                </div>
            @else
                <p class="text-sm text-slate-500 dark:text-slate-400">Kamu belum gabung kelas. Minta kode 6 digit ke gurumu lewat kartu Gabung Kelas di dasbor.</p>
            @endif
        </x-card>
    </div>
</x-sidebar-layout>
