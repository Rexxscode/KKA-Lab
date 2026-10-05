<section>
    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-5">
        @csrf
        @method('patch')

        <div>
            <x-input-label :value="__('Foto Profil')" />
            <div class="mt-2 flex items-center gap-4">
                @if($user->avatar_path)
                    <img src="{{ $user->avatarUrl() }}" alt="Foto profil" class="w-16 h-16 rounded-2xl object-cover border border-slate-200 dark:border-slate-700" />
                @else
                    <span class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-orange-100 text-orange-700 font-extrabold text-2xl">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                @endif
                <input id="avatar" name="avatar" type="file" accept=".jpg,.jpeg,.png,.webp" class="text-sm text-slate-500 dark:text-slate-400 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border file:border-slate-200 dark:file:border-slate-700 file:bg-white dark:file:bg-slate-800 file:font-bold file:text-slate-700 dark:file:text-slate-200 hover:file:border-orange-300" />
            </div>
            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">JPG/PNG/WebP, maks 2MB.</p>
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
                <div class="rounded-xl bg-orange-50 dark:bg-orange-500/10 border border-orange-200 dark:border-orange-500/30 p-4">
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
