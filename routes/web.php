<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Profile & Shared Controllers
use App\Http\Controllers\ProfileController;

// Superadmin Controllers
use App\Http\Controllers\Superadmin\AdminController as SuperadminAdminController;
use App\Http\Controllers\Superadmin\AturanPerusahaanController as SuperadminAturanPerusahaanController;
use App\Http\Controllers\Superadmin\DashboardController as SuperadminDashboardController;
use App\Http\Controllers\Superadmin\JamAbsensiController as SuperadminJamAbsensiController;
use App\Http\Controllers\Superadmin\MetodePembayaranController as SuperadminMetodePembayaranController;
use App\Http\Controllers\Superadmin\SuperadminDivisiController;

// Admin Peserta Controllers
use App\Http\Controllers\AdminPeserta\DashboardController as AdminPesertaDashboardController;
use App\Http\Controllers\AdminPeserta\PesertaMagangController as AdminPesertaMagangController;
use App\Http\Controllers\AdminPeserta\LaporanPesertaController as AdminLaporanPesertaController;
use App\Http\Controllers\AdminPeserta\LaporanPembayaranController as AdminLaporanPembayaranController;
use App\Http\Controllers\AdminPeserta\LaporanAbsensiController as AdminLaporanAbsensiController;
use App\Http\Controllers\AdminPeserta\LaporanPenugasanController as AdminLaporanPenugasanController;
use App\Http\Controllers\AdminPeserta\TugasController as AdminTugasController;
use App\Http\Controllers\AdminPeserta\PermintaanMagangController as AdminPermintaanMagangController;
use App\Http\Controllers\AdminPeserta\DataAbsensiController as AdminDataAbsensiController;
use App\Http\Controllers\AdminPeserta\DataPembayaranController as AdminDataPembayaranController;
use App\Http\Controllers\AdminPeserta\DataMetodePembayaranController as AdminDataMetodePembayaranController;
use App\Http\Controllers\AdminPeserta\PengumpulanTugasController as AdminPengumpulanTugasController;
use App\Http\Controllers\AdminPeserta\NotifikasiController as AdminNotifikasiController;
use App\Http\Controllers\AdminPeserta\NotifikasiController as UserNotifikasiController;

// Admin Karyawan Controllers
use App\Http\Controllers\AdminKaryawan\DashboardController as AdminKaryawanDashboardController;
use App\Http\Controllers\AdminKaryawan\KaryawanController as AdminKaryawanController;
use App\Http\Controllers\AdminKaryawan\AbsensiKaryawanController as AdminAbsensiKaryawanController;
use App\Http\Controllers\AdminKaryawan\PermintaanLamaranController as AdminPermintaanLamaranController;
use App\Http\Controllers\AdminKaryawan\PembayaranKaryawanController as AdminPembayaranKaryawanController;
use App\Http\Controllers\AdminKaryawan\ResignController as AdminResignController;
use App\Http\Controllers\AdminKaryawan\LaporanAbsensiKaryawanController;
use App\Http\Controllers\AdminKaryawan\LaporanKaryawanController;
use App\Http\Controllers\AdminKaryawan\PengumumanController as AdminKaryawanPengumumanController; 

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
use App\Http\Controllers\Karyawan\PengumumanController;
use App\Http\Controllers\Karyawan\AturanController;

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
        'admin',
        'admin_peserta'             => redirect()->route('admin-peserta.dashboard'),
        'admin_karyawan'            => redirect()->route('admin-karyawan.dashboard'),
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
    Route::patch('/notifikasi/baca-semua', [UserNotifikasiController::class, 'tandaiSemuaDibacaWeb'])
        ->name('notifikasi.read-all');

    Route::patch('/notifikasi/{notifikasi}/baca', [UserNotifikasiController::class, 'tandaiDibacaWeb'])
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

        /* Kelola Divisi */
        Route::get('/divisi', [SuperadminDivisiController::class, 'index'])->name('divisi.index');
        Route::post('/divisi', [SuperadminDivisiController::class, 'store'])->name('divisi.store');
        Route::put('/divisi/{divisi}', [SuperadminDivisiController::class, 'update'])->name('divisi.update');
        Route::delete('/divisi/{divisi}', [SuperadminDivisiController::class, 'destroy'])->name('divisi.destroy');
    });

/*
|--------------------------------------------------------------------------
| Admin Peserta
|--------------------------------------------------------------------------
*/

Route::middleware('admin.peserta')
    ->prefix('admin-peserta')
    ->name('admin-peserta.')
    ->group(function (): void {
        Route::get('/dashboard', AdminPesertaDashboardController::class)
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

        Route::get('/tugas/panduan/download', [AdminTugasController::class, 'downloadPanduan'])
            ->name('tugas.panduan.download');

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

        /* Data Pembayaran */
        Route::get('/pembayaran', [AdminDataPembayaranController::class, 'index'])
            ->name('pembayaran.index');

        Route::patch('/pembayaran/{pembayaran}/terima', [AdminDataPembayaranController::class, 'terima'])
            ->name('pembayaran.terima');

        Route::patch('/pembayaran/{pembayaran}/tolak', [AdminDataPembayaranController::class, 'tolak'])
            ->name('pembayaran.tolak');

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

        /* Notifikasi */
        Route::get('/notifikasi', [AdminNotifikasiController::class, 'index'])
            ->name('notifikasi.index');
        Route::get('/notifikasi/create', [AdminNotifikasiController::class, 'create'])
            ->name('notifikasi.create');
        Route::post('/notifikasi', [AdminNotifikasiController::class, 'store'])
            ->name('notifikasi.store');
        Route::get('/notifikasi/{notifikasi}', [AdminNotifikasiController::class, 'show'])
            ->whereNumber('notifikasi')
            ->name('notifikasi.show');
        Route::get('/notifikasi/{notifikasi}/edit', [AdminNotifikasiController::class, 'edit'])
            ->whereNumber('notifikasi')
            ->name('notifikasi.edit');
        Route::put('/notifikasi/{notifikasi}', [AdminNotifikasiController::class, 'update'])
            ->whereNumber('notifikasi')
            ->name('notifikasi.update');
        Route::delete('/notifikasi/{notifikasi}', [AdminNotifikasiController::class, 'destroy'])
            ->whereNumber('notifikasi')
            ->name('notifikasi.destroy');
    });

/*
|--------------------------------------------------------------------------
| Admin Karyawan
|--------------------------------------------------------------------------
*/

Route::middleware('admin.karyawan')
    ->prefix('admin-karyawan')
    ->name('admin-karyawan.')
    ->group(function (): void {
        Route::get('/dashboard', AdminKaryawanDashboardController::class)->name('dashboard');
        
        // Data Karyawan
        Route::get('/karyawan', [AdminKaryawanController::class, 'index'])->name('karyawan.index');
        Route::put('/karyawan/{karyawan}', [AdminKaryawanController::class, 'update'])->name('karyawan.update');
        
        // Absensi Karyawan
        Route::get('/absensi-karyawan/export', [AdminAbsensiKaryawanController::class, 'export'])->name('absensi-karyawan.export');
        Route::resource('absensi-karyawan', AdminAbsensiKaryawanController::class);
        
        // Permintaan Lamaran Karyawan
        Route::get('/permintaan-lamaran', [AdminPermintaanLamaranController::class, 'index'])->name('permintaan-lamaran.index');
        Route::post('/permintaan-lamaran/{id}/action', [AdminPermintaanLamaranController::class, 'action'])->whereNumber('id')->name('permintaan-lamaran.action');

        // Pengumuman Karyawan
        Route::resource('pengumuman',AdminKaryawanPengumumanController::class)->except(['show']);
        
        // Pembayaran Gaji Karyawan
        Route::get('/pembayaran-karyawan', [AdminPembayaranKaryawanController::class, 'index'])->name('pembayaran-karyawan.index');
        Route::post('/pembayaran-karyawan', [AdminPembayaranKaryawanController::class, 'store'])->name('pembayaran-karyawan.store');
        Route::delete('/pembayaran-karyawan/{id}', [AdminPembayaranKaryawanController::class, 'destroy'])->name('pembayaran-karyawan.destroy');

        // Pengajuan Resign (sisi admin)
        Route::get('/resign', [AdminResignController::class, 'index'])->name('resign.index');
        Route::patch('/resign/{resign}/approve', [AdminResignController::class, 'approve'])->name('resign.approve');
        Route::patch('/resign/{resign}/reject', [AdminResignController::class, 'reject'])->name('resign.reject');

        // Laporan Karyawan & Absensi
        Route::prefix('laporan')->name('laporan.')->group(function () {
            // Laporan Absensi
            Route::get('/absensi', [LaporanAbsensiKaryawanController::class, 'index'])->name('absensi');
            Route::get('/absensi/export', [LaporanAbsensiKaryawanController::class, 'export'])->name('absensi.export');

            // Laporan Data Karyawan
            Route::get('/karyawan', [LaporanKaryawanController::class, 'index'])->name('karyawan');
            Route::get('/karyawan/export', [LaporanKaryawanController::class, 'export'])->name('karyawan.export');

            
        });
    });

// Logout khusus Admin
Route::prefix('admin-karyawan')->name('admin-karyawan.')->group(function (): void {
    Route::middleware('admin.karyawan')->post('/logout', function (\Illuminate\Http\Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    })->name('logout');
});

Route::prefix('admin-peserta')->name('admin-peserta.')->group(function (): void {
    Route::middleware('admin.peserta')->post('/logout', function (\Illuminate\Http\Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    })->name('logout');
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
        Route::get('/dashboard', [PesertaMagangDashboardController::class, 'index'])->name('dashboard');

        Route::get('/absensi', [PesertaMagangAbsensiController::class, 'index'])->name('absensi.index');
        Route::post('/absensi', [PesertaMagangAbsensiController::class, 'store'])->name('absensi.store');

        Route::get('/penugasan', [PesertaMagangPenugasanController::class, 'index'])->name('penugasan.index');
        Route::post('/penugasan/{id_tugas}/kumpul', [PesertaMagangPenugasanController::class, 'store'])->name('penugasan.store');

        Route::get('/aturan', [PesertaAturanController::class, 'index'])->name('aturan.index');

        Route::get('/tugas', [PesertaTugasController::class, 'index'])->name('tugas.index');
        Route::get('/tugas/{penugasan}/file', [PesertaTugasController::class, 'downloadTask'])->name('tugas.file.download');
        Route::get('/tugas/{penugasan}/template-laporan', [PesertaTugasController::class, 'downloadReportTemplate'])->name('tugas.template-laporan.download');
        Route::post('/tugas/{penugasan}/kumpulkan', [PesertaTugasController::class, 'submit'])->name('tugas.submit');

        Route::get('/pembayaran', [PesertaMagangPembayaranController::class, 'index'])->name('pembayaran.index');
        Route::post('/pembayaran', [PesertaMagangPembayaranController::class, 'store'])->name('pembayaran.store');

        Route::get('/laporan-mingguan', [PesertaMagangLaporanMingguanController::class, 'index'])->name('laporan-mingguan.index');
        Route::post('/laporan-mingguan', [PesertaMagangLaporanMingguanController::class, 'store'])->name('laporan-mingguan.store');
    });

/*
|--------------------------------------------------------------------------
| Karyawan User Panel
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:karyawan'])
    ->prefix('karyawan')
    ->name('karyawan.')
    ->group(function (): void {
        Route::get('/dashboard', [KaryawanDashboardController::class, 'index'])->name('dashboard');

        Route::get('/absensi', [KaryawanDashboardController::class, 'absensiIndex'])->name('absensi.index');
        Route::post('/absensi/clock-in', [KaryawanDashboardController::class, 'clockIn'])->name('absensi.clockin');

        // Resign
        Route::get('/resign/create', [AdminResignController::class, 'create'])->name('resign.create');
        Route::post('/resign', [AdminResignController::class, 'store'])->name('resign.store');
        Route::get('/resign/{resign}', [AdminResignController::class, 'show'])->name('resign.show');

        // Pengumuman & Aturan Perusahaan
        Route::get('/pengumuman', [PengumumanController::class, 'index'])->name('pengumuman.index');
        Route::get('/pengumuman/{pengumuman}', [PengumumanController::class, 'show'])->name('pengumuman.show');
        Route::get('/aturan', [AturanController::class, 'index'])->name('aturan.index');

        // Menu Tambahan Karyawan (View Langsung)
        Route::get('/cuti', function () {
            return view('karyawan.cuti.index');
        })->name('cuti.index');

        Route::get('/payslip', function () {
            return view('karyawan.payslip.index');
        })->name('payslip.index');

        Route::get('/reimbursement', function () {
            return view('karyawan.reimbursement.index');
        })->name('reimbursement.index');

        Route::get('/helpdesk', function () {
            return view('karyawan.helpdesk.index');
        })->name('helpdesk.index');

        Route::get('/profil/edit', [ProfileController::class, 'edit'])->name('profil.edit');
    });

/*
|--------------------------------------------------------------------------
| Status Pengajuan Pelamar & Karyawan / Peserta Baru
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:pelamar,pelamar_karyawan,karyawan,peserta'])->group(function () {
    Route::get('/pengajuan/status', function () {
        $user = auth()->user();

        session(['has_seen_credentials' => true]);

        $permintaan = \App\Models\PermintaanMagang::where('user_id', $user->id_user)
            ->orWhere('email', $user->email)
            ->latest('id_permintaan')
            ->first();

        if (! $permintaan && class_exists('\App\Models\PermintaanLamaran')) {
            $permintaan = \App\Models\PermintaanLamaran::where('user_id', $user->id_user)
                ->orWhere('email', $user->email)
                ->latest('id_permintaan')
                ->first();
        }

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