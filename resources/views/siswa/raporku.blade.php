<x-sidebar-layout>
    <x-slot name="header">
        <a href="{{ route('siswa.dashboard') }}" class="text-sm font-semibold text-orange-500">← Dasbor</a>
        <h2 class="font-extrabold text-xl text-slate-900 mt-1">Raporku</h2>
        <p class="text-sm text-slate-500">{{ $lulus }}/{{ count($daftar) }} modul lulus • Total {{ $totalXp }} XP • Saran otomatis di bawah grafik.</p>
    </x-slot>

    <div class="grid lg:grid-cols-2 gap-3 sm:gap-4">
        <x-card title="Grafik Skor per Modul" subtitle="Skor kuis terakhir tiap modul (lulus ≥80).">
            <div class="relative h-64">
                <canvas id="grafik-rapor" aria-label="Grafik skor per modul" role="img"></canvas>
            </div>
            <div class="mt-3 space-y-2">
                @foreach($daftar as $d)
                    @php $p = $d['progress']; @endphp
                    <div class="flex items-center justify-between text-sm border border-slate-200 rounded-xl px-4 py-2">
                        <span class="font-semibold text-slate-700">{{ $d['posisi'] }}. {{ $d['module']->title }}</span>
                        @if($p && $p->is_completed)
                            <span class="font-extrabold text-emerald-700">{{ $p->quiz_score }}</span>
                        @elseif($p && $p->quiz_score > 0)
                            <span class="font-extrabold text-orange-600">{{ $p->quiz_score }} • belum lulus</span>
                        @else
                            <span class="font-bold text-slate-400">—</span>
                        @endif
                    </div>
                @endforeach
            </div>
        </x-card>

        <x-card title="Saran Otomatis" subtitle="Fokus ke yang paling lemah dulu.">
            <div class="space-y-2">
                @forelse($saran as $s)
                    <div class="rounded-2xl border p-4 text-sm {{ $s['tipe'] === 'lemah' ? 'border-orange-200 bg-orange-50/60' : ($s['tipe'] === 'mulai' ? 'border-slate-200 bg-slate-50' : 'border-emerald-200 bg-emerald-50/60') }}">
                        <p class="font-bold text-slate-800">{{ $s['modul'] }}</p>
                        <p class="mt-1 text-slate-600">{{ $s['teks'] }}</p>
                        <a href="{{ $s['tautan'] }}" class="inline-block mt-2 text-xs font-extrabold text-orange-600 hover:text-orange-700">{{ $s['label_tautan'] }} →</a>
                    </div>
                @empty
                    <p class="text-sm text-emerald-700 font-semibold">Luar biasa! Semua modul lulus dengan skor ≥90. Pertahankan!</p>
                @endforelse
            </div>
        </x-card>
    </div>

@push('head')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
@endpush
@push('scripts')
<script>
(function () {
    const el = document.getElementById('grafik-rapor');
    if (!el || typeof Chart === 'undefined') return;
    const data = @js($skor);
    new Chart(el, {
        type: 'bar',
        data: {
            labels: data.map(d => d.label),
            datasets: [{
                label: 'Skor',
                data: data.map(d => d.nilai),
                backgroundColor: data.map(d => d.nilai >= 80 ? '#10b981' : (d.nilai > 0 ? '#f97316' : '#cbd5e1')),
                borderRadius: 8,
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: { y: { min: 0, max: 100, ticks: { stepSize: 20 } } },
            plugins: { legend: { display: false } },
        },
    });
})();
</script>
@endpush
</x-sidebar-layout>
