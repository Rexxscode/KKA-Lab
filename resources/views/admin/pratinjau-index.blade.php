<x-sidebar-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="font-extrabold text-xl text-slate-900 dark:text-white">Pratinjau Siswa</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400">Pilih siswa untuk melihat dashboard-nya (read-only, progres tidak ikut tersimpan).</p>
            </div>
            <a href="{{ route('admin.dashboard') }}" class="kka-btn-secondary !py-2 text-sm">← Kembali ke Admin</a>
        </div>
    </x-slot>

    <x-card title="Cari Siswa" subtitle="berdasar nama atau email">
        <form method="GET" action="{{ route('admin.pratinjau.index') }}" class="flex flex-col sm:flex-row gap-2">
            <input name="q" value="{{ $q }}" placeholder="Cari nama / email..." aria-label="Cari siswa"
                class="flex-1 rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 focus:border-orange-500 focus:ring-orange-500" />
            <x-button type="submit">Cari</x-button>
        </form>
    </x-card>

    <div class="grid sm:grid-cols-2 xl:grid-cols-3 gap-3 sm:gap-4 mt-4">
        @forelse($siswa as $s)
            <x-card :title="$s->name" :subtitle="$s->email">
                <p class="text-sm text-slate-500 dark:text-slate-400">{{ $s->lulus_count }} modul lulus • {{ $s->totalXp() }} XP</p>
                <div class="mt-auto pt-3">
                    <a href="{{ route('admin.pratinjau.show', $s) }}" class="kka-btn-primary !py-2 text-sm text-center block w-full">Pratinjau Dashboard →</a>
                </div>
            </x-card>
        @empty
            <x-card title="Tidak ada siswa" subtitle="coba kata kunci lain">
                <p class="text-sm text-slate-500 dark:text-slate-400">Belum ada akun siswa yang cocok.</p>
            </x-card>
        @endforelse
    </div>

    <div class="mt-4">{{ $siswa->links() }}</div>
</x-sidebar-layout>
