<x-sidebar-layout>
    <x-slot name="header">
        <h2 class="font-extrabold text-xl text-slate-900">Bank Soal</h2>
        <p class="text-sm text-slate-500">Langkah 1 — pilih modul dulu, baru kelola soalnya.</p>
    </x-slot>

    <div class="grid sm:grid-cols-2 xl:grid-cols-4 gap-3 sm:gap-4">
        @foreach($modules as $m)
            <x-card :title="$m->title" :subtitle="$m->description">
                <div class="flex items-center justify-between text-xs gap-2">
                    <span class="kka-badge">{{ $m->quizzes_count }} soal</span>
                    <span class="font-bold text-slate-400">+{{ $m->xp_reward }} XP</span>
                </div>
                <a href="{{ route('guru.quiz.module', $m) }}" class="kka-btn-primary w-full mt-3 !py-2 text-sm text-center block">📝 Kelola Soal →</a>
            </x-card>
        @endforeach
    </div>
</x-sidebar-layout>
