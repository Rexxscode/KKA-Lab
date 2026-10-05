<section>
    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-5">
        @csrf
        @method('patch')

        <div x-data="{ pratinjau: null }">
            <x-input-label :value="__('Foto Profil')" />
            <div class="mt-2 flex flex-wrap items-center gap-4">
                <template x-if="! pratinjau">
                    @if($user->avatar_path)
                        <img src="{{ $user->avatarUrl() }}" alt="Foto profil" class="w-16 h-16 rounded-2xl object-cover border border-slate-200 dark:border-slate-700" />
                    @else
                        <span class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-orange-100 dark:bg-orange-500/15 text-orange-700 dark:text-orange-300 font-extrabold text-2xl" aria-hidden="true">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                    @endif
                </template>
                <img x-show="pratinjau" x-bind:src="pratinjau" alt="Pratinjau foto baru" class="w-16 h-16 rounded-2xl object-cover border border-orange-300" style="display: none;" />
                <div class="flex flex-wrap items-center gap-2">
                    <label for="avatar" class="kka-btn-secondary !py-2 text-sm cursor-pointer">{{ $user->avatar_path ? __('Ganti foto') : __('Pilih foto') }}</label>
                    @if($user->avatar_path)
                        <label class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 dark:text-slate-400 hover:text-red-600 dark:hover:text-red-400 cursor-pointer">
                            <input type="checkbox" name="hapus_avatar" value="1" class="rounded border-slate-300 dark:border-slate-700 dark:bg-slate-900 text-red-600 focus:ring-red-500" />
                            {{ __('Hapus foto') }}
                        </label>
                    @endif
                </div>
                <input id="avatar" name="avatar" type="file" accept=".jpg,.jpeg,.png,.webp" class="sr-only" x-on:change="pratinjau = $event.target.files.length ? URL.createObjectURL($event.target.files[0]) : null" />
            </div>
            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">JPG/PNG/WebP, maks 2MB. Pratinjau tampil sebelum disimpan.</p>
            <x-input-error class="mt-2" :messages="$errors->get('avatar')" />
        </div>

        <div>
            <x-input-label for="name" :value="__('Nama')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-2 rounded-xl bg-orange-50 dark:bg-orange-500/10 border border-orange-200 dark:border-orange-500/30 p-4">
                    <p class="text-sm font-bold text-orange-700 dark:text-orange-300">
                        {{ __('Alamat emailmu belum terverifikasi.') }}
                    </p>
                    <p class="text-sm mt-1 text-slate-600 dark:text-slate-300">
                        {{ __('Verifikasi email agar bisa menerima sertifikat & pemulihan akun.') }}
                        <button form="send-verification" class="underline font-semibold text-orange-600 dark:text-orange-400 hover:text-orange-700">
                            {{ __('Kirim ulang email verifikasi.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-emerald-600 dark:text-emerald-400">
                            {{ __('Tautan verifikasi baru sudah dikirim ke emailmu.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Simpan') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
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
