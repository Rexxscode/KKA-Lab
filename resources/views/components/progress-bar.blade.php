@props(['value' => 0, 'max' => 100])

@php $pct = $max > 0 ? min(100, round($value / $max * 100)) : 0; @endphp
<div {{ $attributes->merge(['class' => 'w-full h-2.5 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden border border-slate-200 dark:border-slate-700']) }} role="progressbar" aria-valuemin="0" aria-valuemax="{{ $max }}" aria-valuenow="{{ min($value, $max) }}" aria-label="Progres belajar">
    <div class="h-full bg-emerald-500 transition-all" style="width: {{ $pct }}%"></div>
</div>
