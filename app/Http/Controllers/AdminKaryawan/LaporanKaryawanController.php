<?php

namespace App\Http\Controllers\AdminKaryawan; // PERBAIKAN: Namespace diubah

use App\Http\Controllers\Controller;
use App\Models\Karyawan;
use App\Models\Divisi;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LaporanKaryawanController extends Controller
{
    public function index(Request $request): View
    {
        $dariTgl = $request->get('dari_tanggal');
        $sampaiTgl = $request->get('sampai_tanggal');
        $divisiId = $request->get('divisi_id');
        $search = $request->get('search');

        $divisiList = Divisi::orderBy('nama_divisi')->get();

        $query = Karyawan::query();

        if ($dariTgl) {
            $query->whereDate('tanggal_bergabung', '>=', $dariTgl);
        }
        if ($sampaiTgl) {
            $query->whereDate('tanggal_bergabung', '<=', $sampaiTgl);
        }
        if ($divisiId) {
            $query->where('divisi_id', $divisiId);
        }
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_karyawan', 'like', "%{$search}%")
                    ->orWhere('nip', 'like', "%{$search}%");
            });
        }

        $karyawan = (clone $query)->with('divisi')->latest()->paginate(10)->withQueryString();

        $stats = [
            'total' => Karyawan::count(),
            'aktif' => Karyawan::where('status', 'aktif')->count(),
            'nonaktif' => Karyawan::where('status', 'nonaktif')->count(),
            'baru_bulan_ini' => Karyawan::whereMonth('tanggal_bergabung', now()->month)
                ->whereYear('tanggal_bergabung', now()->year)
                ->count(),
        ];

        $distribusiDivisi = Karyawan::query()
            ->join('divisi', 'divisi.id_divisi', '=', 'karyawan.divisi_id')
            ->selectRaw('divisi.nama_divisi, COUNT(*) as total')
            ->groupBy('divisi.id_divisi', 'divisi.nama_divisi')
            ->orderByDesc('total')
            ->limit(6)
            ->pluck('total', 'nama_divisi');

        $totalStatus = max(1, $stats['aktif'] + $stats['nonaktif']);
        $persenAktif = (int) round($stats['aktif'] / $totalStatus * 100);
        $persenNonaktif = 100 - $persenAktif;

        return view('admin-karyawan.laporan.karyawan', compact(
            'karyawan',
            'stats',
            'divisiList',
            'divisiId',
            'search',
            'dariTgl',
            'sampaiTgl',
            'distribusiDivisi',
            'persenAktif',
            'persenNonaktif'
        ));
    }

    public function export(Request $request)
    {
        $dariTgl = $request->get('dari_tanggal');
        $sampaiTgl = $request->get('sampai_tanggal');
        $search = $request->get('search');

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\LaporanKaryawanExport($dariTgl, $sampaiTgl, $request->get('divisi_id'), $search),
            'laporan-karyawan.xlsx'
        );
    }
}
