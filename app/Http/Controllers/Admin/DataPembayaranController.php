<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DataPembayaranController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->get('search', '');
        $status = $request->get('status', '');
        $dariTgl = $request->get('dari_tanggal');
        $sampaiTgl = $request->get('sampai_tanggal');

        $query = Pembayaran::query()->with(['bank', 'nominalPembayaran', 'peserta.user']);

        if ($search !== '') {
            $query->whereHas('peserta.user', fn ($q) => $q->where('nama', 'like', "%{$search}%"));
        }

        if ($status !== '') {
            $query->where('status', $status);
        }

        if ($dariTgl) {
            $query->whereDate('tgl_bayar', '>=', $dariTgl);
        }

        if ($sampaiTgl) {
            $query->whereDate('tgl_bayar', '<=', $sampaiTgl);
        }

        $pembayarans = $query->latest('tgl_bayar')->paginate(10)->withQueryString();

        // Kartu ringkasan
        $totalDiterima = Pembayaran::where('status', 'lunas')
            ->whereDate('tgl_bayar', '>=', now()->subDays(30))
            ->sum('nominal');

        $belumDiterimaQuery = Pembayaran::where('status', 'menunggu');
        $totalBelumDiterima = (clone $belumDiterimaQuery)->sum('nominal');
        $countBelumDiterima = (clone $belumDiterimaQuery)->count();

        return view('admin.pembayaran.index', compact(
            'pembayarans',
            'search',
            'status',
            'dariTgl',
            'sampaiTgl',
            'totalDiterima',
            'totalBelumDiterima',
            'countBelumDiterima'
        ));
    }

    public function terima(Pembayaran $pembayaran): RedirectResponse
    {
        $pembayaran->update([
            'status' => 'lunas',
            'keterangan' => 'Pembayaran telah diverifikasi admin.',
        ]);

        return back()->with('success', 'Pembayaran berhasil dikonfirmasi lunas.');
    }

    public function tolak(Request $request, Pembayaran $pembayaran): RedirectResponse
    {
        $data = $request->validate([
            'keterangan' => ['required', 'string', 'max:1000'],
        ]);

        $pembayaran->update([
            'status' => 'ditolak',
            'keterangan' => $data['keterangan'],
        ]);

        return back()->with('success', 'Pembayaran berhasil ditolak.');
    }
}