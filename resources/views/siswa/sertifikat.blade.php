@push('head')
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    <style>
        @media print {
            @page {
                size: A4 landscape;
                margin: 0;
            }
        }
    </style>
@endpush

<x-sidebar-layout>
    <x-slot name="header">
        <div class="no-print">
            <h2 class="font-extrabold text-xl text-slate-900 dark:text-white">Sertifikat Kelulusan</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400">Terbit otomatis jika {{ $modules->count() }} modul wajib lulus (skor ≥80) + lulus ujian akhir.</p>
        </div>
    </x-slot>

    <div class="certificate-page py-8">
        @if($eligible)
            <div class="no-print flex flex-wrap items-center gap-2 mb-4">
                <button onclick="window.print()" class="kka-btn-primary">Cetak / Simpan PDF</button>
                <a href="{{ route('siswa.dashboard') }}" class="kka-btn-secondary">Kembali</a>
                <span class="text-xs text-slate-500 dark:text-slate-400 sm:ml-2">Format A4 landscape, tanpa margin.</span>
            </div>

            <div class="certificate-shell">
                <div class="cert-sheet" aria-label="Sertifikat kelulusan {{ auth()->user()->name }}">
                    <svg class="cert-bg" viewBox="0 0 1400 990" preserveAspectRatio="none" aria-hidden="true" focusable="false">
                        <defs>
                            <linearGradient id="certOrange" x1="0" y1="0" x2="1" y2="1">
                                <stop offset="0" stop-color="#f7a95f" />
                                <stop offset="1" stop-color="#ef8f45" />
                            </linearGradient>
                            <linearGradient id="certOrangeDeep" x1="0" y1="0" x2="1" y2="1">
                                <stop offset="0" stop-color="#f09a4e" />
                                <stop offset="1" stop-color="#e8863a" />
                            </linearGradient>
                            <linearGradient id="certGray" x1="0" y1="0" x2="1" y2="1">
                                <stop offset="0" stop-color="#e4e2e0" />
                                <stop offset="1" stop-color="#d3d1cf" />
                            </linearGradient>
                            <linearGradient id="certDark" x1="0" y1="0" x2="1" y2="1">
                                <stop offset="0" stop-color="#454443" />
                                <stop offset="1" stop-color="#393837" />
                            </linearGradient>
                        </defs>

                        <rect width="1400" height="990" fill="#ffffff" />

                        {{-- Gelombang oranye kanan atas: pita abu-abu, oranye tua, lalu oranye utama. --}}
                        <path d="M385 0H1400V295C1330 340 1240 370 1120 362 1010 360 920 338 840 290 760 243 680 188 600 158Z" fill="url(#certGray)" />
                        <path d="M385 0H1400V276C1330 320 1240 350 1120 342 1010 340 920 319 840 271 760 224 680 169 600 139Z" fill="url(#certOrangeDeep)" />
                        <path d="M385 0H1400V256C1330 300 1240 330 1120 322 1010 320 920 300 840 252 760 205 680 150 600 120Z" fill="url(#certOrange)" />

                        {{-- Sudut kiri atas: bidang gelap dengan garis pemisah putih. --}}
                        <path d="M0 0H320L590 112C470 100 330 150 230 190 140 228 60 250 0 266Z" fill="url(#certDark)" />
                        <path d="M320 0 590 112C470 100 330 150 230 190 140 228 60 250 0 266" fill="none" stroke="#ffffff" stroke-width="6" />

                        {{-- Sudut kiri bawah. --}}
                        <path d="M0 790C120 880 220 960 300 990H0Z" fill="url(#certGray)" />
                        <path d="M0 828C90 892 170 958 232 990H0Z" fill="url(#certOrange)" />

                        {{-- Sudut kanan bawah. --}}
                        <path d="M1400 790C1300 890 1210 960 1120 990H1400Z" fill="url(#certGray)" />
                        <path d="M1400 828C1320 892 1250 958 1188 990H1400Z" fill="url(#certOrange)" />
                    </svg>

                    <div class="cert-brand">
                        <img src="{{ asset('logo.png') }}" alt="Logo KKA LAB" width="512" height="512" />
                        <div>
                            <p class="cert-brand-title">KKA<br>LAB</p>
                            <p class="cert-brand-sub">Laboratorium Koding<br>&amp; Kecerdasan<br>Artifisial</p>
                        </div>
                    </div>

                    <div class="cert-heading">
                        <h1>CERTIFICATE</h1>
                        <p>KKA LAB &middot; Laboratorium Koding &amp; Kecerdasan<br>Artifisial</p>
                    </div>

                    <div class="cert-body">
                        <p class="cert-given"><span>Diberikan kepada</span></p>

                        @php $panjangNama = mb_strlen(auth()->user()->name); @endphp
                        <h2 class="cert-name" style="font-size: {{ $panjangNama > 26 ? '3.2cqw' : ($panjangNama > 20 ? '3.9cqw' : '4.6cqw') }}">{{ auth()->user()->name }}</h2>

                        <p class="cert-desc">
                            atas keberhasilan menyelesaikan <strong>{{ $modules->count() }} Modul Pembelajaran KKALAB</strong> dengan baik, serta atas dedikasi, ketekunan, dan komitmen yang telah ditunjukkan selama mengikuti seluruh rangkaian pembelajaran.
                        </p>

                        <p class="cert-meta">
                            Kode Sertifikat: <strong>{{ $certificate->code }}</strong> &middot;
                            Diterbitkan {{ $certificate->issued_at->translatedFormat('d F Y') }} &middot;
                            Total XP {{ $totalXp }}<br>
                            Verifikasi keaslian:
                            <a href="{{ route('sertifikat.verifikasi', $certificate->code) }}">{{ route('sertifikat.verifikasi', $certificate->code) }}</a>
                        </p>
                    </div>

                    <div class="cert-signatures">
                        <div class="cert-sign {{ $certificate->guru_sign_path ? 'has-ttd' : '' }}">
                            <p class="cert-sign-role">Tanda Tangan Instruktur</p>
                            <div class="cert-sign-line">
                                @if($certificate->guru_sign_path)
                                    <img src="{{ Storage::url($certificate->guru_sign_path) }}" alt="Tanda tangan instruktur" class="cert-sign-img" />
                                @endif
                            </div>
                            <p class="cert-sign-name">{{ $certificate->guru_nama ?? '—' }}</p>
                        </div>

                        <div class="cert-sign {{ $certificate->super_admin_sign_path ? 'has-ttd' : '' }}">
                            <p class="cert-sign-role">Tanda Tangan Direktur</p>
                            <div class="cert-sign-line">
                                @if($certificate->super_admin_sign_path)
                                    <img src="{{ Storage::url($certificate->super_admin_sign_path) }}" alt="Tanda tangan direktur" class="cert-sign-img" />
                                @endif
                            </div>
                            <p class="cert-sign-name">{{ $certificate->super_admin_nama ?? '—' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="grid lg:grid-cols-3 gap-4 items-start">
                <x-card title="Belum memenuhi syarat" subtitle="Selesaikan semua modul wajib (skor ≥80), lalu lulus ujian akhir. Modul buatan guru bersifat bonus." class="lg:col-span-2">
                    <div class="space-y-2">
                        @foreach($modules as $m)
                            @php $p = $progress->get($m->id); @endphp
                            <div class="flex items-center justify-between gap-3 text-sm sm:text-[15px] border border-slate-200 dark:border-slate-700 rounded-2xl px-4 sm:px-5 py-3">
                                <span class="font-semibold text-slate-700 dark:text-slate-200">{{ $m->title }}</span>
                                @if($p && $p->is_completed)
                                    <span class="font-bold text-emerald-600 dark:text-emerald-400 whitespace-nowrap">✓ Selesai ({{ $p->quiz_score }})</span>
                                @elseif($p)
                                    <span class="kka-status-warn whitespace-nowrap">Dikerjakan (skor {{ $p->quiz_score }})</span>
                                @else
                                    <span class="kka-status-idle whitespace-nowrap">Belum mulai</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </x-card>
                <x-card title="Progres Sertifikat" subtitle="Semangat, sedikit lagi!" class="lg:sticky lg:top-6 self-start">
                    <p class="text-4xl font-extrabold text-emerald-600 dark:text-emerald-400">{{ $lulus }}<span class="text-4xl font-extrabold text-slate-400">/{{ $modules->count() }}</span></p>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">modul lulus dengan skor ≥80</p>
                    <div class="mt-3 h-2.5 rounded-full bg-slate-100 dark:bg-slate-700 overflow-hidden" role="progressbar" aria-valuemin="0" aria-valuemax="{{ $modules->count() }}" aria-valuenow="{{ $lulus }}">
                        <div class="h-full bg-emerald-500 rounded-full transition-all" style="width: {{ $modules->count() ? round($lulus / $modules->count() * 100) : 0 }}%"></div>
                    </div>
                    <div class="mt-4 space-y-2 text-sm">
                        <div class="flex items-center justify-between rounded-xl bg-slate-50 dark:bg-slate-800/70 border border-slate-200 dark:border-slate-700 px-4 py-2.5">
                            <span class="font-semibold text-slate-600 dark:text-slate-300">Total XP-mu</span>
                            <span class="font-extrabold text-orange-500">{{ $totalXp }} XP</span>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-2 mt-4">
                        <a href="{{ route('siswa.dashboard') }}" class="kka-btn-secondary !py-2 text-sm text-center">Lanjut Belajar</a>
                        <a href="{{ route('siswa.ujian.index') }}" class="kka-btn-primary !py-2 text-sm text-center">Ujian Akhir</a>
                    </div>
                </x-card>
            </div>
        @endif
    </div>
</x-sidebar-layout>
