<x-sidebar-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="font-extrabold text-xl text-slate-900 dark:text-white">Dashboard Admin</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400">Kelola akun guru, pantau kelas & progres. Pendaftaran publik hanya untuk siswa.</p>
            </div>
            <span class="kka-badge">Admin • akses penuh</span>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-3">
            <x-card title="Admin" subtitle="akun"><p class="text-3xl font-extrabold text-slate-900 dark:text-white">{{ $stats['admin'] }}</p></x-card>
            <x-card title="Guru" subtitle="akun"><p class="text-3xl font-extrabold text-orange-500">{{ $stats['guru'] }}</p></x-card>
            <x-card title="Siswa" subtitle="akun"><p class="text-3xl font-extrabold text-emerald-500">{{ $stats['siswa'] }}</p></x-card>
            <x-card title="Kelas" subtitle="dibuat guru"><p class="text-3xl font-extrabold text-slate-900 dark:text-white">{{ $stats['kelas'] }}</p></x-card>
            <x-card title="Modul" subtitle="KKA"><p class="text-3xl font-extrabold text-slate-900 dark:text-white">{{ $stats['modul'] }}</p></x-card>
        </div>

        <x-card title="Pengajuan Akun Guru ({{ $pengajuan->count() }})" subtitle="Verifikasi foto NPK, lalu setujui atau tolak.">
            @forelse($pengajuan as $p)
                <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-3 rounded-xl border border-orange-200 dark:border-orange-500/30 bg-orange-50/60 dark:bg-orange-500/10 px-4 py-3 mb-2">
                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-sm text-slate-800 dark:text-slate-100">{{ $p->name }}</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">{{ $p->email }} • daftar {{ $p->created_at->format('d M Y H:i') }}</p>
                    </div>
                    @if($p->bukti_npk_path)
                        <a href="{{ route('admin.bukti.unduh', $p) }}" class="kka-btn-secondary !py-1.5 text-xs whitespace-nowrap inline-flex items-center gap-1"><x-icon name="doc" class="w-4 h-4" />Lihat NPK</a>
                    @else
                        <span class="text-xs text-slate-400">Tanpa bukti</span>
                    @endif
                    <form method="POST" action="{{ route('admin.pengajuan.setujui', $p) }}">
                        @csrf
                        <button class="kka-btn-primary !py-1.5 text-xs w-full sm:w-auto">Setujui</button>
                    </form>
                    <form method="POST" action="{{ route('admin.pengajuan.tolak', $p) }}" onsubmit="return confirm('Tolak pengajuan {{ $p->email }}?')">
                        @csrf
                        <button class="kka-btn-secondary !py-1.5 text-xs !text-red-600 w-full sm:w-auto">Tolak</button>
                    </form>
                </div>
            @empty
                <p class="text-sm text-slate-500 dark:text-slate-400">Tidak ada pengajuan menunggu. Semua guru sudah terverifikasi.</p>
            @endforelse
        </x-card>

        <x-card title="Buat Akun Guru / Admin" subtitle="Guru juga bisa daftar sendiri (wajib NPK, menunggu verifikasi). Akun di sini langsung aktif. Kata sandi minimal 8 karakter.">
            <form method="POST" action="{{ route('admin.users.store') }}" class="grid sm:grid-cols-2 lg:grid-cols-5 gap-2">
                @csrf
                <input name="name" required maxlength="100" placeholder="Nama guru" class="rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 focus:border-orange-500 focus:ring-orange-500" />
                <input name="email" type="email" required placeholder="Email guru" class="rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 focus:border-orange-500 focus:ring-orange-500" />
                <select name="role" required class="rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 focus:border-orange-500 focus:ring-orange-500">
                    <option value="guru">Guru</option>
                    <option value="admin">Admin</option>
                    <option value="siswa">Siswa (khusus)</option>
                </select>
                <x-password-input name="password" required placeholder="Kata sandi" class="rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 focus:border-orange-500 focus:ring-orange-500" />
                <x-password-input name="password_confirmation" required placeholder="Ulangi kata sandi" class="rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 focus:border-orange-500 focus:ring-orange-500" />
                <div class="sm:col-span-2 lg:col-span-5"><x-button type="submit">Buat Akun</x-button></div>
            </form>
        </x-card>

        <x-card title="Semua Pengguna" subtitle="Cari, reset kata sandi, atau hapus akun.">
            <form method="GET" action="{{ route('admin.dashboard') }}" class="flex flex-col sm:flex-row gap-2 mb-4">
                <input name="q" value="{{ request('q') }}" placeholder="Cari nama / email..." class="flex-1 rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 focus:border-orange-500 focus:ring-orange-500" />
                <select name="role" class="rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100">
                    <option value="">Semua peran</option>
                    @foreach(['admin', 'guru', 'siswa'] as $r)
                        <option value="{{ $r }}" @selected(request('role') === $r)>{{ ucfirst($r) }}</option>
                    @endforeach
                </select>
                <x-button type="submit">Cari</x-button>
            </form>
            <div class="overflow-x-auto">
                <table class="w-full text-sm min-w-[720px]">
                    <thead><tr class="text-left text-xs text-slate-400 border-b border-slate-200 dark:border-slate-700">
                        <th scope="col" class="py-2 pr-3">Nama</th><th scope="col" class="py-2 pr-3">Peran</th><th scope="col" class="py-2 pr-3">Info</th><th scope="col" class="py-2 text-right">Aksi</th>
                    </tr></thead>
                    <tbody>
                        @foreach($users as $u)
                            <tr class="border-b border-slate-100 dark:border-slate-800">
                                <td class="py-3 pr-3"><p class="font-bold text-slate-800 dark:text-slate-100">{{ $u->name }}</p><p class="text-xs text-slate-400">{{ $u->email }}</p></td>
                                <td class="py-3 pr-3"><span class="kka-badge">{{ ucfirst($u->role) }}</span>
                                    @if($u->status === 'pending')
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-bold bg-orange-100 text-orange-700 dark:bg-orange-500/15 dark:text-orange-400 border border-orange-200 dark:border-orange-500/30"><x-icon name="clock" class="w-3 h-3" />Pending</span>
                                    @elseif($u->status === 'ditolak')
                                        <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-700 border border-red-200">Ditolak</span>
                                    @endif
                                </td>
                                <td class="py-3 pr-3 text-xs text-slate-500 dark:text-slate-400">
                                    @if($u->isGuru()) {{ $u->taughtClassrooms->count() }} kelas • {{ $u->taughtClassrooms->sum('students_count') }} siswa
                                    @elseif($u->isSiswa()) {{ $u->totalXp() }} XP • {{ $u->lulus_count }}/{{ $modules->count() }} lulus
                                    @else — @endif
                                </td>
                                <td class="py-3">
                                    <div class="flex justify-end gap-2">
                                        <details class="relative">
                                            <summary class="kka-btn-secondary !py-1.5 !px-3 text-xs cursor-pointer list-none">Reset PW</summary>
                                            <form method="POST" action="{{ route('admin.users.reset', $u) }}" class="absolute right-0 mt-2 p-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl shadow-lg flex gap-1 z-10">
                                                @csrf @method('PATCH')
                                                <x-password-input name="password" required placeholder="Baru" class="w-28 rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 text-xs" />
                                                <x-password-input name="password_confirmation" required placeholder="Ulangi" class="w-28 rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 text-xs" />
                                                <button class="kka-btn-primary !py-1.5 !px-3 text-xs">OK</button>
                                            </form>
                                        </details>
                                        @if($u->id !== auth()->id())
                                            <form method="POST" action="{{ route('admin.users.destroy', $u) }}" onsubmit="return confirm('Hapus {{ $u->email }}?')">
                                                @csrf @method('DELETE')
                                                <button class="kka-btn-secondary !py-1.5 !px-3 text-xs !text-red-600">Hapus</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $users->links() }}</div>
        </x-card>

        <x-card title="Progres Belajar Siswa" subtitle="Data tiap siswa: kelas, XP, badge, dan status per modul.">
            <div x-data="{ cari: '' }">
                <label for="cari-progres" class="sr-only">Saring progres siswa</label>
                <input id="cari-progres" x-model="cari" type="search" placeholder="Saring nama / email / kelas pada tabel ini..." class="w-full sm:max-w-sm rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 text-sm focus:border-orange-500 focus:ring-orange-500 mb-3" />
            <div class="overflow-x-auto">
                <table class="w-full text-sm min-w-[860px]">
                    <thead><tr class="text-left text-xs text-slate-400 border-b border-slate-200 dark:border-slate-700">
                        <th scope="col" class="py-2 pr-3">Siswa</th>
                        <th scope="col" class="py-2 pr-3">Kelas</th>
                        <th scope="col" class="py-2 pr-3">XP</th>
                        <th scope="col" class="py-2 pr-3">Badge</th>
                        @foreach($modules as $m)
                            <th scope="col" class="py-2 pr-3">{{ $m->title }}</th>
                        @endforeach
                    </tr></thead>
                    <tbody>
                        @forelse($siswaList as $s)
                            @php $by = $s->progress->keyBy('module_id'); @endphp
                            <tr class="border-b border-slate-100 dark:border-slate-800" x-show="$el.dataset.cari.toLowerCase().includes(cari.toLowerCase())" data-cari="{{ strtolower($s->name . ' ' . $s->email . ' ' . $s->classrooms->pluck('name')->join(' ')) }}">
                                <td class="py-3 pr-3"><p class="font-bold text-slate-800 dark:text-slate-100">{{ $s->name }}</p><p class="text-xs text-slate-400">{{ $s->email }}</p></td>
                                <td class="py-3 pr-3 text-xs text-slate-500 dark:text-slate-400">{{ $s->classrooms->pluck('name')->join(', ') ?: '—' }}</td>
                                <td class="py-3 pr-3 font-extrabold text-emerald-600 dark:text-emerald-400">{{ $s->progress->sum('xp_earned') }}</td>
                                <td class="py-3 pr-3 font-bold text-slate-700 dark:text-slate-200">{{ $s->badges->count() }}</td>
                                @foreach($modules as $m)
                                    @php $p = $by->get($m->id); @endphp
                                    <td class="py-3 pr-3 text-xs">
                                        @if($p && $p->is_completed)
                                            <span class="kka-badge">✓ {{ $p->quiz_score }}</span>
                                        @elseif($p && ($p->reading_done || $p->quiz_score > 0 || $p->hands_on_count > 0))
                                            <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-bold bg-orange-50 dark:bg-orange-500/10 text-orange-700 dark:text-orange-400 border border-orange-200 dark:border-orange-500/30 whitespace-nowrap">{{ $p->quiz_score }}</span>
                                        @else
                                            <span class="text-slate-300">—</span>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @empty
                            <tr><td colspan="{{ $modules->count() + 4 }}" class="py-6 text-center text-slate-400 text-sm">Belum ada siswa.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $siswaList->links() }}</div>
            </div>
        </x-card>

        <div class="grid lg:grid-cols-2 gap-4">
            <x-card title="Kelas Terbaru" subtitle="Dibuat oleh guru.">
                @forelse($classrooms as $c)
                    <div class="flex items-center justify-between text-sm border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2 mb-2">
                        <div><p class="font-bold">{{ $c->name }}</p><p class="text-xs text-slate-400">{{ $c->teacher->name ?? '-' }} • {{ $c->code }} • {{ $c->students_count }} siswa</p></div>
                    </div>
                @empty
                    <p class="text-sm text-slate-500 dark:text-slate-400">Belum ada kelas.</p>
                @endforelse
            </x-card>
            <x-card title="Modul KKA" subtitle="Kelola soal lewat akun guru / bank soal.">
                @foreach($modules as $m)
                    <div class="flex items-center justify-between text-sm border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2 mb-2">
                        <div><p class="font-bold">{{ $m->title }}</p><p class="text-xs text-slate-400">{{ $m->quizzes_count }} soal</p></div><span class="kka-badge">+{{ $m->xp_reward }} XP</span>
                    </div>
                @endforeach
                <a href="{{ route('guru.quiz.index') }}" class="kka-btn-secondary !py-2 text-xs">Buka Bank Soal</a>
            </x-card>
        </div>
    </div>
</x-sidebar-layout>
