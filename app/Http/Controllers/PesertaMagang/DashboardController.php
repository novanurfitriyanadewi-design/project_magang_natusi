<?php

namespace App\Http\Controllers\PesertaMagang;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\LaporanMingguan;
use App\Models\Pembayaran;
use App\Models\Notifikasi;
use App\Models\Pengumuman;
use App\Models\Tugas;
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

        $userId    = $user->getKey();                          // users.id_user — dipakai untuk Tugas
        $pesertaId = $user->pesertaMagang?->id_peserta;         // peserta_magang.id_peserta — dipakai untuk Absensi, Pembayaran, LaporanMingguan

        $absensi         = $this->getRingkasanAbsensi($pesertaId, $rentang);
        $penugasan       = $this->getRingkasanPenugasan($userId);
        $pembayaran      = $this->getRingkasanPembayaran($pesertaId);
        $laporanMingguan = $this->getRingkasanLaporanMingguan($pesertaId);
        $progressHarian  = $this->getProgressHarian($userId, $rentang);
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

        if (! $pesertaId) {
            return [
                'hadir_hari_ini'   => false,
                'total_hadir'      => 0,
                'total_hari_kerja' => $totalHariKerja,
                'status'           => 'perlu_perhatian',
            ];
        }

        $totalHadir = Absensi::where('peserta_id', $pesertaId)
            ->where('status', 'hadir')
            ->whereBetween('tanggal', [$awalPeriode, Carbon::now()])
            ->count();

        $hadirHariIni = Absensi::where('peserta_id', $pesertaId)
            ->whereDate('tanggal', Carbon::today())
            ->where('status', 'hadir')
            ->exists();

        $persentaseKehadiran = $totalHariKerja > 0 ? ($totalHadir / $totalHariKerja) * 100 : 0;

        return [
            'hadir_hari_ini'   => $hadirHariIni,
            'total_hadir'      => $totalHadir,
            'total_hari_kerja' => $totalHariKerja,
            'status'           => $persentaseKehadiran >= 80 ? 'on_track' : 'perlu_perhatian',
        ];
    }

    private function getRingkasanPenugasan(int $userId): array
    {
        $aktif = Tugas::where('user_id', $userId)
            ->where('status', 'aktif')
            ->count();

        $mendekatiDeadline = Tugas::where('user_id', $userId)
            ->where('status', 'aktif')
            ->whereBetween('pengumpulan', [Carbon::now(), Carbon::now()->addDays(2)])
            ->count();

        return [
            'aktif'              => $aktif,
            'mendekati_deadline' => $mendekatiDeadline,
        ];
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
     */
    private function getProgressHarian(int $userId, string $rentang): array
    {
        $jumlahHari = $rentang === 'minggu' ? 5 : 5; // Senin-Jumat untuk kedua rentang
        $awal = Carbon::now()->startOfWeek();
        $hasil = [];

        for ($i = 0; $i < $jumlahHari; $i++) {
            $tanggal = $awal->copy()->addDays($i);

            $totalTugas = Tugas::where('user_id', $userId)
                ->whereDate('created_at', $tanggal)
                ->count();

            $selesai = Tugas::where('user_id', $userId)
                ->whereDate('created_at', $tanggal)
                ->where('status', 'selesai')
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