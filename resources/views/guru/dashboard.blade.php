<x-sidebar-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="font-extrabold text-xl text-slate-900">Dashboard Guru</h2>
                <p class="text-sm text-slate-500">Buat kelas, bagikan kode 6-digit, pantau progres siswa.</p>
            </div>
            <a href="{{ route('guru.quiz.index') }}" class="kka-btn-secondary !py-2 text-sm">Bank Soal (edit kuis)</a>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        <x-card title="Buat Kelas Baru" subtitle="Kode unik 6-digit dibuat otomatis.">
            <form method="POST" action="{{ route('guru.kelas.store') }}" class="flex flex-col sm:flex-row gap-2">
                @csrf
                <input name="name" required maxlength="100" placeholder="cth: XI RPL 3 — KKA" class="flex-1 rounded-xl border-slate-200 focus:border-orange-500 focus:ring-orange-500" />
                <x-button type="submit">Buat Kelas</x-button>
            </form>
        </x-card>

        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($classrooms as $c)
                <x-card :title="$c->name" :subtitle="'Kode: '.$c->code.' • '.$c->students_count.' siswa'">
                    <div class="flex gap-2">
                        <a href="{{ route('guru.kelas.show', $c) }}" class="kka-btn-primary !py-2 text-sm flex-1 text-center">Detail & Nilai</a>
                        <a href="{{ route('guru.kelas.export', $c) }}" class="kka-btn-secondary !py-2 text-sm">CSV</a>
                    </div>
                </x-card>
            @empty
                <x-card title="Belum ada kelas" subtitle="Buat kelas pertamamu di atas, lalu bagikan kodenya ke siswa." />
            @endforelse
        </div>

        <x-card title="4 Modul KKA" subtitle="Urutan belajar siswa.">
            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-3">
                @foreach($modules as $m)
                    <div class="rounded-2xl border border-slate-200 p-4">
                        <p class="font-bold text-sm">{{ $m->title }}</p>
                        <p class="text-xs text-slate-500 mt-1">{{ $m->quizzes_count ?? $m->quizzes()->count() }} soal • +{{ $m->xp_reward }} XP</p>
                    </div>
                @endforeach
            </div>
        </x-card>
    </div>
</x-sidebar-layout>
