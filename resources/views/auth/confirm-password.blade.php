<x-guest-layout>
    <div class="mb-4 text-sm text-slate-600 dark:text-slate-300">
        {{ __('Ini area aman. Konfirmasi kata sandimu sebelum lanjut.') }}
    </div>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Kata Sandi')" />

            <x-password-input id="password" class="block mt-1 w-full dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex justify-end mt-4">
            <x-primary-button class="w-full sm:w-auto justify-center">
                {{ __('Konfirmasi') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
