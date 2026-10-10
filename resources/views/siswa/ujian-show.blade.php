<x-sidebar-layout>
    <x-slot name="header">
        <h2 class="font-extrabold text-xl text-slate-900 dark:text-white">Ujian Akhir</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400">{{ count($tampil) }} soal acak dari 6 modul • Opsi diacak • Lulus jika skor ≥{{ \App\Services\StudentModuleService::UJIAN_AKHIR_PASS }}</p>
        <p class="mt-1 text-sm font-bold text-orange-600 dark:text-orange-400">Batas waktu: {{ \App\Services\StudentModuleService::UJIAN_AKHIR_MENIT }} menit • Sisa: <span id="sisa-waktu" role="timer" aria-label="Sisa waktu ujian" data-detik="{{ $sisaDetik }}">--:--</span></p>
    </x-slot>

    <div class="max-w-3xl" x-data="kkaBelajar({{ count($tampil) }})">
        <x-card title="Ujian Akhir KKALAB" subtitle="Fokus ya — lulus ujian ini syarat terbitnya sertifikat!">
            @if(session('ujian_result'))
                @php
                    $qr = session('ujian_result');
                    $benar = collect($qr['detail'])->where('ok', true)->count();
                    $total = count($qr['detail']);
                @endphp
                <div class="mb-4 rounded-2xl border p-4 text-sm font-semibold border-orange-200 dark:border-orange-500/30 bg-orange-50 dark:bg-orange-500/10 text-orange-800 dark:text-orange-300" role="status">
                    Skor {{ $qr['score'] }} ({{ $benar }}/{{ $total }} benar) — soal diacak ulang tiap percobaan. Pelajari pembahasan di bawah!
                </div>
            @endif
            <form id="form-kuis" method="POST" action="{{ route('siswa.ujian.submit') }}" class="space-y-4" @submit="if (!kumpulValid()) $event.preventDefault(); else sudahKumpul = true">
                @csrf
                <div class="flex items-center justify-between text-sm">
                    <p class="font-bold text-slate-800 dark:text-slate-100">Soal <span x-text="soalIdx + 1"></span> dari {{ count($tampil) }}</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400"><span x-text="terjawabSemua()"></span>/{{ count($tampil) }} terjawab</p>
                </div>
                <div class="w-full h-2.5 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden border border-slate-200 dark:border-slate-700" role="progressbar" aria-valuemin="0" aria-valuemax="{{ count($tampil) }}" :aria-valuenow="terjawabSemua()">
                    <div class="h-full bg-emerald-500 transition-all" :style="'width: ' + Math.round(terjawabSemua() / {{ count($tampil) }} * 100) + '%'"></div>
                </div>
                <x-exam-questions :tampil="$tampil" resultKey="ujian_result" submitLabel="Kumpulkan Ujian" />
            </form>
        </x-card>
    </div>

@push('scripts')
@include('components.exam-scripts')
@endpush
</x-sidebar-layout>
