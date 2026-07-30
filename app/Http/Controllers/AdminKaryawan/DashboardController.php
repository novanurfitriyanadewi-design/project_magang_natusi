<?php

namespace App\Http\Controllers\AdminKaryawan;

use App\Http\Controllers\Controller;
use App\Models\AbsensiKaryawan;
use App\Models\Karyawan;
use App\Models\Resign;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $user = Auth::user();

        // 1. Total Karyawan
        $totalKaryawan = Karyawan::count();
        $aktifKaryawan = Karyawan::where('status', 'aktif')->count();
        $nonAktifKaryawan = Karyawan::where('status', 'nonaktif')->count();
        $baruBulanIni = Karyawan::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $karyawanData = [
            'total'    => $totalKaryawan,
            'aktif'    => $aktifKaryawan,
            'nonaktif' => $nonAktifKaryawan,
            'baru'     => $baruBulanIni,
        ];

        // 2. Pengajuan Resign
        $totalResign = Resign::count();
        $pendingResign = Resign::whereIn('status', ['pending', 'diproses', 'menunggu_approval'])->count();

        $resignData = [
            'total'   => $totalResign,
            'pending' => $pendingResign,
        ];

        // 3. Penggajian
        $penggajianData = [
            'selesai' => 0,
        ];

        // 4. Absensi Karyawan Hari Ini
        $today = Carbon::today()->toDateString();
        $totalAbsensiHariIni = AbsensiKaryawan::whereDate('tanggal', $today)->count();

        // Hitung persentase kehadiran (mencegah division by zero)
        $persentaseKehadiran = $aktifKaryawan > 0 
            ? round(($totalAbsensiHariIni / $aktifKaryawan) * 100) . '%' 
            : '0%';

        $absensiKaryawanData = [
            'total'      => $totalAbsensiHariIni,
            'persentase' => $persentaseKehadiran,
        ];

        // 5. Status Absensi Karyawan Hari Ini
        $hadirHariIni    = AbsensiKaryawan::whereDate('tanggal', $today)->where('status', 'hadir')->count();
        $terlambatHariIni = AbsensiKaryawan::whereDate('tanggal', $today)->where('status', 'terlambat')->count();
        $izinHariIni     = AbsensiKaryawan::whereDate('tanggal', $today)->whereIn('status', ['izin', 'sakit'])->count();

        $statusAbsensiKaryawanData = [
            'hadir'     => $hadirHariIni,
            'terlambat' => $terlambatHariIni,
            'izin'      => $izinHariIni,
        ];

        // 6. Grafik Pengajuan Resign per Bulan (12 bulan)
        $bulanLabels = [];
        $bulanData   = [];
        $now = Carbon::now();

        for ($i = 11; $i >= 0; $i--) {
            $date = $now->copy()->subMonths($i);
            $bulanLabels[] = $date->translatedFormat('M');
            $bulanData[]   = Resign::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
        }

        $resignPerBulanData = [
            'labels' => $bulanLabels,
            'data'   => $bulanData,
        ];

        return view('admin-karyawan.dashboard', compact(
            'user',
            'karyawanData',
            'resignData',
            'penggajianData',
            'absensiKaryawanData',
            'statusAbsensiKaryawanData',
            'resignPerBulanData',
        ));
    }
}