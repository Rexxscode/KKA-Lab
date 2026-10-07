<x-sidebar-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="font-extrabold text-xl text-slate-900 dark:text-white">{{ $classroom->name }}</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400">Kode kelas: <b class="text-orange-500 tracking-widest">{{ $classroom->code }}</b> • {{ $classroom->students->count() }} siswa
                    @if($classroom->status === 'arsip') <span class="ml-1 px-2 py-0.5 rounded-full bg-slate-200 text-slate-600 dark:text-slate-300 text-xs font-bold">Arsip</span> @endif</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <form method="POST" action="{{ route('guru.kelas.arsip', $classroom) }}" onsubmit="return confirm('{{ $classroom->status === 'arsip' ? 'Buka lagi kelas ini?' : 'Arsipkan kelas ini? Siswa tak bisa gabung & kelas tak aktif.' }}')">
                    @csrf @method('PATCH')
                    <button class="kka-btn-secondary !py-2 text-sm">{{ $classroom->status === 'arsip' ? 'Buka Lagi' : 'Arsipkan' }}</button>
                </form>
                <form method="POST" action="{{ route('guru.kelas.kode', $classroom) }}" onsubmit="return confirm('Buat kode baru? Kode lama langsung hangus.')">
                    @csrf @method('PATCH')
                    <button class="kka-btn-secondary !py-2 text-sm">Kode Baru</button>
                </form>
                <a href="{{ route('guru.kelas.export', $classroom) }}" class="kka-btn-primary !py-2 text-sm">Ekspor CSV</a>
            </div>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <x-card title="Progres & Skor Siswa" subtitle="Hijau = lulus (≥80), orange = belum lulus.">
            <div x-data="{ cari: '' }">
                <label for="cari-siswa" class="sr-only">Cari siswa di kelas ini</label>
                <input id="cari-siswa" x-model="cari" type="search" placeholder="Cari nama / email siswa..." class="w-full sm:max-w-sm rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 text-sm focus:border-orange-500 focus:ring-orange-500 mb-3" />
            <div class="overflow-x-auto">
                <table class="w-full text-sm min-w-[720px]">
                    <thead>
                        <tr class="text-left text-xs text-slate-400 border-b border-slate-200 dark:border-slate-700">
                            <th scope="col" class="py-2 pr-3">Siswa</th>
                            @foreach($modules as $m)
                                <th scope="col" class="py-2 pr-3">{{ $m->title }}</th>
                            @endforeach
                            <th scope="col" class="py-2">Total XP</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($classroom->students as $s)
                            @php $by = $s->progress->keyBy('module_id'); @endphp
                            <tr class="border-b border-slate-100 dark:border-slate-800" x-show="$el.dataset.cari.toLowerCase().includes(cari.toLowerCase())" data-cari="{{ strtolower($s->name . ' ' . $s->email) }}">
                                <td class="py-3 pr-3"><a href="{{ route('guru.kelas.siswa.show', [$classroom, $s->id]) }}" class="font-bold text-slate-800 dark:text-slate-100 hover:text-orange-600">{{ $s->name }}</a><p class="text-xs text-slate-400">{{ $s->email }}</p>
                                    <div class="mt-1 flex gap-2">
                                        <form method="POST" action="{{ route('guru.kelas.siswa.reset', [$classroom, $s->id]) }}" onsubmit="return confirm('Ulangi progres {{ $s->name }} di kelas ini dari nol?')">
                                            @csrf @method('DELETE')
                                            <button class="text-[11px] font-bold text-orange-600 dark:text-orange-400 hover:text-orange-700">Reset progres</button>
                                        </form>
                                        <form method="POST" action="{{ route('guru.kelas.siswa.kick', [$classroom, $s->id]) }}" onsubmit="return confirm('Keluarkan {{ $s->name }} dari kelas?')">
                                            @csrf @method('DELETE')
                                            <button class="text-[11px] font-bold text-red-600 hover:text-red-700">Keluarkan</button>
                                        </form>
                                    </div>
                                </td>
                                @foreach($modules as $m)
                                    @php $p = $by->get($m->id); @endphp
                                    <td class="py-3 pr-3">
                                        @if($p && $p->is_completed)
                                            <span class="kka-badge">{{ $p->quiz_score }} • Selesai</span>
                                        @elseif($p)
                                            <span class="inline-flex px-3 py-1 rounded-full text-xs font-bold bg-orange-50 dark:bg-orange-500/10 text-orange-700 dark:text-orange-400 border border-orange-200 dark:border-orange-500/30">{{ $p->quiz_score }} • Dikerjakan</span>
                                        @else
                                            <span class="text-slate-300 text-xs">—</span>
                                        @endif
                                    </td>
                                @endforeach
                                <td class="py-3 font-extrabold text-emerald-600 dark:text-emerald-400">{{ $by->sum('xp_earned') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="{{ $modules->count() + 2 }}" class="py-6 text-center text-slate-400 text-sm">Belum ada siswa. Bagikan kode <b>{{ $classroom->code }}</b>.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            </div>
        </x-card>

        <x-card title="Susunan Modul Kelas" subtitle="Urutan pengerjaan siswa (1 → 2 → 3 …). Atur dengan tombol naik/turun." class="mt-4">
            <div class="space-y-2">
                @forelse($modules as $i => $m)
                    <div class="flex items-center gap-2 sm:gap-3 rounded-xl border border-slate-200 dark:border-slate-700 px-3 sm:px-4 py-2">
                        <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-orange-100 dark:bg-orange-500/15 text-orange-700 dark:text-orange-400 text-xs font-extrabold shrink-0">{{ $i + 1 }}</span>
                        <div class="flex-1 min-w-0"><p class="font-bold text-sm truncate">{{ $m->title }}</p><p class="text-xs text-slate-400">{{ $m->isDefault() ? 'Default' : 'Modul guru' }}{{ $m->pivot->due_at ? ' • Tenggat ' . \Illuminate\Support\Carbon::parse($m->pivot->due_at)->format('d M Y') : '' }}</p></div>
                        @if($m->isDefault() || ! auth()->user()->isAdmin())
                            <form method="POST" action="{{ route('guru.kelas.modul.tenggat', [$classroom, $m]) }}" class="flex items-center gap-1">
                                @csrf @method('PATCH')
                                <input type="date" name="due_at" value="{{ $m->pivot->due_at ? \Illuminate\Support\Carbon::parse($m->pivot->due_at)->format('Y-m-d') : '' }}" aria-label="Tenggat {{ $m->title }}" class="rounded-lg border-slate-200 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 text-xs py-1" />
                                <button class="kka-btn-secondary !py-1 !px-2 text-xs">Set</button>
                            </form>
                            <form method="POST" action="{{ route('guru.kelas.modul.naik', [$classroom, $m]) }}">
                                @csrf @method('PATCH')
                                <button class="kka-btn-secondary !py-1 !px-2.5 text-xs" @disabled($i === 0)>↑</button>
                            </form>
                            <form method="POST" action="{{ route('guru.kelas.modul.turun', [$classroom, $m]) }}">
                                @csrf @method('PATCH')
                                <button class="kka-btn-secondary !py-1 !px-2.5 text-xs" @disabled($i === $modules->count() - 1)>↓</button>
                            </form>
                            <form method="POST" action="{{ route('guru.kelas.modul.lepas', [$classroom, $m]) }}" onsubmit="return confirm('Lepas modul ini dari kelas?')">
                                @csrf @method('DELETE')
                                <button class="kka-btn-secondary !py-1 !px-2.5 text-xs !text-red-600">Lepas</button>
                            </form>
                        @else
                            <span class="text-[11px] font-bold text-slate-400 whitespace-nowrap">Milik guru</span>
                        @endif
                    </div>
                @empty
                    <p class="text-sm text-slate-500 dark:text-slate-400">Belum ada modul di kelas ini.</p>
                @endforelse
            </div>
            @if($modulTersedia->count())
                <form method="POST" action="{{ route('guru.kelas.modul.tempel', $classroom) }}" class="mt-3 flex flex-col sm:flex-row gap-2">
                    @csrf
                    <select name="module_id" required class="flex-1 rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 text-sm focus:border-orange-500 focus:ring-orange-500">
                        @foreach($modulTersedia as $tm)
                            <option value="{{ $tm->id }}">{{ $tm->title }}{{ $tm->isDefault() ? ' (default)' : '' }}</option>
                        @endforeach
                    </select>
                    <x-button type="submit">Tempel ke Kelas</x-button>
                </form>
            @endif
        </x-card>
    </div>
</x-sidebar-layout>
