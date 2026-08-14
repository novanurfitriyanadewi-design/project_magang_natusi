<?php

namespace App\Http\Controllers\AdminPeserta;

use App\Http\Controllers\Controller;
use App\Models\Notifikasi;
use App\Models\Pembayaran;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

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

    public function terima(Pembayaran $pembayaran): RedirectResponse
    {
        $pembayaran->load('peserta.user');

        $pembayaran->update(['status' => 'lunas']);

        if ($pembayaran->peserta?->user) {
            Notifikasi::create([
                'user_id' => $pembayaran->peserta->user->id_user,
                'judul' => 'Pembayaran Diterima',
                'pesan' => sprintf(
                    'Pembayaran Anda sebesar Rp %s tanggal %s telah diverifikasi dan diterima oleh admin. Terima kasih!',
                    number_format($pembayaran->nominal, 0, ',', '.'),
                    optional($pembayaran->tgl_bayar)->translatedFormat('d F Y') ?? '-'
                ),
                'kategori' => 'pembayaran',
                'tipe' => 'sukses',
                'referensi_id' => $pembayaran->id_pembayaran,
            ]);
        }

        return redirect()
            ->route('admin-peserta.pembayaran.index')
            ->with('success', 'Pembayaran berhasil diterima dan ditandai lunas.');
    }

    public function tolak(Request $request, Pembayaran $pembayaran): RedirectResponse
    {
        $validated = $request->validate([
            'keterangan' => ['required', 'string', 'max:1000'],
        ], [
            'keterangan.required' => 'Tuliskan alasan/kekurangan pembayaran sebelum menolak.',
        ]);

        $pembayaran->load('peserta.user');

        $pembayaran->update([
            'status' => 'ditolak',
            'keterangan' => $validated['keterangan'],
        ]);

        if ($pembayaran->peserta?->user) {
            Notifikasi::create([
                'user_id' => $pembayaran->peserta->user->id_user,
                'judul' => 'Pembayaran Perlu Ditinjau Ulang',
                'pesan' => sprintf(
                    'Pembayaran Anda sebesar Rp %s belum bisa diverifikasi. Catatan dari admin: "%s". Silakan periksa kembali dan kirim ulang bukti pembayaran yang sesuai.',
                    number_format($pembayaran->nominal, 0, ',', '.'),
                    $validated['keterangan']
                ),
                'kategori' => 'pembayaran',
                'tipe' => 'peringatan',
                'referensi_id' => $pembayaran->id_pembayaran,
            ]);
        }

        return redirect()
            ->route('admin-peserta.pembayaran.index')
            ->with('success', 'Pembayaran ditandai perlu ditinjau ulang, dan catatan sudah dikirim ke peserta.');
    }
}