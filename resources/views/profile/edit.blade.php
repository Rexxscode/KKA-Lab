<x-sidebar-layout>
    <div class="max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-6 sm:py-8 space-y-6">
        <!-- Breadcrumb + narrative header -->
        <div>
            <nav aria-label="Breadcrumb">
                <p class="flex items-center gap-1.5 text-xs text-slate-500 dark:text-slate-400">
                    <a href="{{ route('dashboard') }}" class="hover:text-slate-900 dark:hover:text-white transition-colors">Beranda</a>
                    <span aria-hidden="true">›</span>
                    <span class="text-orange-500 font-semibold" aria-current="page">Profil</span>
                </p>
            </nav>
            <div class="mt-2">
                <p class="flex items-center gap-1.5 text-orange-500 text-[11px] font-bold uppercase tracking-wider">
                    <x-icon name="shield" class="w-4 h-4" /> Pusat Akun {{ $peran }}
                </p>
                <h1 class="text-2xl sm:text-4xl font-extrabold tracking-tight text-slate-900 dark:text-white">Profil &amp; Pengaturan Akun</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400 max-w-2xl mt-1">
                    Kelola identitas, keamanan, dan pantau capaian belajarmu di KKA Lab.
                </p>
            </div>
        </div>

        <!-- Hero card -->
        <div class="kka-card relative overflow-hidden !p-0">
            <div class="absolute -right-24 -top-24 w-96 h-96 rounded-full bg-orange-500/10 blur-3xl pointer-events-none" aria-hidden="true"></div>
            <div class="absolute -left-20 -bottom-20 w-80 h-80 rounded-full bg-emerald-500/10 blur-3xl pointer-events-none" aria-hidden="true"></div>
            <div class="relative p-5 sm:p-7 flex flex-col lg:flex-row gap-6 items-start lg:items-center justify-between">
                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-5">
                    <div class="relative">
                        @if($user->avatar_path)
                            <img src="{{ $user->avatarUrl() }}" alt="Foto profil {{ $user->name }}" class="w-24 h-24 sm:w-28 sm:h-28 rounded-2xl object-cover border border-slate-200 dark:border-slate-700 shadow-lg" />
                        @else
                            <span class="flex items-center justify-center w-24 h-24 sm:w-28 sm:h-28 rounded-2xl bg-orange-100 dark:bg-orange-500/15 text-orange-600 dark:text-orange-400 font-extrabold text-4xl shadow-lg select-none" aria-hidden="true">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                        @endif
                        <a href="#informasi" title="Ganti foto profil di form Informasi" aria-label="Ganti foto profil — menuju form Informasi" class="absolute -bottom-2 -right-2 w-9 h-9 rounded-xl bg-orange-500 hover:bg-orange-600 text-white flex items-center justify-center shadow-lg transition active:scale-95">
                            <x-icon name="camera" class="w-4 h-4" />
                        </a>
                    </div>
                    <div class="space-y-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <h2 class="text-xl font-bold text-slate-900 dark:text-white">{{ $user->name }}</h2>
                            <span class="kka-badge">{{ $peran }}@if($kelasNames) • {{ $kelasNames }}@if($kelasLebih) +{{ $kelasLebih }}@endif @endif</span>
                        </div>
                        <p class="text-sm text-slate-500 dark:text-slate-400">
                            {{ $user->email }} <span class="mx-1 opacity-40" aria-hidden="true">•</span> Bergabung {{ $user->created_at->format('M Y') }}
                        </p>
                        @if($isSiswa)
                            <div class="pt-2 flex flex-wrap items-center gap-3">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-orange-100 dark:bg-orange-500/15 text-orange-700 dark:text-orange-300 text-xs font-bold">
                                    <x-icon name="bolt" class="w-4 h-4" /> Lvl {{ $level }}: {{ $levelName }}
                                </span>
                                <div class="flex items-center gap-2">
                                    <div class="w-32 h-2 rounded-full overflow-hidden bg-slate-100 dark:bg-slate-700 border border-slate-200 dark:border-slate-700" role="progressbar" aria-label="Progres menuju level berikutnya" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $levelPct }}">
                                        <div class="h-full bg-orange-500 rounded-full" style="width: {{ $levelPct }}%"></div>
                                    </div>
                                    <span class="text-xs text-slate-500 dark:text-slate-400">{{ $levelPct }}% ke Lvl {{ $level + 1 }}</span>
                                </div>
                            </div>
                        @elseif($user->isGuru())
                            <p class="pt-2 text-xs text-slate-500 dark:text-slate-400">Mengajar <b class="text-slate-800 dark:text-slate-100">{{ $kelasDiajar }} kelas</b>@if($kelasNames) • {{ $kelasNames }}@endif</p>
                        @endif
                    </div>
                </div>
                @if($isSiswa)
                    <div class="w-full lg:w-auto grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-2 gap-2">
                        <div class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 p-3 flex items-center gap-2">
                            <span class="w-9 h-9 rounded-lg bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0"><x-icon name="check" class="w-5 h-5" /></span>
                            <span><span class="block font-bold text-slate-900 dark:text-white leading-none">{{ $selesai }}/{{ $totalModul }}</span><span class="block text-xs text-slate-500 dark:text-slate-400 mt-1">Modul Selesai</span></span>
                        </div>
                        <div class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 p-3 flex items-center gap-2">
                            <span class="w-9 h-9 rounded-lg bg-orange-50 dark:bg-orange-500/10 text-orange-500 flex items-center justify-center shrink-0"><x-icon name="chart" class="w-5 h-5" /></span>
                            <span><span class="block font-bold text-slate-900 dark:text-white leading-none">{{ $akurasi !== null ? round($akurasi).'%' : '—' }}</span><span class="block text-xs text-slate-500 dark:text-slate-400 mt-1">Akurasi Kuis</span></span>
                        </div>
                        <div class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 p-3 flex items-center gap-2">
                            <span class="w-9 h-9 rounded-lg bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0"><x-icon name="bolt" class="w-5 h-5" /></span>
                            <span><span class="block font-bold text-slate-900 dark:text-white leading-none">{{ number_format($totalXp) }}</span><span class="block text-xs text-slate-500 dark:text-slate-400 mt-1">Total XP</span></span>
                        </div>
                        <div class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 p-3 flex items-center gap-2">
                            <span class="w-9 h-9 rounded-lg bg-orange-50 dark:bg-orange-500/10 text-orange-500 flex items-center justify-center shrink-0"><x-icon name="spark" class="w-5 h-5" /></span>
                            <span><span class="block font-bold text-slate-900 dark:text-white leading-none">{{ $user->badges->count() }}</span><span class="block text-xs text-slate-500 dark:text-slate-400 mt-1">Lencana</span></span>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Two columns -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            <div class="lg:col-span-7 space-y-6">
                <div class="kka-card p-5 sm:p-7" id="informasi">
                    <div class="flex items-center gap-2.5 mb-5">
                        <span class="w-8 h-8 rounded-lg bg-orange-50 dark:bg-orange-500/10 text-orange-500 flex items-center justify-center"><x-icon name="pencil" class="w-4 h-4" /></span>
                        <div>
                            <h3 class="font-bold text-slate-900 dark:text-white">Informasi Profil</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Data akun {{ strtolower($peran) }}</p>
                        </div>
                    </div>
                    @include('profile.partials.update-profile-information-form')
                </div>

                <div class="kka-card p-5 sm:p-7" id="keamanan">
                    <div class="flex items-center gap-2.5 mb-5">
                        <span class="w-8 h-8 rounded-lg bg-orange-50 dark:bg-orange-500/10 text-orange-500 flex items-center justify-center"><x-icon name="lock" class="w-4 h-4" /></span>
                        <div>
                            <h3 class="font-bold text-slate-900 dark:text-white">Keamanan &amp; Ubah Kata Sandi</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Minimal 8 karakter, wajib huruf + angka</p>
                        </div>
                    </div>
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="lg:col-span-5 space-y-6">
                @if($isSiswa)
                    <div class="kka-card p-5 sm:p-7">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-2.5">
                                <span class="w-8 h-8 rounded-lg bg-orange-50 dark:bg-orange-500/10 text-orange-500 flex items-center justify-center"><x-icon name="spark" class="w-4 h-4" /></span>
                                <h3 class="font-bold text-slate-900 dark:text-white">Lencana Kehormatan</h3>
                            </div>
                            <span class="text-xs font-bold text-slate-500 dark:text-slate-400">{{ $user->badges->count() }} raihan</span>
                        </div>
                        @if($user->badges->isEmpty())
                            <p class="text-sm text-slate-500 dark:text-slate-400">Belum ada lencana. Selesaikan modul dan kuis untuk meraih lencana pertamamu!</p>
                        @else
                            <div class="grid grid-cols-3 gap-2 text-center">
                                @foreach($user->badges as $badge)
                                    <div class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 p-3 flex flex-col items-center gap-1">
                                        <span class="w-12 h-12 rounded-2xl bg-orange-100 dark:bg-orange-500/15 text-orange-500 flex items-center justify-center"><x-icon name="cap" class="w-6 h-6" /></span>
                                        <span class="text-xs font-bold text-slate-900 dark:text-white truncate w-full">{{ $badge->name }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div class="kka-card p-5 sm:p-7">
                        <div class="flex items-center gap-2.5 mb-4">
                            <span class="w-8 h-8 rounded-lg bg-orange-50 dark:bg-orange-500/10 text-orange-500 flex items-center justify-center"><x-icon name="book" class="w-4 h-4" /></span>
                            <h3 class="font-bold text-slate-900 dark:text-white">Progres Modul</h3>
                        </div>
                        <div class="space-y-2">
                            @foreach($modulList as $modul)
                                @php $p = $progresMap->get($modul->id); @endphp
                                <div class="flex items-center gap-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2.5">
                                    <span class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 {{ $p && $p->is_completed ? 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400' : 'bg-slate-100 dark:bg-slate-700 text-slate-400' }}">
                                        <x-icon name="{{ $p && $p->is_completed ? 'check' : 'book' }}" class="w-4 h-4" />
                                    </span>
                                    <span class="min-w-0 flex-1">
                                        <span class="block text-sm font-bold text-slate-900 dark:text-white truncate">{{ $modul->title }}</span>
                                        <span class="block text-xs text-slate-500 dark:text-slate-400">{{ $p && $p->is_completed ? 'Selesai' : 'Belum selesai' }}{{ $p && $p->quiz_score !== null ? ' • Kuis '.$p->quiz_score : '' }}</span>
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="kka-card p-5 sm:p-7">
                        <div class="flex items-center gap-2.5 mb-2">
                            <span class="w-8 h-8 rounded-lg bg-orange-50 dark:bg-orange-500/10 text-orange-500 flex items-center justify-center"><x-icon name="shield" class="w-4 h-4" /></span>
                            <h3 class="font-bold text-slate-900 dark:text-white">Ringkasan Peran</h3>
                        </div>
                        @if($user->isGuru())
                            <p class="text-sm text-slate-500 dark:text-slate-400">Kamu mengajar <b class="text-slate-800 dark:text-slate-100">{{ $kelasDiajar }} kelas</b>. Kelola kelas dan modulmu dari dashboard guru.</p>
                            <a href="{{ route('guru.kelas.index') }}" class="kka-btn-secondary !py-2 text-sm mt-4">Kelola Kelas Saya</a>
                        @else
                            <p class="text-sm text-slate-500 dark:text-slate-400">Akun admin untuk pengelolaan sistem. Bagian progres belajar hanya tampil untuk siswa.</p>
                            <a href="{{ route('admin.dashboard') }}" class="kka-btn-secondary !py-2 text-sm mt-4">Ke Dashboard Admin</a>
                        @endif
                    </div>
                @endif

                <div class="kka-card p-5 sm:p-7 !border-red-200 dark:!border-red-500/30 border-l-4" id="zona-bahaya">
                    <div class="flex items-center gap-2.5 mb-2 text-red-600 dark:text-red-400">
                        <x-icon name="alert" class="w-5 h-5" />
                        <h3 class="font-bold">Zona Berbahaya</h3>
                    </div>
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-sidebar-layout>
