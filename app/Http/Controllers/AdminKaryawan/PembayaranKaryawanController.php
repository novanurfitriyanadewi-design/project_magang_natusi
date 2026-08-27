<?php

namespace App\Http\Controllers\AdminKaryawan;

use App\Http\Controllers\Controller;
use App\Models\Karyawan;
use App\Models\Notifikasi;
use App\Models\PembayaranKaryawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class PembayaranKaryawanController extends Controller
{
    public function index(Request $request)
    {
        $periode = $request->query('periode', now()->format('Y-m'));
        $status = $request->query('status', '');

        $karyawan = Karyawan::where('status', 'aktif')->orderBy('nama_karyawan')->get();

        $query = PembayaranKaryawan::with('karyawan')->where('periode', $periode);

        if ($status !== '') {
            $query->where('status', $status);
        }

        $riwayat = $query->orderByDesc('created_at')->get();

        $totalKaryawan = $karyawan->count();
        $sudahDibayar = PembayaranKaryawan::where('periode', $periode)->where('status', 'terbayar')->count();
        $belumDibayar = max($totalKaryawan - $sudahDibayar, 0);
        $totalNominalLunas = PembayaranKaryawan::where('periode', $periode)->where('status', 'terbayar')->sum('nominal');

        return view('admin-karyawan.pembayaran-karyawan.index', [
            'karyawan' => $karyawan,
            'riwayat' => $riwayat,
            'periodeFilter' => $periode,
            'statusFilter' => $status,
            'totalKaryawan' => $totalKaryawan,
            'sudahDibayar' => $sudahDibayar,
            'belumDibayar' => $belumDibayar,
            'totalNominalLunas' => $totalNominalLunas,
        ]);
    }

    public function laporan(Request $request)
    {
        $tahun = $request->query('tahun', now()->format('Y'));
        $bulan = $request->query('bulan', '');
        $status = $request->query('status', '');

        $tahunList = PembayaranKaryawan::selectRaw('DISTINCT SUBSTRING(periode, 1, 4) as tahun')
            ->orderByDesc('tahun')
            ->pluck('tahun');
        if (! $tahunList->contains(now()->format('Y'))) {
            $tahunList->prepend(now()->format('Y'));
        }
        if ($tahun === 'all') {
            $query = PembayaranKaryawan::with('karyawan');
        } else {
            $query = PembayaranKaryawan::with('karyawan')->where('periode', 'like', $tahun . '-%');
            if ($bulan !== '') {
                $query->where('periode', sprintf('%s-%02d', $tahun, (int) $bulan));
            }
        }

        if ($status !== '') {
            $query->where('status', $status);
        }

        $data = $query->orderByDesc('periode')->orderBy('karyawan_id')->get();

        // Rekap per karyawan
        $rekapKaryawan = $data->groupBy(fn ($item) => optional($item->karyawan)->nama_karyawan ?? '-')
            ->map(function ($group, $nama) {
                return [
                    'nama' => $nama,
                    'jumlah_slip' => $group->count(),
                    'total_terbayar' => $group->where('status', 'terbayar')->sum('nominal'),
                    'total_belum' => $group->where('status', 'belum_terbayar')->sum('nominal'),
                    'total_keseluruhan' => $group->sum('nominal'),
                ];
            })
            ->sortBy('nama')
            ->values();

        $stats = [
            'total_gaji' => $data->where('status', 'terbayar')->sum('nominal'),
            'total_belum' => $data->where('status', 'belum_terbayar')->sum('nominal'),
            'total_slip' => $data->count(),
            'slip_terbayar' => $data->where('status', 'terbayar')->count(),
        ];

        return view('admin-karyawan.laporan.gaji', compact(
            'data', 'stats', 'rekapKaryawan',
            'tahunList', 'tahun', 'bulan', 'status'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'karyawan_id' => 'required|exists:karyawan,id_karyawan',
            'periode' => 'required|date_format:Y-m',
            'nominal' => 'required|integer|min:0',
            'tanggal_bayar' => 'nullable|date',
            'status' => 'required|in:terbayar,belum_terbayar',
            'keterangan' => 'nullable|string|max:500',
            'bukti_transfer' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        $path = null;
        if ($request->hasFile('bukti_transfer')) {
            $path = $request->file('bukti_transfer')->store('bukti-pembayaran-karyawan', 'public');
        }

        if (!$path) {
            $path = DB::table('pembayaran_karyawan')
                ->where('karyawan_id', $validated['karyawan_id'])
                ->where('periode', $validated['periode'])
                ->value('bukti_transfer');
        }

        $statusSebelumnya = DB::table('pembayaran_karyawan')
            ->where('karyawan_id', $validated['karyawan_id'])
            ->where('periode', $validated['periode'])
            ->value('status');

        $pembayaran = PembayaranKaryawan::updateOrCreate(
            ['karyawan_id' => $validated['karyawan_id'], 'periode' => $validated['periode']],
            [
                'nominal' => $validated['nominal'],
                'tanggal_bayar' => $validated['tanggal_bayar'] ?? null,
                'status' => $validated['status'],
                'keterangan' => $validated['keterangan'] ?? null,
                'bukti_transfer' => $path,
            ]
        );

        if ($validated['status'] === 'terbayar' && $statusSebelumnya !== 'terbayar') {
            $karyawan = Karyawan::find($validated['karyawan_id']);

            if ($karyawan && $karyawan->user_id) {
                $periodeLabel = Carbon::createFromFormat('Y-m', $validated['periode'])->translatedFormat('F Y');

                Notifikasi::create([
                    'user_id' => $karyawan->user_id,
                    'judul' => 'Slip Gaji Telah Dibayarkan',
                    'pesan' => sprintf(
                        'Gaji Anda periode %s sebesar Rp %s telah dibayarkan.',
                        $periodeLabel,
                        number_format($validated['nominal'], 0, ',', '.')
                    ),
                    'kategori' => 'pembayaran',
                    'tipe' => 'sukses',
                    'referensi_id' => $pembayaran->id_pembayaran,
                ]);
            }
        }

        return back()->with('success', 'Slip gaji berhasil disimpan.');
    }

    public function update(Request $request, $id)
    {
        $pembayaran = PembayaranKaryawan::findOrFail($id);

        $validated = $request->validate([
            'karyawan_id' => 'required|exists:karyawan,id_karyawan',
            'periode' => 'required|date_format:Y-m',
            'nominal' => 'required|integer|min:0',
            'tanggal_bayar' => 'nullable|date',
            'status' => 'required|in:terbayar,belum_terbayar',
            'keterangan' => 'nullable|string|max:500',
            'bukti_transfer' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        $path = $pembayaran->bukti_transfer;
        if ($request->hasFile('bukti_transfer')) {
            if ($path) {
                Storage::disk('public')->delete($path);
            }
            $path = $request->file('bukti_transfer')->store('bukti-pembayaran-karyawan', 'public');
        }

        $statusSebelumnya = $pembayaran->status;

        $pembayaran->update([
            'karyawan_id' => $validated['karyawan_id'],
            'periode' => $validated['periode'],
            'nominal' => $validated['nominal'],
            'tanggal_bayar' => $validated['tanggal_bayar'] ?? null,
            'status' => $validated['status'],
            'keterangan' => $validated['keterangan'] ?? null,
            'bukti_transfer' => $path,
        ]);

        if ($validated['status'] === 'terbayar' && $statusSebelumnya !== 'terbayar') {
            $karyawan = Karyawan::find($validated['karyawan_id']);

            if ($karyawan && $karyawan->user_id) {
                $periodeLabel = Carbon::createFromFormat('Y-m', $validated['periode'])->translatedFormat('F Y');

                Notifikasi::create([
                    'user_id' => $karyawan->user_id,
                    'judul' => 'Slip Gaji Telah Dibayarkan',
                    'pesan' => sprintf(
                        'Gaji Anda periode %s sebesar Rp %s telah dibayarkan.',
                        $periodeLabel,
                        number_format($validated['nominal'], 0, ',', '.')
                    ),
                    'kategori' => 'pembayaran',
                    'tipe' => 'sukses',
                    'referensi_id' => $pembayaran->id_pembayaran,
                ]);
            }
        }

        return back()->with('success', 'Slip gaji berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $pembayaran = PembayaranKaryawan::findOrFail($id);

        if ($pembayaran->bukti_transfer) {
            Storage::disk('public')->delete($pembayaran->bukti_transfer);
        }

        $pembayaran->delete();

        return back()->with('success', 'Data slip gaji berhasil dihapus.');
    }
}