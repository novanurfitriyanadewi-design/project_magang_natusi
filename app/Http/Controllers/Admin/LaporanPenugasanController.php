<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tugas;             // SESUAIKAN: pastikan nama model & namespace ini benar
use App\Models\PengumpulanTugas;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LaporanPenugasanController extends Controller
{
    public function index(Request $request): View
    {
        $search        = $request->get('search');
        $jenisTugas    = $request->get('jenis_tugas');
        $statusFilter  = $request->get('status_filter'); // SESUAIKAN dengan nilai enum status di tabel `tugas`

        // Relasi 'pengumpulanTugas' sudah ada di model Tugas (hasMany ke PengumpulanTugas)
        $query = Tugas::query()->withCount([
            'pengumpulanTugas as total_submitted' => fn ($q) => $q->whereNotNull('dikumpulkan_pada'),
        ]);

        if ($search) {
            $query->where('judul', 'like', "%{$search}%");
        }

        if ($jenisTugas) {
            $query->where('jenis_tugas', $jenisTugas); // SESUAIKAN: cek kolom ini beneran ada di tabel tugas
        }

        if ($statusFilter) {
            $query->where('status', $statusFilter); // SESUAIKAN: cek kolom ini beneran ada di tabel tugas
        }

        $tugasList = $query->latest()->paginate(10)->withQueryString();

        // Tambah data agregat per tugas (submission & keterlambatan)
        $tugasList->getCollection()->transform(function ($tugas) {
            $pengumpulan = $tugas->pengumpulanTugas;

            $tugas->total_peserta = $pengumpulan->count();
            $tugas->overdue_count = $pengumpulan->where('status', 'terlambat')->count();

            return $tugas;
        });

        // ==== Statistik ringkasan ====
        // CATATAN: statistik nilai/rata-rata dihapus karena tabel pengumpulan_tugas
        // belum punya kolom 'nilai'. Kalau kamu mau fitur ini, tambahkan kolom nilai
        // dulu lewat migration, nanti aku sambungin lagi ke sini.
        $totalTugas      = Tugas::count();
        $totalSubmission = PengumpulanTugas::whereNotNull('dikumpulkan_pada')->count();
        $totalOverdue    = PengumpulanTugas::where('status', 'terlambat')->count();
        $totalDiharapkan = max(1, PengumpulanTugas::count());
        $completionRate  = round(($totalSubmission / $totalDiharapkan) * 100, 1);

        $stats = [
            'completion_rate' => $completionRate,
            'total_tugas'     => $totalTugas,
            'total_submitted' => $totalSubmission,
            'total_overdue'   => $totalOverdue,
            // Belum ada kolom 'nilai' di tabel pengumpulan_tugas, jadi:
            // - avg_score ditampilkan '-' (belum bisa dihitung)
            // - pending_review dianggap = semua yang sudah submit (karena belum ada mekanisme penilaian)
            'avg_score'       => '-',
            'pending_review'  => $totalSubmission,
        ];

        // ==== Tren bulanan: dikumpulkan vs terlambat ====
        $submittedRows = PengumpulanTugas::selectRaw('MONTH(dikumpulkan_pada) as bulan, COUNT(*) as total')
            ->whereNotNull('dikumpulkan_pada')
            ->whereYear('dikumpulkan_pada', now()->year)
            ->groupBy('bulan')
            ->pluck('total', 'bulan');

        $overdueRows = PengumpulanTugas::selectRaw('MONTH(created_at) as bulan, COUNT(*) as total')
            ->where('status', 'terlambat')
            ->whereYear('created_at', now()->year)
            ->groupBy('bulan')
            ->pluck('total', 'bulan');

        $monthlySubmitted = [];
        $monthlyOverdue   = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthlySubmitted[$m] = (int) ($submittedRows[$m] ?? 0);
            $monthlyOverdue[$m]   = (int) ($overdueRows[$m] ?? 0);
        }
        $chartMax = max(1, max(array_merge($monthlySubmitted, $monthlyOverdue)));

        $jenisTugasList = Tugas::select('jenis_tugas')->distinct()->pluck('jenis_tugas'); // SESUAIKAN: cek kolom ini ada

        return view('admin.laporan.penugasan', compact(
            'tugasList',
            'stats',
            'search',
            'jenisTugas',
            'statusFilter',
            'monthlySubmitted',
            'monthlyOverdue',
            'chartMax',
            'jenisTugasList'
        ));
    }
}