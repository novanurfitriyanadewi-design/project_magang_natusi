<?php

namespace App\Http\Controllers\AdminPeserta;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use Illuminate\Http\Request;

class DataPembayaranController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->input('status', '');
        $search = $request->input('search', '');
        $dariTgl = $request->input('dari_tanggal', '');
        $sampaiTgl = $request->input('sampai_tanggal', '');

        // Query Utama
        $query = Pembayaran::with(['peserta.user', 'peserta.permintaan']);

        if (!empty($status)) {
            $query->where('status', $status);
        }

        if (!empty($search)) {
            $query->whereHas('peserta.user', function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%");
            });
        }

        if (!empty($dariTgl)) {
            $query->whereDate('tgl_bayar', '>=', $dariTgl);
        }

        if (!empty($sampaiTgl)) {
            $query->whereDate('tgl_bayar', '<=', $sampaiTgl);
        }

        $pembayarans = $query->latest()->paginate(10)->withQueryString();

        // Data Ringkasan Stat
        $totalBelumDiterima = Pembayaran::where('status', 'menunggu')->sum('nominal');
        $countBelumDiterima = Pembayaran::where('status', 'menunggu')->count();
        $totalDiterima = Pembayaran::where('status', 'lunas')->sum('nominal');

        // PENTING: Definisi variabel $tabStatus untuk Blade
        $tabStatus = [
            '' => 'Semua',
            'menunggu' => 'Menunggu',
            'lunas' => 'Lunas',
            'ditolak' => 'Ditolak',
        ];

        return view('admin-peserta.pembayaran.index', compact(
            'pembayarans',
            'totalBelumDiterima',
            'countBelumDiterima',
            'totalDiterima',
            'tabStatus',
            'status',
            'search',
            'dariTgl',
            'sampaiTgl'
        ));
    }
}