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
        @if(auth()->user()->status === 'pending')
            <div class="col-span-2 lg:col-span-4 rounded-2xl border border-orange-300 bg-orange-50 p-4 text-sm font-semibold text-orange-800 flex items-start gap-2">
                <x-icon name="clock" class="w-4 h-4 shrink-0 mt-0.5" /><span>Akun gurumu sedang menunggu verifikasi NPK oleh admin. Kamu bisa melihat-lihat dasbor, tetapi tombol Kelas Saya, Bank Soal, dan Modul Saya terkunci untuk sementara.</span>
            </div>
        @endif
        <x-card title="Total Kelas" subtitle="kelas dibuat"><p class="text-3xl font-extrabold text-slate-900">{{ $totalKelas }}</p></x-card>
        <x-card title="Total Siswa" subtitle="tergabung di kelas"><p class="kka-stat text-orange-600">{{ $totalSiswa }}</p></x-card>
        <x-card title="Siswa Tuntas" subtitle="lulus semua modul"><p class="kka-stat text-emerald-600">{{ $siswaTuntas }}</p></x-card>
        <x-card title="Bank Soal" subtitle="{{ $totalModul }} modul"><p class="text-3xl font-extrabold text-slate-900">{{ $totalSoal }} <span class="text-base font-bold text-slate-500">soal</span></p></x-card>
    </div>

    <x-card title="Perlu Perhatian" subtitle="Siswa belum mulai & skor tertinggal per kelas — bantu mereka dulu." class="mt-4">
        @forelse($perhatian ?? [] as $item)
            <div class="rounded-2xl border border-orange-200 bg-orange-50/50 px-4 py-3 mb-3">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <p class="font-extrabold text-sm text-slate-900">{{ $item['kelas']->name }} <span class="font-semibold text-slate-500">• {{ $item['kelas']->code }}</span></p>
                    <a href="{{ route('guru.kelas.show', $item['kelas']) }}" class="text-xs font-bold text-orange-600 hover:text-orange-700">Buka kelas →</a>
                </div>
                <div class="mt-2 grid sm:grid-cols-2 gap-2 text-sm">
                    <div class="rounded-xl bg-white border border-slate-200 p-3">
                        <p class="text-xs font-extrabold text-slate-500 tracking-wide">BELUM MULAI ({{ $item['belumMulaiTotal'] }})</p>
                        @forelse($item['belumMulai'] as $s)
                            <p class="mt-1 font-semibold text-slate-700 truncate">• {{ $s->name }}</p>
                        @empty
                            <p class="mt-1 text-xs text-emerald-700 font-semibold">Semua sudah mulai. Bagus!</p>
                        @endforelse
                        @if($item['belumMulaiTotal'] > count($item['belumMulai']))
                            <p class="mt-1 text-xs text-slate-400">+{{ $item['belumMulaiTotal'] - count($item['belumMulai']) }} lainnya — lihat di detail kelas.</p>
                        @endif
                    </div>
                    <div class="rounded-xl bg-white border border-slate-200 p-3">
                        <p class="text-xs font-extrabold text-slate-500 tracking-wide">SKOR TERTINGGAL ({{ $item['gagalTotal'] }})</p>
                        @forelse($item['gagal'] as $g)
                            <p class="mt-1 font-semibold text-slate-700 truncate">• {{ $g['siswa']->name }} <span class="text-orange-700 font-extrabold">skor {{ $g['skor'] }}</span> <span class="text-xs text-slate-500">{{ $g['modul'] }}</span></p>
                        @empty
                            <p class="mt-1 text-xs text-emerald-700 font-semibold">Tidak ada yang tertinggal.</p>
                        @endforelse
                        @if($item['gagalTotal'] > count($item['gagal']))
                            <p class="mt-1 text-xs text-slate-400">+{{ $item['gagalTotal'] - count($item['gagal']) }} lainnya — lihat di detail kelas.</p>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <p class="text-sm text-slate-500">Belum ada yang perlu perhatian. Semua siswa sudah mulai dan tidak ada skor tertinggal.</p>
        @endforelse
    </x-card>

    <div class="grid lg:grid-cols-2 gap-3 sm:gap-4 mt-4">
        <x-card title="Kelas Terbaru" subtitle="{{ $kelasBaru->count() }} terakhir dibuat">
            <div class="space-y-2">
                @forelse($kelasBaru as $c)
                    <a href="{{ route('guru.kelas.show', $c) }}" class="flex items-center justify-between gap-2 rounded-xl border border-slate-200 px-4 py-2.5 hover:border-orange-300 transition">
                        <div class="min-w-0"><p class="font-bold text-sm text-slate-800 truncate">{{ $c->name }}</p><p class="text-xs text-slate-500">Kode {{ $c->code }} • {{ $c->students_count }} siswa • {{ $c->created_at->format('d M Y') }}</p></div>
                        <span class="text-sm font-bold text-orange-500 shrink-0">→</span>
                    </a>
                @empty
                    <p class="text-sm text-slate-500">Belum ada kelas. <a href="{{ route('guru.kelas.index') }}" class="font-bold text-orange-500">Buat di halaman Kelas Saya →</a></p>
                @endforelse
            </div>
        </x-card>

        <x-card title="Siswa Paling Aktif" subtitle="berdasar XP">
            <div class="space-y-2">
                @forelse($siswaAktif as $i => $s)
                    <div class="flex items-center gap-3 rounded-xl border border-slate-200 px-4 py-2.5">
                        <span class="inline-flex items-center justify-center w-7 h-7 rounded-full text-xs font-extrabold {{ $i === 0 ? 'bg-orange-500 text-white' : 'bg-slate-100 text-slate-500' }}">{{ $i + 1 }}</span>
                        <p class="flex-1 font-bold text-sm text-slate-800 truncate">{{ $s->name }}</p>
                        <span class="text-sm font-extrabold text-emerald-700 whitespace-nowrap inline-flex items-center gap-1"><x-icon name="bolt" class="w-4 h-4" />{{ $s->xp ?? 0 }}</span>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">Belum ada siswa bergabung.</p>
                @endforelse
            </div>
        </x-card>
    </div>
</x-sidebar-layout>
