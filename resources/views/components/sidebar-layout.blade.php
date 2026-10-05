@props(['title' => null])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('app.name', 'KKA Lab') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="antialiased" style="background:var(--color-page)" x-data="{ SidebarTerbuka: false }">
<div class="min-h-screen lg:flex">

    <!-- Overlay HP -->
    <div x-show="SidebarTerbuka" @click="SidebarTerbuka = false" class="fixed inset-0 bg-slate-900/40 z-40 lg:hidden" style="display: none"></div>

    <!-- SIDEBAR -->
    <aside :class="SidebarTerbuka ? 'translate-x-0' : '-translate-x-full'"
           class="fixed inset-y-0 left-0 z-50 w-64 shrink-0 bg-white border-r border-slate-200 flex flex-col transition-transform duration-200 lg:sticky lg:top-0 lg:h-screen lg:translate-x-0">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 px-5 h-16 border-b border-slate-100">
            <span class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-orange-500 text-white font-extrabold">K</span>
            <span>
                <span class="block font-extrabold text-slate-900 leading-none">KKA <span class="text-orange-500">Lab</span></span>
                <span class="block text-[11px] text-slate-500 mt-0.5">Koding & Kecerdasan Artifisial</span>
            </span>
        </a>

        <div class="px-5 pt-4">
            <span class="text-xs font-semibold px-2.5 py-1 rounded-full {{ auth()->user()->isGuru() ? 'bg-orange-100 text-orange-700' : (auth()->user()->isAdmin() ? 'bg-slate-800 text-white' : 'bg-emerald-100 text-emerald-700') }}">
                {{ ucfirst(auth()->user()->role) }}@if(auth()->user()->isSiswa()) • {{ auth()->user()->totalXp() }} XP @endif
            </span>
        </div>

        <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-5">
            @if(auth()->user()->isSiswa())
                <div>
                    <p class="px-3 mb-1.5 text-[11px] font-bold tracking-wider text-slate-500">UTAMA</p>
                    <x-side-link :href="route('siswa.dashboard')" :active="request()->routeIs('siswa.dashboard')" icon="home">Dashboard</x-side-link>
                </div>
                <div>
                    <p class="px-3 mb-1.5 text-[11px] font-bold tracking-wider text-slate-500">BELAJAR</p>
                    <div class="space-y-1">
                        <x-side-link :href="route('siswa.bahan.index')" :active="request()->routeIs('siswa.bahan.*')" icon="book">Bahan Belajar</x-side-link>
                        <x-side-link :href="route('siswa.kuis.index')" :active="request()->routeIs('siswa.kuis.*')" icon="quiz">Kuis</x-side-link>
                        <x-side-link :href="route('siswa.raporku')" :active="request()->routeIs('siswa.raporku')" icon="award">Raporku</x-side-link>
                        <x-side-link :href="route('siswa.sertifikat')" :active="request()->routeIs('siswa.sertifikat')" icon="award">Sertifikat</x-side-link>
                    </div>
                </div>
            @elseif(auth()->user()->isGuru())
                <div>
                    <p class="px-3 mb-1.5 text-[11px] font-bold tracking-wider text-slate-500">UTAMA</p>
                    <x-side-link :href="route('guru.dashboard')" :active="request()->routeIs('guru.dashboard')" icon="home">Dashboard</x-side-link>
                </div>
                <div>
                    <p class="px-3 mb-1.5 text-[11px] font-bold tracking-wider text-slate-500">MENGAJAR</p>
                    <div class="space-y-1">
                        <x-side-link :href="route('guru.kelas.index')" :active="request()->routeIs('guru.kelas.*')" icon="users">Kelas Saya</x-side-link>
                        <x-side-link :href="route('guru.modul.index')" :active="request()->routeIs('guru.modul.*')" icon="book">Modul Saya</x-side-link>
                        <x-side-link :href="route('guru.quiz.index')" :active="request()->routeIs('guru.quiz.*')" icon="quiz">Bank Soal</x-side-link>
                    </div>
                </div>
            @else
                <div>
                    <p class="px-3 mb-1.5 text-[11px] font-bold tracking-wider text-slate-500">UTAMA</p>
                    <x-side-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.*')" icon="home">Dashboard</x-side-link>
                </div>
                <div>
                    <p class="px-3 mb-1.5 text-[11px] font-bold tracking-wider text-slate-500">KELOLA</p>
                    <div class="space-y-1">
                        <x-side-link :href="route('guru.kelas.index')" :active="request()->routeIs('guru.kelas.*')" icon="users">Kelas</x-side-link>
                        <x-side-link :href="route('guru.modul.index')" :active="request()->routeIs('guru.modul.*')" icon="book">Modul Default</x-side-link>
                        <x-side-link :href="route('guru.quiz.index')" :active="request()->routeIs('guru.quiz.*')" icon="quiz">Bank Soal</x-side-link>
                        <x-side-link :href="route('siswa.dashboard')" :active="request()->routeIs('siswa.*')" icon="award">Pratinjau Siswa</x-side-link>
                    </div>
                </div>
            @endif
            <div>
                <p class="px-3 mb-1.5 text-[11px] font-bold tracking-wider text-slate-500">AKUN</p>
                <div class="space-y-1">
                    <x-side-link :href="route('profile.edit')" :active="request()->routeIs('profile.*')" icon="user">Profil</x-side-link>
                </div>
            </div>
        </nav>

        <div class="p-3 border-t border-slate-100">
            <div class="flex items-center gap-3 px-3 py-2">
                <span class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-orange-100 text-orange-700 font-extrabold">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-bold text-slate-800 truncate">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-slate-500 truncate">{{ auth()->user()->email }}</p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="mt-1 w-full inline-flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-sm font-semibold text-slate-500 hover:bg-red-50 hover:text-red-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    Keluar
                </button>
            </form>
        </div>
    </aside>

    <!-- KONTEN -->
    <div class="flex-1 min-w-0 flex flex-col min-h-screen">
        <header class="lg:hidden sticky top-0 z-30 h-16 bg-white/90 backdrop-blur border-b border-slate-200 flex items-center gap-3 px-4">
            <button @click="SidebarTerbuka = true" class="p-2 -ml-1 rounded-xl text-slate-500 hover:bg-slate-100">
                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
            <span class="font-extrabold text-slate-900">KKA <span class="text-orange-500">Lab</span></span>
        </header>

        <main class="flex-1 w-full max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pt-6 sm:pt-8 pb-24 lg:pb-10">
            <x-toast />
            @isset($header)
                <div class="mb-6">{{ $header }}</div>
            @endisset
            {{ $slot }}
        </main>
    </div>
</div>
@auth
    @if(auth()->user()->isSiswa())
        <x-ai-assistant :module="isset($module) ? $module->slug : null" />
    @endif
@endauth
@stack('scripts')
</body>
</html>
