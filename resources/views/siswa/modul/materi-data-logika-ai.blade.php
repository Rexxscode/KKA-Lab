<div class="prose max-w-none text-sm text-slate-700 dark:text-slate-200 space-y-4">
    <div>
        <h4 class="font-extrabold text-slate-900 dark:text-white">1. Dari Kode ke Data</h4>
        <p>Di Modul 5 kamu belajar bahasanya; di sini kamu memakai Python untuk <b>mengolah data</b> — pekerjaan sehari-hari AI. Polanya selalu sama: <b>kumpulkan data (list) → hitung → simpulkan</b>. Semua berjalan asli di browser lewat editor bawah.</p>
        <div class="mt-2 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/30 p-3">
            <p class="font-bold">Contoh:</p>
            <ul class="list-disc ps-5 mt-1 space-y-1 font-mono text-[13px]">
                <li>nilai = [70, 80, 90]</li>
                <li>rata = sum(nilai) / len(nilai) → 80.0</li>
            </ul>
        </div>
    </div>
    <div>
        <h4 class="font-extrabold text-slate-900 dark:text-white">2. Rata-rata: Ringkasan Satu Angka</h4>
        <p><b>Rata-rata</b> (mean) meringkas sekelompok angka jadi satu. Waspada: satu nilai ekstrem (outlier) bisa menariknya — nilai [50, 55, 100] rata-ratanya 68 padahal dua dari tiga siswa di bawah itu. Selalu intip sebarannya, jangan cuma rata-ratanya.</p>
        <div class="mt-2 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/30 p-3">
            <p class="font-bold">Contoh:</p>
            <ul class="list-disc ps-5 mt-1 space-y-1 font-mono text-[13px]">
                <li>sum([50, 55, 100]) / 3 → 68.33...</li>
                <li>max([50, 55, 100]) → 100 (tersangkanya!)</li>
            </ul>
        </div>
    </div>
    <div>
        <h4 class="font-extrabold text-slate-900 dark:text-white">3. Filter: Memilah yang Penting</h4>
        <p><b>Filter</b> memilih data yang memenuhi syarat dengan <b>if</b>. Operatornya: == (sama), != (beda), &gt;, &lt;, &gt;=, &lt;=, dan gabungan <b>and</b>/<b>or</b>. Contoh: "lulus jika nilai ≥ 80 DAN kehadiran ≥ 75%".</p>
        <div class="mt-2 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/30 p-3">
            <p class="font-bold">Contoh:</p>
            <ul class="list-disc ps-5 mt-1 space-y-1 font-mono text-[13px]">
                <li>[x for x in [70, 85, 90] if x &gt;= 80] → [85, 90]</li>
                <li>"BANDUNG".lower() → "bandung" (bersihkan data dulu!)</li>
            </ul>
        </div>
    </div>
    <div>
        <h4 class="font-extrabold text-slate-900 dark:text-white">4. Mini-Classifier Berbasis Aturan</h4>
        <p>Classifier paling sederhana adalah <b>aturan if/else buatan manusia</b>: jika nilai ≥ 80 maka "lulus", selain itu "remidi". Akurasinya = tebakan benar ÷ total. Aturan ini jujur dan cepat, tetapi rapuh: tiap pola baru harus ditulis manual.</p>
        <div class="mt-2 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/30 p-3">
            <p class="font-bold">Contoh:</p>
            <ul class="list-disc ps-5 mt-1 space-y-1 font-mono text-[13px]">
                <li>def label(n): return "lulus" if n &gt;= 80 else "remidi"</li>
                <li>[label(n) for n in [85, 60]] → ["lulus", "remidi"]</li>
            </ul>
        </div>
    </div>
    <div>
        <h4 class="font-extrabold text-slate-900 dark:text-white">5. Jembatan ke Machine Learning</h4>
        <p>Saat polanya terlalu rumit ditulis manual (mengenali wajah, menyaring spam gaya baru) tetapi <b>contohnya banyak</b>, saat itulah aturan diganti <b>model yang belajar dari data</b> — seperti COCO-SSD di Modul 2 yang dulu juga dilatih dari ratusan ribu foto. Dan ingat Modul 3: jika contohnya timpang, model ikut bias walau kodenya Python.</p>
        <div class="mt-2 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/30 p-3">
            <p class="font-bold">Contoh:</p>
            <ul class="list-disc ps-5 mt-1 space-y-1 font-mono text-[13px]">
                <li>Aturan "spam jika ada kata gratis" → diganti model terlatih 1000 email</li>
                <li>Dataset 95% satu kelompok → akurasi menipu (cek per kelompok!)</li>
            </ul>
        </div>
    </div>
</div>
