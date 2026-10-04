<x-sidebar-layout>
    <x-slot name="header">
        <h2 class="font-extrabold text-xl text-slate-900">Sertifikat Kelulusan</h2>
        <p class="text-sm text-slate-500">Terbit otomatis jika {{ $modules->count() }} modul wajib lulus (skor ≥80).</p>
    </x-slot>

    <div class="py-8 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        @if($eligible)
            <div class="no-print flex gap-2 mb-4">
                <button onclick="window.print()" class="kka-btn-primary">Cetak / Simpan PDF</button>
                <a href="{{ route('siswa.dashboard') }}" class="kka-btn-secondary">Kembali</a>
            </div>
            <div class="print-area kka-card !p-10 text-center relative overflow-hidden">
                <div class="absolute top-0 left-0 right-0 h-3 bg-gradient-to-r from-orange-500 to-emerald-500"></div>
                <p class="text-xs font-bold tracking-widest text-emerald-600">KKA LAB • LABORATORIUM KODING & KECERDASAN ARTIFISIAL</p>
                <h1 class="mt-2 text-4xl font-extrabold text-slate-900">Sertifikat Kelulusan</h1>
                <p class="mt-4 text-slate-500 text-sm">Diberikan kepada</p>
                <p class="text-2xl font-extrabold text-orange-500">{{ auth()->user()->name }}</p>
                <p class="mt-3 text-sm text-slate-600 max-w-xl mx-auto">Telah menyelesaikan {{ $modules->count() }} modul wajib ({{ $modules->pluck('title')->join(', ') }}) dengan skor minimal 80 per modul.</p>
                <div class="mt-5 flex items-center justify-center gap-6 text-sm">
                    <div><p class="text-slate-400 text-xs">Total XP</p><p class="font-extrabold text-emerald-600 text-xl">{{ $totalXp }}</p></div>
                    <div><p class="text-slate-400 text-xs">Kode Sertifikat</p><p class="font-bold">{{ $code }}</p></div>
                    <div><p class="text-slate-400 text-xs">Tanggal</p><p class="font-bold">{{ now()->format('d M Y') }}</p></div>
                </div>
                <div class="mt-8 flex justify-between text-xs text-slate-500">
                    <div><p class="border-t border-slate-300 pt-2 px-8">Guru Pembimbing</p></div>
                    <div><p class="border-t border-slate-300 pt-2 px-8">KKA Lab</p></div>
                </div>
            </div>
        @else
            <x-card title="Belum memenuhi syarat" subtitle="Selesaikan semua modul wajib dengan skor ≥80. Modul buatan guru bersifat bonus.">
                <div class="space-y-2">
                    @foreach($modules as $m)
                        @php $p = $progress->get($m->id); @endphp
                        <div class="flex items-center justify-between text-sm border border-slate-200 rounded-xl px-4 py-2">
                            <span>{{ $m->title }}</span>
                            @if($p && $p->is_completed)
                                <span class="font-bold text-emerald-600">✓ Selesai ({{ $p->quiz_score }})</span>
                            @elseif($p)
                                <span class="font-bold text-orange-500">⏳ Dikerjakan (skor {{ $p->quiz_score }})</span>
                            @else
                                <span class="font-bold text-slate-400 whitespace-nowrap">Belum mulai</span>
                            @endif
                        </div>
                    @endforeach
                </div>
                <a href="{{ route('siswa.dashboard') }}" class="kka-btn-primary mt-4">Lanjut Belajar</a>
            </x-card>
        @endif
    </div>
</x-sidebar-layout>
