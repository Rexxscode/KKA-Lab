<x-sidebar-layout>
    <x-slot name="header">
        <a href="{{ route('admin.dashboard') }}" class="text-sm font-semibold text-orange-500">← Dasbor</a>
        <h2 class="font-extrabold text-xl text-slate-900 dark:text-white mt-1">Tanda Tangan Sertifikat</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400">Status gambar tanda tangan para penandatangan. Gambar dibuat di halaman Profil masing-masing.</p>
    </x-slot>

    <div class="py-8 max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
        <x-card title="Super Admin" :subtitle="$superAdmin ? $superAdmin->name . ' • ' . $superAdmin->email : 'Belum ada admin'">
            @if($superAdmin?->signature_path)
                <img src="{{ Storage::url($superAdmin->signature_path) }}" alt="Tanda tangan super admin" class="h-20 rounded-xl border border-slate-200 dark:border-slate-700 bg-white px-4 py-2" />
            @else
                <p class="text-sm font-semibold text-orange-600 dark:text-orange-400">Belum ada gambar — sertifikat memakai nama tercetak.</p>
            @endif
        </x-card>

        <x-card title="Guru Aktif" subtitle="Kolom Guru Pembimbing di sertifikat memakai guru kelas aktif siswa.">
            <div class="space-y-2">
                @forelse($gurus as $guru)
                    <div class="flex items-center justify-between text-sm border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2">
                        <span class="font-semibold text-slate-700 dark:text-slate-200">{{ $guru->name }}</span>
                        @if($guru->signature_path)
                            <span class="font-bold text-emerald-600 dark:text-emerald-400">Sudah ada</span>
                        @else
                            <span class="font-bold text-slate-400">Belum ada</span>
                        @endif
                    </div>
                @empty
                    <p class="text-sm text-slate-500">Belum ada guru aktif.</p>
                @endforelse
            </div>
        </x-card>
    </div>
</x-sidebar-layout>
