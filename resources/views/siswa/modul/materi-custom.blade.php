{{-- Render materi modul custom guru: teks polos diperkaya dengan penanda sederhana.
    Penanda yang didukung (tulis persis seperti ini di kolom materi):
    ## Judul Bagian   → subjudul
    [PRAKTIK] ...     → kotak praktik oranye (satu baris atau lanjutannya)
    [TUGAS] ...       → kotak tugas hijau
    - [ ] ...         → daftar periksa interaktif (checkbox siswa)
    Baris lain        → paragraf biasa. Selalu di-escape agar aman. --}}
@php
    $baris = preg_split('/\r\n|\r|\n/', $materi ?? '');
    $keluar = '';
    $dalamDaftar = false;
    $nomor = 0;
    foreach ($baris as $b) {
        $nomor++;
        $t = trim($b);
        if ($t === '') {
            if ($dalamDaftar) { $keluar .= '</ul>'; $dalamDaftar = false; }
            $keluar .= '';
            continue;
        }
        if (str_starts_with($t, '## ')) {
            if ($dalamDaftar) { $keluar .= '</ul>'; $dalamDaftar = false; }
            $keluar .= '<h4 class="font-extrabold text-slate-900 dark:text-white text-base pt-2">' . e(substr($t, 3)) . '</h4>';
        } elseif (str_starts_with($t, '[PRAKTIK]')) {
            if ($dalamDaftar) { $keluar .= '</ul>'; $dalamDaftar = false; }
            $isi = trim(substr($t, 9));
            $keluar .= '<div class="rounded-2xl border border-orange-200 dark:border-orange-500/30 bg-orange-50 dark:bg-orange-500/10 p-4"><p class="font-extrabold text-orange-800 dark:text-orange-300 text-sm">Praktik</p><p class="mt-1">' . e($isi) . '</p></div>';
        } elseif (str_starts_with($t, '[TUGAS]')) {
            if ($dalamDaftar) { $keluar .= '</ul>'; $dalamDaftar = false; }
            $isi = trim(substr($t, 7));
            $keluar .= '<div class="rounded-2xl border border-emerald-200 dark:border-emerald-500/30 bg-emerald-50 dark:bg-emerald-500/10 p-4"><p class="font-extrabold text-emerald-800 dark:text-emerald-300 text-sm">Tugas</p><p class="mt-1">' . e($isi) . '</p></div>';
        } elseif (str_starts_with($t, '- [ ]')) {
            if (! $dalamDaftar) { $keluar .= '<ul class="space-y-1.5">'; $dalamDaftar = true; }
            $isi = trim(substr($t, 5));
            $unik = 'cek-' . $nomor . '-' . substr(md5($isi), 0, 6);
            $keluar .= '<li><label for="' . $unik . '" class="flex items-start gap-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 cursor-pointer text-slate-700 dark:text-slate-200"><input id="' . $unik . '" type="checkbox" class="mt-1 accent-emerald-500 w-4 h-4"><span>' . e($isi) . '</span></label></li>';
        } else {
            if ($dalamDaftar) { $keluar .= '</ul>'; $dalamDaftar = false; }
            $keluar .= '<p>' . e($t) . '</p>';
        }
    }
    if ($dalamDaftar) { $keluar .= '</ul>'; }
@endphp
<div class="prose max-w-none text-sm text-slate-700 dark:text-slate-200 space-y-4">
    {!! $keluar !!}
</div>
