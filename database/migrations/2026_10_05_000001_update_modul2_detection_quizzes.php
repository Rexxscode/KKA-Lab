<?php

use App\Models\Module;
use App\Models\Quiz;
use Illuminate\Database\Migrations\Migration;

/**
 * Modul 2 beralih dari KNN latih-manual ke deteksi objek
 * COCO-SSD pretrained: perbarui soal default yang usang.
 * Hanya menyentuh soal default (teacher_id NULL); salinan
 * milik guru tidak diubah.
 */
return new class extends Migration
{
    public function up(): void
    {
        $module = Module::where('slug', 'latih-ai-mu')->first();
        if (! $module) {
            return;
        }

        foreach ($this->baru() as $lama => $item) {
            Quiz::where('module_id', $module->id)
                ->whereNull('teacher_id')
                ->where('question', $lama)
                ->update([
                    'question' => $item['q'],
                    'options' => $item['o'],
                    'correct_index' => $item['c'],
                    'explanation' => $item['e'],
                ]);
        }

        Quiz::where('module_id', $module->id)
            ->whereNull('teacher_id')
            ->where('question', 'Apa itu transfer learning (MobileNet di Modul 2)?')
            ->update([
                'question' => 'Apa itu transfer learning (model pretrained di Modul 2)?',
                'explanation' => 'Backbone COCO-SSD sudah dilatih jutaan gambar, lalu dipakai tanpa latih ulang. Hemat waktu dan data.',
            ]);

        // Deskripsi modul tampil di kartu: samakan dengan praktik baru.
        $module->update([
            'description' => 'Deteksi objek real-time via webcam (COCO-SSD pretrained): bounding box, label Indonesia, dan confidence.',
        ]);

        // Satu penjelasan Modul 3 yang mengacu ke latihan KNN lama.
        $bias = Module::where('slug', 'bias-data')->first();
        if ($bias) {
            Quiz::where('module_id', $bias->id)
                ->whereNull('teacher_id')
                ->where('question', 'Apa hubungan jumlah contoh dan keadilan?')
                ->update([
                    'explanation' => 'Model butuh cukup contoh tiap kelompok — dataset timpang melahirkan bias.',
                ]);
        }
    }

    public function down(): void
    {
        $module = Module::where('slug', 'latih-ai-mu')->first();
        if (! $module) {
            return;
        }

        foreach ($this->baru() as $lama => $item) {
            Quiz::where('module_id', $module->id)
                ->whereNull('teacher_id')
                ->where('question', $item['q'])
                ->update(['question' => $lama]);
        }

        Quiz::where('module_id', $module->id)
            ->whereNull('teacher_id')
            ->where('question', 'Apa itu transfer learning (model pretrained di Modul 2)?')
            ->update([
                'question' => 'Apa itu transfer learning (MobileNet di Modul 2)?',
                'explanation' => 'MobileNet sudah dilatih jutaan gambar; KNN tinggal memakai cirinya untuk bendamu. Hemat waktu dan data.',
            ]);

        $module->update([
            'description' => 'Tulis nama 2 benda, kumpulkan contoh webcam, latih model KNN lokal, lalu uji real-time.',
        ]);

        $bias = Module::where('slug', 'bias-data')->first();
        if ($bias) {
            Quiz::where('module_id', $bias->id)
                ->whereNull('teacher_id')
                ->where('question', 'Apa hubungan jumlah contoh dan keadilan?')
                ->update([
                    'explanation' => 'Model butuh cukup contoh tiap kelompok — persis seperti latihan KNN di Modul 2.',
                ]);
        }
    }

    /** Pasangan soal lama => soal baru (kunci = teks soal lama). */
    private function baru(): array
    {
        return [
            'Langkah pertama melatih model gambar?' => ['q' => 'Bedanya klasifikasi gambar dan deteksi objek?', 'o' => ['Sama saja', 'Klasifikasi menjawab satu label untuk seluruh foto, deteksi memberi label + kotak lokasi tiap benda', 'Deteksi hanya untuk video', 'Klasifikasi butuh kamera', 'Deteksi tidak butuh model'], 'c' => 1, 'e' => 'Deteksi objek = klasifikasi + lokalisasi: tiap benda dapat label dan bounding box-nya sendiri.'],
            'Kenapa butuh banyak contoh per benda?' => ['q' => 'Bounding box pada deteksi objek berfungsi untuk...?', 'o' => ['Menghitung nilai rapor', 'Menandai posisi benda di dalam gambar', 'Mempercepat internet', 'Mengganti label', 'Menghapus latar'], 'c' => 1, 'e' => 'Bounding box adalah kotak koordinat yang menunjukkan di mana benda berada.'],
            'Jika model salah menebak, yang dilakukan?' => ['q' => 'Jika kotak deteksi bergoyang atau salah, langkah pertama?', 'o' => ['Buang laptop', 'Menyerah', 'Perbaiki posisi, cahaya, dan cek ambang confidence', 'Tutup mata', 'Mengganti laptop baru'], 'c' => 2, 'e' => 'Model pretrained tidak kamu latih ulang; perbaiki kondisi pengambilan dan ambangnya.'],
            'KNN Classifier bekerja dengan...' => ['q' => 'COCO-SSD disebut model pretrained karena...?', 'o' => ['Menebak acak', 'Menghafal password', 'Sudah dilatih dengan ratusan ribu foto sebelum dipakai', 'Menghitung nilai rapor', 'Dilatih siswa saat praktikum'], 'c' => 2, 'e' => 'Pretrained = bobotnya sudah jadi dari latihan besar; siswa tinggal memakai.'],
            'Urutan tahap machine learning yang benar?' => ['q' => 'Alur memakai model pretrained yang benar?', 'o' => ['Langsung percaya semua hasil', 'Siapkan kamera → muat model → deteksi → sesuaikan ambang', 'Uji dulu baru muat model', 'Asal-asalan dulu', 'Tidur dulu baru mulai'], 'c' => 1, 'e' => 'Alur pakai-pretrained: siapkan input, muat model, deteksi, lalu evaluasi ambangnya.'],
            'Apa itu "benda" (kelas) saat melatih model?' => ['q' => '"Kelas" pada COCO-SSD artinya...?', 'o' => ['Ukuran foto', 'Kategori bawaan model: Botol/Buku/Laptop dan 77 lainnya (total 80)', 'Jenis kamera', 'Kelas di sekolah', 'Nilai ujian tiap siswa'], 'c' => 1, 'e' => 'Kelas COCO-SSD tetap: 80 kategori umum, tidak bisa tambah custom tanpa melatih model baru.'],
            'KNN menentukan jawaban dengan cara...' => ['q' => 'Confidence 91% pada label "Botol" artinya...?', 'o' => ['Model 91% yakin melihat botol di kotak itu', 'Foto 91% buram', '91 piksel terdeteksi', 'Baterai tinggal 91%', 'Ditemukan 91 benda'], 'c' => 0, 'e' => 'Confidence = skor keyakinan model pada temuan itu, bukan jumlah benda.'],
            'Jika akurasi model hanya 60%, langkah terbaik?' => ['q' => 'Dua kotak menumpuk pada benda yang sama biasanya berarti...?', 'o' => ['Ada dua benda kembar', 'Kamera rusak', 'Tidak ada arti', 'Model memberi kandidat ganda — naikkan ambang', 'Hapus aplikasinya'], 'c' => 3, 'e' => 'Kandidat ganda itu wajar; ambang lebih tinggi menyaring temuan yang lemah.'],
            'Apa itu label yang salah (mislabel)?' => ['q' => 'Benda custom seperti "botol bekal biru milikku" tidak dikenal COCO-SSD karena...?', 'o' => ['Kamera rusak', 'Foto terlalu gelap', 'Label itu tidak ada di 80 kelas latihnya', 'Internet lambat', 'Layar terlalu kecil'], 'c' => 2, 'e' => 'Model hanya tahu kelas yang pernah dilihat saat pretraining.'],
            'Contoh tiap benda harus bervariasi (jarak, cahaya, sudut) karena...' => ['q' => 'Botol 91% di meja terang turun ke 45% saat lampu dimatikan. Pelajarannya...?', 'o' => ['Modelnya rusak', 'Ganti laptop', 'Listrik sekolah jelek', 'Kondisi cahaya memengaruhi confidence — perbaiki cahaya dulu', 'Tidak penting'], 'c' => 3, 'e' => 'Evaluasi jujur: kondisi pengambilan mengubah hasil, bukan cuma modelnya.'],
            'Mengapa jumlah contoh tiap benda sebaiknya seimbang?' => ['q' => 'Ambang confidence 70% artinya...?', 'o' => ['Hanya temuan dengan keyakinan ≥70% yang ditampilkan', 'Model berjalan 70% lebih cepat', 'Terdeteksi 70 benda', 'Baterai hemat 70%', 'Kamera zoom 70%'], 'c' => 0, 'e' => 'Ambang = filter: sembunyikan temuan yang kurang meyakinkan.'],
        ];
    }
};
