<section class="space-y-4">
    <p class="text-sm text-slate-500 dark:text-slate-400">
        {{ __('Setelah akun dihapus, nilai kuis, XP, progres modul, dan sertifikatmu hilang permanen dan tidak bisa dipulihkan.') }}
    </p>

    <x-danger-button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
    >{{ __('Hapus Akun') }}</x-danger-button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')

            <h2 class="text-lg font-medium text-slate-900 dark:text-white">
                {{ __('Yakin ingin menghapus akunmu?') }}
            </h2>

            <p class="mt-1 text-sm text-slate-600 dark:text-slate-300">
                {{ __('Setelah dihapus, semua data hilang permanen. Masukkan kata sandimu untuk konfirmasi.') }}
            </p>

            <div class="mt-6">
                <x-input-label for="password" value="{{ __('Kata Sandi') }}" class="sr-only" />

                <x-password-input
                    id="password"
                    name="password"
                    class="mt-1 block w-3/4 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
                    placeholder="{{ __('Kata Sandi') }}"
                />

                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Batal') }}
                </x-secondary-button>

                <x-danger-button class="ms-3">
                    {{ __('Hapus Akun') }}
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</section>
