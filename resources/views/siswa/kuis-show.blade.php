<x-sidebar-layout>
    <x-slot name="header">
        <a href="{{ route('siswa.kuis.index') }}" class="text-sm font-semibold text-orange-500">← Semua Kuis</a>
        <h2 class="font-extrabold text-xl text-slate-900 mt-1">Kuis: {{ $module->title }}</h2>
        <p class="text-sm text-slate-500">{{ $module->quizzes->count() }} soal • Lulus jika skor ≥80 • Materi disembunyikan (murni pemahamanmu)</p>
    </x-slot>

    <div class="max-w-3xl" x-data="kkaBelajar({{ $module->quizzes->count() }})">
        <x-card title="Kuis {{ $module->title }}" :subtitle="$progress && $progress->is_completed ? 'Status: LULUS • Skor '.$progress->quiz_score.' • +'.$progress->xp_earned.' XP' : 'Status: '.($progress && $progress->quiz_score ? 'skor '.$progress->quiz_score.' — belum lulus, coba lagi!' : 'belum dikerjakan')">
            @if(session('quiz_result'))
                @php
                    $qr = session('quiz_result');
                    $benar = collect($qr['detail'])->where('ok', true)->count();
                    $total = count($qr['detail']);
                @endphp
                <div class="mb-4 rounded-2xl border p-4 text-sm font-semibold {{ $qr['passed'] ? 'border-emerald-200 bg-emerald-50 text-emerald-800' : 'border-orange-200 bg-orange-50 text-orange-800' }}">
                    Skor {{ $qr['score'] }} ({{ $benar }}/{{ $total }} benar) — pelajari pembahasan di bawah!
                </div>
            @endif
            <form id="form-kuis" method="POST" action="{{ route('siswa.kuis.submit', $module->slug) }}" class="space-y-4" @submit="if (!kumpulValid()) $event.preventDefault()">
                @csrf
                <div class="flex items-center justify-between text-sm">
                    <p class="font-bold text-slate-800">Soal <span x-text="soalIdx + 1"></span> dari {{ $module->quizzes->count() }}</p>
                    <p class="text-xs text-slate-500"><span x-text="terjawabSemua()"></span>/{{ $module->quizzes->count() }} terjawab</p>
                </div>
                <div class="w-full h-2.5 bg-slate-100 rounded-full overflow-hidden border border-slate-200">
                    <div class="h-full bg-gradient-to-r from-orange-500 to-emerald-500 transition-all" :style="'width: ' + Math.round(terjawabSemua() / {{ $module->quizzes->count() }} * 100) + '%'"></div>
                </div>
                <div class="flex flex-wrap gap-1.5">
                    @foreach($module->quizzes as $i => $q)
                        <button type="button" @click="keSoal({{ $i }})" class="w-8 h-8 rounded-lg text-xs font-bold border transition"
                            :class="soalIdx === {{ $i }} ? 'bg-orange-500 text-white border-orange-500' : (dijawab({{ $i }}) ? 'bg-emerald-100 text-emerald-700 border-emerald-300' : 'bg-white text-slate-500 border-slate-200')">{{ $i + 1 }}</button>
                    @endforeach
                </div>
                @foreach($module->quizzes as $i => $q)
                    @php
                        $pilihan = old("answers.{$i}");
                        $ulas = session('quiz_result.detail.' . $i);
                    @endphp
                    <div x-show="soalIdx === {{ $i }}" class="rounded-2xl border p-4 {{ $ulas ? ($ulas['ok'] ? 'border-emerald-300 bg-emerald-50/50' : 'border-orange-300 bg-orange-50/50') : 'border-slate-200' }}">
                        <p class="font-semibold text-slate-800 text-sm">
                            {{ $i + 1 }}. {{ $q->question }}
                            @if($ulas)
                                <span class="ml-1 font-bold {{ $ulas['ok'] ? 'text-emerald-600' : 'text-orange-600' }}">{{ $ulas['ok'] ? '✓ Benar' : '✗ Kurang tepat' }}</span>
                            @endif
                        </p>
                        <div class="mt-2 space-y-1.5">
                            @foreach($q->options as $oi => $opt)
                                @php
                                    $isBenar = $ulas && $oi == $ulas['correct'];
                                    $isPilihSalah = $ulas && !$ulas['ok'] && (string) $pilihan === (string) $oi;
                                @endphp
                                <label class="flex items-center gap-2 text-sm cursor-pointer rounded-xl px-2 py-1 {{ $isBenar ? 'text-emerald-700 font-semibold' : ($isPilihSalah ? 'text-orange-700 font-semibold' : 'text-slate-700 hover:text-orange-600') }}">
                                    <input type="radio" name="answers[{{ $i }}]" value="{{ $oi }}" class="accent-orange-500" @checked((string) $pilihan === (string) $oi)>
                                    {{ $opt }}
                                    @if($isBenar)<span>✓</span>@endif
                                    @if($isPilihSalah)<span>(pilihanmu) ✗</span>@endif
                                </label>
                            @endforeach
                        </div>
                        @if($ulas && $q->explanation)
                            <p class="mt-2 text-xs rounded-xl bg-white/70 border border-slate-200 p-2 text-slate-600">💡 <b>Pembahasan:</b> {{ $q->explanation }}</p>
                        @endif
                    </div>
                @endforeach
                <p x-show="pesan" x-text="pesan" class="text-xs font-bold text-orange-600"></p>
                <div class="flex items-center justify-between gap-2">
                    <button type="button" @click="keSoal(soalIdx - 1)" :disabled="soalIdx === 0" :class="soalIdx === 0 ? 'kka-btn-secondary opacity-40' : 'kka-btn-secondary'" class="!py-2 text-sm">← Sebelumnya</button>
                    <template x-if="soalIdx < {{ $module->quizzes->count() - 1 }}">
                        <x-button type="button" @click="keSoal(soalIdx + 1)">Lanjut →</x-button>
                    </template>
                    <template x-if="soalIdx === {{ $module->quizzes->count() - 1 }}">
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
        total: total, soalIdx: 0, pesan: '',
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
</script>
@endpush
</x-sidebar-layout>
