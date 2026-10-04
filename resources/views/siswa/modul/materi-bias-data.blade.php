<div class="prose max-w-none text-sm text-slate-700 space-y-4">
    <div>
        <h4 class="font-extrabold text-slate-900">1. Apa itu Bias Data?</h4>
        <p><b>Bias</b> artinya kemiringan. <b>Bias data</b> terjadi saat dataset tidak mewakili semua kelompok secara adil. Model yang lahir dari data timpang akan timpang juga: pintar untuk mayoritas, buruk untuk minoritas.</p>
        <p class="mt-2 rounded-xl bg-emerald-50 border border-emerald-200 p-3"><b>Contoh dataset timpang:</b> (1) 95 foto wajah satu suku, 5 dari suku lain; (2) 1000 suara laki-laki, 50 suara perempuan untuk asisten suara; (3) 900 foto kucing anggora, 10 foto kucing kampung.</p>
    </div>
    <div>
        <h4 class="font-extrabold text-slate-900">2. Akibat di Dunia Nyata</h4>
        <p>AI dipakai untuk hal serius. Jika datanya timpang, sistem bisa <b>menolak atau gagal mengenali kelompok tertentu</b>. Coba geser slider simulasi di bawah dan rasakan bedanya.</p>
        <p class="mt-2 rounded-xl bg-emerald-50 border border-emerald-200 p-3"><b>Contoh nyata:</b> (1) absensi wajah gagal mengenali sebagian siswa; (2) sistem rekrutmen merugikan pelamar perempuan karena data lamanya timpang; (3) penerjemah otomatis memakai stereotip gender ("dokter" selalu dianggap laki-laki).</p>
    </div>
    <div>
        <h4 class="font-extrabold text-slate-900">3. Akurasi Total Bisa Menipu</h4>
        <p>Jangan terkecoh angka besar. Akurasi total <b>95%</b> bisa berarti 100% di kelompok besar tapi hanya 50% di kelompok kecil. Evaluasi yang adil selalu <b>membedah akurasi per kelompok</b> (bukan cuma rata-rata).</p>
        <p class="mt-2 rounded-xl bg-emerald-50 border border-emerald-200 p-3"><b>Contoh:</b> (1) nilai rata-rata kelas 95 karena 30 siswa dapat 100, padahal 2 siswa dapat 20; (2) 950 tebakan benar dari 1000 — semuanya dari kelompok mayoritas; (3) survei "100% puas" dari 10 responden yang semuanya teman sendiri.</p>
    </div>
    <div>
        <h4 class="font-extrabold text-slate-900">4. Cara Memperbaiki</h4>
        <p>Langkahnya: <b>(1)</b> audit komposisi datamu — kelompok mana yang kurang? <b>(2)</b> tambah data kelompok yang kurang sampai seimbang; <b>(3)</b> uji model per kelompok; <b>(4)</b> buat komposisi datamu transparan agar bisa diperiksa orang lain. Ingat pelajaran Modul 2: kelas dengan contoh sedikit memang dikenali lebih buruk.</p>
        <p class="mt-2 rounded-xl bg-emerald-50 border border-emerald-200 p-3"><b>Contoh perbaikan:</b> (1) tambah 90 foto suku yang kurang; (2) tambah rekaman suara perempuan sampai seimbang; (3) publikasikan tabel komposisi dataset agar bisa diaudit.</p>
    </div>
    <div>
        <h4 class="font-extrabold text-slate-900">5. Tanggung Jawab Manusia</h4>
        <p>AI tidak punya niat — bias berasal dari <b>pilihan data dan desain manusia</b>. Maka manusia pembuat dan penggunanyalah yang bertanggung jawab memastikan hasilnya <b>adil (fair)</b> untuk semua kelompok.</p>
        <p class="mt-2 rounded-xl bg-emerald-50 border border-emerald-200 p-3"><b>Contoh tanggung jawab:</b> (1) pembuat model wajib menguji ke semua kelompok sebelum rilis; (2) sekolah tidak memakai absensi wajah yang gagal untuk sebagian siswa; (3) kamu melaporkan bila menemukan AI yang pilih kasih.</p>
    </div>
</div>
