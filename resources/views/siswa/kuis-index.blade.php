<x-sidebar-layout>
    <x-slot name="header">
        <h2 class="font-extrabold text-xl text-slate-900">Kuis</h2>
        <p class="text-sm text-slate-500">Lulus jika skor ≥80. Terbuka berurutan setelah modul sebelumnya selesai. Soal & opsi diacak tiap percobaan.</p>
    </x-slot>

    <div class="grid sm:grid-cols-2 xl:grid-cols-4 gap-3 sm:gap-4">
        @foreach($daftar as $d)
            @php $m = $d['module']; $p = $d['progress']; @endphp
            <x-card :title="$d['posisi'] . '. ' . $m->title" :subtitle="$d['jumlah_soal'] . ' soal pilihan ganda'">
                <div class="flex items-center justify-between text-xs gap-2">
                    @if($d['terkunci'])
                        <span class="kka-status-idle inline-flex items-center gap-1"><x-icon name="lock" class="w-3.5 h-3.5" />Terkunci</span>
                    @elseif($p && $p->is_completed)
                        <span class="kka-status-ok">✓ Selesai • {{ $p->quiz_score }}</span>
                    @elseif($p && $p->quiz_score > 0)
                        <span class="kka-status-warn">Skor {{ $p->quiz_score }}</span>
                    @elseif($p && $p->reading_done)
                        <span class="kka-status-warn">Siap dikerjakan</span>
                    @else
                        <span class="kka-status-idle">Baca bahan dulu</span>
                    @endif
                    @if(! $m->isDefault())
                        <span class="kka-badge">Kelas</span>
                    @endif
                </div>
                @if($d['terkunci'])
                    <span class="kka-btn-secondary w-full mt-3 !py-2 text-sm text-center gap-1.5 opacity-40 inline-flex items-center justify-center"><x-icon name="lock" class="w-4 h-4" />Terkunci</span>
                @elseif($p && $p->reading_done)
                    <a href="{{ route('siswa.kuis.show', $m->slug) }}" class="kka-btn-primary w-full mt-3 !py-2 text-sm text-center block">{{ $p->is_completed ? 'Ulangi Kuis' : 'Kerjakan Kuis' }}</a>
                @else
                    <a href="{{ route('siswa.bahan.show', $m->slug) }}" class="kka-btn-secondary w-full mt-3 !py-2 text-sm text-center gap-1.5 inline-flex items-center justify-center"><x-icon name="book" class="w-4 h-4" />Baca Dulu</a>
                @endif
            </x-card>
        @endforeach
    </div>
</x-sidebar-layout>
