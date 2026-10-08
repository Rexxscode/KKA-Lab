<x-sidebar-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="font-extrabold text-xl text-slate-900 dark:text-white">Ujian Akhir</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400">{{ \App\Services\StudentModuleService::UJIAN_AKHIR_JUMLAH }} soal acak dari ke-6 modul • Lulus jika skor ≥{{ \App\Services\StudentModuleService::UJIAN_AKHIR_PASS }} • Boleh mengulang</p>
            </div>
            @if($lulus)
                <span class="kka-badge">Lulus ✓</span>
            @endif
        </div>
    </x-slot>

    <div class="grid lg:grid-cols-3 gap-3 sm:gap-4">
        <x-card title="Siap Ujian?" subtitle="baca dulu aturannya" class="lg:col-span-2">
            <ul class="text-sm text-slate-600 dark:text-slate-300 space-y-1.5 list-disc pl-5">
                <li>Soal diambil acak dari bank ke-6 modul default ({{ \App\Services\StudentModuleService::UJIAN_AKHIR_JUMLAH }} soal, opsi diacak).</li>
                <li>Batas waktu {{ \App\Services\StudentModuleService::UJIAN_AKHIR_MENIT }} menit. Lewat batas → paket hangus, buat paket baru.</li>
                <li>Tidak ada XP tambahan — hadiahnya sertifikat kelulusan.</li>
                <li>Nilai terbaikmu: <b>{{ $terbaik ?? '—' }}</b></li>
            </ul>
            <div class="mt-4 flex flex-col sm:flex-row gap-2">
                <a href="{{ route('siswa.ujian.show') }}" class="kka-btn-primary !py-2.5 text-sm text-center">Mulai Ujian Akhir</a>
                <a href="{{ route('siswa.sertifikat') }}" class="kka-btn-secondary !py-2.5 text-sm text-center">Lihat Sertifikat</a>
            </div>
        </x-card>

        <x-card title="Riwayat Ujian" subtitle="5 percobaan terakhir">
            <div class="space-y-2">
                @forelse($riwayat as $r)
                    <div class="flex items-center justify-between text-sm border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2">
                        <span class="text-xs text-slate-500 dark:text-slate-400">{{ $r->created_at->format('d M Y H:i') }}</span>
                        <span class="font-extrabold {{ $r->passed ? 'text-emerald-700 dark:text-emerald-400' : 'text-orange-600 dark:text-orange-400' }}">{{ $r->score }}{{ $r->passed ? ' • Lulus' : '' }}</span>
                    </div>
                @empty
                    <p class="text-sm text-slate-500 dark:text-slate-400">Belum pernah ujian. Semangat!</p>
                @endforelse
            </div>
        </x-card>
    </div>
</x-sidebar-layout>
