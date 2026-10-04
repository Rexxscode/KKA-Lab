<x-guest-layout>
    <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data" x-data="{ peran: '{{ old('role', 'siswa') }}' }">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Nama')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Kata Sandi')" />

            <x-password-input id="password" class="block mt-1 w-full"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Konfirmasi Kata Sandi')" />

            <x-password-input id="password_confirmation" class="block mt-1 w-full"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="role" :value="__('Daftar sebagai')" />
            <select id="role" name="role" required x-model="peran" class="block mt-1 w-full border-gray-300 focus:border-orange-500 focus:ring-orange-500 rounded-md shadow-sm">
                <option value="siswa">Siswa (langsung aktif)</option>
                <option value="guru">Guru (verifikasi admin)</option>
            </select>
            <x-input-error :messages="$errors->get('role')" class="mt-2" />
        </div>

        <div class="mt-4" x-show="peran === 'guru'" style="display: none;">
            <x-input-label for="bukti_npk" :value="__('Foto NPK (bukti guru)')" />
            <input id="bukti_npk" type="file" name="bukti_npk" accept=".jpg,.jpeg,.png,.pdf" class="block mt-1 w-full text-sm text-slate-600 file:mr-3 file:px-4 file:py-2 file:rounded-xl file:border-0 file:bg-orange-100 file:text-orange-700 file:font-bold hover:file:bg-orange-200" />
            <p class="mt-1 text-xs text-slate-500">Foto/scan kartu atau dokumen NPK (JPG/PNG/PDF, maks 2 MB). Akun diverifikasi admin 1–2 hari kerja.</p>
            <x-input-error :messages="$errors->get('bukti_npk')" class="mt-2" />
        </div>

        <p class="mt-4 text-xs text-slate-500 rounded-xl border border-slate-200 bg-slate-50 p-3" x-show="peran === 'siswa'">
            Pendaftaran siswa <b>langsung aktif</b>, tanpa menunggu persetujuan.
        </p>
        <p class="mt-4 text-xs text-slate-500 rounded-xl border border-orange-200 bg-orange-50 p-3" x-show="peran === 'guru'" style="display: none;">
            Pendaftar guru wajib melampirkan <b>foto NPK</b>. Kamu tetap bisa masuk, tapi fitur guru terkunci sampai admin menyetujui.
        </p>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                {{ __('Sudah punya akun?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Daftar') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
