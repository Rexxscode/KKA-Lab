<x-sidebar-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="font-extrabold text-xl text-slate-900">Halo {{ auth()->user()->name }}! 👋</h2>
                <p class="text-sm text-slate-500">Lanjutkan misimu belajar AI hari ini.</p>
            </div>
            <a href="{{ route('siswa.sertifikat') }}" class="kka-btn-primary !py-2 text-sm">Sertifikat</a>
        </div>
    </x-slot>

    @php $selesai = $progress->where('is_completed', true)->count(); @endphp
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <x-card title="Total XP" subtitle="dari kuis & praktik"><p class="text-3xl font-extrabold text-orange-500">⚡ {{ $totalXp }}</p></x-card>
        <x-card title="Badge" subtitle="dikumpulkan"><p class="text-3xl font-extrabold text-emerald-500">🏅 {{ $badges->count() }}</p></x-card>
        <x-card title="Modul Selesai" subtitle="skor ≥80"><p class="text-3xl font-extrabold text-slate-900">{{ $selesai }}/{{ $modules->count() }}</p></x-card>
        <div class="kka-card p-6">
            <p class="text-sm text-slate-500">Progres Belajar</p>
            <p class="text-3xl font-extrabold text-slate-900 mt-1">{{ $modules->count() ? round($selesai / $modules->count() * 100) : 0 }}%</p>
            <x-progress-bar :value="$selesai" :max="$modules->count() ?: 1" class="mt-2" />
        </div>
    </div>

    <div class="grid lg:grid-cols-3 gap-3 sm:gap-4 mt-4">
        <x-card title="Kuis yang Lulus" subtitle="riwayat nilaimu">
            <div class="space-y-2">
                @forelse($progress->where('is_completed', true) as $p)
                    <div class="flex items-center justify-between rounded-xl border border-emerald-200 bg-emerald-50/60 px-3 py-2 text-sm">
                        <span class="font-semibold text-slate-700">{{ $p->module->title ?? 'Modul' }}</span>
                        <span class="font-extrabold text-emerald-600">{{ $p->quiz_score }}</span>
                    </div>
                @empty
                    <p class="text-sm text-slate-400">Belum ada. Luluskan kuis dengan skor ≥80!</p>
                @endforelse
            </div>
        </x-card>

        <x-card title="Gabung Kelas" subtitle="6 digit kode dari gurumu">
            <form method="POST" action="{{ route('siswa.join') }}" class="flex gap-2">
                @csrf
                <input name="code" maxlength="6" required placeholder="cth: 482913"
                       class="flex-1 min-w-0 rounded-xl border-slate-200 focus:border-orange-500 focus:ring-orange-500 tracking-widest text-center font-bold" />
                <x-button type="submit">Gabung</x-button>
            </form>
            @if($classrooms->count())
                <div class="mt-3 flex flex-wrap gap-2">
                    @foreach($classrooms as $c)
                        <span class="kka-badge">{{ $c->name }} • {{ $c->code }}</span>
                    @endforeach
                </div>
            @endif
        </x-card>

        <x-card title="Badge Koleksi" subtitle="{{ $badges->count() }} badge didapat">
            <div class="flex flex-wrap gap-2">
                @forelse($badges as $b)
                    <span class="kka-badge">🏅 {{ $b->name }}</span>
                @empty
                    <p class="text-sm text-slate-500">Lulus kuis ≥80 untuk dapat badge!</p>
                @endforelse
            </div>
        </x-card>
    </div>

    <h3 class="mt-6 mb-3 font-extrabold text-slate-900">Modul Belajar</h3>
    <div class="grid sm:grid-cols-2 xl:grid-cols-4 gap-3 sm:gap-4">
        @foreach($modules as $m)
            @php $p = $progress->get($m->id); @endphp
            <x-card :title="$m->title" :subtitle="$m->description">
                <div class="flex items-center justify-between text-xs gap-2">
                    <span class="kka-badge">+{{ $m->xp_reward }} XP</span>
                    @if($p && $p->is_completed)
                        <span class="font-bold text-emerald-600 whitespace-nowrap">✓ Selesai • {{ $p->quiz_score }}</span>
                    @elseif($p && ($p->quiz_score > 0 || $p->reading_done))
                        <span class="font-bold text-orange-500 whitespace-nowrap">⏳ Dikerjakan{{ $p->quiz_score ? ' • ' . $p->quiz_score : '' }}</span>
                    @else
                        <span class="font-bold text-slate-400 whitespace-nowrap">Belum mulai</span>
                    @endif
                </div>
                <div class="grid grid-cols-2 gap-2 mt-3">
                    <a href="{{ route('siswa.bahan.show', $m->slug) }}" class="kka-btn-secondary !py-2 text-sm text-center">📖 Bahan</a>
                    <a href="{{ $p && $p->reading_done ? route('siswa.kuis.show', $m->slug) : route('siswa.bahan.show', $m->slug) }}" class="kka-btn-primary !py-2 text-sm text-center">📝 Kuis</a>
                </div>
            </x-card>
        @endforeach
    </div>
</x-sidebar-layout>
