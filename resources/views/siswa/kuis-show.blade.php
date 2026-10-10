<x-sidebar-layout>
    <x-slot name="header">
        <h2 class="font-extrabold text-xl text-slate-900 dark:text-white">Kuis: {{ $module->title }}</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400">{{ count($tampil) }} soal acak per percobaan • Opsi diacak • Lulus jika skor ≥80 • Materi disembunyikan (murni pemahamanmu)</p>
        <p class="mt-1 text-sm font-bold text-orange-600 dark:text-orange-400">Batas waktu: {{ \App\Http\Controllers\Student\ProgressController::DURASI_KUIS_MENIT }} menit • Sisa: <span id="sisa-waktu" role="timer" aria-label="Sisa waktu kuis" data-detik="{{ $sisaDetik }}">--:--</span></p>
    </x-slot>

    @if(auth()->user()->isAdmin())
        <div class="mb-4 rounded-2xl border border-orange-200 dark:border-orange-500/30 bg-orange-50 dark:bg-orange-500/10 px-4 py-3 flex flex-wrap items-center gap-2 text-sm" role="status">
            <span class="font-bold text-orange-800 dark:text-orange-300 min-w-0 flex-1 basis-48">Mode pratinjau admin — jawaban tidak disimpan. Kembali ke Pratinjau Siswa untuk melihat data siswa.</span>
            <a href="{{ route('admin.pratinjau.index') }}" class="kka-btn-primary !py-1.5 text-xs shrink-0">Buka Pratinjau →</a>
        </div>
    @endif

    <div class="max-w-3xl" x-data="kkaBelajar({{ count($tampil) }})">
        <x-card title="Kuis {{ $module->title }}" :subtitle="$progress && $progress->is_completed ? 'Status: LULUS • Skor '.$progress->quiz_score.' • +'.$progress->xp_earned.' XP' : 'Status: '.($progress && $progress->quiz_score ? 'skor '.$progress->quiz_score.' — belum lulus, coba lagi!' : 'belum dikerjakan')">
            @if(session('quiz_result'))
                @php
                    $qr = session('quiz_result');
                    $benar = collect($qr['detail'])->where('ok', true)->count();
                    $total = count($qr['detail']);
                @endphp
                <div class="mb-4 rounded-2xl border p-4 text-sm font-semibold {{ $qr['passed'] ? 'border-emerald-200 dark:border-emerald-500/30 bg-emerald-50 dark:bg-emerald-500/10 text-emerald-800 dark:text-emerald-300' : 'border-orange-200 dark:border-orange-500/30 bg-orange-50 dark:bg-orange-500/10 text-orange-800 dark:text-orange-300' }}" role="status">
                    Skor {{ $qr['score'] }} ({{ $benar }}/{{ $total }} benar) — soal & opsi diacak ulang tiap percobaan. Pelajari pembahasan di bawah!
                </div>
            @endif
            <form id="form-kuis" method="POST" action="{{ route('siswa.kuis.submit', $module->slug) }}" class="space-y-4" @submit="if (!kumpulValid()) $event.preventDefault(); else sudahKumpul = true">
                @csrf
                <div class="flex items-center justify-between text-sm">
                    <p class="font-bold text-slate-800 dark:text-slate-100">Soal <span x-text="soalIdx + 1"></span> dari {{ count($tampil) }}</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400"><span x-text="terjawabSemua()"></span>/{{ count($tampil) }} terjawab</p>
                </div>
                <div class="w-full h-2.5 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden border border-slate-200 dark:border-slate-700" role="progressbar" aria-valuemin="0" aria-valuemax="{{ count($tampil) }}" :aria-valuenow="terjawabSemua()">
                    <div class="h-full bg-emerald-500 transition-all" :style="'width: ' + Math.round(terjawabSemua() / {{ count($tampil) }} * 100) + '%'"></div>
                </div>
                <x-exam-questions :tampil="$tampil" resultKey="quiz_result" submitLabel="Kumpulkan Kuis" />
            </form>
        </x-card>
    </div>

@push('scripts')
@include('components.exam-scripts')
@endpush
</x-sidebar-layout>
