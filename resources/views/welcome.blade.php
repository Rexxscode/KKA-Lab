<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>KKA Lab — Laboratorium Koding & Kecerdasan Artifisial</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body style="background:#FAFAFA;font-family:'Plus Jakarta Sans',sans-serif" x-data="{
    demo: 'Halo! Aku AI. Tunjukkan gambar ke kamera, aku belajar mengenalinya — semua di browser-mu!',
    ujiIdx: 0,
    uji() {
        const hasil = [
            'Model menebak: 92% kemungkinan ini gambar Kucing. (Simulasi — inferensi asli ada di Modul 2)',
            'Model menebak: 87% kemungkinan ini gambar Botol. (Simulasi — inferensi asli ada di Modul 2)',
            'Model menebak: 95% kemungkinan ini gambar Telapak Tangan. (Simulasi — inferensi asli ada di Modul 2)',
        ];
        this.demo = hasil[this.ujiIdx % hasil.length];
        this.ujiIdx++;
    }
}">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <nav class="flex items-center justify-between py-5">
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center justify-center w-10 h-10 rounded-2xl bg-orange-500 text-white font-extrabold text-xl">K</span>
            <div><p class="font-extrabold text-slate-900 leading-none">KKA <span class="text-orange-500">Lab</span></p><p class="text-xs text-slate-500">Koding & Kecerdasan Artifisial</p></div>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('login') }}" class="text-sm font-semibold text-slate-600 hover:text-orange-600">Masuk</a>
            <a href="{{ route('register') }}" class="kka-btn-primary !px-4 !py-2 text-sm">Mulai Gratis</a>
        </div>
    </nav>

    <section class="grid lg:grid-cols-2 gap-8 items-center py-10">
        <div>
            <span class="kka-badge">Untuk SMA/SMK • Kurikulum KKA • 100% Praktik di Browser</span>
            <h1 class="mt-4 text-4xl sm:text-5xl font-extrabold text-slate-900 leading-tight">Belajar AI & Koding <span class="text-orange-500">hands-on</span> di browser.</h1>
            <p class="mt-4 text-slate-600">4 modul interaktif: apa itu AI → latih model webcam → bias data → etika & hoaks. Privasi terjaga: gambar tidak dikirim ke server. Kuis minimal <b>80</b> untuk lulus, kumpulkan XP & badge, cetak sertifikat otomatis.</p>
            <div class="mt-6 flex flex-wrap gap-3">
                <x-button href="{{ route('register') }}">Masuk sebagai Siswa</x-button>
                <x-button variant="secondary" href="{{ route('login') }}">Portal Guru</x-button>
            </div>
            <p class="mt-2 text-xs text-slate-400">Akun guru dibuatkan oleh admin — siswa bisa daftar gratis.</p>
            <div class="mt-6 grid grid-cols-3 gap-3 text-center">
                <div class="kka-card !p-4"><p class="text-2xl font-extrabold text-orange-500">4</p><p class="text-xs text-slate-500">Modul interaktif</p></div>
                <div class="kka-card !p-4"><p class="text-2xl font-extrabold text-emerald-500">600</p><p class="text-xs text-slate-500">Total XP</p></div>
                <div class="kka-card !p-4"><p class="text-2xl font-extrabold text-slate-900">PDF</p><p class="text-xs text-slate-500">Sertifikat otomatis</p></div>
            </div>
        </div>
        <x-card title="Simulasi AI Mini" subtitle="Demo simulasi bergilir — inferensi kamera asli ada di Modul 2 (KNN lokal).">
            <div class="rounded-2xl bg-slate-900 text-emerald-300 p-5 font-mono text-sm min-h-28" x-text="demo"></div>
            <div class="mt-4 flex flex-wrap gap-2">
                <button class="kka-btn-secondary !py-2 text-sm" @click="demo='Hebat! Model AI-mu berhasil dilatih! Coba tambah contoh gambar lain ya.'">Latih Model</button>
                <button class="kka-btn-secondary !py-2 text-sm" @click="uji()">Uji Model</button>
                <button class="kka-btn-secondary !py-2 text-sm" @click="demo='Privasi aman: tidak ada gambar yang keluar dari perangkatmu.'">Cek Privasi</button>
            </div>
            <p class="mt-2 text-[11px] text-slate-400">Catatan: tombol Uji di sini hanya simulasi teks bergilir (Kucing → Botol → Tangan), bukan hasil kamera.</p>
        </x-card>
    </section>

    <section class="py-8">
        <h2 class="text-2xl font-extrabold text-slate-900">4 Modul KKA</h2>
        <p class="text-slate-500 text-sm mt-1">Urut dari konsep → praktik → kritis → etis.</p>
        <div class="mt-5 grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @forelse($modules as $m)
                <x-card :title="$m->title" :subtitle="$m->description">
                    <div class="flex items-center justify-between">
                        <span class="kka-badge">+{{ $m->xp_reward }} XP</span>
                        <a href="{{ route('login') }}" class="text-sm font-bold text-orange-500 hover:text-orange-600">Mulai →</a>
                    </div>
                </x-card>
            @empty
                <x-card title="Modul 1: Apa itu AI?" subtitle="Konsep dasar AI & ML dengan visual interaktif + kuis." />
                <x-card title="Modul 2: Latih AI-mu" subtitle="Klasifikasi gambar via webcam (KNN lokal) + mode tanpa kamera." />
                <x-card title="Modul 3: Bias Data" subtitle="Simulasi dataset tidak seimbang dengan Alpine.js." />
                <x-card title="Modul 4: Etika & Hoaks AI" subtitle="Studi kasus deepfake & kuis literasi digital." />
            @endforelse
        </div>
    </section>

    <section class="py-8 grid lg:grid-cols-3 gap-4">
        <x-card title="Untuk Siswa" subtitle="Gabung kelas dengan 6 digit kode, kerjakan modul, kumpulkan badge.">
            <x-button href="{{ route('register') }}" class="w-full">Daftar sebagai Siswa</x-button>
        </x-card>
        <x-card title="Untuk Guru" subtitle="Akun dibuatkan admin. Buat kelas, generate kode unik, pantau progres & ekspor CSV nilai.">
            <x-button href="{{ route('login') }}" class="w-full">Masuk Portal Guru</x-button>
        </x-card>
        <x-card title="Sertifikat" subtitle="Otomatis terbit jika 4 modul lulus (skor ≥ 80). Siap cetak PDF via browser.">
            <span class="kka-badge">Syarat: 4/4 lulus • ≥80</span>
        </x-card>
    </section>

    <footer class="py-8 text-center text-xs text-slate-400">KKA Lab • Dibangun dengan Laravel Blade + Alpine.js + Tailwind + TensorFlow.js (client-side) • Untuk pembelajaran AI di sekolah</footer>
</div>
</body>
</html>
