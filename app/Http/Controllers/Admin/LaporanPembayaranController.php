<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LaporanPembayaranController extends Controller
{
    public function index(Request $request): View
    {
        $search       = $request->get('search');
        $statusFilter = $request->get('status_filter'); // berhasil | menunggu | dibatalkan
        $year         = (int) $request->get('year', now()->year);

        $query = Pembayaran::with(['peserta.user', 'bank', 'nominalPembayaran'])
            ->whereYear('tgl_bayar', $year);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('peserta.user', function ($u) use ($search) {
                    $u->where('nama', 'like', "%{$search}%");
                })
                ->orWhere('keterangan', 'like', "%{$search}%");
            });
        }

        if ($statusFilter) {
            $query->where('status', $statusFilter);
        }

        $transaksi = $query->latest('tgl_bayar')->paginate(10)->withQueryString();

        // ==== Statistik ====
        $base = Pembayaran::whereYear('tgl_bayar', $year);

        $totalPendapatan = (clone $base)->where('status', 'berhasil')->sum('nominal');
        $tagihanTertunda = (clone $base)->where('status', 'menunggu')->sum('nominal');
        $jumlahTertunda  = (clone $base)->where('status', 'menunggu')->count();
        $jumlahBerhasil  = (clone $base)->where('status', 'berhasil')->count();
        $rataRata        = $jumlahBerhasil > 0 ? intdiv($totalPendapatan, $jumlahBerhasil) : 0;

        $stats = [
            'total_pendapatan' => $totalPendapatan,
            'tagihan_tertunda' => $tagihanTertunda,
            'jumlah_tertunda'  => $jumlahTertunda,
            'rata_rata'        => $rataRata,
            'jumlah_berhasil'  => $jumlahBerhasil,
        ];

        // ==== Tren pendapatan bulanan (untuk grafik batang) ====
        $rows = Pembayaran::selectRaw('MONTH(tgl_bayar) as bulan, SUM(nominal) as total')
            ->where('status', 'berhasil')
            ->whereYear('tgl_bayar', $year)
            ->groupBy('bulan')
            ->pluck('total', 'bulan');

        $monthly = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthly[$m] = (int) ($rows[$m] ?? 0);
        }
        $chartMax = max(1, max($monthly));

        // ==== Distribusi metode pembayaran (berdasarkan bank) untuk doughnut chart ====
        // CATATAN: nama kolom nama bank di model Bank aku asumsikan 'nama_bank' —
        // sesuaikan kalau ternyata namanya beda (misal 'nama', 'bank_name', dll).
        $metodeRaw = Pembayaran::with('bank')
            ->where('status', 'berhasil')
            ->whereYear('tgl_bayar', $year)
            ->get()
            ->groupBy(fn ($item) => $item->bank->nama_bank ?? 'Lainnya')
            ->map->count();

        $totalMetode = max(1, $metodeRaw->sum());
        $metodePersen = $metodeRaw->map(fn ($v) => round(($v / $totalMetode) * 100))->toArray();

        // build offset conic-gradient (biar bisa dipakai langsung di style="background: conic-gradient(...)")
        $conicStops = [];
        $offset = 0;
        $colors = ['#006191', '#bb0014', '#4d5d70', '#bec7d2'];
        $i = 0;
        foreach ($metodePersen as $metode => $persen) {
            $start = $offset;
            $end   = $offset + $persen;
            $conicStops[] = ($colors[$i] ?? '#bec7d2') . " {$start}% {$end}%";
            $offset = $end;
            $i++;
        }
        $conicGradient = implode(', ', $conicStops) ?: '#bec7d2 0% 100%';

        $availableYears = Pembayaran::selectRaw('YEAR(tgl_bayar) as th')
            ->whereNotNull('tgl_bayar')
            ->distinct()
            ->orderByDesc('th')
            ->pluck('th');

        if ($availableYears->isEmpty()) {
            $availableYears = collect([now()->year]);
        }

        return view('admin.laporan.pembayaran', compact(
            'transaksi',
            'stats',
            'search',
            'statusFilter',
            'year',
            'monthly',
            'chartMax',
            'metodePersen',
            'conicGradient',
            'availableYears'
        ));
    }
}