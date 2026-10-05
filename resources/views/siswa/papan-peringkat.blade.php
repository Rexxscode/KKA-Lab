<x-sidebar-layout>
    <x-slot name="header">
        <h2 class="font-extrabold text-xl text-slate-900 dark:text-white">Papan Peringkat{{ $kelas ? ' — ' . $kelas->name : '' }}</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400">20 teratas berdasar total XP. Kejar terus!</p>
    </x-slot>

    <div class="py-8 grid lg:grid-cols-3 gap-4 items-start">
        <x-card title="Peringkat Kelas" subtitle="Diperbarui setiap ada XP masuk." class="lg:col-span-2">
            @if($kelas)
                <div class="space-y-2">
                    @forelse($papan as $i => $s)
                        <div class="flex items-center gap-3 sm:gap-4 rounded-2xl border px-4 sm:px-5 py-3 text-sm sm:text-[15px] {{ $s->id === auth()->id() ? 'border-orange-300 dark:border-orange-500/40 bg-orange-50 dark:bg-orange-500/10' : 'border-slate-200 dark:border-slate-700' }}">
                            <span class="inline-flex items-center justify-center w-9 h-9 rounded-full text-sm font-extrabold shrink-0 {{ $i === 0 ? 'bg-amber-400 text-white' : ($i < 3 ? 'bg-slate-200 dark:bg-slate-600 text-slate-700 dark:text-slate-200' : 'bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400') }}">{{ $i + 1 }}</span>
                            <span class="flex-1 min-w-0 font-bold text-slate-800 dark:text-slate-100 truncate">{{ $s->name }}{{ $s->id === auth()->id() ? ' (kamu)' : '' }}</span>
                            <span class="font-extrabold text-emerald-600 dark:text-emerald-400 whitespace-nowrap">{{ (int) $s->xp }} XP</span>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500 dark:text-slate-400">Belum ada peringkat. Jadi yang pertama kumpulkan XP!</p>
                    @endforelse
                </div>
            @else
                <p class="text-sm text-slate-500 dark:text-slate-400">Kamu belum gabung kelas. Minta kode 6 digit ke gurumu lewat kartu Gabung Kelas di dasbor.</p>
            @endif
        </x-card>
        <x-card title="Posisimu" subtitle="Tetap semangat, kejar terus!">
            @if($kelas && $peringkatSaya)
                <p class="text-4xl font-extrabold text-orange-500">#{{ $peringkatSaya }}</p>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">dari {{ $kelas->students()->count() }} siswa di {{ $kelas->name }}</p>
                <div class="mt-4 space-y-2 text-sm">
                    <div class="flex items-center justify-between rounded-xl bg-slate-50 dark:bg-slate-800/70 border border-slate-200 dark:border-slate-700 px-4 py-2.5">
                        <span class="font-semibold text-slate-600 dark:text-slate-300">Total XP-mu</span>
                        <span class="font-extrabold text-emerald-600 dark:text-emerald-400">{{ auth()->user()->totalXp() }} XP</span>
                    </div>
                    @if($papan->isNotEmpty())
                        <div class="flex items-center justify-between rounded-xl bg-slate-50 dark:bg-slate-800/70 border border-slate-200 dark:border-slate-700 px-4 py-2.5">
                            <span class="font-semibold text-slate-600 dark:text-slate-300">XP peringkat 1</span>
                            <span class="font-extrabold text-slate-800 dark:text-slate-100">{{ (int) $papan->first()->xp }} XP</span>
                        </div>
                    @endif
                </div>
                <a href="{{ route('siswa.dashboard') }}" class="kka-btn-primary mt-4 !py-2 text-sm">Kejar XP</a>
            @else
                <p class="text-sm text-slate-500 dark:text-slate-400">Gabung kelas dulu untuk melihat posisimu di papan peringkat.</p>
                <a href="{{ route('siswa.dashboard') }}" class="kka-btn-secondary mt-4 !py-2 text-sm">Ke Dasbor</a>
            @endif
        </x-card>
    </div>
</x-sidebar-layout>
