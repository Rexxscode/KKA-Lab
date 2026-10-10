<x-guest-layout>
    <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="role" value="siswa" />

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Nama')" />
            <x-text-input id="name" class="block mt-1 w-full dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Kata Sandi')" />

            <x-password-input id="password" class="block mt-1 w-full dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Konfirmasi Kata Sandi')" />

            <x-password-input id="password_confirmation" class="block mt-1 w-full dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <p class="mt-4 text-xs text-slate-500 dark:text-slate-400 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 p-3">
            Pendaftaran siswa <b>langsung aktif</b>, tanpa menunggu persetujuan.
        </p>
        <p class="mt-3 text-xs text-slate-500 dark:text-slate-400 rounded-xl border border-orange-200 dark:border-orange-500/30 bg-orange-50 dark:bg-orange-500/10 p-3">
            Akun guru dibuat oleh <b>Administrator KKALAB</b>. Silakan hubungi administrator sekolah untuk mendapatkan akun.
        </p>

        <div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-3 mt-4">
            <a class="underline text-sm text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                {{ __('Sudah punya akun?') }}
            </a>

            <x-primary-button class="w-full sm:w-auto sm:ms-4 justify-center">
                {{ __('Daftar') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
