<x-sidebar-layout>
    <x-slot name="header">
        <h2 class="font-extrabold text-xl text-slate-900">Bahan Belajar</h2>
        <p class="text-sm text-slate-500">Baca berurutan dari nomor 1. Modul terkunci terbuka setelah sebelumnya selesai.</p>
    </x-slot>

    <div class="grid sm:grid-cols-2 xl:grid-cols-4 gap-3 sm:gap-4">
        @foreach($daftar as $d)
            @php $m = $d['module']; $p = $d['progress']; @endphp
            <x-card :title="$d['posisi'] . '. ' . $m->title" :subtitle="$m->description">
                <div class="flex items-center justify-between text-xs gap-2">
                    @if($d['terkunci'])
                        <span class="font-bold text-slate-400 whitespace-nowrap">🔒 Terkunci</span>
                    @elseif($p && $p->reading_done)
                        <span class="font-bold text-emerald-600 whitespace-nowrap">✓ Sudah dibaca</span>
                    @else
                        <span class="font-bold text-slate-400 whitespace-nowrap">Belum dibaca</span>
                    @endif
                    @if(! $m->isDefault())
                        <span class="kka-badge">Kelas</span>
                    @endif
                </div>
                @if($d['terkunci'])
                    <span class="kka-btn-secondary w-full mt-3 !py-2 text-sm text-center block opacity-40">🔒 Terkunci</span>
                @else
                    <a href="{{ route('siswa.bahan.show', $m->slug) }}" class="kka-btn-primary w-full mt-3 !py-2 text-sm text-center block">📖 Baca Materi</a>
                @endif
            </x-card>
        @endforeach
    </div>
</x-sidebar-layout>
