@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 shadow-sm focus:border-orange-500 focus:ring-orange-500']) }}>
