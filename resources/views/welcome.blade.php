<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>KKALAB — Laboratorium Koding & Kecerdasan Artifisial</title>
    <meta name="description" content="KKALAB: lab belajar Koding & Kecerdasan Artifisial untuk SMA/SMK. 6 modul interaktif, demo kamera, kuis, XP, badge, dan sertifikat — 100% di browser." />
    <meta name="keywords" content="KKALAB, koding, kecerdasan artifisial, AI untuk sekolah, belajar AI, SMA, SMK, TensorFlow.js, kuis AI, sertifikat" />
    <meta name="author" content="KKALAB" />
    <meta name="theme-color" content="#f97316" />
    <meta name="robots" content="index, follow" />
    <meta property="og:type" content="website" />
    <meta property="og:site_name" content="KKALAB" />
    <meta property="og:title" content="KKALAB — Laboratorium Koding & Kecerdasan Artifisial" />
    <meta property="og:description" content="Belajar AI & koding hands-on di browser: 6 modul interaktif, demo kamera, kuis, XP, dan sertifikat otomatis." />
    <meta property="og:image" content="{{ asset('logo.png') }}" />
    <meta name="twitter:card" content="summary" />
    <meta name="twitter:title" content="KKALAB — Laboratorium Koding & Kecerdasan Artifisial" />
    <meta name="twitter:description" content="Belajar AI & koding hands-on di browser untuk SMA/SMK." />
    <meta name="twitter:image" content="{{ asset('logo.png') }}" />
    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}" />
    <x-favicon-links />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script>
    try { var kkaT = localStorage.getItem('kka-theme'); if (kkaT === 'dark' || (!kkaT && matchMedia('(prefers-color-scheme: dark)').matches)) document.documentElement.classList.add('dark'); } catch (e) {}
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body style="background:var(--color-page);font-family:'Plus Jakarta Sans',sans-serif">
    <style>
        @keyframes kka-nav-masuk {
            from { opacity: 0; transform: translateY(-16px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .kka-nav-animasi { animation: kka-nav-masuk 500ms ease-out both; }
        .kka-nav-logo { transition: transform 200ms ease-out; }
        .kka-nav-logo:hover { transform: scale(1.04) rotate(-2deg); }
    </style>
    <nav x-data="{ sudahScroll: false }" @scroll.window="sudahScroll = window.scrollY > 24"
         class="kka-nav-animasi sticky top-0 z-40 transition-all duration-300"
         :class="sudahScroll ? 'bg-white/90 dark:bg-slate-900/90 backdrop-blur border-b border-slate-200 dark:border-slate-800 shadow-sm py-3' : 'bg-transparent border-b border-transparent py-5'">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between">
            <a href="{{ route('home') }}" class="kka-nav-logo flex items-center gap-2" aria-label="KKALAB - Beranda">
                <img src="{{ asset('logo.png') }}" alt="Logo KKALAB" class="w-10 h-10 rounded-2xl" width="40" height="40" />
                <span><span class="block font-extrabold text-slate-900 dark:text-white dark:text-white leading-none">KKALAB</span><span class="block text-xs text-slate-500 dark:text-slate-400 dark:text-slate-400">Koding & Kecerdasan Artifisial</span></span>
            </a>
            <div class="flex items-center gap-2">
                <x-theme-toggle />
                @auth
                    <a href="{{ route('dashboard') }}" class="kka-btn-primary !px-5 !py-2 text-sm hover:-translate-y-0.5">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="kka-btn-primary !px-5 !py-2 text-sm hover:-translate-y-0.5">Masuk</a>
                @endauth
            </div>
        </div>
    </nav>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

    <section class="py-10">
        <div class="max-w-3xl">
            <span class="kka-badge">Untuk SMA/SMK • Kurikulum KKA • Praktik di Browser</span>
            <h1 class="mt-4 text-4xl sm:text-5xl font-extrabold text-slate-900 dark:text-white dark:text-white leading-tight">Belajar AI & Koding <span class="text-orange-500">hands-on</span> di browser.</h1>
            <p class="mt-4 text-slate-600 dark:text-slate-300">6 modul interaktif: konsep AI → cara kerja model → bias data → etika & hoaks → Python dasar → data & logika AI. Kuis minimal <b>80</b> untuk lulus, kumpulkan XP & badge, cetak sertifikat otomatis.</p>
            <div class="mt-6 flex flex-wrap gap-3">
                @auth
                    <x-button href="{{ route('dashboard') }}">Ke Dashboard</x-button>
                    <div class="w-full text-xs text-slate-500 dark:text-slate-400">Kamu login sebagai <b>{{ auth()->user()->name }}</b> ({{ ucfirst(auth()->user()->role) }}). Tombol login tidak bisa ganti akun — <form method="POST" action="{{ route('logout') }}" class="inline">@csrf<button class="underline font-bold text-orange-600">keluar dulu</button></form> bila ingin masuk akun lain.</div>
                @else
                    <x-button href="{{ route('login') }}">Masuk sebagai Siswa</x-button>
                    <x-button variant="secondary" href="{{ route('login') }}">Portal Guru</x-button>
                @endauth
            </div>
            <div class="mt-6 grid grid-cols-3 gap-3 text-center">
                <div class="kka-card !p-4"><p class="kka-stat text-orange-600 dark:text-orange-400">{{ $modules->count() ?: 6 }}</p><p class="text-xs text-slate-500 dark:text-slate-400">Modul interaktif</p></div>
                <div class="kka-card !p-4"><p class="kka-stat text-emerald-600 dark:text-emerald-400">Hingga {{ $xpMaks ?? 1010 }}</p><p class="text-xs text-slate-500 dark:text-slate-400">Total XP</p></div>
                <div class="kka-card !p-4"><p class="kka-stat text-slate-900 dark:text-white">{{ $jmlSoal ?? 90 }}</p><p class="text-xs text-slate-500 dark:text-slate-400">Soal berpembahasan</p></div>
            </div>
        </div>
    </section>

    <section class="py-8">
        <h2 class="text-2xl font-extrabold text-slate-900 dark:text-white">{{ $modules->count() ?: 6 }} Modul KKA</h2>
        <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">Dari konsep → data kritis → etika → Python untuk AI.</p>
        <div class="mt-5 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 items-stretch">
            @forelse($modules as $m)
                <x-card :title="$m->title" :subtitle="$m->description">
                    <div class="mt-auto pt-4 flex items-center justify-between gap-2">
                        <span class="kka-badge shrink-0">+{{ $m->xp_reward }} XP</span>
                        <a href="{{ route('login') }}" class="text-sm font-bold text-orange-500 hover:text-orange-600 whitespace-nowrap py-1">Mulai →</a>
                    </div>
                </x-card>
            @empty
                <x-card title="Modul 1: Apa itu AI?" subtitle="Konsep dasar AI & ML dengan visual interaktif + kuis.">
                    <div class="mt-auto pt-4 flex items-center justify-between gap-2">
                        <span class="kka-badge shrink-0">+100 XP</span>
                        <a href="{{ route('login') }}" class="text-sm font-bold text-orange-500 hover:text-orange-600 whitespace-nowrap py-1">Mulai →</a>
                    </div>
                </x-card>
                <x-card title="Modul 2: Cara Kerja Model AI" subtitle="Konsep model AI, deteksi objek & bedanya model pretrained.">
                    <div class="mt-auto pt-4 flex items-center justify-between gap-2">
                        <span class="kka-badge shrink-0">+200 XP</span>
                        <a href="{{ route('login') }}" class="text-sm font-bold text-orange-500 hover:text-orange-600 whitespace-nowrap py-1">Mulai →</a>
                    </div>
                </x-card>
                <x-card title="Modul 3: Bias Data" subtitle="Simulasi dataset tidak seimbang dengan Alpine.js.">
                    <div class="mt-auto pt-4 flex items-center justify-between gap-2">
                        <span class="kka-badge shrink-0">+150 XP</span>
                        <a href="{{ route('login') }}" class="text-sm font-bold text-orange-500 hover:text-orange-600 whitespace-nowrap py-1">Mulai →</a>
                    </div>
                </x-card>
                <x-card title="Modul 4: Etika & Hoaks AI" subtitle="Studi kasus deepfake & kuis literasi digital.">
                    <div class="mt-auto pt-4 flex items-center justify-between gap-2">
                        <span class="kka-badge shrink-0">+150 XP</span>
                        <a href="{{ route('login') }}" class="text-sm font-bold text-orange-500 hover:text-orange-600 whitespace-nowrap py-1">Mulai →</a>
                    </div>
                </x-card>
                <x-card title="Modul 5: Python Dasar untuk AI" subtitle="Python asli di browser: print, list, loop, fungsi, dict.">
                    <div class="mt-auto pt-4 flex items-center justify-between gap-2">
                        <span class="kka-badge shrink-0">+150 XP</span>
                        <a href="{{ route('login') }}" class="text-sm font-bold text-orange-500 hover:text-orange-600 whitespace-nowrap py-1">Mulai →</a>
                    </div>
                </x-card>
                <x-card title="Modul 6: Data & Logika AI" subtitle="Rata-rata, filter data, dan mini-classifier aturan.">
                    <div class="mt-auto pt-4 flex items-center justify-between gap-2">
                        <span class="kka-badge shrink-0">+200 XP</span>
                        <a href="{{ route('login') }}" class="text-sm font-bold text-orange-500 hover:text-orange-600 whitespace-nowrap py-1">Mulai →</a>
                    </div>
                </x-card>
            @endforelse
        </div>
    </section>

    <section class="py-8 grid lg:grid-cols-3 gap-4">
        <x-card title="Untuk Siswa" subtitle="Gabung kelas dengan 6 digit kode, kerjakan modul, kumpulkan badge.">
            <x-button href="{{ route('register') }}" class="w-full">Daftar sebagai Siswa</x-button>
        </x-card>
        <x-card title="Untuk Guru" subtitle="Buat kelas, generate kode unik, pantau progres & ekspor CSV nilai.">
            <x-button href="{{ route('login') }}" class="w-full">Masuk Portal Guru</x-button>
        </x-card>
        <x-card title="Sertifikat" subtitle="Otomatis terbit jika 6 modul lulus (skor ≥ 80) + lulus ujian akhir. Siap cetak PDF via browser.">
            <span class="kka-badge">Syarat: 6/6 lulus • ujian lulus</span>
        </x-card>
    </section>

    <section class="py-8">
        <h2 class="text-2xl font-extrabold text-slate-900 dark:text-white">Cara mulai</h2>
        <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">Empat langkah, tanpa instal apa pun. Semua jalan di browser HP atau laptop.</p>
        <div class="mt-5 grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <x-card title="1. Daftar" subtitle="Buat akun siswa gratis dengan email."><p class="text-xs text-slate-500 dark:text-slate-400">Satu menit, langsung masuk dasbor.</p></x-card>
            <x-card title="2. Gabung kelas" subtitle="Minta kode 6 digit ke gurumu."><p class="text-xs text-slate-500 dark:text-slate-400">Urutan modul dan soal mengikuti kelasmu.</p></x-card>
            <x-card title="3. Kerjakan berurutan" subtitle="Baca bahan, coba simulasi, lalu kuis."><p class="text-xs text-slate-500 dark:text-slate-400">Soal dan opsi diacak setiap percobaan.</p></x-card>
            <x-card title="4. Lulus & cetak" subtitle="Skor ≥80 di 6 modul + lulus ujian akhir."><p class="text-xs text-slate-500 dark:text-slate-400">Sertifikat PDF terbit otomatis.</p></x-card>
        </div>
    </section>

    <section class="py-4">
        <div class="rounded-3xl bg-slate-900 p-8 sm:p-10 text-white overflow-hidden">
            <div class="flex flex-wrap items-end justify-between gap-6">
                <div class="max-w-xl">
                    <p class="text-xs font-bold tracking-widest text-emerald-400">6 MODUL • 90 SOAL • 950 XP</p>
                    <h2 class="mt-2 text-2xl sm:text-3xl font-extrabold">Selesaikan 6 modul, bawa pulang sertifikat.</h2>
                    <p class="mt-2 text-sm sm:text-base text-slate-300">Mulai dari konsep AI, data & etika, sampai nulis Python asli di browser. Kuis lulus di skor 80.</p>
                </div>
                <div class="flex flex-wrap gap-3">
                    @auth
                        <a href="{{ route('dashboard') }}" class="inline-block px-7 py-3 rounded-2xl bg-orange-500 hover:bg-orange-600 text-white font-extrabold text-sm transition">Ke Dashboard</a>
                    @else
                        <a href="{{ route('register') }}" class="inline-block px-7 py-3 rounded-2xl bg-orange-500 hover:bg-orange-600 text-white font-extrabold text-sm transition">Daftar gratis</a>
                        <a href="{{ route('login') }}" class="inline-block px-7 py-3 rounded-2xl border border-slate-600 text-slate-200 font-bold text-sm hover:border-slate-400 transition">Masuk</a>
                    @endauth
                </div>
            </div>
        </div>
    </section>

    <section class="py-8">
        <h2 class="text-2xl font-extrabold text-slate-900 dark:text-white text-center">Kontak Kami</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400 text-center mt-1">Punya pertanyaan, saran, atau ingin bekerja sama? Hubungi tim KKALAB.</p>
        <div class="mt-5 grid sm:grid-cols-3 gap-4">
            <x-card title="Email" subtitle="halo@kkalab.sch.id"><p class="text-xs text-slate-500 dark:text-slate-400 flex items-center gap-1.5"><x-icon name="mail" class="w-4 h-4 text-orange-500" />Kami balas dalam 1–2 hari kerja.</p></x-card>
            <x-card title="Lokasi" subtitle="Indonesia"><p class="text-xs text-slate-500 dark:text-slate-400 flex items-center gap-1.5"><x-icon name="pin" class="w-4 h-4 text-orange-500" />Untuk SMA/SMK di seluruh Indonesia.</p></x-card>
            <x-card title="Respon" subtitle="Cepat & ramah"><p class="text-xs text-slate-500 dark:text-slate-400 flex items-center gap-1.5"><x-icon name="chat" class="w-4 h-4 text-orange-500" />Siswa, guru, dan sekolah dipersilakan bertanya.</p></x-card>
        </div>
    </section>
</div>

<footer class="border-t border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 grid gap-8 sm:grid-cols-2 lg:grid-cols-4">
        <div>
            <div class="flex items-center gap-2">
                <img src="{{ asset('logo.png') }}" alt="Logo KKALAB" class="w-9 h-9 rounded-xl" width="36" height="36" />
                <p class="font-extrabold text-slate-900 dark:text-white">KKA <span class="text-orange-500">Lab</span></p>
            </div>
            <p class="mt-3 text-xs text-slate-500 dark:text-slate-400">Laboratorium Koding & Kecerdasan Artifisial untuk pembelajaran AI di sekolah.</p>
        </div>
        <div>
            <p class="font-bold text-sm text-slate-900 dark:text-white">Modul</p>
            <ul class="mt-3 space-y-2 text-xs text-slate-500 dark:text-slate-400">
                <li><a href="{{ route('login') }}" class="hover:text-orange-600">Apa itu AI?</a></li>
                <li><a href="{{ route('login') }}" class="hover:text-orange-600">Cara Kerja Model AI</a></li>
                <li><a href="{{ route('login') }}" class="hover:text-orange-600">Bias Data</a></li>
                <li><a href="{{ route('login') }}" class="hover:text-orange-600">Etika & Hoaks AI</a></li>
                <li><a href="{{ route('login') }}" class="hover:text-orange-600">Python Dasar untuk AI</a></li>
                <li><a href="{{ route('login') }}" class="hover:text-orange-600">Data & Logika AI</a></li>
            </ul>
        </div>
        <div>
            <p class="font-bold text-sm text-slate-900 dark:text-white">Akun</p>
            <ul class="mt-3 space-y-2 text-xs text-slate-500 dark:text-slate-400">
                <li><a href="{{ route('login') }}" class="hover:text-orange-600">Masuk</a></li>
                <li><a href="{{ route('register') }}" class="hover:text-orange-600">Daftar sebagai Siswa</a></li>
                <li><a href="{{ route('login') }}" class="hover:text-orange-600">Portal Guru</a></li>
            </ul>
        </div>
        <div>
            <p class="font-bold text-sm text-slate-900 dark:text-white">Kontak</p>
            <ul class="mt-3 space-y-2 text-xs text-slate-500 dark:text-slate-400">
                <li>halo@kkalab.sch.id</li>
                <li>Indonesia</li>
            </ul>
        </div>
    </div>
    <div class="border-t border-slate-100 dark:border-slate-800">
        <p class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 text-center text-xs text-slate-500 dark:text-slate-400">KKALAB • Untuk pembelajaran AI di sekolah</p>
    </div>
</footer>
</body>
</html>


