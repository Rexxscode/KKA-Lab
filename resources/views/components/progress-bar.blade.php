@props(['value' => 0, 'max' => 100])

@php $pct = $max > 0 ? min(100, round($value / $max * 100)) : 0; @endphp
<div {{ $attributes->merge(['class' => 'w-full h-2.5 bg-slate-100 rounded-full overflow-hidden border border-slate-200']) }}>
    <div class="h-full bg-gradient-to-r from-orange-500 to-emerald-500 transition-all" style="width: {{ $pct }}%"></div>
</div>
