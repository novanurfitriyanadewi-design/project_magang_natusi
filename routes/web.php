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
use App\Http\Controllers\Admin\AbsensiKaryawanController as AdminAbsensiKaryawanController;
use App\Http\Controllers\Admin\DataPembayaranController as AdminDataPembayaranController;
use App\Http\Controllers\Admin\DataMetodePembayaranController as AdminDataMetodePembayaranController;
use App\Http\Controllers\Admin\PengumpulanTugasController as AdminPengumpulanTugasController;
use App\Http\Controllers\Admin\PermintaanLamaranController as AdminPermintaanLamaranController;
use App\Http\Controllers\Admin\KaryawanController;

// Peserta Magang Controllers
use App\Http\Controllers\PesertaMagang\DashboardController as PesertaMagangDashboardController;
use App\Http\Controllers\PesertaMagang\AbsensiController as PesertaMagangAbsensiController;
use App\Http\Controllers\PesertaMagang\PenugasanController as PesertaMagangPenugasanController;
use App\Http\Controllers\PesertaMagang\AturanController as PesertaAturanController;
use App\Http\Controllers\PesertaMagang\PembayaranController as PesertaMagangPembayaranController;
use App\Http\Controllers\PesertaMagang\LaporanMingguanController as PesertaMagangLaporanMingguanController;
use App\Http\Controllers\Peserta\TugasController as PesertaTugasController;

// Karyawan Controllers
use App\Http\Controllers\Karyawan\DashboardController as KaryawanDashboardController;
use App\Http\Controllers\Karyawan\ResignController;
use App\Http\Controllers\Karyawan\PengumumanController; // Sesuaikan jika ada
use App\Http\Controllers\Karyawan\AturanController;
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
| Redirect Dashboard Utama (Sesuai Role Pengguna)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->get('/dashboard', function () {
    $user = auth()->user();

    return match ($user?->role) {
        'superadmin'                => redirect()->route('superadmin.dashboard'),
        'admin'                     => redirect()->route('admin.dashboard'),
        'karyawan'                  => redirect()->route('karyawan.dashboard'),
        'pelamar', 'pelamar_karyawan' => redirect()->route('pengajuan.status'),
        'peserta'                   => redirect()->route('peserta-magang.dashboard'),
        default                     => view('dashboard'),
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
    // PENTING: route 'baca-semua' harus didaftarkan SEBELUM '{notifikasi}/baca',
    // supaya kata 'baca-semua' tidak ketangkep sebagai parameter {notifikasi}.
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

        // Kelola admin
        Route::get('/admin', [SuperadminAdminController::class, 'index'])
            ->name('admin');
        Route::post('/admin', [SuperadminAdminController::class, 'store'])
            ->name('admin.store');
        Route::put('/admin/{admin}', [SuperadminAdminController::class, 'update'])
            ->name('admin.update');
        Route::delete('/admin/{admin}', [SuperadminAdminController::class, 'destroy'])
            ->name('admin.destroy');

        // Kelola aturan perusahaan
        Route::get('/aturan', [SuperadminAturanPerusahaanController::class, 'index'])
            ->name('aturan.index');
        Route::post('/aturan', [SuperadminAturanPerusahaanController::class, 'store'])
            ->name('aturan.store');
        Route::put('/aturan/{aturan}', [SuperadminAturanPerusahaanController::class, 'update'])
            ->name('aturan.update');
        Route::delete('/aturan/{aturan}', [SuperadminAturanPerusahaanController::class, 'destroy'])
            ->name('aturan.destroy');

        // Kelola jam absensi
        Route::get('/jam-absensi', [SuperadminJamAbsensiController::class, 'index'])
            ->name('jam-absensi.index');
        Route::put('/jam-absensi', [SuperadminJamAbsensiController::class, 'update'])
            ->name('jam-absensi.update');
        Route::patch('/jam-absensi/reset', [SuperadminJamAbsensiController::class, 'reset'])
            ->name('jam-absensi.reset');

        // Kelola metode pembayaran
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
| Admin & Karyawan
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:admin,karyawan'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function (): void {
        Route::get('/dashboard', AdminDashboardController::class)
            ->name('dashboard');

        /* Data Peserta Magang */
        Route::get('/peserta/template', function () {
            $templatePath = public_path('template/peserta_magang.xlsx');

            abort_unless(
                file_exists($templatePath),
                404,
                'File template peserta magang tidak ditemukan.'
            );

            return response()->download(
                $templatePath,
                'peserta_magang.xlsx'
            );
        })->name('peserta.template');

        Route::post('/peserta/import', [AdminPesertaMagangController::class, 'import'])
            ->name('peserta.import');

        Route::patch('/peserta/{peserta_magang}/status', [AdminPesertaMagangController::class, 'updateStatus'])
            ->name('peserta.status');

        Route::resource('peserta', AdminPesertaMagangController::class)
            ->except(['create'])
            ->parameters(['peserta' => 'peserta_magang']);


        /* Permintaan Magang */
        Route::get('/permintaan', [AdminPermintaanMagangController::class, 'index'])
            ->name('permintaan.index');

        Route::post('/permintaan/action/{id}', [AdminPermintaanMagangController::class, 'action'])
            ->whereNumber('id')
            ->name('permintaan.action');

        /* Kelola Permintaan Lamaran Registrasi Karyawan */
        Route::get('/permintaan-lamaran', [AdminPermintaanLamaranController::class, 'index'])
            ->name('permintaan-lamaran.index');

        Route::post('/permintaan-lamaran/{id}/action', [AdminPermintaanLamaranController::class, 'action'])
            ->whereNumber('id')
            ->name('permintaan-lamaran.action');

        /* Kelola Laporan Peserta Magang */
        Route::resource('laporan-peserta', AdminLaporanPesertaController::class)
            ->only(['index', 'store', 'update', 'destroy'])
            ->parameters(['laporan-peserta' => 'peserta_magang']);

        /* Laporan Pembayaran, Penugasan, dan Absensi */
        Route::get('/laporan/pembayaran', [AdminLaporanPembayaranController::class, 'index'])
            ->name('laporan.pembayaran');

        Route::get('/laporan/penugasan', [AdminLaporanPenugasanController::class, 'index'])
            ->name('laporan.penugasan');

        Route::get('/laporan/absensi', [AdminLaporanAbsensiController::class, 'index'])
            ->name('laporan.absensi');

        /* Kelola Tugas Magang */
        Route::get('/tugas', [AdminTugasController::class, 'index'])
            ->name('tugas.index');

        Route::post('/tugas', [AdminTugasController::class, 'store'])
            ->name('tugas.store');

        Route::post('/tugas/upload', [AdminTugasController::class, 'upload'])
            ->name('tugas.upload');

        // Panduan materi & tugas — public/template/MATERI DAN TUGAS.xlsx
        // Nama final route: admin.tugas.panduan.download (jangan didaftarkan dobel dengan prefix /admin lagi)
        Route::get('/tugas/panduan/download', [AdminTugasController::class, 'downloadPanduan'])
            ->name('tugas.panduan.download');

        // Template Excel tugas mingguan — public/template/template_tugas_mingguan.xlsx
        // Satu method saja (downloadTemplate) dipakai oleh 2 nama route, biar view lama/baru sama-sama jalan
        Route::get('/tugas/template-excel/download', [AdminTugasController::class, 'downloadTemplate'])
            ->name('tugas.template-excel.download');

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

        /* Pengumpulan Tugas */
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

        /* Data Absensi */
        Route::get('/absensi', [AdminDataAbsensiController::class, 'index'])
            ->name('absensi.index');

        /* Absensi Karyawan */
        Route::get('/absensi-karyawan/export', [AdminAbsensiKaryawanController::class, 'export'])
            ->name('absensi-karyawan.export');
        Route::get('/absensi-karyawan', [AdminAbsensiKaryawanController::class, 'index'])
            ->name('absensi-karyawan.index');
        Route::get('/absensi-karyawan/create', [AdminAbsensiKaryawanController::class, 'create'])
            ->name('absensi-karyawan.create');
        Route::post('/absensi-karyawan', [AdminAbsensiKaryawanController::class, 'store'])
            ->name('absensi-karyawan.store');
        Route::get('/absensi-karyawan/{absensiKaryawan}', [AdminAbsensiKaryawanController::class, 'show'])
            ->whereNumber('absensiKaryawan')
            ->name('absensi-karyawan.show');
        Route::get('/absensi-karyawan/{absensiKaryawan}/edit', [AdminAbsensiKaryawanController::class, 'edit'])
            ->whereNumber('absensiKaryawan')
            ->name('absensi-karyawan.edit');
        Route::put('/absensi-karyawan/{absensiKaryawan}', [AdminAbsensiKaryawanController::class, 'update'])
            ->whereNumber('absensiKaryawan')
            ->name('absensi-karyawan.update');
        Route::delete('/absensi-karyawan/{absensiKaryawan}', [AdminAbsensiKaryawanController::class, 'destroy'])
            ->whereNumber('absensiKaryawan')
            ->name('absensi-karyawan.destroy');
        /* Metode Pembayaran */
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

        /* Data Karyawan */
        Route::get('/karyawan', [KaryawanController::class, 'index'])
        ->name('karyawan.index');

        Route::put('/karyawan/{karyawan}', [KaryawanController::class, 'update'])
        ->name('karyawan.update');
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

        // Aturan Perusahaan
        Route::get('/aturan', [PesertaAturanController::class, 'index'])->name('aturan.index');

        // Fitur penugasan alternatif
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
| Dashboard Karyawan
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:karyawan'])
    ->prefix('karyawan')
    ->name('karyawan.')
    ->group(function (): void {
        Route::get('/dashboard', [KaryawanDashboardController::class, 'index'])->name('dashboard');

        // Tambahkan rute-rute berikut agar tidak error:
        Route::get('/absensi', [KaryawanDashboardController::class, 'absensiIndex'])->name('absensi.index');
        Route::post('/absensi/clock-in', [KaryawanDashboardController::class, 'clockIn'])->name('absensi.clockin');

        Route::get('/resign/create', [ResignController::class, 'create'])->name('resign.create');
        Route::post('/resign', [ResignController::class, 'store'])->name('resign.store');
        Route::get('/resign/{resign}', [ResignController::class, 'show'])->name('resign.show');

        Route::get('/cuti', [KaryawanDashboardController::class, 'cutiIndex'])->name('cuti.index'); // Sesuaikan controller
        Route::get('/payslip', [KaryawanDashboardController::class, 'payslipIndex'])->name('payslip.index'); // Sesuaikan controller
        Route::get('/reimbursement', [KaryawanDashboardController::class, 'reimbursementIndex'])->name('reimbursement.index'); // Sesuaikan controller
        Route::get('/profil/edit', [ProfileController::class, 'edit'])->name('profil.edit'); // Sesuaikan controller

        Route::get('/pengumuman', [PengumumanController::class, 'index'])->name('pengumuman.index');
        Route::get('/pengumuman/{pengumuman}', [PengumumanController::class, 'show'])->name('pengumuman.show');

        Route::get('/aturan', [AturanController::class, 'index'])->name('aturan.index');
        Route::get('/helpdesk', [KaryawanDashboardController::class, 'helpdeskIndex'])->name('helpdesk.index'); // Sesuaikan controller
    });
/*
|--------------------------------------------------------------------------
| Status Pengajuan Pelamar & Karyawan / Peserta Baru
|--------------------------------------------------------------------------
*/

// PERBAIKAN: Role karyawan dan peserta ditambahkan agar bisa membuka halaman ini setelah disetujui
Route::middleware(['auth', 'role:pelamar,pelamar_karyawan,karyawan,peserta'])->group(function () {
    Route::get('/pengajuan/status', function () {
        $user = auth()->user();

        // Mark session bahwa pengguna sudah melihat kredensial
        session(['has_seen_credentials' => true]);

        // 1. Coba cari di PermintaanMagang lebih dulu
        $permintaan = \App\Models\PermintaanMagang::where('user_id', $user->id_user)
            ->orWhere('email', $user->email)
            ->latest('id_permintaan')
            ->first();

        // 2. Jika tidak ada dan ada model PermintaanLamaran (karyawan), ambil dari sana
        if (! $permintaan && class_exists('\App\Models\PermintaanLamaran')) {
            $permintaan = \App\Models\PermintaanLamaran::where('user_id', $user->id_user)
                ->orWhere('email', $user->email)
                ->latest('id_permintaan')
                ->first();
        }

        // 3. Mengambil notifikasi terkait user
        $notifications = \App\Models\Notifikasi::where('user_id', $user->id_user)
            ->latest()
            ->get();

        $unreadNotificationCount = $notifications->where('dibaca', false)->count();

        return view('auth.status-pengajuan', [
            'permintaan' => $permintaan,
            'notifications' => $notifications,
            'unreadNotificationCount' => $unreadNotificationCount,
        ]);
    })->name('pengajuan.status');
});

/*
|--------------------------------------------------------------------------
| Authentication Routes (Breeze / Fortify)
|--------------------------------------------------------------------------
*/

require __DIR__ . '/auth.php';