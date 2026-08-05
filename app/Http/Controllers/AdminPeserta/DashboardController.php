<?php

namespace App\Http\Controllers\AdminPeserta;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $user = Auth::user();

        // TODO: ganti dummy data di bawah dengan query Eloquent asli

        $pesertaMagang = [
            'total' => 0,
            'aktif' => 42,
        ];

        $pengajuanMagang = [
            'total'    => 0,
            'menunggu' => 0,
        ];

        $pembayaranBulanan = [
            'total' => 0,
        ];

        $absensiHariIni = [
            'total' => 0,
        ];

        // Data grafik pengajuan magang 12 bulan terakhir
        $pengajuanPerBulan = [
            'labels' => ['Agu', 'Sep', 'Okt', 'Nov', 'Des', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul'],
            'data'   => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 4],
        ];

        $statusAbsensi = [
            'hadir'     => 0,
            'terlambat' => 0,
            'izin'      => 0,
        ];

        return view('admin-peserta.dashboard', compact(
            'user',
            'pesertaMagang',
            'pengajuanMagang',
            'pembayaranBulanan',
            'absensiHariIni',
            'pengajuanPerBulan',
            'statusAbsensi',
        ));
    }
}