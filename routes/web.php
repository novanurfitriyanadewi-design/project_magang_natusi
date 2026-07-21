<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Superadmin\AdminController as SuperadminAdminController;
use App\Http\Controllers\Superadmin\AturanPerusahaanController as SuperadminAturanPerusahaanController;
use App\Http\Controllers\Superadmin\DashboardController as SuperadminDashboardController;
use App\Http\Controllers\Superadmin\JamAbsensiController as SuperadminJamAbsensiController;
use App\Http\Controllers\Superadmin\MetodePembayaranController as SuperadminMetodePembayaranController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\PesertaMagangController as AdminPesertaMagangController;
use App\Http\Controllers\Admin\LaporanPesertaController as AdminLaporanPesertaController;
use App\Http\Controllers\Admin\LaporanPembayaranController as AdminLaporanPembayaranController;
use App\Http\Controllers\Admin\LaporanAbsensiController as AdminLaporanAbsensiController;
use App\Http\Controllers\Admin\LaporanPenugasanController as AdminLaporanPenugasanController;
use App\Http\Controllers\Admin\PermintaanMagangController as AdminPermintaanMagangController;
use App\Http\Controllers\Admin\DataAbsensiController as AdminDataAbsensiController;
use App\Http\Controllers\Admin\DataPembayaranController as AdminDataPembayaranController;
use App\Http\Controllers\Admin\DataMetodePembayaranController as AdminDataMetodePembayaranController;
use Illuminate\Support\Facades\Route;

/* Halaman Awal & Registrasi */

Route::get('/', fn () => redirect()->route('login'));

Route::middleware('guest')->group(function () {
    Route::get('/register/pelamar', function () {
        session(['register_role' => 'pelamar']);
        return redirect()->route('register');
    })->name('register.pelamar');

    Route::get('/register/karyawan', function () {
        session(['register_role' => 'karyawan']);
        return redirect()->route('register');
    })->name('register.karyawan');
});

/* Dashboard Umum (redirect sesuai role) */

Route::middleware('auth')->get('/dashboard', function () {
    $user = auth()->user();

    return match ($user?->role) {
        'superadmin' => redirect()->route('superadmin.dashboard'),
        'admin'      => redirect()->route('admin.dashboard'),
        default      => view('dashboard'),
    };
})->name('dashboard');

/* Kelola Profil (semua role yang sudah login) */

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::patch('/profile/photo', [ProfileController::class, 'updatePhoto'])->name('profile.photo.update');
    Route::delete('/profile/photo', [ProfileController::class, 'destroyPhoto'])->name('profile.photo.destroy');
    Route::get('/profile/photo', [ProfileController::class, 'showPhoto'])->name('profile.photo.show');
});

/* SUPER ADMIN */

Route::middleware(['auth', 'role:superadmin'])
    ->prefix('superadmin')
    ->name('superadmin.')
    ->group(function () {
        Route::get('/dashboard', SuperadminDashboardController::class)->name('dashboard');

        // Kelola Admin
        Route::get('/admin', [SuperadminAdminController::class, 'index'])->name('admin');
        Route::post('/admin', [SuperadminAdminController::class, 'store'])->name('admin.store');
        Route::put('/admin/{admin}', [SuperadminAdminController::class, 'update'])->name('admin.update');
        Route::delete('/admin/{admin}', [SuperadminAdminController::class, 'destroy'])->name('admin.destroy');

        // Kelola Aturan Perusahaan
        Route::get('/aturan', [SuperadminAturanPerusahaanController::class, 'index'])->name('aturan.index');
        Route::post('/aturan', [SuperadminAturanPerusahaanController::class, 'store'])->name('aturan.store');
        Route::put('/aturan/{aturan}', [SuperadminAturanPerusahaanController::class, 'update'])->name('aturan.update');
        Route::delete('/aturan/{aturan}', [SuperadminAturanPerusahaanController::class, 'destroy'])->name('aturan.destroy');

        // Kelola Jam Absensi
        Route::get('/jam-absensi', [SuperadminJamAbsensiController::class, 'index'])->name('jam-absensi.index');
        Route::put('/jam-absensi', [SuperadminJamAbsensiController::class, 'update'])->name('jam-absensi.update');
        Route::patch('/jam-absensi/reset', [SuperadminJamAbsensiController::class, 'reset'])->name('jam-absensi.reset');

        // Kelola Metode Pembayaran (controller khusus superadmin)
        Route::get('/metode-pembayaran', [SuperadminMetodePembayaranController::class, 'index'])->name('metode-pembayaran.index');
        Route::put('/metode-pembayaran/nominal', [SuperadminMetodePembayaranController::class, 'updateNominal'])->name('metode-pembayaran.nominal.update');
        Route::post('/metode-pembayaran/rekening', [SuperadminMetodePembayaranController::class, 'storeBank'])->name('metode-pembayaran.bank.store');
        Route::put('/metode-pembayaran/rekening/{bank}', [SuperadminMetodePembayaranController::class, 'updateBank'])->name('metode-pembayaran.bank.update');
        Route::delete('/metode-pembayaran/rekening/{bank}', [SuperadminMetodePembayaranController::class, 'destroyBank'])->name('metode-pembayaran.bank.destroy');
    });

/* ADMIN */

Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', AdminDashboardController::class)->name('dashboard');

        // Kelola Data Peserta Magang
        Route::resource('peserta', AdminPesertaMagangController::class)
            ->except(['create', 'show', 'edit'])
            ->parameters(['peserta' => 'peserta_magang']);

        // Permintaan Magang
        Route::get('/permintaan', [AdminPermintaanMagangController::class, 'index'])->name('permintaan.index');
        Route::post('/permintaan/action/{id}', [AdminPermintaanMagangController::class, 'action'])->name('permintaan.action');

        // Kelola Laporan Peserta Magang
        Route::resource('laporan-peserta', AdminLaporanPesertaController::class)
            ->only(['index', 'store', 'update', 'destroy'])
            ->parameters(['laporan-peserta' => 'peserta_magang']);

        // Laporan
        Route::get('/laporan/pembayaran', [AdminLaporanPembayaranController::class, 'index'])->name('laporan.pembayaran');
        Route::get('/laporan/penugasan', [AdminLaporanPenugasanController::class, 'index'])->name('laporan.penugasan');
        Route::get('/laporan/absensi', [AdminLaporanAbsensiController::class, 'index'])->name('laporan.absensi');

        // Data Absensi (pakai controller yang sudah ada: DataAbsensiController)
        Route::get('/absensi', [AdminDataAbsensiController::class, 'index'])->name('absensi.index');

        // Tugas (placeholder — buat controller/view kalau sudah siap)
        Route::get('/tugas', fn () => view('admin.tugas.index'))->name('tugas.index');
        Route::get('/pengumpulan-tugas', fn () => view('admin.pengumpulan-tugas.index'))->name('pengumpulan-tugas.index');

        // Metode Pembayaran (pakai controller yang sudah ada: DataMetodePembayaranController)
        Route::get('/metode-pembayaran', [AdminDataMetodePembayaranController::class, 'index'])->name('metode-pembayaran.index');
        Route::put('/metode-pembayaran/nominal', [AdminDataMetodePembayaranController::class, 'updateNominal'])->name('metode-pembayaran.nominal.update');
        Route::post('/metode-pembayaran/rekening', [AdminDataMetodePembayaranController::class, 'storeBank'])->name('metode-pembayaran.bank.store');
        Route::put('/metode-pembayaran/rekening/{bank}', [AdminDataMetodePembayaranController::class, 'updateBank'])->name('metode-pembayaran.bank.update');
        Route::delete('/metode-pembayaran/rekening/{bank}', [AdminDataMetodePembayaranController::class, 'destroyBank'])->name('metode-pembayaran.bank.destroy');

        // Data Pembayaran (pakai controller yang sudah ada: DataPembayaranController)
        Route::get('/pembayaran', [AdminDataPembayaranController::class, 'index'])->name('pembayaran.index');
        Route::patch('/pembayaran/{pembayaran}/terima', [AdminDataPembayaranController::class, 'terima'])->name('pembayaran.terima');
        Route::patch('/pembayaran/{pembayaran}/tolak', [AdminDataPembayaranController::class, 'tolak'])->name('pembayaran.tolak');
    });

/* Authentication */
require __DIR__ . '/auth.php';