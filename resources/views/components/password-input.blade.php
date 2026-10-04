<div class="relative" x-data="{ lihat: false }">
    <x-text-input {{ $attributes->merge(['type' => 'password', 'class' => 'pr-11']) }} x-bind:type="lihat ? 'text' : 'password'" />
    <button type="button" @click="lihat = !lihat" :aria-label="lihat ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi'" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-orange-500 transition">
        <svg x-show="!lihat" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
        <svg x-show="lihat" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="display: none"><path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l-5.636-5.637m5.636 5.637l6.364 6.364m-6.364-6.364L3.514 3.514m6.364 6.364l9.192 9.192M9.878 9.878l6.364 6.364m0 0l3.644 3.644M9.878 9.878L3.514 3.514" /></svg>
    </button>
</div>
