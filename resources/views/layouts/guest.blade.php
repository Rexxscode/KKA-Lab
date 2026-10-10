<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'KKALAB') }}</title>
        <meta name="description" content="Masuk ke KKALAB: lab belajar Koding & Kecerdasan Artifisial untuk SMA/SMK." />
        <link rel="icon" type="image/png" href="{{ asset('logo.png') }}" />
        <x-favicon-links />

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />

        <!-- Tema awal tanpa kedip: localStorage dulu, lalu sistem -->
        <script>
        try { var kkaT = localStorage.getItem('kka-theme'); if (kkaT === 'dark' || (!kkaT && matchMedia('(prefers-color-scheme: dark)').matches)) document.documentElement.classList.add('dark'); } catch (e) {}
        </script>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-slate-900 dark:text-slate-100 antialiased" style="background:var(--color-page)">
        <x-toast />
        <div class="min-h-screen min-h-dvh flex flex-col justify-center items-center px-4 sm:px-0 py-10 sm:py-12">
            <div class="flex items-center gap-3">
                <a href="/" class="flex items-center gap-2.5">
                    <img src="{{ asset('logo.png') }}" alt="Logo KKALAB" class="w-11 h-11 rounded-2xl" width="44" height="44" />
                    <span class="text-left">
                        <span class="block font-extrabold text-slate-900 dark:text-white leading-none">KKALAB</span>
                        <span class="block text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">Koding & Kecerdasan Artifisial</span>
                    </span>
                </a>
                <x-theme-toggle />
            </div>

            <div class="w-full max-w-md mx-auto mt-6 px-6 py-6 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden rounded-2xl">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
