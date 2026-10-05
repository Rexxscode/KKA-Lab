<section>
    <form method="post" action="{{ route('password.update') }}" class="space-y-5">
        @csrf
        @method('put')

        <div>
            <x-input-label for="update_password_current_password" :value="__('Kata Sandi Saat Ini')" />
            <x-password-input id="update_password_current_password" name="current_password" class="mt-1 block w-full dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" autocomplete="current-password" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <div
            x-data="{
                pwd: '',
                skor() {
                    let s = 0;
                    if (this.pwd.length >= 8) s++;
                    if (/[A-Za-z]/.test(this.pwd) && /[0-9]/.test(this.pwd)) s++;
                    if (/[^A-Za-z0-9]/.test(this.pwd)) s++;
                    if (this.pwd.length >= 12) s++;
                    return s;
                },
                label() {
                    return ['Lemah', 'Lemah', 'Cukup', 'Kuat', 'Sangat Kuat'][this.skor()];
                },
                warna() {
                    return ['text-red-500', 'text-red-500', 'text-orange-500', 'text-emerald-500', 'text-emerald-600 dark:text-emerald-400'][this.skor()];
                },
                barWarna() {
                    if (this.skor() < 2) return 'bg-red-500';
                    if (this.skor() < 3) return 'bg-orange-500';
                    return 'bg-emerald-500';
                }
            }"
        >
            <x-input-label for="update_password_password" :value="__('Kata Sandi Baru')" />
            <x-password-input id="update_password_password" name="password" x-model="pwd" class="mt-1 block w-full dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
            <div class="mt-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 p-3 space-y-2">
                <div class="flex items-center justify-between text-xs">
                    <span class="text-slate-500 dark:text-slate-400 font-semibold">Kekuatan Kata Sandi:</span>
                    <span class="font-bold" aria-live="polite" :class="warna()" x-text="pwd ? label() : '—'">—</span>
                </div>
                <div class="grid grid-cols-4 gap-1.5 h-1.5" aria-hidden="true">
                    <template x-for="i in 4" :key="i">
                        <div class="rounded-full" :class="skor() >= i ? barWarna() : 'bg-slate-200 dark:bg-slate-700'"></div>
                    </template>
                </div>
                <ul class="grid grid-cols-1 sm:grid-cols-2 gap-1 text-xs list-none m-0 p-0">
                    <li class="flex items-center gap-1.5" :class="pwd.length >= 8 ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-500 dark:text-slate-400'"><x-icon name="check" class="w-3.5 h-3.5" /> Minimal 8 karakter</li>
                    <li class="flex items-center gap-1.5" :class="(/[A-Za-z]/.test(pwd) && /[0-9]/.test(pwd)) ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-500 dark:text-slate-400'"><x-icon name="check" class="w-3.5 h-3.5" /> Huruf + angka (wajib)</li>
                    <li class="flex items-center gap-1.5" :class="/[^A-Za-z0-9]/.test(pwd) ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-500 dark:text-slate-400'"><x-icon name="check" class="w-3.5 h-3.5" /> Simbol bikin lebih kuat</li>
                    <li class="flex items-center gap-1.5" :class="pwd.length >= 12 ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-500 dark:text-slate-400'"><x-icon name="check" class="w-3.5 h-3.5" /> 12+ karakter lebih baik</li>
                </ul>
            </div>
        </div>

        <div>
            <x-input-label for="update_password_password_confirmation" :value="__('Konfirmasi Kata Sandi')" />
            <x-password-input id="update_password_password_confirmation" name="password_confirmation" class="mt-1 block w-full dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Simpan') }}</x-primary-button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-slate-600 dark:text-slate-300"
                >{{ __('Tersimpan.') }}</p>
            @endif
        </div>
    </form>
</section>
