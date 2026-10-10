@props(['wrapperClass' => null])

@php
    $inputClass = $attributes->get('class', '');
    // Lebar outer mengikuti pemanggil: dropdown reset PW pakai w-28, form lain w-full.
    $outerWidth = $wrapperClass
        ?? (str_contains($inputClass, 'w-28') ? 'w-28 shrink-0' : 'w-full');
@endphp

<div class="relative {{ $outerWidth }}" x-data="{ lihat: false }">
    <x-text-input {{ $attributes->merge(['type' => 'password', 'class' => 'pr-12 w-full']) }} x-bind:type="lihat ? 'text' : 'password'" data-password-input />
    <button type="button" @click="lihat = !lihat" :aria-pressed="lihat.toString()" :aria-label="lihat ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi'"
        onclick="try{var i=this.previousElementSibling;if(i&&i.tagName==='INPUT'){var show=i.type==='password';i.type=show?'text':'password';this.setAttribute('aria-pressed',show);}}catch(e){}"
        class="absolute right-1 top-1/2 -translate-y-1/2 min-w-9 min-h-9 p-2 inline-flex items-center justify-center rounded-lg text-slate-400 hover:text-orange-500 hover:bg-orange-50 dark:hover:bg-orange-500/10 transition" aria-label="Tampilkan kata sandi">
        <svg x-show="!lihat" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
        <svg x-show="lihat" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="display: none" aria-hidden="true" x-cloak><path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l-5.636-5.637m5.636 5.637l6.364 6.364m-6.364-6.364L3.514 3.514m6.364 6.364l9.192 9.192M9.878 9.878l6.364 6.364m0 0l3.644 3.644M9.878 9.878L3.514 3.514" /></svg>
        <span class="sr-only">Toggle kata sandi</span>
    </button>
</div>
