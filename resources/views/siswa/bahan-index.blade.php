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
                        <span class="kka-status-idle inline-flex items-center gap-1"><x-icon name="lock" class="w-3.5 h-3.5" />Terkunci</span>
                    @elseif($p && $p->reading_done)
                        <span class="kka-status-ok">✓ Sudah dibaca</span>
                    @else
                        <span class="kka-status-idle">Belum dibaca</span>
                    @endif
                    @if(! $m->isDefault())
                        <span class="kka-badge">Kelas</span>
                    @endif
                    @if(isset($m->pivot) && $m->pivot->due_at)
                        @php $tenggat = \Illuminate\Support\Carbon::parse($m->pivot->due_at); @endphp
                        @if(! ($p && $p->is_completed) && $tenggat->isPast())
                            <span class="kka-badge !bg-red-100 !text-red-700 !border-red-300">Terlambat {{ $tenggat->format('d M') }}</span>
                        @else
                            <span class="kka-badge">Tenggat {{ $tenggat->format('d M Y') }}</span>
                        @endif
                    @endif
                </div>
                @if($d['terkunci'])
                    <span class="kka-btn-secondary w-full mt-3 !py-2 text-sm text-center gap-1.5 opacity-40 inline-flex items-center justify-center"><x-icon name="lock" class="w-4 h-4" />Terkunci</span>
                @else
                    <a href="{{ route('siswa.bahan.show', $m->slug) }}" class="kka-btn-primary w-full mt-3 !py-2 text-sm text-center gap-1.5 inline-flex items-center justify-center"><x-icon name="book" class="w-4 h-4" />Baca Materi</a>
                @endif
            </x-card>
        @endforeach
    </div>
</x-sidebar-layout>
