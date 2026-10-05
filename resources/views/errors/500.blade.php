<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>500 — Gangguan Server | KKA Lab</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <script>try{var kkaT=localStorage.getItem('kka-theme');if(kkaT==='dark'||(!kkaT&&matchMedia('(prefers-color-scheme: dark)').matches))document.documentElement.classList.add('dark')}catch(e){}</script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body style="background:var(--color-page);font-family:'Plus Jakarta Sans',sans-serif">
<div class="min-h-screen flex items-center justify-center px-4">
    <div class="kka-card p-8 max-w-md w-full text-center">
        <span class="inline-flex items-center justify-center w-16 h-16 rounded-3xl bg-red-100 dark:bg-red-500/15 text-red-600 dark:text-red-400"><x-icon name="shield" class="w-8 h-8" /></span>
        <h1 class="mt-3 text-2xl font-extrabold text-slate-900 dark:text-white">Server Gangguan (500)</h1>
        <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">
            Maaf, ada yang tidak beres di sisi kami. Tim sudah diberitahu otomatis — coba muat ulang sebentar lagi.
        </p>
        <div class="mt-5 flex flex-col sm:flex-row gap-2 justify-center">
            <button onclick="window.location.reload()" class="kka-btn-primary text-sm">Muat Ulang</button>
            <a href="{{ route('home') }}" class="kka-btn-secondary text-sm">Beranda</a>
        </div>
    </div>
</div>
</body>
</html>
