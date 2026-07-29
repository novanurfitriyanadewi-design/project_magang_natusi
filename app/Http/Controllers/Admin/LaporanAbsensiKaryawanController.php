<?php

namespace App\Http\Controllers\Admin;

use App\Exports\LaporanAbsensiKaryawanExport;
use App\Http\Controllers\Controller;
use App\Models\AbsensiKaryawan;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class LaporanAbsensiKaryawanController extends Controller
{
    public function index(Request $request): View
    {
        $dariTgl = $request->get('dari_tanggal', now()->startOfMonth()->toDateString());
        $sampaiTgl = $request->get('sampai_tanggal', now()->toDateString());
        $jabatan = $request->get('jabatan');
        $search = $request->get('search');

        $jabatanList = Karyawan::whereNotNull('jabatan')->distinct()->pluck('jabatan');

        $queryBase = AbsensiKaryawan::query()
            ->whereDate('tanggal', '>=', $dariTgl)
            ->whereDate('tanggal', '<=', $sampaiTgl);

        if ($jabatan) {
            $queryBase->whereHas('karyawan', fn ($q) => $q->where('jabatan', $jabatan));
        }

        $totalAbsen = (clone $queryBase)->count();
        $totalHadir = (clone $queryBase)->where('status', 'hadir')->count();
        $totalTerlambat = (clone $queryBase)
            ->where('status', 'hadir')
            ->whereNotNull('jam_masuk')
            ->whereTime('jam_masuk', '>', '08:00:00')
            ->count();
        $totalIzinSakit = (clone $queryBase)->whereIn('status', ['izin', 'sakit'])->count();
        $totalAlpha = (clone $queryBase)->where('status', 'alpha')->count();

        $tingkatKehadiran = $totalAbsen > 0 ? round(($totalHadir / $totalAbsen) * 100, 1) : 0;

        $rataTerlambatMenit = (clone $queryBase)
            ->where('status', 'hadir')
            ->whereNotNull('jam_masuk')
            ->whereTime('jam_masuk', '>', '08:00:00')
            ->get()
            ->map(function ($absen) {
                return \Carbon\Carbon::parse('08:00:00')->diffInMinutes(\Carbon\Carbon::parse($absen->jam_masuk));
            })
            ->avg();

        $stats = [
            'tingkat_kehadiran' => $tingkatKehadiran,
            'rata_terlambat_menit' => round($rataTerlambatMenit ?? 0),
            'total_izin_sakit' => $totalIzinSakit,
            'total_alpha' => $totalAlpha,
        ];

        $monthlyHadir = AbsensiKaryawan::selectRaw('MONTH(tanggal) as bulan, COUNT(*) as total')
            ->whereYear('tanggal', now()->year)
            ->where('status', 'hadir')
            ->groupBy('bulan')
            ->pluck('total', 'bulan');

        $monthlyTotal = AbsensiKaryawan::selectRaw('MONTH(tanggal) as bulan, COUNT(*) as total')
            ->whereYear('tanggal', now()->year)
            ->groupBy('bulan')
            ->pluck('total', 'bulan');

        $monthlyRate = [];
        for ($m = 1; $m <= 12; $m++) {
            $hadir = $monthlyHadir[$m] ?? 0;
            $total = $monthlyTotal[$m] ?? 0;
            $monthlyRate[$m] = $total > 0 ? round(($hadir / $total) * 100) : 0;
        }

        $rekapQuery = AbsensiKaryawan::select('id_karyawan')
            ->selectRaw("
                COUNT(*) as total_absen,
                SUM(CASE WHEN status = 'hadir' AND (jam_masuk IS NULL OR jam_masuk <= '08:00:00') THEN 1 ELSE 0 END) as total_hadir,
                SUM(CASE WHEN status = 'hadir' AND jam_masuk > '08:00:00' THEN 1 ELSE 0 END) as total_terlambat,
                SUM(CASE WHEN status = 'izin' THEN 1 ELSE 0 END) as total_izin,
                SUM(CASE WHEN status = 'sakit' THEN 1 ELSE 0 END) as total_sakit,
                SUM(CASE WHEN status = 'alpha' THEN 1 ELSE 0 END) as total_alpha
            ")
            ->whereDate('tanggal', '>=', $dariTgl)
            ->whereDate('tanggal', '<=', $sampaiTgl)
            ->with('karyawan')
            ->groupBy('id_karyawan');

        if ($jabatan) {
            $rekapQuery->whereHas('karyawan', fn ($q) => $q->where('jabatan', $jabatan));
        }
        if ($search) {
            $rekapQuery->whereHas('karyawan', fn ($q) => $q->where('nama_karyawan', 'like', "%{$search}%"));
        }

        $rekap = $rekapQuery->paginate(10)->withQueryString();

        $rekap->getCollection()->transform(function ($item) {
            $hadir = $item->total_hadir + $item->total_terlambat;
            $item->persentase = $item->total_absen > 0 ? round(($hadir / $item->total_absen) * 100) : 0;
            return $item;
        });

        return view('admin.laporan.absensi-karyawan', compact(
            'jabatanList',
            'jabatan',
            'search',
            'dariTgl',
            'sampaiTgl',
            'stats',
            'monthlyRate',
            'rekap'
        ));
    }

    public function export(Request $request)
    {
        $dariTgl = $request->get('dari_tanggal');
        $sampaiTgl = $request->get('sampai_tanggal');

        return Excel::download(
            new LaporanAbsensiKaryawanExport($dariTgl, $sampaiTgl),
            'laporan-absensi-karyawan.xlsx'
        );
    }
}