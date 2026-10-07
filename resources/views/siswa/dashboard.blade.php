<x-sidebar-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="font-extrabold text-xl text-slate-900 dark:text-white">Halo {{ auth()->user()->name }}!</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400">
                    @if($kelasAktif)
                        Kelas aktif: <b class="text-orange-600 dark:text-orange-400">{{ $kelasAktif->name }}</b> — kerjakan modul berurutan ya!
                    @else
                        Lanjutkan misimu belajar AI hari ini. Gabung kelas untuk mulai!
                    @endif
                </p>
            </div>
            <a href="{{ route('siswa.sertifikat') }}" class="kka-btn-primary !py-2 text-sm">Sertifikat</a>
        </div>
    </x-slot>

    @php
        $selesai = $selesai ?? collect($daftar)->where(fn ($d) => $d['progress'] && $d['progress']->is_completed)->count();
        $totalModul = $totalModul ?? count($daftar);
        $persen = $persen ?? ($totalModul ? round($selesai / $totalModul * 100) : 0);
    @endphp
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <x-card title="Total XP" subtitle="dari kuis & praktik"><p class="kka-stat text-orange-600 dark:text-orange-400 flex items-center gap-2"><x-icon name="bolt" class="w-7 h-7" />{{ $totalXp }}</p></x-card>
        <x-card title="Badge" subtitle="dikumpulkan"><p class="kka-stat text-emerald-600 dark:text-emerald-400 flex items-center gap-2"><x-icon name="cap" class="w-7 h-7" />{{ $badges->count() }}</p></x-card>
        <x-card title="Modul Selesai" subtitle="skor ≥80"><p class="text-3xl font-extrabold text-slate-900 dark:text-white">{{ $selesai }}/{{ $totalModul }}</p></x-card>
        <div class="kka-card p-6 flex flex-col h-full">
            <p class="text-sm text-slate-500 dark:text-slate-400">Progres Belajar</p>
            <div class="mt-auto pt-2">
                <p class="text-3xl font-extrabold text-slate-900 dark:text-white">{{ $persen }}%</p>
                <x-progress-bar :value="$selesai" :max="$totalModul ?: 1" class="mt-2" />
            </div>
        </div>
    </div>

    @if(isset($lanjut) && $lanjut)
        @php $lm = $lanjut['module']; $lp = $lanjut['progress']; @endphp
        <div class="mt-4 rounded-2xl border border-orange-200 dark:border-orange-500/30 bg-orange-50 dark:bg-orange-500/10 px-4 py-3 flex flex-wrap items-center gap-2 text-sm" role="status">
            <span class="font-extrabold text-orange-800 dark:text-orange-300 truncate">
                Lanjut: {{ $lanjut['posisi'] }}. {{ $lm->title }}@if($lp && $lp->quiz_score > 0) (skor {{ $lp->quiz_score }})@endif
            </span>
            <a href="{{ route('siswa.bahan.show', $lm->slug) }}" class="kka-btn-primary !py-1.5 text-xs text-center ml-auto shrink-0">Lanjut →</a>
        </div>
    @endif

    @if(isset($terakhir) && $terakhir && $terakhir->module)
        <div class="mt-4 rounded-2xl border {{ $terakhir->is_completed ? 'border-emerald-200 dark:border-emerald-500/30 bg-emerald-50 dark:bg-emerald-500/10' : 'border-orange-200 dark:border-orange-500/30 bg-orange-50 dark:bg-orange-500/10' }} px-4 py-3 flex flex-wrap items-center gap-2 text-sm" role="status">
            <span class="font-extrabold {{ $terakhir->is_completed ? 'text-emerald-800 dark:text-emerald-300' : 'text-orange-800 dark:text-orange-300' }}">
                Terakhir: {{ $terakhir->module->title }} — skor {{ $terakhir->quiz_score }}{{ $terakhir->is_completed ? ' (lulus)' : ' (belum lulus)' }} • +{{ $terakhir->xp_earned }} XP
            </span>
            <span class="flex gap-2 ml-auto">
                <a href="{{ route('siswa.raporku') }}" class="font-bold text-orange-600 dark:text-orange-400 hover:text-orange-700 text-xs">Lihat Raporku →</a>
                @if(! $terakhir->is_completed)
                    <a href="{{ route('siswa.bahan.show', $terakhir->module->slug) }}" class="font-bold text-slate-600 dark:text-slate-300 hover:text-slate-800 text-xs">Pelajari lagi →</a>
                @endif
            </span>
        </div>
    @endif

    <div class="grid lg:grid-cols-3 gap-3 sm:gap-4 mt-4">
        <x-card title="Kuis yang Lulus" subtitle="riwayat nilaimu">
            <div class="space-y-2">
                @forelse(collect($daftar)->filter(fn ($d) => $d['progress'] && $d['progress']->is_completed) as $d)
                    <div class="flex items-center justify-between rounded-xl border border-emerald-200 dark:border-emerald-500/30 bg-emerald-50/60 dark:bg-emerald-500/10 px-3 py-2 text-sm">
                        <span class="font-semibold text-slate-700 dark:text-slate-200">{{ $d['module']->title }}</span>
                        <span class="font-extrabold text-emerald-700 dark:text-emerald-400">{{ $d['progress']->quiz_score }}</span>
                    </div>
                @empty
                    <p class="text-sm text-slate-500 dark:text-slate-400">Belum ada. Luluskan kuis dengan skor ≥80!</p>
                @endforelse
            </div>
        </x-card>

        <x-card title="Gabung Kelas" subtitle="6 digit kode dari gurumu">
            <form method="POST" action="{{ route('siswa.join') }}" class="flex gap-2">
                @csrf
                <input name="code" id="kode-kelas" value="{{ old('code') }}" minlength="6" maxlength="6" required inputmode="numeric" pattern="[0-9]{6}" autocomplete="one-time-code" placeholder="cth: 482913" aria-label="Kode kelas 6 digit"
                       class="flex-1 min-w-0 rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 focus:border-orange-500 focus:ring-orange-500 tracking-[0.35em] text-center font-bold text-lg py-2" />
                <x-button type="submit">Gabung</x-button>
            </form>
            <x-input-error :messages="$errors->get('code')" class="mt-2" />
            @if($classrooms->count())
                <div class="mt-3 flex flex-wrap gap-2">
                    @foreach($classrooms as $c)
                        <span class="kka-badge {{ $kelasAktif && $c->id === $kelasAktif->id ? '!bg-orange-100 dark:!bg-orange-500/15 !text-orange-700 dark:!text-orange-300 !border-orange-300 dark:!border-orange-500/40' : '' }}">{{ $c->name }} • {{ $c->code }}</span>
                    @endforeach
                </div>
                <p class="kka-note">Kelas aktif (oranye) menentukan urutan modul & soalmu.</p>
                @if($kelasAktif)
                    <form method="POST" action="{{ route('siswa.leave') }}" onsubmit="return confirm('Keluar dari kelas {{ $kelasAktif->name }}? Progres belajarmu tetap tersimpan.')" class="mt-2">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-xs font-bold text-red-600 hover:text-red-700">Keluar dari {{ $kelasAktif->name }}</button>
                    </form>
                @endif
            @endif
        </x-card>

        <x-card title="Badge Koleksi" subtitle="{{ $badges->count() }} badge didapat">
            <div class="flex flex-wrap gap-2">
                @forelse($badges as $b)
                    <span class="kka-badge"><x-icon name="cap" class="w-3.5 h-3.5" />{{ $b->name }}</span>
                @empty
                    <p class="text-sm text-slate-500 dark:text-slate-400">Lulus kuis ≥80 untuk dapat badge!</p>
                @endforelse
            </div>
        </x-card>

        <x-card title="Riwayat Kuis" subtitle="5 percobaan terakhir">
            <div class="space-y-2">
                @forelse($riwayat as $r)
                    <div class="flex items-center justify-between text-sm border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2">
                        <span class="font-semibold text-slate-700 dark:text-slate-200 truncate">{{ $r->module->title ?? '—' }}</span>
                        <span class="font-extrabold {{ $r->passed ? 'text-emerald-700 dark:text-emerald-400' : 'text-orange-600 dark:text-orange-400' }}">{{ $r->score }}</span>
                    </div>
                @empty
                    <p class="text-sm text-slate-500 dark:text-slate-400">Belum ada percobaan. Kerjakan kuismu!</p>
                @endforelse
            </div>
        </x-card>
    </div>

    <div class="mt-4 rounded-2xl border {{ ($ujianLulus ?? false) ? 'border-emerald-200 dark:border-emerald-500/30 bg-emerald-50 dark:bg-emerald-500/10' : 'border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800' }} px-4 py-3 flex flex-wrap items-center gap-2 text-sm" role="status">
        <span class="font-extrabold {{ ($ujianLulus ?? false) ? 'text-emerald-800 dark:text-emerald-300' : 'text-slate-800 dark:text-slate-100' }} truncate">
            @if(($ujianLulus ?? false))
                Ujian Akhir: lulus ✓
            @elseif(! ($modulLulusSemua ?? false))
                Ujian Akhir: terkunci ({{ $selesai }}/{{ $totalModul }} modul)
            @else
                Ujian Akhir: terbuka — 40 soal (±60 menit)
            @endif
        </span>
        @if(($ujianLulus ?? false) || ($modulLulusSemua ?? false))
            <a href="{{ route('siswa.ujian.index') }}" class="kka-btn-primary !py-1.5 text-xs text-center ml-auto shrink-0">{{ ($ujianLulus ?? false) ? 'Lihat Ujian' : 'Mulai Ujian →' }}</a>
        @else
            <span class="kka-btn-secondary !py-1.5 text-xs text-center opacity-40 ml-auto shrink-0">Terkunci</span>
        @endif
    </div>

    <div class="grid lg:grid-cols-3 gap-3 sm:gap-4 mt-4">
        <x-card title="Report Belajar" subtitle="ringkasan dari database">
            <div class="grid grid-cols-2 gap-2 text-sm">
                <div class="rounded-xl border border-slate-200 dark:border-slate-700 p-3"><p class="text-xs text-slate-500 dark:text-slate-400">Selesai</p><p class="text-xl font-extrabold">{{ $selesai }}/{{ $totalModul }}</p></div>
                <div class="rounded-xl border border-slate-200 dark:border-slate-700 p-3"><p class="text-xs text-slate-500 dark:text-slate-400">Total XP</p><p class="text-xl font-extrabold">{{ $totalXp }}</p></div>
                <div class="rounded-xl border border-slate-200 dark:border-slate-700 p-3"><p class="text-xs text-slate-500 dark:text-slate-400">Rata-rata</p><p class="text-xl font-extrabold">{{ $rataNilai ?? 0 }}</p></div>
                <div class="rounded-xl border border-slate-200 dark:border-slate-700 p-3"><p class="text-xs text-slate-500 dark:text-slate-400">Kuis</p><p class="text-xl font-extrabold">{{ $totalKuis ?? 0 }}×</p></div>
            </div>
            <div class="mt-3 overflow-x-auto">
                <table class="w-full text-xs">
                    <thead><tr class="text-left text-slate-500 dark:text-slate-400"><th class="py-1 pr-2">Modul</th><th class="py-1 pr-2">Status</th><th class="py-1 pr-2 text-right">Nilai</th><th class="py-1 text-right">XP</th></tr></thead>
                    <tbody>
                        @foreach($daftar as $d)
                            @php $p = $d['progress']; @endphp
                            <tr class="border-t border-slate-100 dark:border-slate-800">
                                <td class="py-1.5 pr-2 font-semibold truncate max-w-32">{{ $d['module']->title }}</td>
                                <td class="py-1.5 pr-2">@if($d['terkunci'])<span class="kka-status-idle">Terkunci</span>@elseif($p && $p->is_completed)<span class="kka-status-ok">Selesai</span>@elseif($p && ($p->reading_done || $p->quiz_score > 0))<span class="kka-status-warn">Berjalan</span>@else<span class="kka-status-idle">Belum</span>@endif</td>
                                <td class="py-1.5 pr-2 text-right font-bold">{{ $p ? $p->quiz_score : '—' }}</td>
                                <td class="py-1.5 text-right">{{ $p ? $p->xp_earned : 0 }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-card>

        <x-card title="Raport Belajar" subtitle="sama dengan halaman raport">
            <div class="text-sm space-y-1.5">
                <p>Nama: <b>{{ auth()->user()->name }}</b>@if(isset($kelasAktif) && $kelasAktif) • Kelas: <b>{{ $kelasAktif->name }}</b>@endif</p>
                <p>Modul: <b>{{ $selesai }}/{{ $totalModul }}</b> • Rata-rata: <b>{{ $rataNilai ?? 0 }}</b> • Tertinggi: <b>{{ $nilaiTerbaik ?? 0 }}</b></p>
                <p>Total XP: <b>{{ $totalXp }}</b> • Badge: <b>{{ $badges->count() }}</b></p>
                <p>Status: @if(($sertifikatAda ?? false) || ($sertifikatEligible ?? false))<span class="kka-status-ok">Sertifikat diperoleh</span>@else<span class="kka-status-warn">Belum lulus semua</span>@endif</p>
            </div>
            <div class="grid grid-cols-2 gap-2 mt-auto pt-3">
                <a href="{{ route('siswa.raporku') }}" class="kka-btn-secondary !py-2 text-sm text-center">Lihat Raport Lengkap</a>
                <a href="{{ route('siswa.raporku.cetak') }}" class="kka-btn-secondary !py-2 text-sm text-center">Cetak Raport</a>
            </div>
        </x-card>

        <x-card title="Sertifikat" subtitle="6 modul (≥80) + lulus ujian akhir">
            @if(($sertifikatAda ?? false) || ($sertifikatEligible ?? false))
                <p class="text-sm font-bold text-emerald-700 dark:text-emerald-400">Sertifikat Kamu Sudah Terbit 🎓</p>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">6/6 modul + ujian akhir lulus — progresmu {{ $persen }}%.</p>
                <div class="mt-auto pt-3"><a href="{{ route('siswa.sertifikat') }}" class="kka-btn-primary !py-2 text-sm text-center block w-full">Lihat Sertifikat</a></div>
            @else
                <p class="text-sm text-slate-600 dark:text-slate-300">Selesaikan 6 modul lalu lulus ujian akhir untuk sertifikat.</p>
                <x-progress-bar :value="$selesai" :max="$totalModul ?: 1" class="mt-2" />
                <div class="mt-auto pt-3"><a href="{{ route('siswa.sertifikat') }}" class="kka-btn-secondary !py-2 text-sm text-center block w-full">Progres Sertifikat ({{ $selesai }}/{{ $totalModul }})</a></div>
            @endif
        </x-card>
    </div>

    <h3 class="mt-6 mb-3 font-extrabold text-slate-900 dark:text-white">Modul Belajar (berurutan)</h3>
    <div class="grid sm:grid-cols-2 xl:grid-cols-4 gap-3 sm:gap-4">
        @foreach($daftar as $d)
            @php $m = $d['module']; $p = $d['progress']; @endphp
            <x-card :title="$d['posisi'] . '. ' . $m->title" :subtitle="$m->description">
                <div class="mt-auto pt-3 space-y-3">
                    <div class="flex items-center justify-between text-xs gap-2">
                        <span class="kka-badge shrink-0">+{{ $m->xp_reward }} XP</span>
                        @if($d['terkunci'])
                            <span class="kka-status-idle inline-flex items-center gap-1 whitespace-nowrap"><x-icon name="lock" class="w-3.5 h-3.5" />Terkunci</span>
                        @elseif($p && $p->is_completed)
                            <span class="kka-status-ok whitespace-nowrap">✓ Selesai • {{ $p->quiz_score }}</span>
                        @elseif($p && ($p->quiz_score > 0 || $p->reading_done))
                            <span class="kka-status-warn inline-flex items-center gap-1 whitespace-nowrap"><x-icon name="clock" class="w-3.5 h-3.5" />Dikerjakan{{ $p->quiz_score ? ' • ' . $p->quiz_score : '' }}</span>
                        @else
                            <span class="kka-status-idle whitespace-nowrap">Belum mulai</span>
                        @endif
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <a href="{{ route('siswa.bahan.show', $m->slug) }}" class="kka-btn-secondary !py-2 text-sm text-center">Bahan</a>
                        @if($d['terkunci'])
                            <span class="kka-btn-secondary !py-2 text-sm text-center opacity-40">Kuis</span>
                        @else
                            <a href="{{ route('siswa.kuis.show', $m->slug) }}" class="kka-btn-primary !py-2 text-sm text-center">Kuis</a>
                        @endif
                    </div>
                </div>
            </x-card>
        @endforeach
    </div>
</x-sidebar-layout>
