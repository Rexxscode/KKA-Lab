<x-sidebar-layout>
    <x-slot name="header">
        <h2 class="font-extrabold text-xl text-slate-900">Modul Saya</h2>
        <p class="text-sm text-slate-500">Buat modul sendiri untuk kelasmu. Modul default milik admin tidak bisa diubah.</p>
    </x-slot>

    <x-card title="Buat Modul Baru" subtitle="Otomatis ditempel ke semua kelasmu (posisi akhir).">
        <form method="POST" action="{{ route('guru.modul.store') }}" class="grid sm:grid-cols-2 gap-2 sm:gap-3">
            @csrf
            <input name="title" required maxlength="150" placeholder="Judul modul, cth: Jaringan Komputer Dasar" class="sm:col-span-2 rounded-xl border-slate-200 focus:border-orange-500 focus:ring-orange-500" />
            <input name="description" maxlength="500" placeholder="Deskripsi singkat (opsional)" class="rounded-xl border-slate-200 focus:border-orange-500 focus:ring-orange-500" />
            <label class="text-xs font-semibold text-slate-600">Hadiah XP lulus (isi 0 bila tanpa XP)
                <input name="xp_reward" type="number" min="0" max="1000" value="0" class="mt-1 w-full rounded-xl border-slate-200 focus:border-orange-500 focus:ring-orange-500" />
            </label>
            <label class="sm:col-span-2 text-xs font-semibold text-slate-600">Bahan ajar (wajib diisi — dibaca siswa sebelum kuis)
                <textarea name="materi" required rows="5" placeholder="Tulis materi bacaan: penjelasan konsep + contoh-contoh. Baris baru = paragraf baru." class="mt-1 w-full rounded-xl border-slate-200 text-sm focus:border-orange-500 focus:ring-orange-500"></textarea>
            </label>
            <div class="sm:col-span-2"><x-button type="submit">Buat Modul</x-button></div>
        </form>
    </x-card>

    <h3 class="mt-6 mb-3 font-extrabold text-slate-900">Modul Milikmu ({{ $milik->count() }})</h3>
    <div class="grid sm:grid-cols-2 xl:grid-cols-3 gap-3 sm:gap-4">
        @forelse($milik as $m)
            <x-card :title="$m->title" :subtitle="$m->soal . ' soal • +' . $m->xp_reward . ' XP'">
                <p class="text-xs text-slate-500 line-clamp-2">{{ $m->description }}</p>
                <div class="flex flex-wrap gap-2 mt-3">
                    <a href="{{ route('guru.quiz.module', $m) }}" class="kka-btn-primary !py-1.5 text-xs">📝 Kelola Soal</a>
                    <details class="relative">
                        <summary class="kka-btn-secondary !py-1.5 text-xs cursor-pointer list-none">Ubah</summary>
                        <form method="POST" action="{{ route('guru.modul.update', $m) }}" class="mt-2 rounded-2xl border border-slate-200 p-3 space-y-2">
                            @csrf @method('PATCH')
                            <input name="title" value="{{ $m->title }}" required maxlength="150" class="w-full rounded-xl border-slate-200 text-sm" />
                            <input name="description" value="{{ $m->description }}" maxlength="500" placeholder="Deskripsi" class="w-full rounded-xl border-slate-200 text-sm" />
                            <textarea name="materi" rows="4" placeholder="Bahan ajar {{ $m->isDefault() ? '(kosongkan = pakai materi bawaan)' : '(wajib diisi)' }}" @if(! $m->isDefault()) required @endif class="w-full rounded-xl border-slate-200 text-sm">{{ $m->materi }}</textarea>
                            <input name="xp_reward" type="number" min="0" max="1000" value="{{ $m->xp_reward }}" class="w-full rounded-xl border-slate-200 text-sm" />
                            <button class="kka-btn-primary !py-1.5 text-xs w-full">Simpan</button>
                        </form>
                    </details>
                    <form method="POST" action="{{ route('guru.modul.destroy', $m) }}" onsubmit="return confirm('Hapus modul ini beserta soalnya?')">
                        @csrf @method('DELETE')
                        <button class="kka-btn-secondary !py-1.5 text-xs !text-red-600">Hapus</button>
                    </form>
                </div>
            </x-card>
        @empty
            <x-card title="Belum ada modul sendiri" subtitle="Buat modul pertamamu lewat formulir di atas." />
        @endforelse
    </div>

    @if($default->count())
        <h3 class="mt-6 mb-3 font-extrabold text-slate-900">Modul Default (milik admin, baca saja)</h3>
        <div class="grid sm:grid-cols-2 xl:grid-cols-4 gap-3 sm:gap-4">
            @foreach($default as $m)
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                    <p class="font-bold text-sm">{{ $m->title }}</p>
                    <p class="text-xs text-slate-500 mt-1">{{ $m->soal }} soal</p>
                    <a href="{{ route('guru.quiz.module', $m) }}" class="text-xs font-bold text-orange-500 hover:text-orange-600">Lihat soal →</a>
                </div>
            @endforeach
        </div>
    @endif
</x-sidebar-layout>
