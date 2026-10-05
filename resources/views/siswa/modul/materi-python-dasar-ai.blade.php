<div class="prose max-w-none text-sm text-slate-700 dark:text-slate-200 space-y-4">
    <div>
        <h4 class="font-extrabold text-slate-900 dark:text-white">1. Program Pertamamu: print</h4>
        <p>Python adalah <b>bahasa pembuatan AI</b>: pustaka AI populer (TensorFlow, PyTorch) semuanya dipakai lewat Python. Perintah paling dasar adalah <b>print</b> — menampilkan teks ke layar. Tulis persis <b>print("Halo AI")</b> lalu tekan Jalankan di editor bawah.</p>
        <div class="mt-2 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/30 p-3">
            <p class="font-bold">Contoh:</p>
            <ul class="list-disc ps-5 mt-1 space-y-1 font-mono text-[13px]">
                <li>print("Halo AI") → Halo AI</li>
                <li>print(2 + 3) → 5</li>
                <li>print("Belajar", "Python") → Belajar Python</li>
            </ul>
        </div>
    </div>
    <div>
        <h4 class="font-extrabold text-slate-900 dark:text-white">2. Variabel & Tipe Data</h4>
        <p><b>Variabel</b> adalah nama untuk menyimpan nilai. Isinya punya <b>tipe</b>: <b>int</b> (bulat, mis. 17), <b>str</b> (teks, mis. "Sinta"), <b>float</b> (desimal, mis. 3.5), <b>bool</b> (True/False). AI butuh tipe yang benar: umur "17" (teks) tidak bisa dihitung sebelum diubah ke 17 (angka).</p>
        <div class="mt-2 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/30 p-3">
            <p class="font-bold">Contoh:</p>
            <ul class="list-disc ps-5 mt-1 space-y-1 font-mono text-[13px]">
                <li>nama = "Sinta"</li>
                <li>umur = 17</li>
                <li>type(umur) → int, sedangkan type("17") → str</li>
            </ul>
        </div>
    </div>
    <div>
        <h4 class="font-extrabold text-slate-900 dark:text-white">3. List: Menyimpan Banyak Data</h4>
        <p>AI bekerja dengan <b>banyak data</b>, dan wadahnya adalah <b>list</b>: daftar berurutan dalam kurung siku. Indeks mulai dari <b>0</b>. Fungsi <b>len</b> menghitung isinya, <b>append</b> menambah isi baru.</p>
        <div class="mt-2 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/30 p-3">
            <p class="font-bold">Contoh:</p>
            <ul class="list-disc ps-5 mt-1 space-y-1 font-mono text-[13px]">
                <li>nilai = [70, 80, 90] → nilai[0] adalah 70</li>
                <li>len(nilai) → 3</li>
                <li>nilai.append(100) → [70, 80, 90, 100]</li>
            </ul>
        </div>
    </div>
    <div>
        <h4 class="font-extrabold text-slate-900 dark:text-white">4. Loop: Mengulang Tanpa Lelah</h4>
        <p><b>for</b> mengulang sejumlah yang pasti (mis. tiap nilai dalam list), <b>while</b> mengulang selama syarat benar. Hati-hati: <b>while True</b> tanpa penghenti berjalan selamanya — editor di bawah otomatis menghentikannya setelah 10 detik agar tab tidak beku.</p>
        <div class="mt-2 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/30 p-3">
            <p class="font-bold">Contoh:</p>
            <ul class="list-disc ps-5 mt-1 space-y-1 font-mono text-[13px]">
                <li>for n in [70, 80]: print(n) → 70 lalu 80</li>
                <li>for i in range(3): print(i) → 0, 1, 2</li>
            </ul>
        </div>
    </div>
    <div>
        <h4 class="font-extrabold text-slate-900 dark:text-white">5. Fungsi & Dict</h4>
        <p><b>Fungsi</b> (def) membungkus langkah agar bisa dipakai ulang; <b>return</b> mengirim hasilnya ke pemanggil. <b>Dict</b> menyimpan pasangan kunci–nilai, mis. {"nama": "Budi", "nilai": 85} — mirip satu baris data siswa.</p>
        <div class="mt-2 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/30 p-3">
            <p class="font-bold">Contoh:</p>
            <ul class="list-disc ps-5 mt-1 space-y-1 font-mono text-[13px]">
                <li>def sapa(nama): return "Halo, " + nama</li>
                <li>siswa = {"nama": "Budi", "nilai": 85} → siswa["nilai"] adalah 85</li>
            </ul>
        </div>
    </div>
</div>
