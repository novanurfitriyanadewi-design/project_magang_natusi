<?php

namespace App\Http\Controllers\PesertaMagang;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\NominalPembayaran;
use App\Models\Pembayaran;
use App\Services\AdminMagangNotificationService;
use App\Services\PembayaranPeriodService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class PembayaranController extends Controller
{
    private const QRIS_BANK_NAME = 'QRIS';
    private const QRIS_ACCOUNT = 'QRIS-CV-NATUSI';

    public function index(Request $request, PembayaranPeriodService $periodService)
    {
        $user = Auth::user();
        $peserta = $user->pesertaMagang;
        $pesertaId = $peserta?->id_peserta;

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

        $allPayments = Pembayaran::where('peserta_id', $pesertaId)
            ->orderBy('created_at')
            ->get();

        $periodeMulaiBerikutnya = $periodService->firstUnpaidMonth($peserta, $allPayments);
        $maxBulanPilihan = $periodService->maxConsecutiveSelectableMonths(
            $peserta,
            $allPayments,
            $periodeMulaiBerikutnya
        );

        $pendingMap = $periodService->pendingMonthMap($allPayments);
        $periodeBerikutnyaMenunggu = isset($pendingMap[$periodeMulaiBerikutnya->format('Y-m')]);
        $lunasBulanIni = $periodService->isCurrentMonthPaid($allPayments);
        $lunasSampai = $periodService->paidThroughLabel($peserta, $allPayments);

        $pembayaranTerkini = $allPayments->sortByDesc('created_at')->first();
        $riwayat = Pembayaran::where('peserta_id', $pesertaId)
            ->orderByDesc('created_at')
            ->paginate(10);

        $resolvedRejectedIds = $riwayat->getCollection()
            ->filter(fn (Pembayaran $payment) => $periodService->isRejectedResolved($payment, $allPayments))
            ->pluck('id_pembayaran')
            ->all();

        $periodeLabels = $riwayat->getCollection()
            ->mapWithKeys(fn (Pembayaran $payment) => [
                $payment->id_pembayaran => $periodService->labelFor($payment),
            ])
            ->all();

        return view('peserta-magang.pembayaran', compact(
            'nominalAktif',
            'qris',
            'pembayaranTerkini',
            'riwayat',
            'periodeMulaiBerikutnya',
            'maxBulanPilihan',
            'periodeBerikutnyaMenunggu',
            'lunasBulanIni',
            'lunasSampai',
            'resolvedRejectedIds',
            'periodeLabels'
        ));
    }

    public function store(Request $request, PembayaranPeriodService $periodService)
    {
        $user = Auth::user();
        $peserta = $user->pesertaMagang;
        $pesertaId = $peserta?->id_peserta;

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
            'jumlah_bulan' => ['required', 'integer', 'min:1', 'max:12'],
            'keterangan' => ['nullable', 'string', 'max:500'],
            'bukti_transfer' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ]);

        $allPayments = Pembayaran::where('peserta_id', $pesertaId)
            ->orderBy('created_at')
            ->get();

        $periodeMulai = $periodService->firstUnpaidMonth($peserta, $allPayments);
        $maxBulan = $periodService->maxConsecutiveSelectableMonths($peserta, $allPayments, $periodeMulai);
        $jumlahBulan = (int) $validated['jumlah_bulan'];

        if ($maxBulan < 1) {
            throw ValidationException::withMessages([
                'jumlah_bulan' => 'Periode berikutnya sedang menunggu verifikasi atau seluruh periode magang sudah lunas.',
            ]);
        }

        if ($jumlahBulan > $maxBulan) {
            throw ValidationException::withMessages([
                'jumlah_bulan' => "Maksimal pembayaran yang bisa dipilih saat ini adalah {$maxBulan} bulan berturut-turut.",
            ]);
        }

        $periodeSelesai = $periodeMulai->copy()
            ->addMonthsNoOverflow($jumlahBulan - 1)
            ->endOfMonth();

        $nominalTotal = (int) $nominalAktif->jumlah_nominal * $jumlahBulan;
        $path = $request->file('bukti_transfer')->store('pembayaran/bukti', 'public');

        $pembayaran = Pembayaran::create([
            'id_bank' => $qris->id_bank,
            'nominal_id' => $nominalAktif->id_nominal,
            'peserta_id' => $pesertaId,
            'nominal' => $nominalTotal,
            'bukti_transfer' => $path,
            'tgl_bayar' => Carbon::parse($validated['tgl_bayar']),
            'periode_mulai' => $periodeMulai->toDateString(),
            'periode_selesai' => $periodeSelesai->toDateString(),
            'jumlah_bulan' => $jumlahBulan,
            'status' => 'menunggu',
            'keterangan' => $validated['keterangan'] ?? null,
        ]);

        app(AdminMagangNotificationService::class)->notify(
            'Pembayaran Peserta Menunggu Verifikasi',
            sprintf(
                '%s mengirim bukti pembayaran QRIS sebesar Rp %s untuk periode %s dan menunggu verifikasi admin.',
                $user->nama,
                number_format($pembayaran->nominal, 0, ',', '.'),
                $periodService->labelFor($pembayaran)
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
