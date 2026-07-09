<?php

use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\AturanPerusahaanController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BankController;
use App\Http\Controllers\JamOperasionalController;
use App\Http\Controllers\LaporanMingguanController;
use App\Http\Controllers\NominalPembayaranController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\PengumpulanTugasController;
use App\Http\Controllers\PermintaanMagangController;
use App\Http\Controllers\PesertaMagangController;
use App\Http\Controllers\TugasController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/status', fn () => response()->json([
    'success' => true,
    'message' => 'API Project Magang Natusi aktif.',
]));

Route::post('/login', [AuthController::class, 'login']);
Route::post('/permintaan-magang', [PermintaanMagangController::class, 'store']);

Route::middleware('auth.api')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profil', [AuthController::class, 'profil']);
    Route::put('/ganti-password', [AuthController::class, 'gantiPassword']);

    Route::get('/notifikasi-saya', [NotifikasiController::class, 'milikSaya']);
    Route::put('/notifikasi/tandai-semua-dibaca', [NotifikasiController::class, 'tandaiSemuaDibaca']);
    Route::put('/notifikasi/{id}/dibaca', [NotifikasiController::class, 'tandaiDibaca']);

    Route::get('/aturan-perusahaan-aktif', [AturanPerusahaanController::class, 'aktif']);
});

Route::middleware(['auth.api', 'role:peserta'])->group(function () {
    Route::get('/tugas-peserta', [TugasController::class, 'index']);
    Route::post('/pengumpulan-tugas', [PengumpulanTugasController::class, 'store']);
    Route::post('/laporan-mingguan', [LaporanMingguanController::class, 'store']);
    Route::post('/pembayaran', [PembayaranController::class, 'store']);
    Route::post('/absensi/hadir', [AbsensiController::class, 'absen']);
    Route::post('/absensi/izin', [AbsensiController::class, 'izin']);
    Route::post('/absensi/sakit', [AbsensiController::class, 'sakit']);
});

Route::middleware(['auth.api', 'role:admin,superadmin'])->group(function () {
    Route::apiResource('permintaan-magang', PermintaanMagangController::class)->except(['store']);
    Route::put('/permintaan-magang/{id}/setujui', [PermintaanMagangController::class, 'setujui']);
    Route::put('/permintaan-magang/{id}/tolak', [PermintaanMagangController::class, 'tolak']);

    Route::apiResource('peserta-magang', PesertaMagangController::class);

    Route::post('/tugas/import-excel', [TugasController::class, 'importExcel']);
    Route::apiResource('tugas', TugasController::class);

    Route::apiResource('pengumpulan-tugas', PengumpulanTugasController::class)->only(['index','show']);
    Route::put('/pengumpulan-tugas/{id}/dinilai', [PengumpulanTugasController::class, 'tandaiDinilai']);

    Route::apiResource('laporan-mingguan', LaporanMingguanController::class)->only(['index','show']);

    Route::apiResource('pembayaran', PembayaranController::class)->only(['index','show']);
    Route::put('/pembayaran/{pembayaran}/lunas', [PembayaranController::class, 'lunas']);
    Route::put('/pembayaran/{pembayaran}/tolak', [PembayaranController::class, 'tolak']);

    Route::apiResource('bank', BankController::class);
    Route::apiResource('nominal-pembayaran', NominalPembayaranController::class);

    Route::apiResource('jam-operasional', JamOperasionalController::class);
    Route::put('/jam-operasional/{id}/aktifkan', [JamOperasionalController::class, 'aktifkan']);
    Route::put('/jam-operasional/{id}/nonaktifkan', [JamOperasionalController::class, 'nonaktifkan']);

    Route::apiResource('absensi', AbsensiController::class)->only(['index','show']);
});

Route::middleware(['auth.api', 'role:superadmin'])->group(function () {
    Route::apiResource('users', UserController::class);
    Route::apiResource('aturan-perusahaan', AturanPerusahaanController::class);
});
