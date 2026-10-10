<x-guest-layout>
    <div class="py-12 max-w-xl mx-auto px-4 sm:px-6">
        <div class="kka-card !p-8 text-center">
            <p class="text-xs font-bold tracking-widest text-emerald-600 dark:text-emerald-400">KKALAB • VERIFIKASI SERTIFIKAT</p>
            <div class="mt-4 inline-flex items-center justify-center w-14 h-14 rounded-full bg-emerald-100 dark:bg-emerald-500/15 text-emerald-700 dark:text-emerald-400 text-2xl font-extrabold">✓</div>
            <h1 class="mt-3 text-2xl font-extrabold text-slate-900 dark:text-white">Sertifikat Valid</h1>
            <p class="mt-4 text-sm text-slate-500 dark:text-slate-400">Diberikan kepada</p>
            <p class="text-xl font-extrabold text-orange-500 dark:text-orange-400">{{ $certificate->user->name }}</p>
            <div class="mt-5 grid grid-cols-2 gap-3 text-sm">
                <div class="rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 px-4 py-3">
                    <p class="text-xs text-slate-500 dark:text-slate-400">Kode Sertifikat</p>
                    <p class="font-bold">{{ $certificate->code }}</p>
                </div>
                <div class="rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 px-4 py-3">
                    <p class="text-xs text-slate-500 dark:text-slate-400">Tanggal Terbit</p>
                    <p class="font-bold">{{ $certificate->issued_at->format('d M Y') }}</p>
                </div>
                <div class="rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 px-4 py-3">
                    <p class="text-xs text-slate-500 dark:text-slate-400">Super Admin</p>
                    <p class="font-bold">{{ $certificate->super_admin_nama ?? '—' }}</p>
                </div>
                <div class="rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 px-4 py-3">
                    <p class="text-xs text-slate-500 dark:text-slate-400">Guru Pembimbing</p>
                    <p class="font-bold">{{ $certificate->guru_nama ?? '—' }}</p>
                </div>
            </div>
            <a href="{{ route('home') }}" class="kka-btn-primary mt-6">Ke Beranda KKALAB</a>
        </div>
    </div>
</x-guest-layout>
