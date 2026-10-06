<x-sidebar-layout>
    <x-slot name="header">
        <a href="{{ route('guru.dashboard') }}" class="text-sm font-semibold text-orange-500">← Dasbor</a>
        <h2 class="font-extrabold text-xl text-slate-900 dark:text-white mt-1">Moderasi Model Siswa</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400">Periksa karya latihan siswa. Yang disetujui tayang sebagai Model Global + pembuat dapat +30 XP.</p>
    </x-slot>

    <div class="py-8 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
        @if($global)
            <x-card title="Model Global Aktif" :subtitle="$global->name . ' • v' . $global->version . ' • ' . implode(', ', $global->labels)">
                <p class="text-xs text-slate-500 dark:text-slate-400">Menyetujui model baru akan menggantikan yang ini.</p>
            </x-card>
        @endif

        <x-card title="Menunggu Persetujuan" :subtitle="$antre->total() . ' model antre'">
            <div class="space-y-3">
                @forelse($antre as $model)
                    <div class="rounded-2xl border border-slate-200 dark:border-slate-700 p-4">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <div>
                                <p class="font-bold text-slate-900 dark:text-white">{{ $model->name }}</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400">{{ $model->student->name }} • {{ implode(', ', $model->labels) }} • {{ $model->created_at->format('d M Y H:i') }}</p>
                            </div>
                        </div>
                        <form method="POST" action="{{ route('guru.model.setujui', $model) }}" class="mt-3 flex flex-col sm:flex-row gap-2">
                            @csrf
                            <input name="accuracy" type="number" min="0" max="100" step="1" placeholder="Akurasi uji (%) opsional" class="rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 text-sm sm:w-56" />
                            <x-button type="submit">Setujui & Tayangkan</x-button>
                        </form>
                        <form method="POST" action="{{ route('guru.model.tolak', $model) }}" class="mt-2 flex flex-col sm:flex-row gap-2">
                            @csrf
                            <input name="note" required maxlength="500" placeholder="Alasan penolakan (wajib)..." class="flex-1 rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 text-sm" />
                            <button class="kka-btn-secondary !py-2 text-sm !text-red-600">Tolak</button>
                        </form>
                    </div>
                @empty
                    <p class="text-sm text-slate-500 dark:text-slate-400">Antrean kosong. Kerja bagus!</p>
                @endforelse
            </div>
            <div class="mt-4">{{ $antre->links() }}</div>
        </x-card>

        <x-card title="Riwayat Keputusan" subtitle="Disetujui dan ditolak.">
            <div class="space-y-2">
                @forelse($riwayat as $model)
                    <div class="flex items-center justify-between text-sm border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2">
                        <span class="font-semibold text-slate-700 dark:text-slate-200">{{ $model->name }} <span class="font-normal text-slate-400">• {{ $model->student->name }}</span></span>
                        <span class="font-extrabold {{ $model->status === 'approved' ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">{{ $model->status === 'approved' ? 'Tayang' : 'Ditolak' }}</span>
                    </div>
                @empty
                    <p class="text-sm text-slate-500 dark:text-slate-400">Belum ada riwayat.</p>
                @endforelse
            </div>
            <div class="mt-4">{{ $riwayat->links() }}</div>
        </x-card>
    </div>
</x-sidebar-layout>
