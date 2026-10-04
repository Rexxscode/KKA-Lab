<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\AiChatController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Student\BahanController;
use App\Http\Controllers\Student\CertificateController;
use App\Http\Controllers\Student\ClassroomJoinController;
use App\Http\Controllers\Student\DashboardController as SiswaDashboardController;
use App\Http\Controllers\Student\KuisController;
use App\Http\Controllers\Student\ProgressController;
use App\Http\Controllers\Teacher\ClassroomController as GuruClassroomController;
use App\Http\Controllers\Teacher\ExportController;
use App\Http\Controllers\Teacher\QuizController;
use App\Models\Module;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    try {
        $modules = Module::orderBy('order')->get();
    } catch (\Throwable $e) {
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
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/ai/chat', [AiChatController::class, 'chat'])->name('ai.chat');
});

Route::middleware(['auth', 'role:siswa'])->prefix('siswa')->name('siswa.')->group(function () {
    Route::get('/dashboard', [SiswaDashboardController::class, 'index'])->name('dashboard');
    Route::post('/gabung-kelas', [ClassroomJoinController::class, 'store'])->name('join');
    Route::get('/bahan', [BahanController::class, 'index'])->name('bahan.index');
    Route::get('/bahan/{slug}', [BahanController::class, 'show'])->name('bahan.show');
    Route::post('/bahan/{slug}/selesai', [BahanController::class, 'markRead'])->name('bahan.read');
    Route::get('/kuis', [KuisController::class, 'index'])->name('kuis.index');
    Route::get('/kuis/{slug}', [KuisController::class, 'show'])->name('kuis.show');
    Route::post('/kuis/{slug}', [ProgressController::class, 'submitQuiz'])->name('kuis.submit');
    Route::post('/modul/{slug}/hands-on', [ProgressController::class, 'completeHandsOn'])->name('modul.handson');
    Route::get('/sertifikat', [CertificateController::class, 'index'])->name('sertifikat');
});

Route::middleware(['auth', 'role:guru,admin'])->prefix('guru')->name('guru.')->group(function () {
    Route::get('/dashboard', [GuruClassroomController::class, 'index'])->name('dashboard');
    Route::post('/kelas', [GuruClassroomController::class, 'store'])->name('kelas.store');
    Route::get('/kelas/{classroom}', [GuruClassroomController::class, 'show'])->name('kelas.show');
    Route::get('/kelas/{classroom}/export-csv', [ExportController::class, 'csv'])->name('kelas.export');
    Route::get('/quiz', [QuizController::class, 'index'])->name('quiz.index');
    Route::post('/quiz', [QuizController::class, 'store'])->name('quiz.store');
    Route::patch('/quiz/{quiz}', [QuizController::class, 'update'])->name('quiz.update');
    Route::delete('/quiz/{quiz}', [QuizController::class, 'destroy'])->name('quiz.destroy');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');
    Route::patch('/users/{user}/reset-password', [AdminUserController::class, 'resetPassword'])->name('users.reset');
    Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');
});

require __DIR__ . '/auth.php';
