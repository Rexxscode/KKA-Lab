{{-- Daftar riwayat Tutor AI shared (guru + admin).
  Props: $title, $subtitle, $filterAction (route URL), $modules, $filter,
  $logs (paginator), $emptyText, $showRole (bool). --}}
<x-sidebar-layout>
    <x-slot name="header">
        <h2 class="font-extrabold text-xl text-slate-900 dark:text-white">{{ $title }}</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400">{{ $subtitle }}</p>
    </x-slot>

    <div class="py-8 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <x-card title="Filter" subtitle="Saring berdasar modul atau nama/email.">
            <form method="GET" action="{{ $filterAction }}" class="flex flex-col sm:flex-row gap-2">
                <label for="filter-modul" class="sr-only">Filter modul</label>
                <select id="filter-modul" name="modul" class="rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 text-sm">
                    <option value="">Semua modul</option>
                    @foreach($modules as $m)
                        <option value="{{ $m->slug }}" @selected(($filter['modul'] ?? '') === $m->slug)>{{ $m->title }}</option>
                    @endforeach
                </select>
                <label for="filter-cari" class="sr-only">Cari nama atau email</label>
                <input id="filter-cari" name="cari" value="{{ $filter['cari'] ?? '' }}" maxlength="100" placeholder="Cari nama / email..." class="flex-1 rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 text-sm" />
                <x-button type="submit">Tampilkan</x-button>
            </form>
        </x-card>

        <div class="mt-4 space-y-2">
            @forelse($logs as $log)
                <div class="rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 p-4 text-sm">
                    <p class="font-bold text-slate-800 dark:text-slate-100">{{ $log->user->name ?? '—' }} <span class="font-normal text-slate-400">@if($showRole)({{ $log->user->role ?? '—' }}) • @endif{{ $log->created_at->format('d M Y H:i') }}{{ $log->module_slug ? ' • ' . $log->module_slug : '' }}</span></p>
                    <p class="mt-1 text-slate-700 dark:text-slate-200"><b>Tanya:</b> {{ $log->message }}</p>
                    <p class="mt-1 text-slate-500 dark:text-slate-400"><b>Jawab:</b> {{ \Illuminate\Support\Str::limit($log->reply, 300) }}</p>
                </div>
            @empty
                <p class="text-sm text-slate-500 dark:text-slate-400">{{ $emptyText }}</p>
            @endforelse
        </div>
        <div class="mt-4">{{ $logs->links() }}</div>
    </div>
</x-sidebar-layout>
