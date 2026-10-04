<x-sidebar-layout>
    <x-slot name="header">
        <a href="{{ route('siswa.kuis.index') }}" class="text-sm font-semibold text-orange-500">← Semua Kuis</a>
        <h2 class="font-extrabold text-xl text-slate-900 mt-1">Kuis: {{ $module->title }}</h2>
        <p class="text-sm text-slate-500">{{ count($tampil) }} soal acak per percobaan • Opsi diacak • Lulus jika skor ≥80 • Materi disembunyikan (murni pemahamanmu)</p>
    </x-slot>

    <div class="max-w-3xl" x-data="kkaBelajar({{ count($tampil) }})">
        <x-card title="Kuis {{ $module->title }}" :subtitle="$progress && $progress->is_completed ? 'Status: LULUS • Skor '.$progress->quiz_score.' • +'.$progress->xp_earned.' XP' : 'Status: '.($progress && $progress->quiz_score ? 'skor '.$progress->quiz_score.' — belum lulus, coba lagi!' : 'belum dikerjakan')">
            @if(session('quiz_result'))
                @php
                    $qr = session('quiz_result');
                    $benar = collect($qr['detail'])->where('ok', true)->count();
                    $total = count($qr['detail']);
                @endphp
                <div class="mb-4 rounded-2xl border p-4 text-sm font-semibold {{ $qr['passed'] ? 'border-emerald-200 bg-emerald-50 text-emerald-800' : 'border-orange-200 bg-orange-50 text-orange-800' }}" role="status">
                    Skor {{ $qr['score'] }} ({{ $benar }}/{{ $total }} benar) — soal & opsi diacak ulang tiap percobaan. Pelajari pembahasan di bawah!
                </div>
            @endif
            <form id="form-kuis" method="POST" action="{{ route('siswa.kuis.submit', $module->slug) }}" class="space-y-4" @submit="if (!kumpulValid()) $event.preventDefault(); else sudahKumpul = true">
                @csrf
                <div class="flex items-center justify-between text-sm">
                    <p class="font-bold text-slate-800">Soal <span x-text="soalIdx + 1"></span> dari {{ count($tampil) }}</p>
                    <p class="text-xs text-slate-500"><span x-text="terjawabSemua()"></span>/{{ count($tampil) }} terjawab</p>
                </div>
                <div class="w-full h-2.5 bg-slate-100 rounded-full overflow-hidden border border-slate-200" role="progressbar" aria-valuemin="0" aria-valuemax="{{ count($tampil) }}" :aria-valuenow="terjawabSemua()">
                    <div class="h-full bg-gradient-to-r from-orange-500 to-emerald-500 transition-all" :style="'width: ' + Math.round(terjawabSemua() / {{ count($tampil) }} * 100) + '%'"></div>
                </div>
                <nav aria-label="Navigasi soal" class="flex flex-wrap gap-1.5">
                    @foreach($tampil as $i => $t)
                        <button type="button" @click="keSoal({{ $i }})" aria-label="Ke soal {{ $i + 1 }}" :aria-current="soalIdx === {{ $i }} ? 'true' : 'false'" class="w-8 h-8 rounded-lg text-xs font-bold border transition focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-400 focus-visible:ring-offset-2"
                            :class="soalIdx === {{ $i }} ? 'bg-orange-500 text-white border-orange-500' : (dijawab({{ $i }}) ? 'bg-emerald-100 text-emerald-800 border-emerald-300' : 'bg-white text-slate-500 border-slate-200')">{{ $i + 1 }}</button>
                    @endforeach
                </nav>
                @foreach($tampil as $i => $t)
                    @php
                        $pilihan = old("answers.{$i}");
                        $ulas = session('quiz_result.detail.' . $i);
                    @endphp
                    <fieldset x-show="soalIdx === {{ $i }}" class="rounded-2xl border p-4 {{ $ulas ? ($ulas['ok'] ? 'border-emerald-300 bg-emerald-50/50' : 'border-orange-300 bg-orange-50/50') : 'border-slate-200' }}">
                        <legend class="sr-only">Soal {{ $i + 1 }}</legend>
                        <p class="font-semibold text-slate-800 text-sm">
                            {{ $i + 1 }}. {{ $t['quiz']->question }}
                            @if($ulas)
                                <span class="ml-1 font-bold {{ $ulas['ok'] ? 'text-emerald-700' : 'text-orange-700' }}">{{ $ulas['ok'] ? '✓ Benar' : '✗ Kurang tepat' }}</span>
                            @endif
                        </p>
                        <div class="mt-2 space-y-1.5" role="radiogroup" aria-label="Pilihan jawaban soal {{ $i + 1 }}">
                            @foreach($t['options'] as $oi => $opt)
                                @php
                                    $isBenar = $ulas && $oi == $ulas['correct'];
                                    $isPilihSalah = $ulas && !$ulas['ok'] && (string) $pilihan === (string) $oi;
                                    $inputId = "soal-{$i}-opsi-{$oi}";
                                @endphp
                                <label for="{{ $inputId }}" class="flex items-center gap-2 text-sm cursor-pointer rounded-xl px-2 py-1.5 focus-within:ring-2 focus-within:ring-orange-400 {{ $isBenar ? 'text-emerald-800 font-semibold' : ($isPilihSalah ? 'text-orange-800 font-semibold' : 'text-slate-700 hover:text-orange-700') }}">
                                    <input id="{{ $inputId }}" type="radio" name="answers[{{ $i }}]" value="{{ $oi }}" class="accent-orange-500 w-4 h-4" @checked((string) $pilihan === (string) $oi)>
                                    <span><b>{{ chr(65 + $oi) }}.</b> {{ $opt }}</span>
                                    @if($isBenar)<span aria-label="jawaban benar">✓</span>@endif
                                    @if($isPilihSalah)<span>(pilihanmu) ✗</span>@endif
                                </label>
                            @endforeach
                        </div>
                        @if($ulas && ($ulas['explanation'] ?? null))
                            <p class="mt-2 text-xs rounded-xl bg-white/70 border border-slate-200 p-2 text-slate-600">💡 <b>Pembahasan:</b> {{ $ulas['explanation'] }}</p>
                        @endif
                    </fieldset>
                @endforeach
                <p x-show="pesan" x-text="pesan" class="text-xs font-bold text-orange-700" role="alert"></p>
                <div class="flex items-center justify-between gap-2">
                    <button type="button" @click="keSoal(soalIdx - 1)" :disabled="soalIdx === 0" :class="soalIdx === 0 ? 'kka-btn-secondary opacity-40' : 'kka-btn-secondary'" class="!py-2 text-sm focus-visible:ring-2 focus-visible:ring-orange-400">← Sebelumnya</button>
                    <template x-if="soalIdx < {{ count($tampil) - 1 }}">
                        <x-button type="button" @click="keSoal(soalIdx + 1)">Lanjut →</x-button>
                    </template>
                    <template x-if="soalIdx === {{ count($tampil) - 1 }}">
                        <x-button type="submit">Kumpulkan Kuis</x-button>
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
// Cegah jawaban hilang karena salah tekan back / tutup tab saat kuis berjalan.
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
</script>
@endpush
</x-sidebar-layout>
