<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\NotifikasiController;

// Superadmin Controllers
use App\Http\Controllers\Superadmin\AdminController as SuperadminAdminController;
use App\Http\Controllers\Superadmin\AturanPerusahaanController as SuperadminAturanPerusahaanController;
use App\Http\Controllers\Superadmin\DashboardController as SuperadminDashboardController;
use App\Http\Controllers\Superadmin\JamAbsensiController as SuperadminJamAbsensiController;
use App\Http\Controllers\Superadmin\MetodePembayaranController as SuperadminMetodePembayaranController;


// Admin Controllers

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\PesertaMagangController as AdminPesertaMagangController;
use App\Http\Controllers\Admin\LaporanPesertaController as AdminLaporanPesertaController;
use App\Http\Controllers\Admin\LaporanPembayaranController as AdminLaporanPembayaranController;
use App\Http\Controllers\Admin\LaporanAbsensiController as AdminLaporanAbsensiController;
use App\Http\Controllers\Admin\LaporanPenugasanController as AdminLaporanPenugasanController;
use App\Http\Controllers\Admin\TugasController as AdminTugasController;
use App\Http\Controllers\Admin\PermintaanMagangController as AdminPermintaanMagangController;
use App\Http\Controllers\Admin\DataAbsensiController as AdminDataAbsensiController;
use App\Http\Controllers\Admin\DataPembayaranController as AdminDataPembayaranController;
use App\Http\Controllers\Admin\DataMetodePembayaranController as AdminDataMetodePembayaranController;
use App\Http\Controllers\Admin\PengumpulanTugasController as AdminPengumpulanTugasController;

// Peserta Magang Controllers
use App\Http\Controllers\PesertaMagang\DashboardController as PesertaMagangDashboardController;
use App\Http\Controllers\PesertaMagang\AbsensiController as PesertaMagangAbsensiController;
use App\Http\Controllers\PesertaMagang\PenugasanController as PesertaMagangPenugasanController;

use App\Http\Controllers\PesertaMagang\PembayaranController as PesertaMagangPembayaranController;
use App\Http\Controllers\PesertaMagang\LaporanMingguanController as PesertaMagangLaporanMingguanController;
use App\Http\Controllers\Peserta\TugasController as PesertaTugasController;


use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Halaman Awal dan Registrasi
|--------------------------------------------------------------------------
*/


Route::get('/', static fn () => redirect()->route('login'));

Route::middleware('guest')->group(function (): void {
    Route::get('/register/pelamar', function () {
        session(['register_role' => 'pelamar']);

        return redirect()->route('register');
    })->name('register.pelamar');

    Route::get('/register/karyawan', function () {
        session(['register_role' => 'karyawan']);

        return redirect()->route('register');
    })->name('register.karyawan');
});

/*
|--------------------------------------------------------------------------
| Dashboard Umum
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->get('/dashboard', function () {
    $user = auth()->user();

    return match ($user?->role) {
        'superadmin' => redirect()->route('superadmin.dashboard'),
        'admin'      => redirect()->route('admin.dashboard'),
        'peserta'    => redirect()->route('peserta-magang.dashboard'),
        default      => view('dashboard'),
    };
})->name('dashboard');

/*
|--------------------------------------------------------------------------
| Profil Pengguna & Notifikasi
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function (): void {
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');

    Route::patch('/profile/photo', [ProfileController::class, 'updatePhoto'])
        ->name('profile.photo.update');

    Route::delete('/profile/photo', [ProfileController::class, 'destroyPhoto'])
        ->name('profile.photo.destroy');

    Route::get('/profile/photo', [ProfileController::class, 'showPhoto'])
        ->name('profile.photo.show');

    // Notifikasi
    Route::patch('/notifikasi/baca-semua', [NotifikasiController::class, 'tandaiSemuaDibacaWeb'])
        ->name('notifikasi.read-all');

    Route::patch('/notifikasi/{notifikasi}/baca', [NotifikasiController::class, 'tandaiDibacaWeb'])
        ->whereNumber('notifikasi')
        ->name('notifikasi.read');
});

/*
|--------------------------------------------------------------------------
| Super Admin
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:superadmin'])
    ->prefix('superadmin')
    ->name('superadmin.')
    ->group(function (): void {
        Route::get('/dashboard', SuperadminDashboardController::class)
            ->name('dashboard');

        // Kelola admin.
        Route::get('/admin', [SuperadminAdminController::class, 'index'])
            ->name('admin');
        Route::post('/admin', [SuperadminAdminController::class, 'store'])
            ->name('admin.store');
        Route::put('/admin/{admin}', [SuperadminAdminController::class, 'update'])
            ->name('admin.update');
        Route::delete('/admin/{admin}', [SuperadminAdminController::class, 'destroy'])
            ->name('admin.destroy');

        // Kelola aturan perusahaan.
        Route::get('/aturan', [SuperadminAturanPerusahaanController::class, 'index'])
            ->name('aturan.index');
        Route::post('/aturan', [SuperadminAturanPerusahaanController::class, 'store'])
            ->name('aturan.store');
        Route::put('/aturan/{aturan}', [SuperadminAturanPerusahaanController::class, 'update'])
            ->name('aturan.update');
        Route::delete('/aturan/{aturan}', [SuperadminAturanPerusahaanController::class, 'destroy'])
            ->name('aturan.destroy');

        // Kelola jam absensi.
        Route::get('/jam-absensi', [SuperadminJamAbsensiController::class, 'index'])
            ->name('jam-absensi.index');
        Route::put('/jam-absensi', [SuperadminJamAbsensiController::class, 'update'])
            ->name('jam-absensi.update');
        Route::patch('/jam-absensi/reset', [SuperadminJamAbsensiController::class, 'reset'])
            ->name('jam-absensi.reset');

        // Kelola metode pembayaran.
        Route::get('/metode-pembayaran', [SuperadminMetodePembayaranController::class, 'index'])
            ->name('metode-pembayaran.index');
        Route::put('/metode-pembayaran/nominal', [SuperadminMetodePembayaranController::class, 'updateNominal'])
            ->name('metode-pembayaran.nominal.update');
        Route::post('/metode-pembayaran/rekening', [SuperadminMetodePembayaranController::class, 'storeBank'])
            ->name('metode-pembayaran.bank.store');
        Route::put('/metode-pembayaran/rekening/{bank}', [SuperadminMetodePembayaranController::class, 'updateBank'])
            ->name('metode-pembayaran.bank.update');
        Route::delete('/metode-pembayaran/rekening/{bank}', [SuperadminMetodePembayaranController::class, 'destroyBank'])
            ->name('metode-pembayaran.bank.destroy');
    });

/*
|--------------------------------------------------------------------------
| Admin
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function (): void {
        Route::get('/dashboard', AdminDashboardController::class)
            ->name('dashboard');

        /* Data Peserta Magang */
        Route::get('/peserta/template', function () {
            $templatePath = public_path('template/template_peserta_magang.xlsx');

            abort_unless(
                file_exists($templatePath),
                404,
                'File template peserta magang tidak ditemukan.'
            );

            return response()->download(
                $templatePath,
                'template_peserta_magang.xlsx'
            );
        })->name('peserta.template');

        Route::post('/peserta/import', [AdminPesertaMagangController::class, 'import'])
            ->name('peserta.import');

        Route::patch('/peserta/{peserta_magang}/status', [AdminPesertaMagangController::class, 'updateStatus'])
            ->name('peserta.status');

        // Kelola Data Peserta Magang (Hapus except jika butuh show & edit, atau cukup kecualikan create saja)
        Route::resource('peserta', AdminPesertaMagangController::class)
            ->except(['create'])
            ->parameters(['peserta' => 'peserta_magang']);

        /* Permintaan magang. */
        Route::get('/permintaan', [AdminPermintaanMagangController::class, 'index'])
            ->name('permintaan.index');

        Route::post('/permintaan/action/{id}', [AdminPermintaanMagangController::class, 'action'])
            ->whereNumber('id')
            ->name('permintaan.action');

        /* Kelola laporan peserta magang. */
        Route::resource('laporan-peserta', AdminLaporanPesertaController::class)
            ->only(['index', 'store', 'update', 'destroy'])
            ->parameters(['laporan-peserta' => 'peserta_magang']);

        /* Laporan pembayaran, penugasan, dan absensi. */
        Route::get('/laporan/pembayaran', [AdminLaporanPembayaranController::class, 'index'])
            ->name('laporan.pembayaran');

        Route::get('/laporan/penugasan', [AdminLaporanPenugasanController::class, 'index'])
            ->name('laporan.penugasan');

        Route::get('/laporan/absensi', [AdminLaporanAbsensiController::class, 'index'])
            ->name('laporan.absensi');

        /* Kelola tugas magang. */
        Route::get('/tugas', [AdminTugasController::class, 'index'])
            ->name('tugas.index');

        Route::post('/tugas', [AdminTugasController::class, 'store'])
            ->name('tugas.store');

        Route::post('/tugas/upload', [AdminTugasController::class, 'upload'])
            ->name('tugas.upload');

        Route::get('/tugas/panduan/download', [AdminTugasController::class, 'downloadPanduan'])
            ->name('tugas.panduan.download');

        Route::get('/tugas/template/download', [AdminTugasController::class, 'downloadTemplate'])
            ->name('tugas.template.download');

        Route::post('/tugas/template-laporan', [AdminTugasController::class, 'storeTemplateLaporan'])
            ->name('tugas.template-laporan.store');

        Route::delete('/tugas/template-laporan/{templateLaporan}', [AdminTugasController::class, 'destroyTemplateLaporan'])
            ->name('tugas.template-laporan.destroy');

        Route::put('/tugas/{tugas}', [AdminTugasController::class, 'update'])
            ->name('tugas.update');

        Route::delete('/tugas/{tugas}', [AdminTugasController::class, 'destroy'])
            ->name('tugas.destroy');

        /* Pengumpulan tugas. */
        Route::get('/pengumpulan-tugas', [AdminPengumpulanTugasController::class, 'index'])
            ->name('pengumpulan-tugas.index');

        Route::post('/pengumpulan-tugas/penugasan/{penugasan}/ingatkan', [AdminPengumpulanTugasController::class, 'remind'])
            ->whereNumber('penugasan')
            ->name('pengumpulan-tugas.remind');

        Route::get('/pengumpulan-tugas/{pengumpulan}/file', [AdminPengumpulanTugasController::class, 'file'])
            ->whereNumber('pengumpulan')
            ->name('pengumpulan-tugas.file');

        Route::get('/pengumpulan-tugas/{pengumpulan}', [AdminPengumpulanTugasController::class, 'show'])
            ->whereNumber('pengumpulan')
            ->name('pengumpulan-tugas.show');

        /* Data absensi. */
        Route::get('/absensi', [AdminDataAbsensiController::class, 'index'])
            ->name('absensi.index');

        /* Metode pembayaran. */
        Route::get('/metode-pembayaran', [AdminDataMetodePembayaranController::class, 'index'])
            ->name('metode-pembayaran.index');

        Route::put('/metode-pembayaran/nominal', [AdminDataMetodePembayaranController::class, 'updateNominal'])
            ->name('metode-pembayaran.nominal.update');

        Route::post('/metode-pembayaran/rekening', [AdminDataMetodePembayaranController::class, 'storeBank'])
            ->name('metode-pembayaran.bank.store');

        Route::put('/metode-pembayaran/rekening/{bank}', [AdminDataMetodePembayaranController::class, 'updateBank'])
            ->name('metode-pembayaran.bank.update');

        Route::delete('/metode-pembayaran/rekening/{bank}', [AdminDataMetodePembayaranController::class, 'destroyBank'])
            ->name('metode-pembayaran.bank.destroy');

        /* Data pembayaran. */
        Route::get('/pembayaran', [AdminDataPembayaranController::class, 'index'])
            ->name('pembayaran.index');

        Route::patch('/pembayaran/{pembayaran}/terima', [AdminDataPembayaranController::class, 'terima'])
            ->name('pembayaran.terima');

        Route::patch('/pembayaran/{pembayaran}/tolak', [AdminDataPembayaranController::class, 'tolak'])
            ->name('pembayaran.tolak');
    });

/*
|--------------------------------------------------------------------------
| Peserta Magang
|--------------------------------------------------------------------------
*/


Route::middleware(['auth', 'role:peserta'])
    ->prefix('peserta-magang')
    ->name('peserta-magang.')
    ->group(function (): void {
        // Dashboard
        Route::get('/dashboard', [PesertaMagangDashboardController::class, 'index'])->name('dashboard');

        // Absensi
        Route::get('/absensi', [PesertaMagangAbsensiController::class, 'index'])->name('absensi.index');
        Route::post('/absensi', [PesertaMagangAbsensiController::class, 'store'])->name('absensi.store');

        // Penugasan
        Route::get('/penugasan', [PesertaMagangPenugasanController::class, 'index'])->name('penugasan.index');
        Route::post('/penugasan/{id_tugas}/kumpul', [PesertaMagangPenugasanController::class, 'store'])->name('penugasan.store');

        // Fitur penugasan alternatif dari main branch
        Route::get('/tugas', [PesertaTugasController::class, 'index'])->name('tugas.index');
        Route::get('/tugas/{penugasan}/file', [PesertaTugasController::class, 'downloadTask'])->name('tugas.file.download');
        Route::get('/tugas/{penugasan}/template-laporan', [PesertaTugasController::class, 'downloadReportTemplate'])->name('tugas.template-laporan.download');
        Route::post('/tugas/{penugasan}/kumpulkan', [PesertaTugasController::class, 'submit'])->name('tugas.submit');

        // Pembayaran
        Route::get('/pembayaran', [PesertaMagangPembayaranController::class, 'index'])->name('pembayaran.index');
        Route::post('/pembayaran', [PesertaMagangPembayaranController::class, 'store'])->name('pembayaran.store');

        // Laporan Mingguan
        Route::get('/laporan-mingguan', [PesertaMagangLaporanMingguanController::class, 'index'])->name('laporan-mingguan.index');
        Route::post('/laporan-mingguan', [PesertaMagangLaporanMingguanController::class, 'store'])->name('laporan-mingguan.store');
    });

/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
*/

require __DIR__ . '/auth.php';