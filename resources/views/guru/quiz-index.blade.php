<x-sidebar-layout>
    <x-slot name="header">
        <h2 class="font-extrabold text-xl text-slate-900 dark:text-white">Bank Soal</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400">Langkah 1 — pilih modul dulu, baru kelola soalnya. Soal default hanya bisa diubah admin.</p>
    </x-slot>

    <div x-data="{ cari: '' }">
        <label for="cari-bank-soal" class="sr-only">Cari modul di bank soal</label>
        <input id="cari-bank-soal" x-model="cari" type="search" placeholder="Cari modul..." class="w-full sm:max-w-sm rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 text-sm focus:border-orange-500 focus:ring-orange-500" />

    <h3 class="mt-4 mb-3 font-extrabold text-slate-900 dark:text-white">Modul Milikmu ({{ $milik->count() }})</h3>
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4">
        @forelse($milik as $m)
            <div x-show="$el.dataset.judul.toLowerCase().includes(cari.toLowerCase())" data-judul="{{ strtolower($m->title) }}" class="h-full">
            <x-card :title="$m->title" :subtitle="$m->soal . ' soal'">
                <div class="mt-auto pt-2"><a href="{{ route('guru.quiz.module', $m) }}" class="kka-btn-primary w-full !py-2 text-sm text-center gap-1.5 inline-flex items-center justify-center"><x-icon name="pencil" class="w-4 h-4" />Kelola Soal</a></div>
            </x-card>
            </div>
        @empty
            <p class="text-sm text-slate-500 dark:text-slate-400 sm:col-span-2">Belum ada. Buat dulu di halaman <a href="{{ route('guru.modul.index') }}" class="font-bold text-orange-500">Modul Saya</a>.</p>
        @endforelse
    </div>

    @if($default->count())
        <h3 class="mt-6 mb-3 font-extrabold text-slate-900 dark:text-white">Modul Default (salin dulu untuk mengedit)</h3>
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4">
            @foreach($default as $m)
                <div x-show="$el.dataset.judul.toLowerCase().includes(cari.toLowerCase())" data-judul="{{ strtolower($m->title) }}" class="h-full">
                <x-card :title="$m->title" :subtitle="$m->soal . ' soal default'">
                    <div class="mt-auto pt-2"><a href="{{ route('guru.quiz.module', $m) }}" class="kka-btn-secondary w-full !py-2 text-sm text-center block">Lihat / Salin →</a></div>
                </x-card>
                </div>
            @endforeach
        </div>
    @endif
    </div>
</x-sidebar-layout>
