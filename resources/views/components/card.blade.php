@props(['title' => null, 'subtitle' => null])

<div {{ $attributes->merge(['class' => 'kka-card p-4 sm:p-6 flex flex-col h-full min-w-0']) }}>
    @if($title)
        <h3 class="font-bold text-slate-900 dark:text-white text-base sm:text-lg leading-snug break-words">{{ $title }}</h3>
    @endif
    @if($subtitle)
        <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">{{ $subtitle }}</p>
    @endif
    <div class="mt-4 flex-1 flex flex-col min-w-0">{{ $slot }}</div>
</div>
