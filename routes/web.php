<?php

use App\Http\Controllers\PortalSearchController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Superadmin\AdminController as SuperadminAdminController;
use App\Http\Controllers\Superadmin\AturanPerusahaanController as SuperadminAturanPerusahaanController;
use App\Http\Controllers\Superadmin\DashboardController as SuperadminDashboardController;
use App\Http\Controllers\Superadmin\JamAbsensiController as SuperadminJamAbsensiControl;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Superadmin\MetodePembayaranController as SuperadminMetodePembayaranController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Halaman Awal
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
});

/*
|--------------------------------------------------------------------------
| Registrasi
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/register/pelamar', function () {
        session([
            'register_role' => 'pelamar',
        ]);

        return redirect()->route('register');
    })->name('register.pelamar');

    Route::get('/register/karyawan', function () {
        session([
            'register_role' => 'karyawan',
        ]);

        return redirect()->route('register');
    })->name('register.karyawan');
});

/*
|--------------------------------------------------------------------------
| Dashboard Umum
|--------------------------------------------------------------------------
*/

Route::get('/dashboard', function () {
    $user = auth()->user();

    if ($user?->role === 'superadmin') {
        return redirect()->route('superadmin.dashboard');
    }

    if ($user?->role === 'admin') {
        return redirect()->route('admin.dashboard');
    }

    return view('dashboard');
})
    ->middleware('auth')
    ->name('dashboard');

/*
|--------------------------------------------------------------------------
| Super Admin
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:superadmin'])
    ->prefix('superadmin')
    ->name('superadmin.')
    ->group(function () {
        Route::get(
            '/dashboard',
            SuperadminDashboardController::class
        )->name('dashboard');

        /*
        |--------------------------------------------------------------------------
        | Kelola Admin
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/admin',
            [SuperadminAdminController::class, 'index']
        )->name('admin');

        Route::post(
            '/admin',
            [SuperadminAdminController::class, 'store']
        )->name('admin.store');

        Route::put(
            '/admin/{admin}',
            [SuperadminAdminController::class, 'update']
        )->name('admin.update');

        Route::delete(
            '/admin/{admin}',
            [SuperadminAdminController::class, 'destroy']
        )->name('admin.destroy');

        /*
        |--------------------------------------------------------------------------
        | Kelola Aturan Perusahaan
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/aturan',
            [SuperadminAturanPerusahaanController::class, 'index']
        )->name('aturan.index');

        Route::post(
            '/aturan',
            [SuperadminAturanPerusahaanController::class, 'store']
        )->name('aturan.store');

        Route::put(
            '/aturan/{aturan}',
            [SuperadminAturanPerusahaanController::class, 'update']
        )->name('aturan.update');

        Route::delete(
            '/aturan/{aturan}',
            [SuperadminAturanPerusahaanController::class, 'destroy']
        )->name('aturan.destroy');

        /*
        |--------------------------------------------------------------------------
        | Kelola Jam Absensi
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/jam-absensi',
            [SuperadminJamAbsensiController::class, 'index']
        )->name('jam-absensi.index');

        Route::put(
            '/jam-absensi',
            [SuperadminJamAbsensiController::class, 'update']
        )->name('jam-absensi.update');

        Route::patch(
            '/jam-absensi/reset',
            [SuperadminJamAbsensiController::class, 'reset']
        )->name('jam-absensi.reset');


        /*
        |--------------------------------------------------------------------------
        | Kelola Metode Pembayaran
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/metode-pembayaran',
            [SuperadminMetodePembayaranController::class, 'index']
        )->name('metode-pembayaran.index');

        Route::put(
            '/metode-pembayaran/nominal',
            [SuperadminMetodePembayaranController::class, 'updateNominal']
        )->name('metode-pembayaran.nominal.update');

        Route::post(
            '/metode-pembayaran/rekening',
            [SuperadminMetodePembayaranController::class, 'storeBank']
        )->name('metode-pembayaran.bank.store');

        Route::put(
            '/metode-pembayaran/rekening/{bank}',
            [SuperadminMetodePembayaranController::class, 'updateBank']
        )->name('metode-pembayaran.bank.update');

        Route::delete(
            '/metode-pembayaran/rekening/{bank}',
            [SuperadminMetodePembayaranController::class, 'destroyBank']
        )->name('metode-pembayaran.bank.destroy');
    });

/*
|--------------------------------------------------------------------------
| Admin
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get(
            '/dashboard',
            AdminDashboardController::class
        )->name('dashboard');

        /*
        |--------------------------------------------------------------------------
        | Menu di bawah ini belum ada controller/view-nya.
        | Sidebar otomatis tampil "Soon" sampai route-nya didaftarkan di sini,
        | dengan nama route yang SAMA seperti di resources/views/partials/sidebar.blade.php
        |--------------------------------------------------------------------------
        */

        // Route::resource('permintaan-magang', PermintaanMagangController::class);
        // Route::resource('peserta', PesertaController::class);
        // Route::resource('absensi', AbsensiController::class);
        // Route::resource('tugas', TugasController::class);
        // Route::resource('pengumpulan-tugas', PengumpulanTugasController::class);
        // Route::resource('metode-pembayaran', MetodePembayaranController::class);
        // Route::resource('pembayaran', PembayaranController::class);
        // Route::get('laporan/peserta', [LaporanController::class, 'peserta'])->name('laporan.peserta');
        // Route::get('laporan/absensi', [LaporanController::class, 'absensi'])->name('laporan.absensi');
        // Route::get('laporan/penugasan', [LaporanController::class, 'penugasan'])->name('laporan.penugasan');
        // Route::get('laporan/pembayaran', [LaporanController::class, 'pembayaran'])->name('laporan.pembayaran');
    });

/*
|--------------------------------------------------------------------------
| Fitur Pengguna Terautentikasi
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    /*
    |--------------------------------------------------------------------------
    | Pencarian Portal
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/search',
        [PortalSearchController::class, 'index']
    )->name('search.index');

    Route::get(
        '/search/suggestions',
        [PortalSearchController::class, 'suggestions']
    )->name('search.suggestions');

    /*
    |--------------------------------------------------------------------------
    | Profil
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/profile',
        [ProfileController::class, 'edit']
    )->name('profile.edit');

    Route::patch(
        '/profile',
        [ProfileController::class, 'update']
    )->name('profile.update');

    /*
    |--------------------------------------------------------------------------
    | Foto Profil
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/profile/photo',
        [ProfileController::class, 'showPhoto']
    )->name('profile.photo.show');

    Route::patch(
        '/profile/photo',
        [ProfileController::class, 'updatePhoto']
    )->name('profile.photo.update');

    Route::delete(
        '/profile/photo',
        [ProfileController::class, 'destroyPhoto']
    )->name('profile.photo.destroy');

    /*
    |--------------------------------------------------------------------------
    | Hapus Akun
    |--------------------------------------------------------------------------
    */

    Route::delete(
        '/profile',
        [ProfileController::class, 'destroy']
    )->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
*/

require __DIR__ . '/auth.php';
