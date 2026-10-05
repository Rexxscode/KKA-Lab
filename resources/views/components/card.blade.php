@props(['title' => null, 'subtitle' => null])

<div {{ $attributes->merge(['class' => 'kka-card p-6 flex flex-col h-full']) }}>
    @if($title)
        <h3 class="font-bold text-slate-900 dark:text-white text-lg">{{ $title }}</h3>
    @endif
    @if($subtitle)
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">{{ $subtitle }}</p>
    @endif
    <div class="mt-4 flex-1 flex flex-col">{{ $slot }}</div>
</div>
