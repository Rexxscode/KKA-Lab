<div align="center">

  # KKA Lab
  ### Laboratorium Koding & Kecerdasan Artifisial — Belajar AI Hands-On di Browser

  [![GitHub](https://img.shields.io/badge/GitHub-Repository-181717?style=for-the-badge&logo=github)](https://github.com/Rexxscode/ITX)
  [![Laravel](https://img.shields.io/badge/Laravel-13-FF2D20?style=for-the-badge&logo=laravel)](https://laravel.com)
  [![License](https://img.shields.io/badge/License-MIT-blue?style=for-the-badge)](LICENSE)

  **Submission for ITX 2026 - Web Development**

  **By [Nama Tim]**

</div>

---

## 📋 Daftar Isi

- [Tentang Proyek](#-tentang-proyek)
- [Fitur Unggulan](#-fitur-unggulan)
- [Demo & Screenshot](#-demo--screenshot)
- [Teknologi](#-teknologi)
- [Arsitektur Sistem](#-arsitektur-sistem)
- [Instalasi & Setup](#-instalasi--setup)
- [Penggunaan](#-penggunaan)
- [Testing](#-testing)
- [Tim Pengembang](#-tim-developer)
- [Lisensi](#-lisensi)

---

## 🎯 Tentang Proyek

### Latar Belakang

Kemendikdasmen memperkuat pembelajaran mendalam lewat Koding & Kecerdasan Artifisial (KKA) dan melatih semua guru mapel — tetapi guru butuh bahan ajar yang praktis, dan siswa butuh cara merasakan AI secara langsung, bukan sekadar membaca teori.

### Solusi yang Ditawarkan

KKA Lab adalah aplikasi web interaktif tempat siswa **melatih model AI sungguhan langsung di browser** (klasifikasi gambar via webcam dengan TensorFlow.js + KNN — gambar tidak pernah keluar dari perangkat), mengerjakan bahan ajar berurutan + kuis 15 soal per modul, mengumpulkan XP & badge, dan mencetak sertifikat otomatis. Guru memantau progres lewat kode kelas 6-digit dan ekspor CSV.

### Tujuan Proyek

- 🎯 **Tujuan Utama**: Membuat pembelajaran AI yang hands-on, aman (privasi), dan terukur untuk sekolah.
- 📊 **Target Pengguna**: Siswa SMA/SMK, guru semua mapel, dan admin sekolah.
- 💡 **Value Proposition**: Satu-satunya bahan ajar KKA yang menggabungkan praktik ML client-side nyata + evaluasi berurutan + peran guru-siswa-admin dalam satu aplikasi.

---

## ✨ Fitur Unggulan

### Fitur Utama

| Fitur | Deskripsi | Keunggulan |
|----------|--------------|---------------|
| **Latih AI-mu (KNN Webcam)** | Kumpulkan contoh 2 benda, latih model KNN lokal, uji real-time + pantau live | 100% client-side: 0 latency, hemat kuota, privasi terjaga |
| **Alur 2 Tahap Berurutan** | Bahan bacaan wajib selesai (terkunci scroll) sebelum kuis terbuka; modul N+1 terkunci sampai modul N lulus | Murni pemahaman, tidak bisa contek |
| **Kuis 15 Soal + Pembahasan** | Stepper 1 soal per layar, indikator progres, skor & XP otomatis, ulasan benar/salah + pembahasan | Evaluasi adil, jawaban dipertahankan |
| **Soal & Modul per Guru** | Guru salin/fork soal default jadi versinya (otomatis), buat modul sendiri, atur urutan per kelas | Default admin aman, tiap guru merdeka |
| **Kelas + Kode 6-Digit** | Guru buat kelas, siswa gabung pakai kode, progres terpantau + ekspor CSV | Tanpa input manual berulang |
| **Sertifikat Otomatis** | Terbit saat 4 modul default lulus (≥80), cetak PDF via browser | Penghargaan instan |

### Fitur Tambahan

- **Tutor AI Gemini** - Asisten teks + tombol "Jelaskan Hasilku" (jawaban teks polos tanpa markdown)
- **Coba AI di Landing Page** - Klasifikasi kamera instan tanpa login
- **3 Peran** - Admin (kelola guru + lihat progres semua siswa), Guru, Siswa
- **UI Indonesia Penuh** - Seluruh antarmuka + pesan validasi berbahasa Indonesia, sidebar responsif HP & desktop

---

## 📸 Demo & Screenshot

### Struktur Halaman

- `/` — Landing page (hero, demo kamera, misi, kontak, footer)
- `/siswa/dashboard` — XP, badge, progres, daftar modul
- `/siswa/bahan` & `/siswa/bahan/{slug}` — Bahan belajar + praktik
- `/siswa/kuis` & `/siswa/kuis/{slug}` — Kuis stepper 15 soal
- `/siswa/sertifikat` — Sertifikat kelulusan
- `/guru/dashboard`, `/guru/kelas`, `/guru/modul`, `/guru/quiz` — Panel guru
- `/admin/dashboard` — Panel admin + progres semua siswa

### Video Demo

📹 **[Link Video Demo](https://[URL_VIDEO])** _(opsional)_

---

## 🛠️ Teknologi

### Tech Stack

#### Frontend
```
Framework    : Laravel Blade + Alpine.js
UI Library   : Tailwind CSS
AI Client    : TensorFlow.js (MobileNet + KNN Classifier via CDN)
```

#### Backend
```
Runtime      : PHP 8.3
Framework    : Laravel 13 (Breeze Blade auth)
Database     : MySQL 8 (Laragon) — SQLite untuk testing
Auth         : Session + middleware peran (admin/guru/siswa)
AI Teks      : Google Gemini API (gemini-3.8-flash, via backend)
```

#### DevOps & Tools
```
Testing      : PHPUnit (25 tes, browser via Playwright)
AI Assistant : Tutor chat + penjelasan hasil praktikum
```

### Alasan Pemilihan Teknologi

| Teknologi | Alasan Pemilihan |
|-----------|------------------|
| **Laravel Blade + Alpine.js** | Full-stack ringan tanpa build frontend berat; cocok untuk tugas sekolah dan hosting sederhana |
| **TensorFlow.js client-side** | Inferensi di browser = privasi (gambar tak keluar device), 0 latency, hemat kuota API |
| **MySQL (Laragon)** | Sudah tersedia di lingkungan sekolah + terlihat langsung di phpMyAdmin |
| **Gemini (teks saja)** | Hanya untuk penjelasan edukatif, bukan klasifikasi — LLM generalis tidak cocok untuk visi real-time |

### Dependencies Utama

```json
{
  "require": {
    "php": "^8.3",
    "laravel/framework": "^13.0",
    "laravel/breeze": "^2.4"
  }
}
```

---

## 🏗️ Arsitektur Sistem

### Database Schema

```
users (id, name, email, password, role: admin/guru/siswa)
classrooms (id, teacher_id, name, code[6-digit unik])
classroom_user (classroom_id, student_id)          <- keanggotaan
classroom_module (classroom_id, module_id, position) <- urutan per kelas
modules (id, teacher_id NULL=default, title, slug, order, xp_reward)
quizzes (id, teacher_id NULL=default, module_id, question, options[A-E], correct_index, explanation)
student_progress (student_id, module_id, is_completed, quiz_score, xp_earned, hands_on_count, reading_done)
badges + badge_user (koleksi per siswa)
```

### Folder Structure

```
ITX/
├── app/Http/Controllers/
│   ├── Admin/ (Dashboard, User)
│   ├── Auth/ (Breeze: login, register, password...)
│   ├── Student/ (Dashboard, Bahan, Kuis, Progress, Certificate, ClassroomJoin)
│   └── Teacher/ (Classroom, Module, Quiz, Export)
├── app/Services/ (GeminiService, StudentModuleService)
├── database/migrations/ + seeders/ (60 soal default + 4 modul)
├── resources/views/
│   ├── layouts/ (sidebar, guest) + components/
│   ├── siswa/ (dashboard, bahan-*, kuis-*, sertifikat)
│   ├── guru/ (dashboard, kelas-*, modul-*, quiz-*)
│   └── admin/ + auth/ + profile/
├── routes/web.php
└── tests/Feature + Unit
```

---

## ⚙️ Instalasi & Setup

### Prerequisites

- **PHP** 8.3 + ekstensi `pdo_mysql`, `pdo_sqlite`, `sqlite3`
- **Composer** 2.x
- **Node.js** 18+ & npm
- **MySQL** 8 (atau Laragon) + database `kka_lab`

### Langkah Instalasi

#### 1️⃣ Clone Repository

```bash
git clone https://github.com/Rexxscode/ITX.git
cd ITX/ITX
git checkout sofianBranch
```

#### 2️⃣ Install Dependencies

```bash
composer install
npm install
```

#### 3️⃣ Setup Environment

```bash
cp .env.example .env
php artisan key:generate
```

Isi di `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=kka_lab
DB_USERNAME=root
DB_PASSWORD=

GEMINI_API_KEY=[isi_key_dari_aistudio]
GEMINI_MODEL=gemini-3.8-flash
```

#### 4️⃣ Setup Database

```bash
php artisan migrate --seed
```

#### 5️⃣ Jalankan

```bash
composer dev
# atau dua terminal:
php artisan serve
npm run dev
```

Buka `http://127.0.0.1:8000`. Akun demo (password `password`): `admin@kkalab.sch.id`, `guru@kkalab.sch.id`, `siswa@kkalab.sch.id`.

---

## 🚀 Penggunaan

### Untuk Siswa

1. **Daftar/Masuk**, lalu **Gabung Kelas** dengan kode 6-digit dari guru.
2. **Bahan Belajar** — baca materi sampai bawah, tekan konfirmasi.
3. **Kuis** — kerjakan 15 soal berurutan (skor ≥80 lulus, otomatis ke dasbor).
4. **Modul 2** — latih AI dengan webcam (2 benda → Latih → Uji), 2x Uji meyakinkan = +20 XP.
5. **Sertifikat** — otomatis terbuka setelah 4 modul default lulus.

### Untuk Guru

1. Minta akun ke **admin** (guru tidak bisa daftar sendiri).
2. **Kelas Saya** — buat kelas, bagikan kode, lihat nilai, ekspor CSV, atur urutan modul.
3. **Modul Saya** — buat modul sendiri (otomatis ditempel ke kelasmu).
4. **Bank Soal** — pilih modul → edit soal langsung (otomatis jadi versi kelasmu, soal admin aman).

### Untuk Admin

1. **Dashboard** — buat akun guru, reset password, lihat progres belajar tiap siswa.
2. **Kelola Modul/Soal Default** — hanya admin yang bisa mengubah bank default.

---

## 🧪 Testing

```bash
php artisan test
```

25 tes lolos (auth, peran, profil, contoh). Verifikasi browser (Playwright + Chrome headless + kamera palsu) dipakai untuk alur kritis: kamera, kuis, bank soal, dan matriks akses peran.

---

## 👥 Tim Developer

| Nama | Peran | GitHub |
|------|-------|--------|
| **[Nama Lengkap 1]** | Project Lead & Full Stack Developer | [GitHub](https://github.com/[username1]) |
| **[Nama Lengkap 2]** | Frontend Developer | [GitHub](https://github.com/[username2]) |
| **[Nama Lengkap 3]** | Backend Developer | [GitHub](https://github.com/[username3]) |
| **[Nama Lengkap 4]** | UI/UX Designer | [GitHub](https://github.com/[username4]) |

---

## 📄 Lisensi

Proyek ini dilisensikan di bawah [MIT License](LICENSE) - lihat file LICENSE untuk detail lebih lanjut.

---

<div align="center">

  **Made with ❤️ by [Nama Tim] for ITX 2026**

</div>
