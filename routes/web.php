<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\PreviewController as AdminPreviewController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\AiChatController;
use App\Http\Controllers\AiLogController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Student\BahanController;
use App\Http\Controllers\Student\CertificateController;
use App\Http\Controllers\Student\ClassroomJoinController;
use App\Http\Controllers\Student\DashboardController as SiswaDashboardController;
use App\Http\Controllers\Student\KuisController;
use App\Http\Controllers\Student\UjianAkhirController;
use App\Http\Controllers\Student\LatihController;
use App\Http\Controllers\Student\ProgressController;
use App\Http\Controllers\Student\RaportController;
use App\Http\Controllers\Teacher\ClassroomController as GuruClassroomController;
use App\Http\Controllers\Teacher\ExportController;
use App\Http\Controllers\Teacher\ModuleController as GuruModuleController;
use App\Http\Controllers\Teacher\ModelModerasiController;
use App\Http\Controllers\Teacher\QuizController;
use App\Http\Controllers\Teacher\StudentReportController;
use App\Http\Controllers\VerifyCertificateController;
use App\Models\Module;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    try {
        $modules = Module::orderBy('order')->get();
    } catch (\Throwable $e) {
        report($e);
        $modules = collect();
    }

    return view('welcome', compact('modules'));
})->name('home');

Route::get('/dashboard', function () {
    $user = auth()->user();
    if ($user && $user->isAdmin()) {
        return redirect()->route('admin.dashboard');
    }
    if ($user && $user->isGuru()) {
        return redirect()->route('guru.dashboard');
    }

    return redirect()->route('siswa.dashboard');
})->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->middleware('throttle:10,1')->name('profile.update');
    Route::post('/profile/tanda-tangan', [ProfileController::class, 'tandaTangan'])->middleware('throttle:10,1')->name('profile.ttd');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->middleware('throttle:10,1')->name('profile.destroy');
    Route::post('/ai/chat', [AiChatController::class, 'chat'])->middleware('throttle:30,1')->name('ai.chat');
});

Route::middleware(['auth', 'role:siswa,admin'])->prefix('siswa')->name('siswa.')->group(function () {
    Route::get('/dashboard', [SiswaDashboardController::class, 'index'])->name('dashboard');
    Route::get('/papan-peringkat', [SiswaDashboardController::class, 'leaderboard'])->name('leaderboard');
    Route::post('/gabung-kelas', [ClassroomJoinController::class, 'store'])->middleware('throttle:10,1')->name('join');
    Route::delete('/keluar-kelas', [ClassroomJoinController::class, 'destroy'])->middleware('throttle:10,1')->name('leave');
    Route::get('/bahan', [BahanController::class, 'index'])->name('bahan.index');
    Route::get('/bahan/{slug}', [BahanController::class, 'show'])->name('bahan.show');
    Route::post('/bahan/{slug}/selesai', [BahanController::class, 'markRead'])->middleware('throttle:10,1')->name('bahan.read');
    Route::get('/kuis', [KuisController::class, 'index'])->name('kuis.index');
    Route::get('/latih-model', [LatihController::class, 'index'])->name('latih.index');
    Route::post('/latih-model/publish', [LatihController::class, 'publish'])->middleware('throttle:10,1')->name('latih.publish');
    Route::get('/model-global', [LatihController::class, 'global'])->name('model.global');
    Route::get('/kuis/{slug}', [KuisController::class, 'show'])->name('kuis.show');
    Route::post('/kuis/{slug}', [ProgressController::class, 'submitQuiz'])->middleware('throttle:10,1')->name('kuis.submit');
    Route::post('/modul/{slug}/hands-on', [ProgressController::class, 'completeHandsOn'])->middleware('throttle:30,1')->name('modul.handson');
    Route::get('/raporku', [RaportController::class, 'index'])->name('raporku');
    Route::get('/raporku/cetak', [RaportController::class, 'cetak'])->name('raporku.cetak');
    Route::get('/ujian-akhir', [UjianAkhirController::class, 'index'])->name('ujian.index');
    Route::get('/ujian-akhir/mulai', [UjianAkhirController::class, 'show'])->name('ujian.show');
    Route::post('/ujian-akhir', [UjianAkhirController::class, 'submit'])->middleware('throttle:10,1')->name('ujian.submit');
    Route::get('/sertifikat', [CertificateController::class, 'index'])->name('sertifikat');
    // Kompatibilitas bookmark lama.
    Route::redirect('/modul/{slug}', '/siswa/bahan/{slug}');
});

Route::middleware(['auth', 'role:guru,admin'])->prefix('guru')->name('guru.')->group(function () {
    // Dasbor tetap terbuka untuk guru pending (baca saja + banner).
    Route::get('/dashboard', [GuruClassroomController::class, 'dashboard'])->name('dashboard');

    // Fitur guru dikunci sampai status aktif (kecuali admin).
    Route::middleware('guru.aktif')->group(function () {
        Route::get('/kelas', [GuruClassroomController::class, 'index'])->name('kelas.index');
        Route::post('/kelas', [GuruClassroomController::class, 'store'])->middleware('throttle:10,1')->name('kelas.store');
        Route::get('/kelas/{classroom}', [GuruClassroomController::class, 'show'])->name('kelas.show');
        // Lihat modul di kelas (read-only): admin bisa melihat modul guru
        // hanya lewat kelas, tanpa bisa mengubah.
        Route::get('/kelas/{classroom}/modul/{module}', [GuruClassroomController::class, 'modulShow'])->name('kelas.modul.show');
        Route::get('/kelas/{classroom}/export-csv', [ExportController::class, 'csv'])->middleware('throttle:5,1')->name('kelas.export');
        Route::patch('/kelas/{classroom}/kode-baru', [GuruClassroomController::class, 'kodeBaru'])->middleware('throttle:10,1')->name('kelas.kode');
        Route::patch('/kelas/{classroom}/arsip', [GuruClassroomController::class, 'arsip'])->middleware('throttle:10,1')->name('kelas.arsip');
        Route::delete('/kelas/{classroom}/siswa/{user}', [GuruClassroomController::class, 'kick'])->middleware('throttle:10,1')->name('kelas.siswa.kick');
        Route::delete('/kelas/{classroom}/siswa/{user}/progres', [GuruClassroomController::class, 'resetProgres'])->middleware('throttle:10,1')->name('kelas.siswa.reset');
        Route::get('/kelas/{classroom}/siswa/{siswa}', [StudentReportController::class, 'show'])->name('kelas.siswa.show');
        Route::post('/kelas/{classroom}/siswa/{siswa}/umpan-balik', [StudentReportController::class, 'feedback'])->middleware('throttle:10,1')->name('kelas.siswa.feedback');
        Route::get('/quiz', [QuizController::class, 'index'])->name('quiz.index');
        Route::get('/quiz/modul/{module}', [QuizController::class, 'module'])->name('quiz.module');
        Route::post('/quiz/modul/{module}', [QuizController::class, 'store'])->middleware('throttle:10,1')->name('quiz.module.store');
        Route::post('/quiz/modul/{module}/bulk', [QuizController::class, 'storeBulk'])->middleware('throttle:10,1')->name('quiz.module.bulk');
        Route::get('/modul', [GuruModuleController::class, 'index'])->name('modul.index');
        Route::post('/modul', [GuruModuleController::class, 'store'])->middleware('throttle:10,1')->name('modul.store');
        Route::patch('/modul/{module}', [GuruModuleController::class, 'update'])->middleware('throttle:10,1')->name('modul.update');
        Route::delete('/modul/{module}', [GuruModuleController::class, 'destroy'])->middleware('throttle:10,1')->name('modul.destroy');
        Route::post('/modul/{module}/duplikat', [GuruModuleController::class, 'duplikat'])->middleware('throttle:10,1')->name('modul.duplikat');
        Route::post('/kelas/{classroom}/modul', [GuruModuleController::class, 'tempel'])->middleware('throttle:10,1')->name('kelas.modul.tempel');
        Route::delete('/kelas/{classroom}/modul/{module}', [GuruModuleController::class, 'lepas'])->middleware('throttle:10,1')->name('kelas.modul.lepas');
        Route::patch('/kelas/{classroom}/modul/{module}/naik', [GuruModuleController::class, 'naik'])->middleware('throttle:10,1')->name('kelas.modul.naik');
        Route::patch('/kelas/{classroom}/modul/{module}/turun', [GuruModuleController::class, 'turun'])->middleware('throttle:10,1')->name('kelas.modul.turun');
        Route::patch('/quiz/{quiz}', [QuizController::class, 'update'])->middleware('throttle:10,1')->name('quiz.update');
        Route::delete('/quiz/{quiz}', [QuizController::class, 'destroy'])->middleware('throttle:10,1')->name('quiz.destroy');
        Route::get('/ai-logs', [AiLogController::class, 'guru'])->name('ai.index');
        Route::get('/model-siswa', [ModelModerasiController::class, 'index'])->name('model.index');
        Route::post('/model-siswa/{model}/setujui', [ModelModerasiController::class, 'setujui'])->middleware('throttle:10,1')->name('model.setujui');
        Route::post('/model-siswa/{model}/tolak', [ModelModerasiController::class, 'tolak'])->middleware('throttle:10,1')->name('model.tolak');
        Route::get('/model-siswa/{model}/unduh', [ModelModerasiController::class, 'unduh'])->middleware('throttle:10,1')->name('model.unduh');
    });
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/pratinjau/siswa', [AdminPreviewController::class, 'index'])->name('pratinjau.index');
    Route::get('/pratinjau/siswa/{siswa}', [AdminPreviewController::class, 'show'])->name('pratinjau.show');
    Route::post('/users', [AdminUserController::class, 'store'])->middleware('throttle:10,1')->name('users.store');
    Route::patch('/users/{user}/reset-password', [AdminUserController::class, 'resetPassword'])->middleware('throttle:10,1')->name('users.reset');
    Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->middleware('throttle:10,1')->name('users.destroy');
    Route::post('/pengajuan/{user}/setujui', [AdminUserController::class, 'setujui'])->middleware('throttle:10,1')->name('pengajuan.setujui');
    Route::post('/pengajuan/{user}/tolak', [AdminUserController::class, 'tolak'])->middleware('throttle:10,1')->name('pengajuan.tolak');
    Route::get('/bukti/{user}', [AdminUserController::class, 'unduhBukti'])->middleware('throttle:10,1')->name('bukti.unduh');
    Route::get('/tanda-tangan', [AdminUserController::class, 'tandaTangan'])->name('tanda-tangan');
    Route::get('/ai-logs', [AiLogController::class, 'admin'])->name('ai.index');
});

Route::get('/verifikasi-sertifikat/{code}', [VerifyCertificateController::class, 'show'])
    ->middleware('throttle:30,1')
    ->name('sertifikat.verifikasi');

require __DIR__ . '/auth.php';
