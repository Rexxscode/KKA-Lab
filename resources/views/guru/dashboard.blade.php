<x-sidebar-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="font-extrabold text-xl text-slate-900">Dashboard Guru</h2>
                <p class="text-sm text-slate-500">Ringkasan data mengajar — kelola kelas lewat halaman Kelas Saya.</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('guru.kelas.index') }}" class="kka-btn-primary !py-2 text-sm">Kelas Saya</a>
                <a href="{{ route('guru.quiz.index') }}" class="kka-btn-secondary !py-2 text-sm">Bank Soal</a>
            </div>
        </div>
    </x-slot>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <x-card title="Total Kelas" subtitle="kelas dibuat"><p class="text-3xl font-extrabold text-slate-900">{{ $totalKelas }}</p></x-card>
        <x-card title="Total Siswa" subtitle="tergabung di kelas"><p class="text-3xl font-extrabold text-orange-500">{{ $totalSiswa }}</p></x-card>
        <x-card title="Siswa Tuntas" subtitle="lulus semua modul"><p class="text-3xl font-extrabold text-emerald-500">{{ $siswaTuntas }}</p></x-card>
        <x-card title="Bank Soal" subtitle="{{ $totalModul }} modul"><p class="text-3xl font-extrabold text-slate-900">{{ $totalSoal }} <span class="text-base font-bold text-slate-400">soal</span></p></x-card>
    </div>

    <div class="grid lg:grid-cols-2 gap-3 sm:gap-4 mt-4">
        <x-card title="Kelas Terbaru" subtitle="{{ $kelasBaru->count() }} terakhir dibuat">
            <div class="space-y-2">
                @forelse($kelasBaru as $c)
                    <a href="{{ route('guru.kelas.show', $c) }}" class="flex items-center justify-between gap-2 rounded-xl border border-slate-200 px-4 py-2.5 hover:border-orange-300 transition">
                        <div class="min-w-0"><p class="font-bold text-sm text-slate-800 truncate">{{ $c->name }}</p><p class="text-xs text-slate-400">Kode {{ $c->code }} • {{ $c->students_count }} siswa • {{ $c->created_at->format('d M Y') }}</p></div>
                        <span class="text-sm font-bold text-orange-500 shrink-0">→</span>
                    </a>
                @empty
                    <p class="text-sm text-slate-400">Belum ada kelas. <a href="{{ route('guru.kelas.index') }}" class="font-bold text-orange-500">Buat di halaman Kelas Saya →</a></p>
                @endforelse
            </div>
        </x-card>

        <x-card title="Siswa Paling Aktif" subtitle="berdasar XP">
            <div class="space-y-2">
                @forelse($siswaAktif as $i => $s)
                    <div class="flex items-center gap-3 rounded-xl border border-slate-200 px-4 py-2.5">
                        <span class="inline-flex items-center justify-center w-7 h-7 rounded-full text-xs font-extrabold {{ $i === 0 ? 'bg-orange-500 text-white' : 'bg-slate-100 text-slate-500' }}">{{ $i + 1 }}</span>
                        <p class="flex-1 font-bold text-sm text-slate-800 truncate">{{ $s->name }}</p>
                        <span class="text-sm font-extrabold text-emerald-600 whitespace-nowrap">⚡ {{ $s->xp ?? 0 }}</span>
                    </div>
                @empty
                    <p class="text-sm text-slate-400">Belum ada siswa bergabung.</p>
                @endforelse
            </div>
        </x-card>
    </div>
</x-sidebar-layout>
