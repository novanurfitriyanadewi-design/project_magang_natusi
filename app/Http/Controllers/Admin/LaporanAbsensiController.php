<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\PesertaMagang;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class LaporanAbsensiController extends Controller
{
    public function index(Request $request): View
    {
        // 1. Tangkap Request Filter
        $kategori   = $request->get('kategori');
        $status     = $request->get('status');
        $dariTgl    = $request->get('dari_tanggal');
        $sampaiTgl  = $request->get('sampai_tanggal');
        $search     = $request->get('search');

        // Daftar kategori diambil dari tingkat pendidikan/jurusan peserta
        $kategoriList = PesertaMagang::whereNotNull('tingkat_pendidikan')
            ->distinct()
            ->pluck('tingkat_pendidikan');

        // 2. Base Query Absensi untuk Statistik & Grafik
        $queryBase = Absensi::query();

        if ($dariTgl) {
            $queryBase->whereDate('tanggal', '>=', $dariTgl);
        }
        if ($sampaiTgl) {
            $queryBase->whereDate('tanggal', '<=', $sampaiTgl);
        }
        if ($kategori) {
            $queryBase->whereHas('peserta', function ($q) use ($kategori) {
                $q->where('tingkat_pendidikan', $kategori);
            });
        }

        // 3. Hitung Statistik
        $totalAbsen = (clone $queryBase)->count();
        $totalHadir = (clone $queryBase)->whereIn('status', ['hadir', 'terlambat'])->count();
        $totalTerlambat = (clone $queryBase)->where('status', 'terlambat')->count();
        $totalIzinSakit = (clone $queryBase)->whereIn('status', ['izin', 'sakit'])->count();
        $totalAlfa = (clone $queryBase)->where('status', 'alfa')->count();

        $tingkatKehadiran = $totalAbsen > 0 ? round(($totalHadir / $totalAbsen) * 100, 1) : 0;
        $tingkatKetidakhadiran = $totalAbsen > 0 ? round((($totalIzinSakit + $totalAlfa) / $totalAbsen) * 100, 1) : 0;

        $stats = [
            'tingkat_kehadiran'    => $tingkatKehadiran,
            'rata_terlambat_menit' => $totalHadir > 0 ? round($totalTerlambat / $totalHadir * 10, 1) : 0, // Estimasi rata-rata
            'total_izin_sakit'     => $totalIzinSakit,
            'tingkat_ketidakhadiran' => $tingkatKetidakhadiran,
        ];

        // 4. Hitung Trend Frekuensi Kehadiran Bulanan (Grafik)
        $monthlyData = Absensi::selectRaw('MONTH(tanggal) as bulan, COUNT(*) as total')
            ->whereYear('tanggal', now()->year)
            ->whereIn('status', ['hadir', 'terlambat'])
            ->groupBy('bulan')
            ->pluck('total', 'bulan');

        $monthlyRate = [];
        for ($m = 1; $m <= 12; $m++) {
            $count = $monthlyData[$m] ?? 0;
            // Hitung persentase sederhana berdasarkan skala max
            $monthlyRate[$m] = $totalAbsen > 0 ? min(100, round(($count / max(1, $totalAbsen / 12)) * 100)) : 0;
        }

        // 5. Query Rekap Data Per Peserta (Tabel Utama)
        $rekapQuery = Absensi::select('peserta_id')
            ->selectRaw("
                COUNT(*) as total_absen,
                SUM(CASE WHEN status = 'hadir' THEN 1 ELSE 0 END) as total_hadir,
                SUM(CASE WHEN status = 'terlambat' THEN 1 ELSE 0 END) as total_terlambat,
                SUM(CASE WHEN status = 'izin' THEN 1 ELSE 0 END) as total_izin,
                SUM(CASE WHEN status = 'sakit' THEN 1 ELSE 0 END) as total_sakit,
                SUM(CASE WHEN status = 'alfa' THEN 1 ELSE 0 END) as total_alfa
            ")
            ->with(['peserta.user'])
            ->groupBy('peserta_id');

        // Filter tabel per peserta
        if ($dariTgl) {
            $rekapQuery->whereDate('tanggal', '>=', $dariTgl);
        }
        if ($sampaiTgl) {
            $rekapQuery->whereDate('tanggal', '<=', $sampaiTgl);
        }
        if ($status) {
            $rekapQuery->where('status', $status);
        }
        if ($kategori) {
            $rekapQuery->whereHas('peserta', function ($q) use ($kategori) {
                $q->where('tingkat_pendidikan', $kategori);
            });
        }
        if ($search) {
            $rekapQuery->whereHas('peserta.user', function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%");
            });
        }

        $rekap = $rekapQuery->paginate(10)->withQueryString();

        // Hitung persentase kehadiran individual
        $rekap->getCollection()->transform(function ($item) {
            $hadir = $item->total_hadir + $item->total_terlambat;
            $item->persentase = $item->total_absen > 0 ? round(($hadir / $item->total_absen) * 100) : 0;
            return $item;
        });

        return view('admin.laporan.absensi', compact(
            'kategoriList',
            'kategori',
            'status',
            'dariTgl',
            'sampaiTgl',
            'search',
            'stats',
            'monthlyRate',
            'rekap'
        ));
    }
}