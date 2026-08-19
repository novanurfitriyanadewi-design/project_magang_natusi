<?php

namespace App\Http\Controllers\AdminPeserta;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LaporanPembayaranController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->get('search', ''));
        $statusFilter = (string) $request->get('status_filter', '');
        $year = (int) $request->get('year', 0);

        $query = Pembayaran::query()
            ->with(['peserta.user', 'peserta.permintaan', 'nominalPembayaran'])
            ->when($year > 0, fn ($q) => $q->whereYear('tgl_bayar', $year))
            ->when($statusFilter !== '', fn ($q) => $q->where('status', $statusFilter))
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->whereHas('peserta.user', fn ($u) => $u->where('nama', 'like', "%{$search}%"))
                        ->orWhereHas('peserta.permintaan', fn ($p) => $p->where('nama_pemohon', 'like', "%{$search}%"));
                });
            });

        $transaksi = $query
            ->orderByDesc('tgl_bayar')
            ->orderByDesc('id_pembayaran')
            ->paginate(10)
            ->withQueryString();

        $base = Pembayaran::query()->when($year > 0, fn ($q) => $q->whereYear('tgl_bayar', $year));
        $totalPendapatan = (clone $base)->where('status', 'lunas')->sum('nominal');
        $tagihanTertunda = (clone $base)->where('status', 'menunggu')->sum('nominal');
        $jumlahTertunda = (clone $base)->where('status', 'menunggu')->count();
        $jumlahLunas = (clone $base)->where('status', 'lunas')->count();

        $stats = [
            'total_pendapatan' => (int) $totalPendapatan,
            'tagihan_tertunda' => (int) $tagihanTertunda,
            'jumlah_tertunda' => $jumlahTertunda,
            'rata_rata' => $jumlahLunas > 0 ? intdiv((int) $totalPendapatan, $jumlahLunas) : 0,
            'jumlah_berhasil' => $jumlahLunas,
        ];

        $availableYears = Pembayaran::query()
            ->selectRaw('YEAR(tgl_bayar) as th')
            ->whereNotNull('tgl_bayar')
            ->distinct()
            ->orderByDesc('th')
            ->pluck('th')
            ->filter();

        return view('admin-peserta.laporan.pembayaran', compact(
            'transaksi',
            'stats',
            'search',
            'statusFilter',
            'year',
            'availableYears'
        ));
    }
}
