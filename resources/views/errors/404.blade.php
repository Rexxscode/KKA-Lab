<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>404 — Tidak Ketemu | KKA Lab</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body style="background:#FAFAFA;font-family:'Plus Jakarta Sans',sans-serif">
<div class="min-h-screen flex items-center justify-center px-4">
    <div class="kka-card p-8 max-w-md w-full text-center">
        <p class="text-5xl">🧭</p>
        <h1 class="mt-3 text-2xl font-extrabold text-slate-900">Halaman Tidak Ketemu (404)</h1>
        <p class="mt-2 text-sm text-slate-500">Alamatnya salah atau sudah dipindah. Coba lewat menu yang tersedia.</p>
        <div class="mt-5 flex flex-col sm:flex-row gap-2 justify-center">
            @auth
                <a href="{{ route('dashboard') }}" class="kka-btn-primary text-sm">← Kembali ke Dasbor</a>
            @else
                <a href="{{ route('home') }}" class="kka-btn-primary text-sm">← Beranda</a>
            @endauth
        </div>
    </div>
</div>
</body>
</html>
