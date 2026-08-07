<?php

namespace App\Http\Controllers\AdminPeserta;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\PesertaMagang;
use Illuminate\Http\Request;

class AbsensiController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');
        $date = $request->input('date');

        // Mulai query dengan relasi peserta.user
        $query = Absensi::with('peserta.user')
            ->where('absentable_type', PesertaMagang::class)
            ->whereDate('tanggal', today()); // default hari ini

        // Filter pencarian nama/instansi
        if ($search) {
            $query->whereHas('peserta.user', function ($q) use ($search) {
                $q->where('nama', 'like', '%' . $search . '%')
                  ->orWhere('instansi', 'like', '%' . $search . '%');
            });
        }

        // Filter status absensi
        if ($status) {
            $query->where('status', $status);
        }

        // Filter tanggal manual
        if ($date) {
            $query->whereDate('tanggal', $date);
        }

        // Urutkan dan paginate
        $absensi = $query->latest('tanggal')->paginate(10)->withQueryString();

        return view('admin-peserta.absensi', compact('absensi'));
    }
}