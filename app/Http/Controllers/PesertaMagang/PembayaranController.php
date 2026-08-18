<?php

namespace App\Http\Controllers\PesertaMagang;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\NominalPembayaran;
use App\Models\Pembayaran;
use App\Services\AdminMagangNotificationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PembayaranController extends Controller
{
    private const QRIS_BANK_NAME = 'QRIS';
    private const QRIS_ACCOUNT = 'QRIS-CV-NATUSI';

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
        $qris = Bank::query()
            ->where('nama_bank', self::QRIS_BANK_NAME)
            ->where('no_rekening', self::QRIS_ACCOUNT)
            ->first();

        $pembayaranTerkini = Pembayaran::where('peserta_id', $pesertaId)
            ->latest('created_at')
            ->first();

        $riwayat = Pembayaran::where('peserta_id', $pesertaId)
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('peserta-magang.pembayaran', compact(
            'nominalAktif',
            'qris',
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
            return redirect()->route('peserta-magang.pembayaran.index')
                ->with('error', 'Nominal pembayaran belum diatur oleh admin.');
        }

        $qris = Bank::query()
            ->where('nama_bank', self::QRIS_BANK_NAME)
            ->where('no_rekening', self::QRIS_ACCOUNT)
            ->whereNotNull('qris_image')
            ->first();

        if (! $qris) {
            return redirect()->route('peserta-magang.pembayaran.index')
                ->with('error', 'Kode QR pembayaran belum tersedia. Hubungi admin.');
        }

        $validated = $request->validate([
            'tgl_bayar' => ['required', 'date'],
            'keterangan' => ['nullable', 'string', 'max:500'],
            'bukti_transfer' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ]);

        $path = $request->file('bukti_transfer')->store('pembayaran/bukti', 'public');

        $pembayaran = Pembayaran::create([
            'id_bank' => $qris->id_bank,
            'nominal_id' => $nominalAktif->id_nominal,
            'peserta_id' => $pesertaId,
            'nominal' => $nominalAktif->jumlah_nominal,
            'bukti_transfer' => $path,
            'tgl_bayar' => Carbon::parse($validated['tgl_bayar']),
            'status' => 'menunggu',
            'keterangan' => $validated['keterangan'] ?? null,
        ]);

        app(AdminMagangNotificationService::class)->notify(
            'Pembayaran Peserta Menunggu Verifikasi',
            sprintf(
                '%s mengirim bukti pembayaran QRIS sebesar Rp %s dan menunggu verifikasi admin.',
                $user->nama,
                number_format($pembayaran->nominal, 0, ',', '.')
            ),
            'pembayaran',
            $pembayaran->id_pembayaran,
            'info'
        );

        return redirect()
            ->route('peserta-magang.pembayaran.index')
            ->with('success', 'Bukti pembayaran berhasil dikirim. Menunggu verifikasi admin.');
    }
}
