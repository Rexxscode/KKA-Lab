<x-sidebar-layout>
    <x-slot name="header">
        <h2 class="font-extrabold text-xl text-slate-900 dark:text-white">Sertifikat Kelulusan</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400">Terbit otomatis jika {{ $modules->count() }} modul wajib lulus (skor ≥80).</p>
    </x-slot>

    <div class="py-8">
        @if($eligible)
            <div class="no-print flex gap-2 mb-4">
                <button onclick="window.print()" class="kka-btn-primary">Cetak / Simpan PDF</button>
                <a href="{{ route('siswa.dashboard') }}" class="kka-btn-secondary">Kembali</a>
            </div>
            <div class="print-area kka-card !p-10 text-center relative overflow-hidden">
                <div class="absolute top-0 left-0 right-0 h-3 bg-gradient-to-r from-orange-500 to-emerald-500"></div>
                <p class="text-xs font-bold tracking-widest text-emerald-600 dark:text-emerald-400">KKA LAB • LABORATORIUM KODING & KECERDASAN ARTIFISIAL</p>
                <h1 class="mt-2 text-4xl font-extrabold text-slate-900 dark:text-white">Sertifikat Kelulusan</h1>
                <p class="mt-4 text-slate-500 dark:text-slate-400 text-sm">Diberikan kepada</p>
                <p class="text-2xl font-extrabold text-orange-500">{{ auth()->user()->name }}</p>
                <p class="mt-3 text-sm text-slate-600 dark:text-slate-300 max-w-xl mx-auto">Telah menyelesaikan {{ $modules->count() }} modul wajib ({{ $modules->pluck('title')->join(', ') }}) dengan skor minimal 80 per modul.</p>
                <div class="mt-5 flex items-center justify-center gap-6 text-sm">
                    <div><p class="text-slate-500 dark:text-slate-400 text-xs">Total XP</p><p class="font-extrabold text-emerald-600 dark:text-emerald-400 text-xl">{{ $totalXp }}</p></div>
                    <div><p class="text-slate-500 dark:text-slate-400 text-xs">Kode Sertifikat</p><p class="font-bold">{{ $certificate->code }}</p></div>
                    <div><p class="text-slate-500 dark:text-slate-400 text-xs">Tanggal Terbit</p><p class="font-bold">{{ $certificate->issued_at->format('d M Y') }}</p></div>
                </div>
                <p class="mt-4 text-xs text-slate-500 dark:text-slate-400">Verifikasi keaslian: <a href="{{ route('sertifikat.verifikasi', $certificate->code) }}" class="font-bold text-emerald-600 dark:text-emerald-400 underline">{{ route('sertifikat.verifikasi', $certificate->code) }}</a></p>
                <div class="mt-8 flex justify-between text-xs text-slate-500 dark:text-slate-400">
                    <div><p class="border-t border-slate-300 dark:border-slate-600 pt-2 px-8">Guru Pembimbing</p></div>
                    <div><p class="border-t border-slate-300 dark:border-slate-600 pt-2 px-8">KKA Lab</p></div>
                </div>
            </div>
        @else
            <div class="grid lg:grid-cols-3 gap-4 items-start">
                <x-card title="Belum memenuhi syarat" subtitle="Selesaikan semua modul wajib dengan skor ≥80. Modul buatan guru bersifat bonus." class="lg:col-span-2">
                    <div class="space-y-2">
                        @foreach($modules as $m)
                            @php $p = $progress->get($m->id); @endphp
                            <div class="flex items-center justify-between gap-3 text-sm sm:text-[15px] border border-slate-200 dark:border-slate-700 rounded-2xl px-4 sm:px-5 py-3">
                                <span class="font-semibold text-slate-700 dark:text-slate-200">{{ $m->title }}</span>
                                @if($p && $p->is_completed)
                                    <span class="font-bold text-emerald-600 dark:text-emerald-400 whitespace-nowrap">✓ Selesai ({{ $p->quiz_score }})</span>
                                @elseif($p)
                                    <span class="kka-status-warn whitespace-nowrap">Dikerjakan (skor {{ $p->quiz_score }})</span>
                                @else
                                    <span class="kka-status-idle whitespace-nowrap">Belum mulai</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </x-card>
                <x-card title="Progres Sertifikat" subtitle="Semangat, sedikit lagi!">
                    <p class="text-4xl font-extrabold text-emerald-600 dark:text-emerald-400">{{ $lulus }}<span class="text-xl text-slate-400">/{{ $modules->count() }}</span></p>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">modul lulus dengan skor ≥80</p>
                    <div class="mt-3 h-2.5 rounded-full bg-slate-100 dark:bg-slate-700 overflow-hidden" role="progressbar" aria-valuemin="0" aria-valuemax="{{ $modules->count() }}" aria-valuenow="{{ $lulus }}">
                        <div class="h-full bg-emerald-500 rounded-full transition-all" style="width: {{ $modules->count() ? round($lulus / $modules->count() * 100) : 0 }}%"></div>
                    </div>
                    @if($berikutnya)
                        <div class="mt-4 rounded-2xl border border-orange-200 dark:border-orange-500/30 bg-orange-50/60 dark:bg-orange-500/10 p-4">
                            <p class="text-xs font-bold tracking-wide text-orange-700 dark:text-orange-300">SELANJUTNYA</p>
                            <p class="mt-1 text-sm font-bold text-slate-800 dark:text-slate-100">{{ $berikutnya->title }}</p>
                            <a href="{{ route('siswa.bahan.show', $berikutnya->slug) }}" class="inline-block mt-2 text-xs font-extrabold text-orange-600 dark:text-orange-400 hover:text-orange-700">Kerjakan sekarang →</a>
                        </div>
                    @endif
                    <ul class="mt-4 space-y-1.5 text-xs text-slate-500 dark:text-slate-400">
                        <li class="flex items-center gap-2"><span class="font-extrabold text-emerald-600 dark:text-emerald-400">✓</span> Skor minimal 80 per modul</li>
                        <li class="flex items-center gap-2"><span class="font-extrabold text-emerald-600 dark:text-emerald-400">✓</span> {{ $modules->count() }} modul wajib</li>
                        <li class="flex items-center gap-2"><span class="font-extrabold text-emerald-600 dark:text-emerald-400">✓</span> Terbit otomatis + bisa diverifikasi</li>
                    </ul>
                    <div class="mt-4 flex items-center justify-between rounded-xl bg-slate-50 dark:bg-slate-800/70 border border-slate-200 dark:border-slate-700 px-4 py-2.5 text-sm">
                        <span class="font-semibold text-slate-600 dark:text-slate-300">Total XP-mu</span>
                        <span class="font-extrabold text-orange-500">{{ $totalXp }} XP</span>
                    </div>
                    <a href="{{ route('siswa.dashboard') }}" class="kka-btn-primary mt-4 !py-2 text-sm">Lanjut Belajar</a>
                </x-card>
            </div>
        @endif
    </div>
</x-sidebar-layout>
