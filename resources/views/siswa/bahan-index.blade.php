<x-sidebar-layout>
    <x-slot name="header">
        <h2 class="font-extrabold text-xl text-slate-900">Bahan Belajar</h2>
        <p class="text-sm text-slate-500">Baca materi tiap modul sampai tuntas, lalu buka kuisnya.</p>
    </x-slot>

    <div class="grid sm:grid-cols-2 xl:grid-cols-4 gap-3 sm:gap-4">
        @foreach($modules as $m)
            @php $p = $progress->get($m->id); @endphp
            <x-card :title="$m->title" :subtitle="$m->description">
                <div class="flex items-center justify-between text-xs gap-2">
                    <span class="kka-badge">Materi + praktik</span>
                    @if($p && $p->reading_done)
                        <span class="font-bold text-emerald-600 whitespace-nowrap">✓ Dibaca</span>
                    @else
                        <span class="font-bold text-slate-400 whitespace-nowrap">Belum dibaca</span>
                    @endif
                </div>
                <a href="{{ route('siswa.bahan.show', $m->slug) }}" class="kka-btn-primary w-full mt-3 !py-2 text-sm text-center block">📖 Baca Materi</a>
            </x-card>
        @endforeach
    </div>
</x-sidebar-layout>
