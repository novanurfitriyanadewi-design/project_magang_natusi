<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LaporanKaryawanController extends Controller
{
    public function index(Request $request): View
    {
        $dariTgl = $request->get('dari_tanggal');
        $sampaiTgl = $request->get('sampai_tanggal');
        $jabatan = $request->get('jabatan');
        $search = $request->get('search');

        $jabatanList = Karyawan::whereNotNull('jabatan')->distinct()->pluck('jabatan');

        $query = Karyawan::query();

        if ($dariTgl) {
            $query->whereDate('created_at', '>=', $dariTgl);
        }
        if ($sampaiTgl) {
            $query->whereDate('created_at', '<=', $sampaiTgl);
        }
        if ($jabatan) {
            $query->where('jabatan', $jabatan);
        }
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_karyawan', 'like', "%{$search}%")
                    ->orWhere('nip', 'like', "%{$search}%");
            });
        }

        $karyawan = (clone $query)->latest()->paginate(10)->withQueryString();

        $stats = [
            'total' => Karyawan::count(),
            'aktif' => Karyawan::where('status', 'aktif')->count(),
            'nonaktif' => Karyawan::where('status', 'nonaktif')->count(),
            'baru_bulan_ini' => Karyawan::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
        ];

        $distribusiJabatan = Karyawan::selectRaw('jabatan, COUNT(*) as total')
            ->whereNotNull('jabatan')
            ->groupBy('jabatan')
            ->orderByDesc('total')
            ->limit(6)
            ->pluck('total', 'jabatan');

        $totalStatus = max(1, $stats['aktif'] + $stats['nonaktif']);
        $persenAktif = (int) round($stats['aktif'] / $totalStatus * 100);
        $persenNonaktif = 100 - $persenAktif;

        return view('admin.laporan.karyawan', compact(
            'karyawan',
            'stats',
            'jabatanList',
            'jabatan',
            'search',
            'dariTgl',
            'sampaiTgl',
            'distribusiJabatan',
            'persenAktif',
            'persenNonaktif'
        ));
    }
}
