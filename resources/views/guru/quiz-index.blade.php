<x-sidebar-layout>
    <x-slot name="header">
        <h2 class="font-extrabold text-xl text-slate-900">Bank Soal</h2>
        <p class="text-sm text-slate-500">Langkah 1 — pilih modul dulu, baru kelola soalnya. Soal default hanya bisa diubah admin.</p>
    </x-slot>

    <div x-data="{ cari: '' }">
        <label for="cari-bank-soal" class="sr-only">Cari modul di bank soal</label>
        <input id="cari-bank-soal" x-model="cari" type="search" placeholder="Cari modul..." class="w-full sm:max-w-sm rounded-xl border-slate-200 text-sm focus:border-orange-500 focus:ring-orange-500" />

    <h3 class="mt-4 mb-3 font-extrabold text-slate-900">Modul Milikmu ({{ $milik->count() }})</h3>
    <div class="grid sm:grid-cols-2 xl:grid-cols-4 gap-3 sm:gap-4">
        @forelse($milik as $m)
            <div x-show="$el.dataset.judul.toLowerCase().includes(cari.toLowerCase())" data-judul="{{ strtolower($m->title) }}">
            <x-card :title="$m->title" :subtitle="$m->soal . ' soal'">
                <a href="{{ route('guru.quiz.module', $m) }}" class="kka-btn-primary w-full mt-1 !py-2 text-sm text-center gap-1.5 inline-flex items-center justify-center"><x-icon name="pencil" class="w-4 h-4" />Kelola Soal</a>
            </x-card>
            </div>
        @empty
            <p class="text-sm text-slate-500 sm:col-span-2">Belum ada. Buat dulu di halaman <a href="{{ route('guru.modul.index') }}" class="font-bold text-orange-500">Modul Saya</a>.</p>
        @endforelse
    </div>

    @if($default->count())
        <h3 class="mt-6 mb-3 font-extrabold text-slate-900">Modul Default (salin dulu untuk mengedit)</h3>
        <div class="grid sm:grid-cols-2 xl:grid-cols-4 gap-3 sm:gap-4">
            @foreach($default as $m)
                <div x-show="$el.dataset.judul.toLowerCase().includes(cari.toLowerCase())" data-judul="{{ strtolower($m->title) }}">
                <x-card :title="$m->title" :subtitle="$m->soal . ' soal default'">
                    <a href="{{ route('guru.quiz.module', $m) }}" class="kka-btn-secondary w-full mt-1 !py-2 text-sm text-center block">Lihat / Salin →</a>
                </x-card>
                </div>
            @endforeach
        </div>
    @endif
    </div>
</x-sidebar-layout>
