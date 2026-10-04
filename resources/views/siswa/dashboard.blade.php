<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="font-extrabold text-xl text-slate-900">Dashboard Siswa</h2>
                <p class="text-sm text-slate-500">Halo {{ auth()->user()->name }}! Lanjutkan misimu.</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="kka-badge !text-sm !px-4 !py-1.5">⚡ {{ $totalXp }} XP</span>
                <a href="{{ route('siswa.sertifikat') }}" class="kka-btn-primary !py-2 text-sm">Sertifikat</a>
            </div>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        <div class="grid lg:grid-cols-3 gap-4">
            <x-card title="Gabung Kelas" subtitle="Masukkan 6 digit kode dari gurumu.">
                <form method="POST" action="{{ route('siswa.join') }}" class="flex gap-2">
                    @csrf
                    <input name="code" maxlength="6" required placeholder="cth: 482913"
                           class="flex-1 rounded-xl border-slate-200 focus:border-orange-500 focus:ring-orange-500 tracking-widest text-center font-bold" />
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

            <x-card title="Badge Koleksi" subtitle="{{ $badges->count() }} badge didapat.">
                <div class="flex flex-wrap gap-2">
                    @forelse($badges as $b)
                        <span class="kka-badge">🏅 {{ $b->name }}</span>
                    @empty
                        <p class="text-sm text-slate-500">Belum ada badge. Lulus kuis ≥80 untuk dapat badge!</p>
                    @endforelse
                </div>
            </x-card>

            <x-card title="Progres" subtitle="Selesaikan 4 modul dengan skor ≥80.">
                @php $done = $progress->where('is_completed', true)->count(); @endphp
                <p class="text-3xl font-extrabold text-slate-900">{{ $done }}/{{ $modules->count() }}</p>
                <x-progress-bar :value="$done" :max="$modules->count() ?: 1" class="mt-2" />
            </x-card>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach($modules as $m)
                @php $p = $progress->get($m->id); @endphp
                <x-card :title="$m->title" :subtitle="$m->description">
                    <div class="flex items-center justify-between text-xs">
                        <span class="kka-badge">+{{ $m->xp_reward }} XP</span>
                        @if($p && $p->is_completed)
                            <span class="font-bold text-emerald-600">✓ Selesai • Skor {{ $p->quiz_score }}</span>
                        @elseif($p)
                            <span class="font-bold text-orange-500">⏳ Dikerjakan • Skor {{ $p->quiz_score }}</span>
                        @else
                            <span class="font-bold text-slate-400">Belum mulai</span>
                        @endif
                    </div>
                    <a href="{{ route('siswa.modul.show', $m->slug) }}" class="kka-btn-primary w-full mt-3 !py-2 text-sm">Buka Modul</a>
                </x-card>
            @endforeach
        </div>
    </div>
</x-app-layout>
