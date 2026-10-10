<x-guest-layout>
    <div class="mb-4 text-sm text-slate-600 dark:text-slate-300">
        {{ __('Terima kasih sudah mendaftar! Sebelum mulai, verifikasi dulu alamat emailmu lewat tautan yang baru kami kirim. Kalau belum terima emailnya, kami kirim ulang dengan senang hati.') }}
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 font-medium text-sm text-green-600 dark:text-green-400">
            {{ __('Tautan verifikasi baru sudah dikirim ke email yang kamu daftarkan.') }}
        </div>
    @endif

    <div class="mt-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf

            <div>
                <x-primary-button class="w-full sm:w-auto justify-center">
                    {{ __('Kirim Ulang Email Verifikasi') }}
                </x-primary-button>
            </div>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit" class="underline text-sm text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                {{ __('Keluar') }}
            </button>
        </form>
    </div>
</x-guest-layout>
