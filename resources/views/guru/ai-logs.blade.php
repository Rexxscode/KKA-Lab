<x-sidebar-layout>
    <x-slot name="header">
        <a href="{{ route('guru.dashboard') }}" class="text-sm font-semibold text-orange-500">← Dasbor</a>
        <h2 class="font-extrabold text-xl text-slate-900 mt-1">Log AI Siswa Kelasmu</h2>
        <p class="text-sm text-slate-500">Pantau pertanyaan ke Tutor AI — bantu yang nyasar, apresiasi yang kritis. Kuota 30/hari/siswa.</p>
    </x-slot>

    <div class="py-8 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <x-card title="Filter" subtitle="Saring berdasar modul atau nama/email siswa.">
            <form method="GET" action="{{ route('guru.ai.index') }}" class="flex flex-col sm:flex-row gap-2">
                <select name="modul" class="rounded-xl border-slate-200 text-sm">
                    <option value="">Semua modul</option>
                    @foreach($modules as $m)
                        <option value="{{ $m->slug }}" @selected(($filter['modul'] ?? '') === $m->slug)>{{ $m->title }}</option>
                    @endforeach
                </select>
                <input name="cari" value="{{ $filter['cari'] ?? '' }}" maxlength="100" placeholder="Cari nama / email..." class="flex-1 rounded-xl border-slate-200 text-sm" />
                <x-button type="submit">Tampilkan</x-button>
            </form>
        </x-card>

        <div class="mt-4 space-y-2">
            @forelse($logs as $log)
                <div class="rounded-2xl border border-slate-200 bg-white p-4 text-sm">
                    <p class="font-bold text-slate-800">{{ $log->user->name ?? '—' }} <span class="font-normal text-slate-400">• {{ $log->created_at->format('d M Y H:i') }}{{ $log->module_slug ? ' • ' . $log->module_slug : '' }}</span></p>
                    <p class="mt-1 text-slate-700"><b>Tanya:</b> {{ $log->message }}</p>
                    <p class="mt-1 text-slate-500"><b>Jawab:</b> {{ \Illuminate\Support\Str::limit($log->reply, 300) }}</p>
                </div>
            @empty
                <p class="text-sm text-slate-500">Belum ada percakapan AI dari siswamu.</p>
            @endforelse
        </div>
        <div class="mt-4">{{ $logs->links() }}</div>
    </div>
</x-sidebar-layout>
