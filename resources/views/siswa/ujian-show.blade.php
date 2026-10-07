<x-sidebar-layout>
    <x-slot name="header">
        <h2 class="font-extrabold text-xl text-slate-900 dark:text-white">Ujian Akhir</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400">{{ count($tampil) }} soal acak dari 6 modul • Opsi diacak • Lulus jika skor ≥{{ \App\Services\StudentModuleService::UJIAN_AKHIR_PASS }}</p>
        <p class="mt-1 text-sm font-bold text-orange-600 dark:text-orange-400">Batas waktu: {{ \App\Services\StudentModuleService::UJIAN_AKHIR_MENIT }} menit • Sisa: <span id="sisa-waktu" data-detik="{{ $sisaDetik }}">--:--</span></p>
    </x-slot>

    <div class="max-w-3xl" x-data="kkaBelajar({{ count($tampil) }})">
        <x-card title="Ujian Akhir KobotAI Lab" subtitle="Fokus ya — lulus ujian ini syarat terbitnya sertifikat!">
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
                <nav aria-label="Navigasi soal" class="flex flex-wrap gap-1.5">
                    @foreach($tampil as $i => $t)
                        <button type="button" @click="keSoal({{ $i }})" aria-label="Ke soal {{ $i + 1 }}" :aria-current="soalIdx === {{ $i }} ? 'true' : 'false'" class="w-10 h-10 rounded-xl text-xs font-bold border transition focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-400 focus-visible:ring-offset-2"
                            :class="soalIdx === {{ $i }} ? 'bg-orange-500 text-white border-orange-500' : (dijawab({{ $i }}) ? 'bg-emerald-100 dark:bg-emerald-500/20 text-emerald-800 dark:text-emerald-300 border-emerald-300 dark:border-emerald-500/40' : 'bg-white dark:bg-slate-800 text-slate-500 dark:text-slate-400 border-slate-200 dark:border-slate-700')">{{ $i + 1 }}</button>
                    @endforeach
                </nav>
                @foreach($tampil as $i => $t)
                    @php
                        $pilihan = old("answers.{$i}");
                        $ulas = session('ujian_result.detail.' . $i);
                    @endphp
                    <fieldset x-show="soalIdx === {{ $i }}" class="rounded-2xl border p-4 {{ $ulas ? ($ulas['ok'] ? 'border-emerald-300 dark:border-emerald-500/40 bg-emerald-50/50 dark:bg-emerald-500/10' : 'border-orange-300 dark:border-orange-500/40 bg-orange-50/50 dark:bg-orange-500/10') : 'border-slate-200 dark:border-slate-700' }}">
                        <legend class="sr-only">Soal {{ $i + 1 }}</legend>
                        <p class="font-semibold text-slate-800 dark:text-slate-100 text-sm">
                            {{ $i + 1 }}. {{ $t['quiz']->question }}
                            @if($ulas)
                                <span class="ml-1 font-bold {{ $ulas['ok'] ? 'text-emerald-700 dark:text-emerald-400' : 'text-orange-700 dark:text-orange-400' }}">{{ $ulas['ok'] ? '✓ Benar' : '✗ Kurang tepat' }}</span>
                            @endif
                        </p>
                        <div class="mt-2 space-y-1.5" role="radiogroup" aria-label="Pilihan jawaban soal {{ $i + 1 }}">
                            @foreach($t['options'] as $oi => $opt)
                                @php
                                    $isBenar = $ulas && $oi == $ulas['correct'];
                                    $isPilihSalah = $ulas && !$ulas['ok'] && (string) $pilihan === (string) $oi;
                                    $inputId = "soal-{$i}-opsi-{$oi}";
                                @endphp
                                <label for="{{ $inputId }}" class="flex items-center gap-2 text-sm cursor-pointer rounded-xl px-2 py-1.5 focus-within:ring-2 focus-within:ring-orange-400 {{ $isBenar ? 'text-emerald-800 dark:text-emerald-300 font-semibold' : ($isPilihSalah ? 'text-orange-800 dark:text-orange-300 font-semibold' : 'text-slate-700 dark:text-slate-200 hover:text-orange-700') }}">
                                    <input id="{{ $inputId }}" type="radio" name="answers[{{ $i }}]" value="{{ $oi }}" class="accent-orange-500 w-4 h-4" @checked((string) $pilihan === (string) $oi)>
                                    <span><b>{{ chr(65 + $oi) }}.</b> {{ $opt }}</span>
                                    @if($isBenar)<span aria-label="jawaban benar">✓</span>@endif
                                    @if($isPilihSalah)<span>(pilihanmu) ✗</span>@endif
                                </label>
                            @endforeach
                        </div>
                        @if($ulas && ($ulas['explanation'] ?? null))
                            <p class="mt-2 text-xs rounded-xl bg-white/70 dark:bg-slate-800/70 border border-slate-200 dark:border-slate-700 p-2 text-slate-600 dark:text-slate-300"><b>Pembahasan:</b> {{ $ulas['explanation'] }}</p>
                        @endif
                    </fieldset>
                @endforeach
                <p x-show="pesan" x-text="pesan" class="text-xs font-bold text-orange-700 dark:text-orange-400" role="alert"></p>
                <div class="flex items-center justify-between gap-2">
                    <button type="button" @click="keSoal(soalIdx - 1)" :disabled="soalIdx === 0" :class="soalIdx === 0 ? 'kka-btn-secondary opacity-40' : 'kka-btn-secondary'" class="!py-2 text-sm focus-visible:ring-2 focus-visible:ring-orange-400">← Sebelumnya</button>
                    <template x-if="soalIdx < {{ count($tampil) - 1 }}">
                        <x-button type="button" @click="keSoal(soalIdx + 1)">Lanjut →</x-button>
                    </template>
                    <template x-if="soalIdx === {{ count($tampil) - 1 }}">
                        <x-button type="submit" ::disabled="sudahKumpul" x-text="sudahKumpul ? 'Mengumpulkan...' : 'Kumpulkan Ujian'">Kumpulkan Ujian</x-button>
                    </template>
                </div>
            </form>
        </x-card>
    </div>

@push('scripts')
<script>
function kkaBelajar(total) {
    return {
        total: total, soalIdx: 0, pesan: '', sudahKumpul: false,
        dijawab(i) {
            return !!document.querySelector('input[name="answers[' + i + ']"]:checked');
        },
        terjawabSemua() {
            let n = 0;
            for (let i = 0; i < this.total; i++) if (this.dijawab(i)) n++;
            return n;
        },
        keSoal(i) {
            this.soalIdx = Math.max(0, Math.min(this.total - 1, i));
            this.pesan = '';
            const aktif = document.querySelector('fieldset[x-show]');
            if (aktif) {
                const radio = aktif.querySelector('input[type="radio"]');
                if (radio) radio.focus({ preventScroll: true });
            }
        },
        kumpulValid() {
            for (let i = 0; i < this.total; i++) {
                if (!this.dijawab(i)) {
                    this.soalIdx = i;
                    this.pesan = 'Masih ada soal belum dijawab (cek soal ' + (i + 1) + ').';
                    return false;
                }
            }
            return true;
        }
    }
}
(function () {
    let terkunci = true;
    document.getElementById('form-kuis')?.addEventListener('submit', function () { terkunci = false; });
    window.addEventListener('beforeunload', function (e) {
        if (!terkunci) return;
        const terjawab = document.querySelectorAll('#form-kuis input[type="radio"]:checked').length;
        if (terjawab > 0) {
            e.preventDefault();
            e.returnValue = '';
        }
    });
})();
(function () {
    const el = document.getElementById('sisa-waktu');
    if (!el) return;
    let sisa = parseInt(el.dataset.detik || '0', 10);
    const cetak = () => {
        const m = String(Math.floor(Math.max(0, sisa) / 60)).padStart(2, '0');
        const d = String(Math.max(0, sisa) % 60).padStart(2, '0');
        el.textContent = m + ':' + d;
    };
    cetak();
    setInterval(function () {
        sisa--;
        cetak();
        if (sisa <= 0) {
            const form = document.getElementById('form-kuis');
            if (form && !form.dataset.kadaluarsa) {
                form.dataset.kadaluarsa = '1';
                form.submit();
            }
        }
    }, 1000);
})();
</script>
@endpush
</x-sidebar-layout>
