<x-guest-layout>
    <div class="mb-4 text-sm text-slate-600 dark:text-slate-300">
        {{ __('Lupa kata sandi? Tenang saja. Beri tahu kami alamat emailmu, nanti kami kirimkan tautan untuk mengatur ulang kata sandimu.') }}
    </div>

    <p class="mb-4 text-xs text-slate-500 dark:text-slate-400 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 p-3">
        Cara tercepat: minta <b>reset langsung ke guru/admin</b> (lewat WA), lalu ganti sendiri di Profil setelah bisa masuk.
    </p>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button class="w-full sm:w-auto justify-center">
                {{ __('Kirim Tautan Reset') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
