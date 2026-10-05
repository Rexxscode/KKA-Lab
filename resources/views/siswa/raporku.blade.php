<x-sidebar-layout>
    <x-slot name="header">
        <a href="{{ route('siswa.dashboard') }}" class="text-sm font-semibold text-orange-500">← Dasbor</a>
        <div class="flex flex-wrap items-center justify-between gap-3 mt-1">
            <div>
                <h2 class="font-extrabold text-xl text-slate-900 dark:text-white">Raporku</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400">{{ $lulus }}/{{ count($daftar) }} modul lulus • Total {{ $totalXp }} XP • Saran otomatis di bawah grafik.</p>
            </div>
            <a href="{{ route('siswa.raporku.cetak') }}" class="kka-btn-primary !py-2 text-sm">Cetak / Simpan PDF</a>
        </div>
    </x-slot>

    <div class="grid lg:grid-cols-2 gap-3 sm:gap-4">
        <x-card title="Grafik Skor per Modul" subtitle="Skor kuis terakhir tiap modul (lulus ≥80).">
            <div class="relative h-64">
                <canvas id="grafik-rapor" aria-label="Grafik skor per modul" role="img"></canvas>
            </div>
            <div class="mt-3 space-y-2">
                @foreach($daftar as $d)
                    @php $p = $d['progress']; @endphp
                    <div class="flex items-center justify-between text-sm border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2">
                        <span class="font-semibold text-slate-700 dark:text-slate-200">{{ $d['posisi'] }}. {{ $d['module']->title }}</span>
                        @if($p && $p->is_completed)
                            <span class="font-extrabold text-emerald-700 dark:text-emerald-400">{{ $p->quiz_score }}</span>
                        @elseif($p && $p->quiz_score > 0)
                            <span class="font-extrabold text-orange-600 dark:text-orange-400">{{ $p->quiz_score }} • belum lulus</span>
                        @else
                            <span class="font-bold text-slate-400">—</span>
                        @endif
                    </div>
                    @if(($terbaik[$d['module']->id] ?? 0) > 0)
                        <p class="text-xs text-slate-400 -mt-1 px-1">Terbaik: {{ $terbaik[$d['module']->id] }}</p>
                    @endif
                @endforeach
            </div>
        </x-card>

        <x-card title="Saran Otomatis" subtitle="Fokus ke yang paling lemah dulu.">
            <div class="space-y-2">
                @forelse($saran as $s)
                    <div class="rounded-2xl border p-4 text-sm {{ $s['tipe'] === 'lemah' ? 'border-orange-200 dark:border-orange-500/30 bg-orange-50/60 dark:bg-orange-500/10' : ($s['tipe'] === 'mulai' ? 'border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50' : 'border-emerald-200 dark:border-emerald-500/30 bg-emerald-50/60 dark:bg-emerald-500/10') }}">
                        <p class="font-bold text-slate-800 dark:text-slate-100">{{ $s['modul'] }}</p>
                        <p class="mt-1 text-slate-600 dark:text-slate-300">{{ $s['teks'] }}</p>
                        <a href="{{ $s['tautan'] }}" class="inline-block mt-2 text-xs font-extrabold text-orange-600 dark:text-orange-400 hover:text-orange-700">{{ $s['label_tautan'] }} →</a>
                    </div>
                @empty
                    <p class="text-sm text-emerald-700 dark:text-emerald-400 font-semibold">Luar biasa! Semua modul lulus dengan skor ≥90. Pertahankan!</p>
                @endforelse
            </div>
        </x-card>
    </div>

    @if($umpanBalik->count())
        <x-card title="Catatan dari Gurumu" subtitle="Dibaca baik-baik ya!" class="mt-3 sm:mt-4">
            <div class="space-y-2">
                @foreach($umpanBalik as $fb)
                    <div class="rounded-2xl border border-orange-200 dark:border-orange-500/30 bg-orange-50/60 dark:bg-orange-500/10 p-4 text-sm">
                        <p class="font-bold text-slate-800 dark:text-slate-100">{{ $fb->module->title ?? 'Umum' }} <span class="font-normal text-slate-400">• {{ $fb->classroom->name ?? '' }}</span></p>
                        <p class="mt-1 text-slate-600 dark:text-slate-300">{{ $fb->body }}</p>
                    </div>
                @endforeach
            </div>
        </x-card>
    @endif

@push('head')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
@endpush
@push('scripts')
<script>
(function () {
    const el = document.getElementById('grafik-rapor');
    if (!el || typeof Chart === 'undefined') return;
    const data = @js($skor);
    const bacaToken = () => {
        const css = getComputedStyle(document.documentElement);
        const token = (nama, ganti) => css.getPropertyValue(nama).trim() || ganti;
        return {
            lulus: token('--color-success', '#10b981'),
            proses: token('--color-primary', '#f97316'),
            kosong: token('--color-border', '#e2e8f0'),
            teks: token('--color-muted', '#64748b'),
            grid: token('--color-border', '#e2e8f0'),
        };
    };
    const warnaBar = () => {
        const C = bacaToken();
        return data.map(d => d.nilai >= 80 ? C.lulus : (d.nilai > 0 ? C.proses : C.kosong));
    };
    const chart = new Chart(el, {
        type: 'bar',
        data: {
            labels: data.map(d => d.label),
            datasets: [{
                label: 'Skor',
                data: data.map(d => d.nilai),
                backgroundColor: warnaBar(),
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
    const terapkanTema = () => {
        const C = bacaToken();
        chart.data.datasets[0].backgroundColor = warnaBar();
        chart.options.scales.x.ticks = { color: C.teks };
        chart.options.scales.y.ticks = { color: C.teks, stepSize: 20 };
        chart.options.scales.x.grid = { color: C.grid };
        chart.options.scales.y.grid = { color: C.grid };
        chart.update();
    };
    terapkanTema();
    window.addEventListener('kka:theme', terapkanTema);
})();
</script>
@endpush
</x-sidebar-layout>
