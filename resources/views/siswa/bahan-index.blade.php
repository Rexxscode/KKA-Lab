<x-sidebar-layout>
    <x-slot name="header">
        <h2 class="font-extrabold text-xl text-slate-900 dark:text-white">Bahan Belajar</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400">Baca berurutan dari nomor 1. Modul terkunci terbuka setelah sebelumnya selesai.</p>
    </x-slot>

    <div class="grid sm:grid-cols-2 xl:grid-cols-4 gap-3 sm:gap-4">
        @foreach($daftar as $d)
            @php $m = $d['module']; $p = $d['progress']; @endphp
            <x-card :title="$d['posisi'] . '. ' . $m->title" :subtitle="$m->description">
                <div class="mt-auto pt-3 space-y-3">
                    <div class="flex items-center justify-between text-xs gap-2">
                        @if($d['terkunci'])
                            <span class="kka-status-idle inline-flex items-center gap-1 whitespace-nowrap"><x-icon name="lock" class="w-3.5 h-3.5" />Terkunci</span>
                        @elseif($p && $p->reading_done)
                            <span class="kka-status-ok whitespace-nowrap">✓ Sudah dibaca</span>
                        @else
                            <span class="kka-status-idle whitespace-nowrap">Belum dibaca</span>
                        @endif
                        @if(! $m->isDefault())
                            <span class="kka-badge shrink-0">Kelas</span>
                        @endif

                    </div>
                    @if($d['terkunci'])
                        <span class="kka-btn-secondary px-4 !py-1.5 text-sm gap-1.5 opacity-40 cursor-not-allowed inline-flex items-center"><x-icon name="lock" class="w-4 h-4" />Terkunci</span>
                    @else
                        <div><a href="{{ route('siswa.bahan.show', $m->slug) }}" class="kka-btn-primary px-4 !py-1.5 text-sm gap-1.5 inline-flex items-center"><x-icon name="book" class="w-4 h-4" />Baca Materi</a></div>
                    @endif
                </div>
            </x-card>
        @endforeach
    </div>
</x-sidebar-layout>
