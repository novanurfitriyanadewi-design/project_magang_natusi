<?php

namespace App\Http\Controllers\AdminPeserta;

use App\Http\Controllers\Controller;
use App\Models\PengumpulanTugas;
use App\Models\Tugas;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LaporanPenugasanController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->get('search', ''));
        $jenisTugas = (string) $request->get('jenis_tugas', '');
        $statusFilter = (string) $request->get('status_filter', '');

        $query = Tugas::query()
            ->withCount([
                'penugasanPeserta as total_ditugaskan',
                'pengumpulanTugas as total_submitted' => fn ($q) => $q->whereNotNull('dikumpulkan_pada'),
                'pengumpulanTugas as total_terlambat' => fn ($q) => $q->where('status', 'telat'),
            ])
            ->when($search !== '', fn ($q) => $q->where('judul', 'like', "%{$search}%"))
            ->when($jenisTugas !== '', fn ($q) => $q->where('jenis_tugas', $jenisTugas))
            ->when($statusFilter !== '', fn ($q) => $q->where('status', $statusFilter));

        $tugasList = $query
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        $totalTugas = Tugas::query()->count();
        $totalSubmission = PengumpulanTugas::query()->whereNotNull('dikumpulkan_pada')->count();
        $totalOverdue = PengumpulanTugas::query()->where('status', 'telat')->count();
        $totalAssigned = Tugas::query()->withCount('penugasanPeserta')->get()->sum('penugasan_peserta_count');

        $stats = [
            'completion_rate' => $totalAssigned > 0 ? round(($totalSubmission / $totalAssigned) * 100, 1) : 0,
            'total_tugas' => $totalTugas,
            'total_submitted' => $totalSubmission,
            'total_overdue' => $totalOverdue,
            'avg_score' => '-',
            'pending_review' => max(0, $totalSubmission),
        ];

        $jenisTugasList = Tugas::query()
            ->whereNotNull('jenis_tugas')
            ->distinct()
            ->orderBy('jenis_tugas')
            ->pluck('jenis_tugas');

        return view('admin-peserta.laporan.penugasan', compact(
            'tugasList',
            'stats',
            'search',
            'jenisTugas',
            'statusFilter',
            'jenisTugasList'
        ));
    }
}
