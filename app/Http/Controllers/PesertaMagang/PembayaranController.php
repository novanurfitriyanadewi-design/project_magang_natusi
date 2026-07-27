<?php

namespace App\Http\Controllers\PesertaMagang;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\NominalPembayaran;
use App\Models\Pembayaran;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PembayaranController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $pesertaId = $user->pesertaMagang?->id_peserta;

        if (! $pesertaId) {
            return redirect()
                ->route('peserta-magang.dashboard')
                ->with('error', 'Data peserta magang Anda belum terdaftar di sistem. Hubungi admin.');
        }

        $nominalAktif = NominalPembayaran::latest('id_nominal')->first();
        $banks = Bank::all();

        $pembayaranTerkini = Pembayaran::where('peserta_id', $pesertaId)
            ->latest('created_at')
            ->first();

        $riwayat = Pembayaran::with('bank')
            ->where('peserta_id', $pesertaId)
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('peserta-magang.pembayaran', compact(
            'nominalAktif',
            'banks',
            'pembayaranTerkini',
            'riwayat'
        ));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $pesertaId = $user->pesertaMagang?->id_peserta;

        if (! $pesertaId) {
            return redirect()
                ->route('peserta-magang.dashboard')
                ->with('error', 'Data peserta magang Anda belum terdaftar di sistem. Hubungi admin.');
        }

        $nominalAktif = NominalPembayaran::latest('id_nominal')->first();

        if (! $nominalAktif) {
            return redirect()
                ->route('peserta-magang.pembayaran.index')
                ->with('error', 'Nominal pembayaran belum diatur oleh admin. Hubungi admin.');
        }

        $validated = $request->validate([
            'id_bank'        => ['required', 'exists:bank,id_bank'],
            'tgl_bayar'      => ['required', 'date'],
            'keterangan'     => ['nullable', 'string', 'max:500'],
            'bukti_transfer' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ]);

        $path = $request->file('bukti_transfer')->store('pembayaran/bukti', 'public');

        Pembayaran::create([
            'id_bank'        => $validated['id_bank'],
            'nominal_id'     => $nominalAktif->id_nominal,
            'peserta_id'     => $pesertaId,
            'nominal'        => $nominalAktif->jumlah_nominal,
            'bukti_transfer' => $path,
            'tgl_bayar'      => Carbon::parse($validated['tgl_bayar']),
            'status'         => 'menunggu',
            'keterangan'     => $validated['keterangan'] ?? null,
        ]);

        return redirect()
            ->route('peserta-magang.pembayaran.index')
            ->with('success', 'Bukti pembayaran berhasil dikirim. Menunggu verifikasi admin.');
    }
}
