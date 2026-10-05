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
                <p id="grafik-gagal" class="hidden absolute inset-0 flex items-center justify-center text-center text-sm text-slate-500 dark:text-slate-400 px-6">Grafik tidak bisa dimuat (CDN Chart.js tidak terjangkau). Daftarmu tetap tampil lengkap di bawah.</p>
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
    if (!el || typeof Chart === 'undefined') {
        const gagal = document.getElementById('grafik-gagal');
        if (el) el.classList.add('hidden');
        if (gagal) gagal.classList.remove('hidden');
        return;
    }
    const BATAS = 80;
    const FONT = "'Plus Jakarta Sans', 'Figtree', sans-serif";
    const data = @js($skor);
    const labelPenuh = data.map(d => d.label);
    const labelPendek = data.map(d => {
        const m = String(d.label).match(/^(\d+)\./);
        return m ? 'M' + m[1] : String(d.label).slice(0, 12);
    });
    const statusSkor = (n) => n >= BATAS ? 'Lulus' : (n > 0 ? 'Belum lulus' : 'Belum dikerjakan');
    const bacaToken = () => {
        const css = getComputedStyle(document.documentElement);
        const token = (nama, ganti) => css.getPropertyValue(nama).trim() || ganti;
        return {
            lulus: token('--color-success', '#10b981'),
            proses: token('--color-primary', '#f97316'),
            kosong: token('--color-border', '#e2e8f0'),
            teks: token('--color-muted', '#64748b'),
            grid: token('--color-border', '#e2e8f0'),
            garis: token('--color-primary-strong', '#ea580c'),
        };
    };
    const warnaBar = () => {
        const C = bacaToken();
        return data.map(d => d.nilai >= BATAS ? C.lulus : (d.nilai > 0 ? C.proses : C.kosong));
    };
    // Angka skor digambar di atas tiap batang (tanpa plugin tambahan).
    const pluginNilai = {
        id: 'nilaiDiAtas',
        afterDatasetsDraw(chart) {
            const { ctx } = chart;
            const meta = chart.getDatasetMeta(0);
            if (!meta) return;
            const C = bacaToken();
            ctx.save();
            ctx.font = "700 11px " + FONT;
            ctx.textAlign = 'center';
            ctx.textBaseline = 'bottom';
            ctx.fillStyle = C.teks;
            meta.data.forEach((bar, i) => {
                ctx.fillText(String(data[i].nilai), bar.x, bar.y - 5);
            });
            ctx.restore();
        }
    };
    const chart = new Chart(el, {
        type: 'bar',
        data: {
            labels: labelPendek,
            datasets: [
                {
                    label: 'Skor',
                    data: data.map(d => d.nilai),
                    backgroundColor: warnaBar(),
                    borderRadius: 10,
                    borderSkipped: 'start',
                    maxBarThickness: 54,
                },
                {
                    type: 'line',
                    label: 'Batas lulus (80)',
                    data: data.map(() => BATAS),
                    borderColor: bacaToken().garis,
                    borderDash: [7, 5],
                    borderWidth: 2,
                    pointRadius: 0,
                    pointHoverRadius: 0,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            layout: { padding: { top: 22 } },
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    align: 'end',
                    labels: { usePointStyle: true, boxWidth: 8, boxHeight: 8, padding: 14, font: { family: FONT, size: 11 } },
                },
                tooltip: {
                    callbacks: {
                        title: (items) => labelPenuh[items[0].dataIndex] ?? '',
                        label: (item) => item.datasetIndex === 0
                            ? ' Skor: ' + data[item.dataIndex].nilai + ' — ' + statusSkor(data[item.dataIndex].nilai)
                            : ' Batas lulus: ' + BATAS,
                    },
                },
            },
            scales: {
                y: {
                    min: 0,
                    max: 100,
                    ticks: { stepSize: 20, font: { family: FONT, size: 11 } },
                },
                x: {
                    ticks: { maxRotation: 0, font: { family: FONT, size: 11, weight: 'bold' } },
                    grid: { display: false },
                },
            },
        },
        plugins: [pluginNilai],
    });
    const terapkanTema = () => {
        const C = bacaToken();
        chart.data.datasets[0].backgroundColor = warnaBar();
        chart.data.datasets[1].borderColor = C.garis;
        chart.options.scales.x.ticks = { maxRotation: 0, color: C.teks, font: { family: FONT, size: 11, weight: 'bold' } };
        chart.options.scales.y.ticks = { color: C.teks, stepSize: 20, font: { family: FONT, size: 11 } };
        chart.options.scales.x.grid = { display: false };
        chart.options.scales.y.grid = { color: C.grid };
        chart.options.plugins.legend.labels.color = C.teks;
        chart.update();
    };
    terapkanTema();
    window.addEventListener('kka:theme', terapkanTema);
})();
</script>
@endpush
</x-sidebar-layout>
