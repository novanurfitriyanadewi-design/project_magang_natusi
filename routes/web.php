<?php

use App\Http\Controllers\PortalSearchController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Superadmin\AdminController as SuperadminAdminController;
use App\Http\Controllers\Superadmin\AturanPerusahaanController as SuperadminAturanPerusahaanController;
use App\Http\Controllers\Superadmin\DashboardController as SuperadminDashboardController;
use App\Http\Controllers\Superadmin\JamAbsensiController as SuperadminJamAbsensiController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\PesertaMagangController as AdminPesertaMagangController;
use App\Http\Controllers\Admin\LaporanPesertaController as AdminLaporanPesertaController;
use App\Http\Controllers\Admin\LaporanPembayaranController as AdminLaporanPembayaranController;
use App\Http\Controllers\Admin\LaporanAbsensiController as AdminLaporanAbsensiController;
use App\Http\Controllers\Admin\LaporanPenugasanController as AdminLaporanPenugasanController;
use App\Http\Controllers\Superadmin\MetodePembayaranController as SuperadminMetodePembayaranController;
use App\Http\Controllers\Admin\PermintaanMagangController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

/* Halaman Awal */

Route::get('/', function () {
    return redirect()->route('login');
});

/* Registrasi */

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

/* Dashboard Umum */

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

/* Super Admin */

Route::middleware(['auth', 'role:superadmin'])
    ->prefix('superadmin')
    ->name('superadmin.')
    ->group(function () {
        Route::get(
            '/dashboard',
            SuperadminDashboardController::class
        )->name('dashboard');

        /* Kelola Admin */

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

        /* Kelola Aturan Perusahaan */

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

        /* Kelola Jam Absensi */

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


        /* Kelola Metode Pembayaran */

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

/* Admin */

Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get(
            '/dashboard',
            AdminDashboardController::class
        )->name('dashboard');

        /* Kelola Data Peserta Magang */

        Route::resource('peserta', AdminPesertaMagangController::class)
            ->except(['create', 'show', 'edit']) 
            ->parameters(['peserta' => 'peserta_magang']); 

        
        /* Kelola Laporan Peserta Magang */

        Route::resource('laporan-peserta', AdminLaporanPesertaController::class)
            ->only(['index', 'store', 'update', 'destroy'])
            ->parameters(['laporan-peserta' => 'peserta_magang']);

        Route::get('/laporan/pembayaran', [AdminLaporanPembayaranController::class, 'index'])
            ->name('laporan.pembayaran');

        Route::get('/laporan/penugasan', [AdminLaporanPenugasanController::class, 'index'])
            ->name('laporan.penugasan');

        Route::get('/laporan/absensi', [AdminLaporanAbsensiController::class, 'index'])
            ->name('laporan.absensi');

        /* Menu Sementara / Placeholder Sidebar */

        Route::get('/permintaan', function () {
            return view('admin.permintaan-magang');
        })->name('permintaan.index');

        Route::get('/absensi', function () {
            return view('admin-absensi');
        })->name('absensi.index');

        Route::get('/tugas', function () {
            return view('admin-tugas');
        })->name('tugas.index');

        Route::get('/pengumpulan-tugas', function () {
            return view('admin-pengumpulantugas');
        })->name('pengumpulan-tugas.index');

        Route::get('/metode-pembayaran', function () {
            return view('admin-metodepembayaran');
        })->name('metode-pembayaran.index');

        // ROUTE DATA PEMBAYARAN (Baru ditambahkan agar tidak "Soon")
        Route::get('/pembayaran', function () {
            return view('admin-pembayaran');
        })->name('pembayaran.index');
    });

/* Fitur Pengguna Terautentikasi */

// --- ROUTE ADMIN (Mulai Baris 228) ---
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/permintaan', function () {
        $total_pendaftar = \Illuminate\Support\Facades\DB::table('permintaan_magang')->count();
        $total_menunggu   = \Illuminate\Support\Facades\DB::table('permintaan_magang')->where('status', 'menunggu')->count();
        $total_diterima   = \Illuminate\Support\Facades\DB::table('permintaan_magang')->where('status', 'diterima')->count();

        $query = \Illuminate\Support\Facades\DB::table('permintaan_magang');

        // Filter berdasarkan tombol status
        if (request()->has('status') && request('status') !== 'all') {
            $query->where('status', request('status'));
        }

        // Filter berdasarkan Pencarian (Nama / Institusi)
        if (request()->filled('search')) {
            $search = request('search');
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('institusi', 'like', "%{$search}%");
            });
        }

        $permintaan_magang = $query->orderBy('id', 'desc')->paginate(10);

        return view('admin.permintaan-magang', compact(
            'permintaan_magang',
            'total_pendaftar',
            'total_menunggu',
            'total_diterima'
        ));
    })->name('permintaan.index');

    Route::post('/permintaan/action/{id}', function ($id) {
        $action = request('action');
        $pendaftar = \Illuminate\Support\Facades\DB::table('permintaan_magang')->where('id', $id)->first();
        
        if (!$pendaftar) {
            return redirect()->back()->with('error', 'Data pendaftar tidak ditemukan.');
        }

        $statusBaru = ($action === 'accept') ? 'diterima' : 'ditolak';
        $pesanText  = ($action === 'accept') ? 'DITERIMA' : 'DITOLAK';

        \Illuminate\Support\Facades\DB::table('permintaan_magang')
            ->where('id', $id)
            ->update(['status' => $statusBaru]);

        return redirect()->back()->with('success', "Akses pendaftaran {$pendaftar->nama} berhasil di-{$pesanText}.");
    })->name('permintaan.action');
}); // <--- Penutup group middleware admin

/* Authentication */
require __DIR__ . '/auth.php';