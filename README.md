<div align="center">

  # KKALAB
  ### Laboratorium Koding & Kecerdasan Artifisial — Belajar AI Hands-On di Browser

  [![GitHub](https://img.shields.io/badge/GitHub-Repository-181717?style=for-the-badge&logo=github)](https://github.com/Rexxscode/ITX)
  [![Laravel](https://img.shields.io/badge/Laravel-13-FF2D20?style=for-the-badge&logo=laravel)](https://laravel.com)
  [![License](https://img.shields.io/badge/License-MIT-blue?style=for-the-badge)](LICENSE)

  **Submission for ITX 2026 - Web Development**

  **By Gatau dah yang penting mau juara**

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

KKALAB adalah aplikasi web interaktif tempat siswa belajar Koding & Kecerdasan Artifisial lewat bahan ajar berurutan + kuis 15 soal per modul, mengumpulkan XP & badge, dan mencetak sertifikat otomatis. Guru memantau progres lewat kode kelas 6-digit dan ekspor CSV.

### Tujuan Proyek

- 🎯 **Tujuan Utama**: Membuat pembelajaran AI yang hands-on, aman (privasi), dan terukur untuk sekolah.
- 📊 **Target Pengguna**: Siswa SMA/SMK, guru semua mapel, dan admin sekolah.
- 💡 **Value Proposition**: Satu-satunya bahan ajar KKA yang menggabungkan praktik ML client-side nyata + evaluasi berurutan + peran guru-siswa-admin dalam satu aplikasi.

---

## ✨ Fitur Unggulan

### Fitur Utama

| Fitur | Deskripsi | Keunggulan |
|----------|--------------|---------------|
| **Alur 2 Tahap Berurutan** | Bahan bacaan wajib selesai (terkunci scroll) sebelum kuis terbuka; modul N+1 terkunci sampai modul N lulus | Murni pemahaman, tidak bisa contek |
| **Kuis 15 Soal + Pembahasan** | Stepper 1 soal per layar, indikator progres, skor & XP otomatis, ulasan benar/salah + pembahasan | Evaluasi adil, jawaban dipertahankan |
| **Soal & Modul per Guru** | Guru salin/fork soal default jadi versinya (otomatis), buat modul sendiri, atur urutan per kelas | Default admin aman, tiap guru merdeka |
| **Kelas + Kode 6-Digit** | Guru buat kelas, siswa gabung pakai kode, progres terpantau + ekspor CSV | Tanpa input manual berulang |
| **Sertifikat Otomatis** | Terbit saat 6 modul default lulus (≥80) + lulus ujian akhir (≥80), cetak PDF via browser | Penghargaan instan |

### Fitur Tambahan

- **Tutor AI Gemini** - Asisten teks (jawaban teks polos tanpa markdown, kuota 30/hari + cooldown)
- **3 Peran** - Admin (kelola guru + lihat progres semua siswa), Guru, Siswa
- **UI Indonesia Penuh** - Seluruh antarmuka + pesan validasi berbahasa Indonesia, sidebar responsif HP & desktop

---

## 📸 Demo & Screenshot

### Struktur Halaman

- `/` — Landing page (hero, misi, kontak, footer)
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
| **Python di browser (Pyodide)** | Editor Python asli via WebAssembly + Web Worker anti-hang |

### Praktik di Browser (tanpa kamera)

```
Python       : Pyodide (WASM) + Web Worker, timeout paksa 10 detik
Simulasi     : Bias Data (slider Alpine.js, tanpa backend)
Halaman      : resources/views/siswa/bahan-show.blade.php
```

- Editor Python asli: contoh siap jalan, output + pesan error Indonesia.
- Kode dilarang (`import os/sys`, `open(`, socket) ditolak client-side.
| **MySQL (Laragon)** | Sudah tersedia di lingkungan sekolah + terlihat langsung di phpMyAdmin |
| **Gemini (teks saja)** | Hanya untuk penjelasan edukatif, bukan klasifikasi — LLM generalis tidak cocok untuk visi real-time |

### Dependencies Utama

```json
{
  "require": {
    "php": "^8.3",
    "laravel/framework": "^13.0",
    "laravel/breeze": "^2.4",
    "laravel/ai": "^1.0",
    "laravel/tinker": "^3.0"
  }
}
```

---

## 🏗️ Arsitektur Sistem

### Database Schema

```
users (id, name, email, password, role: admin/guru/siswa, status: aktif/pending/ditolak)
classrooms (id, teacher_id, name, code[6-digit unik], status: buka/arsip)
classroom_user (classroom_id, student_id)          <- keanggotaan
classroom_module (classroom_id, module_id, position) <- urutan per kelas
modules (id, teacher_id NULL=default, title, slug, order, xp_reward, materi)
quizzes (id, teacher_id NULL=default, origin_id NULL=fork, module_id, question, options[A-E], correct_index, explanation)
student_progress (student_id, module_id)[unik] (is_completed, status, quiz_score, best_score, xp_earned, hands_on_*, reading_done, training_xp_awarded, tracking started_at/last_accessed_at/completed_at)
quiz_attempts + final_exam_attempts (riwayat nilai per percobaan)
certificates (user_id[unik], code[unik KBT-*], issued_at) <- 6 modul + ujian akhir
feedbacks (classroom, student, module)[unik] <- catatan guru
ai_chats (riwayat Tutor AI, kuota 30/hari + cooldown + budget global) + student_models/global_models (legacy, tabel dibiarkan, fitur latih sudah dihapus)
badges + badge_user (koleksi per siswa)
```

### Folder Structure

```
ITX/
├── app/Http/Controllers/
│   ├── Admin/ (Dashboard, User, Preview)
│   ├── Auth/ (Breeze: login, register, password...)
│   ├── Student/ (Dashboard, Bahan, Kuis, Progress, Certificate, ClassroomJoin, UjianAkhir, Raport)
│   └── Teacher/ (Classroom, Module, Quiz, Export, StudentReport)
├── app/Ai/Agents/ (TutorAgent) + app/Ai/Tools/ (profil & progres siswa)
├── app/Services/ (GeminiService, StudentModuleService, CertificateService, StudentProgressTracker)
├── database/migrations/ + seeders/ (90 soal default + 6 modul)
├── resources/views/
│   ├── layouts/ (guest) + components/ (sidebar-layout, exam-questions, ai-log-list, ...)
│   ├── siswa/ (dashboard, bahan-*, kuis-*, ujian-*, raporku, sertifikat)
│   ├── guru/ (dashboard, kelas-*, modul-*, quiz-*, ai-logs)
│   └── admin/ (dashboard, pratinjau-index, ai-logs) + auth/ + profile/
├── public/js/pyodide-worker.js (Python di browser untuk modul Python)
├── routes/web.php + auth.php
└── tests/Feature + Unit (PHP) + tests/js (Node)
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
cd ITX
```

> Branch utama: `main`. Jangan checkout cabang lama bila tidak diminta.

#### 2️⃣ Install Dependencies

```bash
composer install
npm install
```

#### 3️⃣ Setup Environment

```bash
copy .env.example .env
php artisan key:generate
```

> Di Linux/Mac ganti `copy` dengan `cp`.

Isi di `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=kka_lab
DB_USERNAME=root
DB_PASSWORD=

# Buat STANDARD key (diawali AIza) di https://aistudio.google.com/app/apikey.
# Key Auth baru (diawali AQ.) DITOLAK gateway — wajib pakai Standard key.
GEMINI_API_KEY=[isi_key_dari_aistudio]
GEMINI_MODEL=gemini-3.8-flash
```

Chatbot memakai Laravel AI SDK (`laravel/ai`, agen `App\Ai\Agents\TutorAgent`, provider `gemini`). Riwayat chat tetap di tabel `ai_chats`; tabel `agent_conversations` bawaan SDK dicadangkan untuk memori percakapan persisten nanti.

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

> Catatan `public/hot`: selama file ini ada, Blade memuat JS dari Vite dev-server (`npm run dev`). Bila dev-server mati (mis. Laragon tetap menyala), matikan dev atau jalankan `npm run build` agar Alpine/JS kembali jalan.

### Checklist Produksi

- `APP_ENV=production`, `APP_DEBUG=false` (jangan bocorkan stack trace/SQL/env).
- `APP_URL=https://domain-sekolah` (otomatis mengaktifkan cookie aman).
- `DB_PASSWORD` kuat (jangan root kosong), backup rutin database `kka_lab`.
- Isi `MAIL_*` SMTP asli bila verifikasi email diwajibkan; putuskan `verified` middleware vs lepas interface `MustVerifyEmail`.
- `GEMINI_API_KEY` Standard key via vault/env server (jangan commit `.env`).
- `php artisan storage:link` (avatar/tanda tangan), `npm run build`, `php artisan migrate --force`.
- Recovery admin: `SUPER_ADMIN_EMAIL` (default `admin@kkalab.sch.id`) + `php artisan db:seed` mengembalikan akun super-admin.

---

## 🚀 Penggunaan

### Untuk Siswa

1. **Daftar/Masuk**, lalu **Gabung Kelas** dengan kode 6-digit dari guru.
2. **Bahan Belajar** — baca materi sampai bawah, tekan konfirmasi.
3. **Kuis** — kerjakan 15 soal berurutan per modul (skor ≥80 lulus, otomatis ke dasbor).
4. **Modul 2: Latih AI-mu** — materi konsep model AI, deteksi objek, dan cara kerja model pretrained + kuis (tanpa praktik webcam).
5. **Ujian Akhir + Sertifikat** — ujian 40 soal terbuka setelah 6 modul lulus; sertifikat otomatis terbit setelah 6 modul (≥80) + ujian akhir lulus (≥80).

### Untuk Guru

1. Minta akun ke **admin** (guru tidak bisa daftar sendiri).
2. **Kelas Saya** — buat kelas, bagikan kode, lihat nilai, ekspor CSV, atur urutan modul.
3. **Modul Saya** — buat modul sendiri (otomatis ditempel ke kelasmu).
4. **Bank Soal** — pilih modul → edit soal langsung (otomatis jadi versi kelasmu, soal admin aman).

### Untuk Admin

1. **Dashboard** — buat akun guru, reset password, lihat progres belajar tiap siswa.
2. **Kelola Modul/Soal Default** — hanya admin yang bisa mengubah bank default.
3. **SOP lupa kata sandi (tanpa email)** — murid/guru lapor via WA → cari email di Semua Pengguna → Reset Kata Sandi (min. 8 karakter) → kasih password sementara → suruh ganti sendiri di Profil. Email reset otomatis baru jalan setelah SMTP diisi di `.env` prod.

---

## 🧪 Testing

```bash
php artisan test
```

Tes PHP (auth, peran, profil, kuis, ujian akhir, bank soal guru, Tutor AI + kuota, pratinjau admin, hardening).

> Catatan: alur kuis/browser kritis saat ini terverifikasi via tes logika + pengujian manual — belum ada e2e browser otomatis (Playwright).

---

## 👥 Tim Developer

| Nama | Peran | GitHub |
|------|-------|--------|
| **Sofian Bahtiar** | Frontend & Backend Developer | [@Sayayyan](https://github.com/Sayayyan) |
| **Muhamad Adzka Lainufar** | ... | [@Rexxscode](https://github.com/Rexxscode) |
| **Fauzan Aji Wibisono** | ... | [@morganaji17-gif](https://github.com/morganaji17-gif) |

---

## 📄 Lisensi

Proyek ini dilisensikan di bawah [MIT License](LICENSE) - lihat file LICENSE untuk detail lebih lanjut.

---

<div align="center">

  **Made with ❤️ by Gatau dah yang penting mau juara for ITX 2026**

</div>
