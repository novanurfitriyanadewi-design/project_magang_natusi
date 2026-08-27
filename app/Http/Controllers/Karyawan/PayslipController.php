<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Models\PembayaranKaryawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PayslipController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $karyawan = $user->karyawan;

        $slipGaji = collect();
        $tahunList = collect();

        if ($karyawan) {
            $query = PembayaranKaryawan::where('karyawan_id', $karyawan->id_karyawan);

            if ($request->filled('tahun')) {
                $query->where('periode', 'like', $request->tahun . '-%');
            }

            $slipGaji = $query->orderByDesc('periode')->paginate(12)->withQueryString();

            // Total gaji sesuai filter yang aktif
            $totalQuery = PembayaranKaryawan::where('karyawan_id', $karyawan->id_karyawan);
            if ($request->filled('tahun')) {
                $totalQuery->where('periode', 'like', $request->tahun . '-%');
            }
            $totalGaji = (clone $totalQuery)->where('status', 'terbayar')->sum('nominal');
            $jumlahSlipTerbayar = (clone $totalQuery)->where('status', 'terbayar')->count();
            $slipTerakhir = (clone $totalQuery)->where('status', 'terbayar')->orderByDesc('periode')->first();

            $tahunList = PembayaranKaryawan::where('karyawan_id', $karyawan->id_karyawan)
                ->selectRaw('DISTINCT SUBSTRING(periode, 1, 4) as tahun')
                ->orderByDesc('tahun')
                ->pluck('tahun');
        }

        return view('karyawan.payslip.index', compact('slipGaji', 'tahunList', 'totalGaji', 'jumlahSlipTerbayar', 'slipTerakhir'));
    }
}