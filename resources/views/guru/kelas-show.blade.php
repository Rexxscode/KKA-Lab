<x-sidebar-layout>
    <x-slot name="header">
        <a href="{{ route('guru.dashboard') }}" class="text-sm font-semibold text-orange-500">← Semua Kelas</a>
        <div class="flex flex-wrap items-center justify-between gap-3 mt-1">
            <div>
                <h2 class="font-extrabold text-xl text-slate-900">{{ $classroom->name }}</h2>
                <p class="text-sm text-slate-500">Kode kelas: <b class="text-orange-500 tracking-widest">{{ $classroom->code }}</b> • {{ $classroom->students->count() }} siswa</p>
            </div>
            <a href="{{ route('guru.kelas.export', $classroom) }}" class="kka-btn-primary !py-2 text-sm">Ekspor CSV</a>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <x-card title="Progres & Skor Siswa" subtitle="Hijau = lulus (≥80), orange = belum lulus.">
            <div class="overflow-x-auto">
                <table class="w-full text-sm min-w-[720px]">
                    <thead>
                        <tr class="text-left text-xs text-slate-400 border-b border-slate-200">
                            <th class="py-2 pr-3">Siswa</th>
                            @foreach($modules as $m)
                                <th class="py-2 pr-3">{{ $m->title }}</th>
                            @endforeach
                            <th class="py-2">Total XP</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($classroom->students as $s)
                            @php $by = $s->progress->keyBy('module_id'); @endphp
                            <tr class="border-b border-slate-100">
                                <td class="py-3 pr-3"><p class="font-bold text-slate-800">{{ $s->name }}</p><p class="text-xs text-slate-400">{{ $s->email }}</p></td>
                                @foreach($modules as $m)
                                    @php $p = $by->get($m->id); @endphp
                                    <td class="py-3 pr-3">
                                        @if($p && $p->is_completed)
                                            <span class="kka-badge">{{ $p->quiz_score }} • Selesai</span>
                                        @elseif($p)
                                            <span class="inline-flex px-3 py-1 rounded-full text-xs font-bold bg-orange-50 text-orange-700 border border-orange-200">{{ $p->quiz_score }} • Dikerjakan</span>
                                        @else
                                            <span class="text-slate-300 text-xs">—</span>
                                        @endif
                                    </td>
                                @endforeach
                                <td class="py-3 font-extrabold text-emerald-600">{{ $by->sum('xp_earned') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="{{ $modules->count() + 2 }}" class="py-6 text-center text-slate-400 text-sm">Belum ada siswa. Bagikan kode <b>{{ $classroom->code }}</b>.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>
    </div>
</x-sidebar-layout>
