<x-sidebar-layout>
@php
    $kelasNames = $user->classrooms->pluck('name')->implode(', ');
    $peran = match ($user->role) {
        'siswa' => 'Siswa',
        'guru' => 'Guru',
        'admin' => 'Admin',
        default => ucfirst((string) $user->role),
    };
    $totalXp = $user->totalXp();
    $modulList = \App\Models\Module::orderBy('order')->get();
    $progresMap = $user->progress()->get()->keyBy('module_id');
    $selesai = $progresMap->where('is_completed', true)->count();
    $totalModul = $modulList->count();
    $akurasi = $progresMap->whereNotNull('quiz_score')->avg('quiz_score');
    $pctSelesai = $totalModul > 0 ? round($selesai / $totalModul * 100) : 0;
    $level = intdiv($totalXp, 500) + 1;
    $levelPct = round(($totalXp % 500) / 500 * 100);
    $levelName = match (true) {
        $level <= 1 => 'AI Rookie',
        $level == 2 => 'AI Explorer',
        $level == 3 => 'AI Builder',
        $level == 4 => 'AI Creator',
        default => 'AI Master',
    };
@endphp

    <div class="max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-6 sm:py-8 space-y-6">
        <!-- Breadcrumb + narrative header -->
        <div>
            <p class="flex items-center gap-1.5 text-xs text-slate-500 dark:text-slate-400">
                <a href="{{ route('dashboard') }}" class="hover:text-slate-900 dark:hover:text-white transition-colors">Beranda</a>
                <span aria-hidden="true">›</span>
                <span class="text-orange-500 font-semibold">Profil</span>
            </p>
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
        <div class="relative overflow-hidden rounded-2xl border border-slate-200 dark:border-white/5 bg-white dark:bg-[#111c2d] shadow-sm">
            <div class="absolute -right-24 -top-24 w-96 h-96 rounded-full bg-orange-500/10 blur-3xl pointer-events-none"></div>
            <div class="absolute -left-20 -bottom-20 w-80 h-80 rounded-full bg-emerald-500/10 blur-3xl pointer-events-none"></div>
            <div class="relative p-5 sm:p-7 flex flex-col lg:flex-row gap-6 items-start lg:items-center justify-between">
                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-5">
                    <div class="relative">
                        @if($user->avatar_path)
                            <img src="{{ $user->avatarUrl() }}" alt="Foto profil {{ $user->name }}" class="w-24 h-24 sm:w-28 sm:h-28 rounded-2xl object-cover border border-slate-200 dark:border-white/10 shadow-lg" />
                        @else
                            <span class="flex items-center justify-center w-24 h-24 sm:w-28 sm:h-28 rounded-2xl bg-orange-100 dark:bg-orange-500/15 text-orange-600 dark:text-orange-400 font-extrabold text-4xl shadow-lg select-none">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                        @endif
                        <label for="avatar" title="Ganti Foto Profil" class="absolute -bottom-2 -right-2 w-9 h-9 rounded-xl bg-orange-500 hover:bg-orange-600 text-white flex items-center justify-center shadow-lg transition active:scale-95 cursor-pointer">
                            <x-icon name="camera" class="w-4 h-4" />
                        </label>
                    </div>
                    <div class="space-y-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <h2 class="text-xl font-bold text-slate-900 dark:text-white">{{ $user->name }}</h2>
                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-500/30">{{ $peran }}{{ $kelasNames ? ' • '.$kelasNames : '' }}</span>
                        </div>
                        <p class="text-sm text-slate-500 dark:text-slate-400">
                            {{ $user->email }} <span class="mx-1 opacity-40">•</span> Bergabung {{ $user->created_at->format('M Y') }}
                        </p>
                        <div class="pt-2 flex flex-wrap items-center gap-3">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-orange-100 dark:bg-orange-500/15 text-orange-700 dark:text-orange-300 text-xs font-bold">
                                <x-icon name="bolt" class="w-4 h-4" /> Lvl {{ $level }}: {{ $levelName }}
                            </span>
                            <div class="flex items-center gap-2">
                                <div class="w-32 h-2 rounded-full overflow-hidden bg-slate-100 dark:bg-white/10">
                                    <div class="h-full bg-orange-500 rounded-full" style="width: {{ $levelPct }}%"></div>
                                </div>
                                <span class="text-xs text-slate-500 dark:text-slate-400">{{ $levelPct }}% ke Lvl {{ $level + 1 }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="w-full lg:w-auto grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-2 gap-2">
                    <div class="rounded-xl bg-slate-50 dark:bg-[#152031] p-3 flex items-center gap-2">
                        <span class="w-9 h-9 rounded-lg bg-white dark:bg-white/5 text-emerald-500 flex items-center justify-center shrink-0"><x-icon name="check" class="w-5 h-5" /></span>
                        <span><span class="block font-bold text-slate-900 dark:text-white leading-none">{{ $selesai }}</span><span class="block text-xs text-slate-500 dark:text-slate-400 mt-1">Modul Selesai</span></span>
                    </div>
                    <div class="rounded-xl bg-slate-50 dark:bg-[#152031] p-3 flex items-center gap-2">
                        <span class="w-9 h-9 rounded-lg bg-white dark:bg-white/5 text-orange-500 flex items-center justify-center shrink-0"><x-icon name="chart" class="w-5 h-5" /></span>
                        <span><span class="block font-bold text-slate-900 dark:text-white leading-none">{{ $akurasi !== null ? round($akurasi).'%' : '—' }}</span><span class="block text-xs text-slate-500 dark:text-slate-400 mt-1">Akurasi Kuis</span></span>
                    </div>
                    <div class="rounded-xl bg-slate-50 dark:bg-[#152031] p-3 flex items-center gap-2">
                        <span class="w-9 h-9 rounded-lg bg-white dark:bg-white/5 text-emerald-500 flex items-center justify-center shrink-0"><x-icon name="bolt" class="w-5 h-5" /></span>
                        <span><span class="block font-bold text-slate-900 dark:text-white leading-none">{{ number_format($totalXp) }}</span><span class="block text-xs text-slate-500 dark:text-slate-400 mt-1">XP Diperoleh</span></span>
                    </div>
                    <div class="rounded-xl bg-slate-50 dark:bg-[#152031] p-3 flex items-center gap-2">
                        <span class="w-9 h-9 rounded-lg bg-white dark:bg-white/5 text-orange-500 flex items-center justify-center shrink-0"><x-icon name="spark" class="w-5 h-5" /></span>
                        <span><span class="block font-bold text-slate-900 dark:text-white leading-none">{{ $user->badges->count() }}</span><span class="block text-xs text-slate-500 dark:text-slate-400 mt-1">Lencana</span></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Two columns -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            <div class="lg:col-span-7 space-y-6">
                <div class="rounded-2xl border border-slate-200 dark:border-white/5 bg-white dark:bg-[#111c2d] shadow-sm p-5 sm:p-7">
                    <div class="flex items-center justify-between mb-5">
                        <div class="flex items-center gap-2.5">
                            <span class="w-8 h-8 rounded-lg bg-slate-100 dark:bg-[#152031] text-orange-500 flex items-center justify-center"><x-icon name="pencil" class="w-4 h-4" /></span>
                            <h3 class="font-bold text-slate-900 dark:text-white">Informasi Profil</h3>
                        </div>
                        <span class="text-xs text-slate-500 dark:text-slate-400">Data Akun {{ $peran }}</span>
                    </div>
                    @include('profile.partials.update-profile-information-form')
                </div>

                <div class="rounded-2xl border border-slate-200 dark:border-white/5 bg-white dark:bg-[#111c2d] shadow-sm p-5 sm:p-7">
                    <div class="flex items-center gap-2.5 mb-5">
                        <span class="w-8 h-8 rounded-lg bg-slate-100 dark:bg-[#152031] text-orange-500 flex items-center justify-center"><x-icon name="lock" class="w-4 h-4" /></span>
                        <h3 class="font-bold text-slate-900 dark:text-white">Keamanan &amp; Ubah Kata Sandi</h3>
                    </div>
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="lg:col-span-5 space-y-6">
                <div class="rounded-2xl border border-slate-200 dark:border-white/5 bg-white dark:bg-[#111c2d] shadow-sm p-5 sm:p-7">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-2.5">
                            <span class="w-8 h-8 rounded-lg bg-slate-100 dark:bg-[#152031] text-orange-500 flex items-center justify-center"><x-icon name="spark" class="w-4 h-4" /></span>
                            <h3 class="font-bold text-slate-900 dark:text-white">Lencana Kehormatan</h3>
                        </div>
                        <span class="text-xs font-bold text-slate-500 dark:text-slate-400">{{ $user->badges->count() }} raihan</span>
                    </div>
                    @if($user->badges->isEmpty())
                        <p class="text-sm text-slate-500 dark:text-slate-400">Belum ada lencana. Selesaikan modul dan kuis untuk meraih lencana pertamamu!</p>
                    @else
                        <div class="grid grid-cols-3 gap-2 text-center">
                            @foreach($user->badges as $badge)
                                <div class="rounded-xl bg-slate-50 dark:bg-[#152031] p-3 flex flex-col items-center gap-1">
                                    <span class="w-12 h-12 rounded-2xl bg-orange-100 dark:bg-orange-500/15 text-orange-500 flex items-center justify-center"><x-icon name="cap" class="w-6 h-6" /></span>
                                    <span class="text-xs font-bold text-slate-900 dark:text-white truncate w-full">{{ $badge->name }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="rounded-2xl border border-slate-200 dark:border-white/5 bg-white dark:bg-[#111c2d] shadow-sm p-5 sm:p-7">
                    <div class="flex items-center gap-2.5 mb-4">
                        <span class="w-8 h-8 rounded-lg bg-slate-100 dark:bg-[#152031] text-orange-500 flex items-center justify-center"><x-icon name="book" class="w-4 h-4" /></span>
                        <h3 class="font-bold text-slate-900 dark:text-white">Progres Modul</h3>
                    </div>
                    <div class="space-y-2">
                        @foreach($modulList as $modul)
                            @php $p = $progresMap->get($modul->id); @endphp
                            <div class="flex items-center gap-3 rounded-xl bg-slate-50 dark:bg-[#152031] px-3 py-2.5">
                                <span class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 {{ $p && $p->is_completed ? 'text-emerald-500' : 'text-slate-400' }}">
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

                <div class="relative overflow-hidden rounded-2xl border border-slate-200 dark:border-white/5 bg-white dark:bg-[#111c2d] shadow-sm p-5 sm:p-7">
                    <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-red-500"></div>
                    <div class="flex items-center gap-2.5 mb-2 text-red-600 dark:text-red-500">
                        <x-icon name="alert" class="w-5 h-5" />
                        <h3 class="font-bold">Zona Berbahaya</h3>
                    </div>
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-sidebar-layout>
