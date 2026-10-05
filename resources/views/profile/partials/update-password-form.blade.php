<section>
    <form method="post" action="{{ route('password.update') }}" class="space-y-5">
        @csrf
        @method('put')

        <div>
            <x-input-label for="update_password_current_password" :value="__('Kata Sandi Saat Ini')" />
            <x-password-input id="update_password_current_password" name="current_password" class="mt-1 block w-full dark:border-white/10 dark:bg-[#152031] dark:text-slate-100" autocomplete="current-password" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <div x-data="{ pwd: '', skor() { let s = 0; if (this.pwd.length >= 8) s++; if (/[A-Z]/.test(this.pwd) && /[0-9]/.test(this.pwd)) s++; if (/[^A-Za-z0-9]/.test(this.pwd)) s++; if (this.pwd.length >= 12) s++; return s; }, label() { return ['Lemah', 'Lemah', 'Cukup', 'Kuat', 'Sangat Kuat'][this.skor()]; }, warna() { return ['text-red-500', 'text-red-500', 'text-orange-500', 'text-emerald-400', 'text-emerald-500'][this.skor()]; } }">
            <x-input-label for="update_password_password" :value="__('Kata Sandi Baru')" />
            <x-password-input id="update_password_password" name="password" x-model="pwd" class="mt-1 block w-full dark:border-white/10 dark:bg-[#152031] dark:text-slate-100" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
            <div class="mt-3 rounded-xl bg-slate-50 dark:bg-[#152031] p-3 space-y-2">
                <div class="flex items-center justify-between text-xs">
                    <span class="text-slate-500 dark:text-slate-400 font-semibold">Kekuatan Kata Sandi:</span>
                    <span class="font-bold" :class="warna()" x-text="pwd ? label() : '—'"></span>
                </div>
                <div class="grid grid-cols-4 gap-1.5 h-1.5">
                    <template x-for="i in 4" :key="i">
                        <div class="rounded-full" :class="skor() >= i ? (skor() < 2 ? 'bg-red-500' : (skor() < 3 ? 'bg-orange-500' : 'bg-emerald-500')) : 'bg-slate-200 dark:bg-white/10'"></div>
                    </template>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-1 text-xs">
                    <span class="flex items-center gap-1.5" :class="pwd.length >= 8 ? 'text-emerald-500' : 'text-slate-500 dark:text-slate-400'"><x-icon name="check" class="w-3.5 h-3.5" /> Minimal 8 karakter</span>
                    <span class="flex items-center gap-1.5" :class="(/[A-Z]/.test(pwd) && /[0-9]/.test(pwd)) ? 'text-emerald-500' : 'text-slate-500 dark:text-slate-400'"><x-icon name="check" class="w-3.5 h-3.5" /> Huruf kapital &amp; angka</span>
                    <span class="flex items-center gap-1.5" :class="/[^A-Za-z0-9]/.test(pwd) ? 'text-emerald-500' : 'text-slate-500 dark:text-slate-400'"><x-icon name="check" class="w-3.5 h-3.5" /> Simbol unik (@#$%)</span>
                    <span class="flex items-center gap-1.5" :class="pwd.length >= 12 ? 'text-emerald-500' : 'text-slate-500 dark:text-slate-400'"><x-icon name="check" class="w-3.5 h-3.5" /> 12+ karakter lebih baik</span>
                </div>
            </div>
        </div>

        <div>
            <x-input-label for="update_password_password_confirmation" :value="__('Konfirmasi Kata Sandi')" />
            <x-password-input id="update_password_password_confirmation" name="password_confirmation" class="mt-1 block w-full dark:border-white/10 dark:bg-[#152031] dark:text-slate-100" autocomplete="new-password" />
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
