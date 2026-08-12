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

            $tahunList = PembayaranKaryawan::where('karyawan_id', $karyawan->id_karyawan)
                ->selectRaw('DISTINCT SUBSTRING(periode, 1, 4) as tahun')
                ->orderByDesc('tahun')
                ->pluck('tahun');
        }

        return view('karyawan.payslip.index', compact('slipGaji', 'tahunList'));
    }
}