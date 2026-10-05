<x-sidebar-layout>
    <x-slot name="header">
        <a href="{{ route('guru.kelas.show', $classroom) }}" class="text-sm font-semibold text-orange-500">← {{ $classroom->name }}</a>
        <h2 class="font-extrabold text-xl text-slate-900 mt-1">Rapor {{ $siswa->name }}</h2>
        <p class="text-sm text-slate-500">{{ $siswa->email }} • {{ $lulus }}/{{ count($daftar) }} modul lulus • Total {{ $totalXp }} XP</p>
    </x-slot>

    <div class="py-8 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
        <x-card title="Skor per Modul" subtitle="Skor kuis terakhir tiap modul (lulus ≥80).">
            <div class="space-y-2">
                @foreach($daftar as $d)
                    @php $p = $d['progress']; @endphp
                    <div class="flex items-center justify-between text-sm border border-slate-200 rounded-xl px-4 py-2">
                        <span class="font-semibold text-slate-700">{{ $d['posisi'] }}. {{ $d['module']->title }}</span>
                        @if($p && $p->is_completed)
                            <span class="font-extrabold text-emerald-700">{{ $p->quiz_score }} • Lulus</span>
                        @elseif($p && $p->quiz_score > 0)
                            <span class="font-extrabold text-orange-600">{{ $p->quiz_score }} • Belum lulus</span>
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
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 text-sm">
                        <p class="font-bold text-slate-800">{{ $s['modul'] }}</p>
                        <p class="mt-1 text-slate-600">{{ $s['teks'] }}</p>
                    </div>
                @empty
                    <p class="text-sm text-emerald-700 font-semibold">Semua modul lulus dengan skor ≥90. Luar biasa!</p>
                @endforelse
            </div>
        </x-card>

        <x-card title="Umpan Balik untuk {{ $siswa->name }}" subtitle="Satu catatan per modul, bisa dibaca siswa di rapornya.">
            <form method="POST" action="{{ route('guru.kelas.siswa.feedback', [$classroom, $siswa]) }}" class="space-y-2">
                @csrf
                <select name="module_id" required class="w-full rounded-xl border-slate-200 text-sm">
                    @foreach($daftar as $d)
                        <option value="{{ $d['module']->id }}">{{ $d['posisi'] }}. {{ $d['module']->title }}</option>
                    @endforeach
                </select>
                <textarea name="body" required maxlength="2000" rows="3" placeholder="Tulis catatan: apa yang sudah bagus + 1 hal yang perlu ditingkatkan..." class="w-full rounded-xl border-slate-200 text-sm"></textarea>
                <x-button type="submit">Simpan Catatan</x-button>
            </form>
            <div class="mt-3 space-y-2">
                @forelse($feedbacks as $f)
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-3 text-sm">
                        <p class="font-bold text-slate-800">{{ $f->module->title }} <span class="font-normal text-slate-400">• {{ $f->updated_at->format('d M Y') }}</span></p>
                        <p class="mt-1 text-slate-600">{{ $f->body }}</p>
                    </div>
                @empty
                    <p class="text-sm text-slate-400">Belum ada catatan.</p>
                @endforelse
            </div>
        </x-card>
    </div>
</x-sidebar-layout>
