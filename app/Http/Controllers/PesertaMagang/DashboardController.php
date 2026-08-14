<?php

namespace App\Http\Controllers\PesertaMagang;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\LaporanMingguan;
use App\Models\Pembayaran;
use App\Models\PengumpulanTugas;
use App\Models\PenugasanPeserta;
use App\Models\PesertaMagang;
use App\Models\Notifikasi;
use App\Models\Pengumuman;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Tampilkan dashboard overview untuk karyawan/intern yang sedang login.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $rentang = $request->query('rentang', 'bulan');

        $pesertaId = $user->pesertaMagang?->id_peserta;         // peserta_magang.id_peserta — dipakai untuk Absensi, Pembayaran, LaporanMingguan, Penugasan

        $absensi         = $this->getRingkasanAbsensi($pesertaId, $rentang);
        $penugasan       = $this->getRingkasanPenugasan($pesertaId);
        $pembayaran      = $this->getRingkasanPembayaran($pesertaId);
        $laporanMingguan = $this->getRingkasanLaporanMingguan($pesertaId);
        $progressHarian  = $this->getProgressHarian($pesertaId, $rentang);
        $tugasBelumMingguIni = $this->getTugasBelumMingguIni($pesertaId);
        $pengumuman      = Pengumuman::where('aktif', true)
            ->latest()
            ->take(4)
            ->get();

        return view('peserta-magang.dashboard', compact(
            'user',
            'absensi',
            'penugasan',
            'pembayaran',
            'laporanMingguan',
            'progressHarian',
            'tugasBelumMingguIni',
            'pengumuman',
            'rentang'
        ));
    }

    private function getRingkasanAbsensi(?int $pesertaId, string $rentang): array
    {
        $awalPeriode = $rentang === 'minggu'
            ? Carbon::now()->startOfWeek()
            : Carbon::now()->startOfMonth();

        $totalHariKerja = Carbon::now()->diffInWeekdays($awalPeriode) + 1;

        $breakdownKosong = [
            'hadir' => 0,
            'terlambat' => 0,
            'izin' => 0,
            'sakit' => 0,
            'alpa' => 0,
        ];

        if (! $pesertaId) {
            return [
                'hadir_hari_ini'   => false,
                'status_hari_ini'  => null,
                'total_hadir'      => 0,
                'total_hari_kerja' => $totalHariKerja,
                'status'           => 'perlu_perhatian',
                'breakdown'        => $breakdownKosong,
            ];
        }

        $breakdown = Absensi::where('absentable_id', $pesertaId)
            ->where('absentable_type', PesertaMagang::class)
            ->whereBetween('tanggal', [$awalPeriode, Carbon::now()])
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        foreach ($breakdownKosong as $key => $default) {
            $breakdownKosong[$key] = (int) ($breakdown[$key] ?? 0);
        }

        $totalHadir = $breakdownKosong['hadir'] + $breakdownKosong['terlambat'];

        $absensiHariIni = Absensi::where('absentable_id', $pesertaId)
            ->where('absentable_type', PesertaMagang::class)
            ->whereDate('tanggal', Carbon::today())
            ->first();

        $persentaseKehadiran = $totalHariKerja > 0 ? ($totalHadir / $totalHariKerja) * 100 : 0;

        return [
            'hadir_hari_ini'   => $absensiHariIni && in_array($absensiHariIni->status, ['hadir', 'terlambat'], true),
            'status_hari_ini'  => $absensiHariIni?->status,
            'total_hadir'      => $totalHadir,
            'total_hari_kerja' => $totalHariKerja,
            'status'           => $persentaseKehadiran >= 80 ? 'on_track' : 'perlu_perhatian',
            'breakdown'        => $breakdownKosong,
        ];
    }

    private function getRingkasanPenugasan(?int $pesertaId): array
    {
        if (! $pesertaId) {
            return ['aktif' => 0, 'selesai' => 0, 'mendekati_deadline' => 0];
        }

        $sudahDikumpulkan = PengumpulanTugas::where('peserta_id', $pesertaId)
            ->pluck('tugas_id');

        $aktif = PenugasanPeserta::where('peserta_id', $pesertaId)
            ->where('status', 'aktif')
            ->whereNotIn('tugas_id', $sudahDikumpulkan)
            ->count();

        $selesai = PenugasanPeserta::where('peserta_id', $pesertaId)
            ->whereIn('tugas_id', $sudahDikumpulkan)
            ->count();

        $mendekatiDeadline = PenugasanPeserta::where('peserta_id', $pesertaId)
            ->where('status', 'aktif')
            ->whereNotIn('tugas_id', $sudahDikumpulkan)
            ->whereBetween('deadline', [Carbon::now(), Carbon::now()->addDays(2)])
            ->count();

        return [
            'aktif'              => $aktif,
            'selesai'            => $selesai,
            'mendekati_deadline' => $mendekatiDeadline,
        ];
    }

    /**
     * Daftar tugas yang belum dikerjakan (belum ada pengumpulan) dengan
     * deadline jatuh pada minggu berjalan (Senin s/d Minggu ini).
     */
    private function getTugasBelumMingguIni(?int $pesertaId): \Illuminate\Support\Collection
    {
        if (! $pesertaId) {
            return collect();
        }

        $sudahDikumpulkan = PengumpulanTugas::where('peserta_id', $pesertaId)
            ->pluck('tugas_id');

        return PenugasanPeserta::with('tugas')
            ->where('peserta_id', $pesertaId)
            ->whereIn('status', ['aktif', 'terjadwal'])
            ->whereNotIn('tugas_id', $sudahDikumpulkan)
            ->whereBetween('deadline', [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek(),
            ])
            ->orderBy('deadline')
            ->get()
            ->filter(fn (PenugasanPeserta $p) => $p->tugas !== null)
            ->map(fn (PenugasanPeserta $p) => [
                'judul'    => $p->tugas->judul,
                'deadline' => $p->deadline,
                'terlambat' => $p->deadline && $p->deadline->isPast(),
            ])
            ->values();
    }

    private function getRingkasanPembayaran(?int $pesertaId): array
    {
        if (! $pesertaId) {
            return ['status' => 'Belum Lunas', 'periode' => '-'];
        }

        $pembayaran = Pembayaran::where('peserta_id', $pesertaId)
            ->latest('tgl_bayar')
            ->first();

        return [
            'status'  => $pembayaran && $pembayaran->status === 'lunas' ? 'Lunas' : 'Belum Lunas',
            'periode' => $pembayaran ? Carbon::parse($pembayaran->tgl_bayar)->translatedFormat('F Y') : '-',
        ];
    }

    private function getRingkasanLaporanMingguan(?int $pesertaId): array
    {
        $mingguKe = (int) ceil(Carbon::now()->day / 7);

        if (! $pesertaId) {
            return ['minggu_ke' => $mingguKe, 'sudah_dikirim' => false];
        }

        $laporan = LaporanMingguan::where('peserta_id', $pesertaId)
            ->where('minggu_ke', $mingguKe)
            ->first();

        return [
            'minggu_ke'     => $mingguKe,
            'sudah_dikirim' => (bool) $laporan,
        ];
    }

    /**
     * Data batang progress penugasan harian, dipakai untuk chart sederhana di view.
     * Dihitung dari deadline tugas yang dijadwalkan untuk peserta ini per hari.
     */
    private function getProgressHarian(?int $pesertaId, string $rentang): array
    {
        $jumlahHari = 5; // Senin-Jumat
        $awal = Carbon::now()->startOfWeek();
        $hasil = [];

        if (! $pesertaId) {
            for ($i = 0; $i < $jumlahHari; $i++) {
                $tanggal = $awal->copy()->addDays($i);
                $hasil[] = [
                    'label' => $tanggal->translatedFormat('D'),
                    'persentase' => 5,
                    'is_today' => $tanggal->isToday(),
                ];
            }

            return $hasil;
        }

        $sudahDikumpulkan = PengumpulanTugas::where('peserta_id', $pesertaId)
            ->pluck('tugas_id');

        for ($i = 0; $i < $jumlahHari; $i++) {
            $tanggal = $awal->copy()->addDays($i);

            $totalTugas = PenugasanPeserta::where('peserta_id', $pesertaId)
                ->whereDate('deadline', $tanggal)
                ->count();

            $selesai = PenugasanPeserta::where('peserta_id', $pesertaId)
                ->whereDate('deadline', $tanggal)
                ->whereIn('tugas_id', $sudahDikumpulkan)
                ->count();

            $persentase = $totalTugas > 0 ? round(($selesai / $totalTugas) * 100) : 0;

            $hasil[] = [
                'label'      => $tanggal->translatedFormat('D'),
                'persentase' => max($persentase, 5), // minimal 5% biar bar tetap kelihatan
                'is_today'   => $tanggal->isToday(),
            ];
        }

        return $hasil;
    }
}