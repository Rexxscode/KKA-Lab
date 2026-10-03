<nav x-data="{ open: false }" class="bg-white/90 backdrop-blur border-b border-slate-200 sticky top-0 z-40">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center gap-3">
                <a href="{{ auth()->check() ? route('dashboard') : route('home') }}" class="flex items-center gap-2">
                    <span class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-orange-500 text-white font-extrabold">K</span>
                    <span class="font-extrabold text-slate-900">KKA <span class="text-orange-500">Lab</span></span>
                </a>
                <div class="hidden sm:flex items-center gap-1 ms-6">
                    @auth
                        @if(auth()->user()->isAdmin())
                            <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.*')">Admin</x-nav-link>
                            <x-nav-link :href="route('guru.dashboard')" :active="request()->routeIs('guru.dashboard')">Kelas</x-nav-link>
                            <x-nav-link :href="route('guru.quiz.index')" :active="request()->routeIs('guru.quiz.*')">Bank Soal</x-nav-link>
                        @elseif(auth()->user()->isSiswa())
                            <x-nav-link :href="route('siswa.dashboard')" :active="request()->routeIs('siswa.*')">Dashboard</x-nav-link>
                            <x-nav-link :href="route('siswa.sertifikat')" :active="request()->routeIs('siswa.sertifikat')">Sertifikat</x-nav-link>
                        @else
                            <x-nav-link :href="route('guru.dashboard')" :active="request()->routeIs('guru.*')">Kelas</x-nav-link>
                            <x-nav-link :href="route('guru.quiz.index')" :active="request()->routeIs('guru.quiz.*')">Bank Soal</x-nav-link>
                        @endif
                    @endauth
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6 gap-3">
                @auth
                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full {{ auth()->user()->isGuru() ? 'bg-orange-100 text-orange-700' : 'bg-emerald-100 text-emerald-700' }}">
                        {{ ucfirst(auth()->user()->role) }} • {{ auth()->user()->totalXp() }} XP
                    </span>
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 border border-slate-200 text-sm font-medium rounded-xl text-slate-600 bg-white hover:text-orange-600 transition">
                                <div>Profil</div>
                                <div class="ms-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                </div>
                            </button>
                        </x-slot>
                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')">Profil</x-dropdown-link>
                            <form method="POST" action="{{ route('logout') }}">@csrf
                                <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">Keluar</x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @else
                    <a href="{{ route('login') }}" class="text-sm font-semibold text-slate-600 hover:text-orange-600">Masuk</a>
                    <a href="{{ route('register') }}" class="kka-btn-primary !px-4 !py-2 text-sm">Daftar</a>
                @endauth
            </div>

            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="p-2 rounded-md text-slate-400 hover:text-orange-500 hover:bg-orange-50 transition">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24"><path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /><path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden border-t border-slate-200">
        <div class="pt-2 pb-3 space-y-1 px-4">
            @auth
                @if(auth()->user()->isAdmin())
                    <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.*')">Admin</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('guru.dashboard')" :active="request()->routeIs('guru.dashboard')">Kelas</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('guru.quiz.index')" :active="request()->routeIs('guru.quiz.*')">Bank Soal</x-responsive-nav-link>
                @elseif(auth()->user()->isSiswa())
                    <x-responsive-nav-link :href="route('siswa.dashboard')" :active="request()->routeIs('siswa.dashboard')">Dashboard</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('siswa.sertifikat')" :active="request()->routeIs('siswa.sertifikat')">Sertifikat</x-responsive-nav-link>
                @else
                    <x-responsive-nav-link :href="route('guru.dashboard')" :active="request()->routeIs('guru.dashboard')">Kelas</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('guru.quiz.index')" :active="request()->routeIs('guru.quiz.*')">Bank Soal</x-responsive-nav-link>
                @endif
                <x-responsive-nav-link :href="route('profile.edit')">Profil</x-responsive-nav-link>
                <form method="POST" action="{{ route('logout') }}">@csrf
                    <button class="text-sm font-semibold text-slate-600 py-2">Keluar ({{ Auth::user()->name }})</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="block py-2 text-sm font-semibold">Masuk</a>
                <a href="{{ route('register') }}" class="block py-2 text-sm font-semibold text-orange-600">Daftar</a>
            @endauth
        </div>
    </div>
</nav>
