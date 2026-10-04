<x-sidebar-layout>
    <x-slot name="header">
        <h2 class="font-extrabold text-xl text-slate-900">Kuis</h2>
        <p class="text-sm text-slate-500">15 soal per modul, lulus jika skor ≥80. Kuis terbuka setelah bahan dibaca.</p>
    </x-slot>

    <div class="grid sm:grid-cols-2 xl:grid-cols-4 gap-3 sm:gap-4">
        @foreach($modules as $m)
            @php $p = $progress->get($m->id); @endphp
            <x-card :title="$m->title" subtitle="{{ $m->quizzes_count }} soal pilihan ganda">
                <div class="flex items-center justify-between text-xs gap-2">
                    @if($p && $p->is_completed)
                        <span class="font-bold text-emerald-600 whitespace-nowrap">✓ Selesai • {{ $p->quiz_score }}</span>
                    @elseif($p && $p->quiz_score > 0)
                        <span class="font-bold text-orange-500 whitespace-nowrap">⏳ Skor {{ $p->quiz_score }}</span>
                    @elseif($p && $p->reading_done)
                        <span class="font-bold text-orange-500 whitespace-nowrap">Siap dikerjakan</span>
                    @else
                        <span class="font-bold text-slate-400 whitespace-nowrap">🔒 Terkunci</span>
                    @endif
                </div>
                @if($p && $p->reading_done)
                    <a href="{{ route('siswa.kuis.show', $m->slug) }}" class="kka-btn-primary w-full mt-3 !py-2 text-sm text-center block">📝 {{ $p->is_completed ? 'Ulangi Kuis' : 'Kerjakan Kuis' }}</a>
                @else
                    <a href="{{ route('siswa.bahan.show', $m->slug) }}" class="kka-btn-secondary w-full mt-3 !py-2 text-sm text-center block">📖 Baca Dulu</a>
                @endif
            </x-card>
        @endforeach
    </div>
</x-sidebar-layout>
