{{-- Badan soal ujian/kuis shared: navigasi + fieldset + tombol.
  Props: $tampil (array soal), $resultKey ('quiz_result'|'ujian_result'),
  $submitLabel ('Kumpulkan Kuis'|'Kumpulkan Ujian'). --}}
<nav aria-label="Navigasi soal" class="flex flex-wrap gap-1.5">
    @foreach($tampil as $i => $t)
        <button type="button" @click="keSoal({{ $i }})" aria-label="Ke soal {{ $i + 1 }}" :aria-current="soalIdx === {{ $i }} ? 'true' : 'false'" class="w-11 h-11 rounded-xl text-xs font-bold border transition focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-400 focus-visible:ring-offset-2"
            :class="soalIdx === {{ $i }} ? 'bg-orange-500 text-white border-orange-500' : (dijawab({{ $i }}) ? 'bg-emerald-100 dark:bg-emerald-500/20 text-emerald-800 dark:text-emerald-300 border-emerald-300 dark:border-emerald-500/40' : 'bg-white dark:bg-slate-800 text-slate-500 dark:text-slate-400 border-slate-200 dark:border-slate-700')">{{ $i + 1 }}</button>
    @endforeach
</nav>
@foreach($tampil as $i => $t)
    @php
        $pilihan = old("answers.{$i}");
        $ulas = session($resultKey . '.detail.' . $i);
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
        <button type="submit" :disabled="sudahKumpul" x-text="sudahKumpul ? 'Mengumpulkan...' : '{{ $submitLabel }}'" class="kka-btn-primary">{{ $submitLabel }}</button>
    </template>
</div>
