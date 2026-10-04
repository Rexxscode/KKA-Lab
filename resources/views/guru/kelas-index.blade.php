<x-sidebar-layout>
    <x-slot name="header">
        <h2 class="font-extrabold text-xl text-slate-900">Kelas Saya</h2>
        <p class="text-sm text-slate-500">Buat kelas baru dan bagikan kode 6-digit ke siswa.</p>
    </x-slot>

    <x-card title="Buat Kelas Baru" subtitle="Kode unik 6-digit dibuat otomatis.">
        <form method="POST" action="{{ route('guru.kelas.store') }}" class="flex flex-col sm:flex-row gap-2">
            @csrf
            <input name="name" required maxlength="100" placeholder="cth: XI RPL 3 — KKA" class="flex-1 rounded-xl border-slate-200 focus:border-orange-500 focus:ring-orange-500" />
            <x-button type="submit">Buat Kelas</x-button>
        </form>
    </x-card>

    <h3 class="mt-6 mb-3 font-extrabold text-slate-900">Daftar Kelas ({{ $classrooms->count() }})</h3>
    <div x-data="{ cari: '' }">
        <label for="cari-kelas" class="sr-only">Cari kelas</label>
        <input id="cari-kelas" x-model="cari" type="search" placeholder="Cari nama / kode kelas..." class="w-full sm:max-w-sm rounded-xl border-slate-200 text-sm focus:border-orange-500 focus:ring-orange-500" />
    <div class="grid sm:grid-cols-2 xl:grid-cols-3 gap-3 sm:gap-4 mt-3">
        @forelse($classrooms as $c)
            <div x-show="$el.dataset.cari.toLowerCase().includes(cari.toLowerCase())" data-cari="{{ strtolower($c->name . ' ' . $c->code) }}">
            <x-card :title="$c->name" :subtitle="'Kode: '.$c->code.' • '.$c->students_count.' siswa' . (isset($c->teacher) ? ' • '.$c->teacher->name : '')">
                <div class="flex gap-2">
                    <a href="{{ route('guru.kelas.show', $c) }}" class="kka-btn-primary !py-2 text-sm flex-1 text-center">Detail & Nilai</a>
                    <a href="{{ route('guru.kelas.export', $c) }}" class="kka-btn-secondary !py-2 text-sm">CSV</a>
                </div>
            </x-card>
            </div>
        @empty
            <x-card title="Belum ada kelas" subtitle="Buat kelas pertamamu lewat formulir di atas." />
        @endforelse
    </div>
    </div>

    <x-card title="Modul KKA ({{ $modules->count() }})" subtitle="Urutan belajar siswa." class="mt-4">
        <div class="grid sm:grid-cols-2 xl:grid-cols-4 gap-3">
            @foreach($modules as $m)
                <div class="rounded-2xl border border-slate-200 p-4">
                    <p class="font-bold text-sm">{{ $m->title }}</p>
                    <p class="text-xs text-slate-500 mt-1">{{ $m->soal }} soal • +{{ $m->xp_reward }} XP</p>
                </div>
            @endforeach
        </div>
    </x-card>
</x-sidebar-layout>
