<x-sidebar-layout>
    <x-slot name="header">
        <h2 class="font-extrabold text-xl text-slate-900 dark:text-white">Rapor {{ $siswa->name }}</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400">{{ $siswa->email }} • {{ $lulus }}/{{ count($daftar) }} modul lulus • Total {{ $totalXp }} XP</p>
    </x-slot>

    <div class="py-8 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
        <x-card title="Track Record Belajar" subtitle="kronologi dari database — hanya siswa kelas ini.">
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 text-sm">
                <div class="rounded-xl border border-slate-200 dark:border-slate-700 p-3"><p class="text-xs text-slate-500">Progres</p><p class="font-extrabold">{{ $lulus }}/{{ count($daftar) }}</p></div>
                <div class="rounded-xl border border-slate-200 dark:border-slate-700 p-3"><p class="text-xs text-slate-500">Total XP</p><p class="font-extrabold">{{ $totalXp }}</p></div>
                <div class="rounded-xl border border-slate-200 dark:border-slate-700 p-3"><p class="text-xs text-slate-500">Badge</p><p class="font-extrabold">{{ $badgeCount ?? 0 }}</p></div>
                <div class="rounded-xl border border-slate-200 dark:border-slate-700 p-3"><p class="text-xs text-slate-500">Sertifikat</p><p class="font-extrabold">@if(isset($sertifikat) && $sertifikat) Diperoleh @else Belum @endif</p></div>
            </div>
            <div class="mt-3 overflow-x-auto">
                <table class="w-full text-xs">
                    <thead><tr class="text-left text-slate-500"><th class="py-1 pr-2">Modul</th><th class="py-1 pr-2">Status</th><th class="py-1 pr-2 text-right">Nilai</th><th class="py-1 pr-2 text-right">Coba</th><th class="py-1 text-right">Terakhir</th></tr></thead>
                    <tbody>
                        @foreach($daftar as $d)
                            @php $p = $track[$d['module']->id] ?? $d['progress']; $a = isset($attempts) ? $attempts->get($d['module']->id) : null; @endphp
                            <tr class="border-t border-slate-100 dark:border-slate-800">
                                <td class="py-1.5 pr-2 font-semibold">{{ $d['posisi'] }}. {{ $d['module']->title }}</td>
                                <td class="py-1.5 pr-2">@if($p && $p->is_completed)<span class="kka-status-ok">Selesai</span>@elseif($p && ($p->reading_done || $p->quiz_score > 0))<span class="kka-status-warn">Berjalan</span>@else<span class="kka-status-idle">Belum</span>@endif</td>
                                <td class="py-1.5 pr-2 text-right font-bold">{{ $p ? $p->quiz_score : '—' }}{{$a && $a->maks ? ' (maks '.$a->maks.')' : ''}}</td>
                                <td class="py-1.5 pr-2 text-right">{{ $p->attempts ?? $a->jml ?? 0 }}×</td>
                                <td class="py-1.5 text-right">@if($p && $p->last_accessed_at){{ $p->last_accessed_at->format('d M Y') }}@elseif($p && $p->updated_at){{ $p->updated_at->format('d M Y') }}@else — @endif</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-card>

        <x-card title="Skor per Modul" subtitle="Skor kuis terakhir tiap modul (lulus ≥80).">
            <div class="space-y-2">
                @foreach($daftar as $d)
                    @php $p = $d['progress']; @endphp
                    <div class="flex items-center justify-between text-sm border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2">
                        <span class="font-semibold text-slate-700 dark:text-slate-200">{{ $d['posisi'] }}. {{ $d['module']->title }}</span>
                        @if($p && $p->is_completed)
                            <span class="font-extrabold text-emerald-700 dark:text-emerald-400">{{ $p->quiz_score }} • Lulus</span>
                        @elseif($p && $p->quiz_score > 0)
                            <span class="font-extrabold text-orange-600 dark:text-orange-400">{{ $p->quiz_score }} • Belum lulus</span>
                        @else
                            <span class="font-bold text-slate-400">—</span>
                        @endif
                    </div>
                @endforeach
            </div>
        </x-card>

        <x-card title="Saran untuk Siswa Ini" subtitle="Bisa disampaikan saat bimbingan.">
            <div class="space-y-2">
                @forelse($saran as $s)
                    <div class="rounded-2xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 p-4 text-sm">
                        <p class="font-bold text-slate-800 dark:text-slate-100">{{ $s['modul'] }}</p>
                        <p class="mt-1 text-slate-600 dark:text-slate-300">{{ $s['teks'] }}</p>
                    </div>
                @empty
                    <p class="text-sm text-emerald-700 dark:text-emerald-400 font-semibold">Semua modul lulus dengan skor ≥90. Luar biasa!</p>
                @endforelse
            </div>
        </x-card>

        <x-card title="Umpan Balik untuk {{ $siswa->name }}" subtitle="Satu catatan per modul, bisa dibaca siswa di rapornya.">
            <form method="POST" action="{{ route('guru.kelas.siswa.feedback', [$classroom, $siswa]) }}" class="space-y-2">
                @csrf
                <select name="module_id" required class="w-full rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 text-sm">
                    @foreach($daftar as $d)
                        <option value="{{ $d['module']->id }}">{{ $d['posisi'] }}. {{ $d['module']->title }}</option>
                    @endforeach
                </select>
                <textarea name="body" required maxlength="2000" rows="3" placeholder="Tulis catatan: apa yang sudah bagus + 1 hal yang perlu ditingkatkan..." class="w-full rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 text-sm"></textarea>
                <x-button type="submit">Simpan Catatan</x-button>
            </form>
            <div class="mt-3 space-y-2">
                @forelse($feedbacks as $f)
                    <div class="rounded-2xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 p-3 text-sm">
                        <p class="font-bold text-slate-800 dark:text-slate-100">{{ $f->module->title }} <span class="font-normal text-slate-400">• {{ $f->updated_at->format('d M Y') }}</span></p>
                        <p class="mt-1 text-slate-600 dark:text-slate-300">{{ $f->body }}</p>
                    </div>
                @empty
                    <p class="text-sm text-slate-400">Belum ada catatan.</p>
                @endforelse
            </div>
        </x-card>
    </div>
</x-sidebar-layout>
