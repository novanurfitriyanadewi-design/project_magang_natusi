<?php

namespace App\Http\Controllers\AdminPeserta;

use App\Http\Controllers\Controller;
use App\Models\LaporanMingguan;
use App\Models\PesertaMagang;
use App\Models\TemplateLaporan;
use App\Services\PenugasanTemplateService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class LaporanMingguanController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $mingguKe = $request->query('minggu_ke', '');

        $riwayat = LaporanMingguan::query()
            ->with(['peserta.user', 'peserta.permintaan', 'peserta.jurusan'])
            ->when($search !== '', function (Builder $query) use ($search) {
                $query->whereHas('peserta.user', function (Builder $q) use ($search) {
                    $q->where('nama', 'like', "%{$search}%");
                });
            })
            ->when($mingguKe !== '', function (Builder $query) use ($mingguKe) {
                $query->where('minggu_ke', (int) $mingguKe);
            })
            ->orderByDesc('minggu_ke')
            ->orderByDesc('dikumpulkan_pada')
            ->paginate(10)
            ->withQueryString();

        $belumLaporMingguIni = PesertaMagang::query()
            ->with(['user', 'permintaan'])
            ->where('status', 'aktif')
            ->when($search !== '', function (Builder $query) use ($search) {
                $query->whereHas('user', function (Builder $q) use ($search) {
                    $q->where('nama', 'like', "%{$search}%");
                });
            })
            ->get()
            ->filter(function (PesertaMagang $peserta) {
                $mingguSaatIni = $this->hitungMingguSaatIni($peserta);

                return ! LaporanMingguan::query()
                    ->where('peserta_id', $peserta->id_peserta)
                    ->where('minggu_ke', $mingguSaatIni)
                    ->exists();
            })
            ->values();

        $stats = [
            'total_laporan' => LaporanMingguan::query()->count(),
            'minggu_ini' => LaporanMingguan::query()
                ->whereDate('dikumpulkan_pada', '>=', now()->startOfWeek())
                ->count(),
            'belum_lapor' => $belumLaporMingguIni->count(),
        ];

        $mingguList = LaporanMingguan::query()
            ->select('minggu_ke')
            ->distinct()
            ->orderByDesc('minggu_ke')
            ->pluck('minggu_ke');

        $templateLaporan = TemplateLaporan::query()
            ->latest('id_template_laporan')
            ->get();

        return view('admin-peserta.laporan-mingguan', compact(
            'riwayat',
            'belumLaporMingguIni',
            'stats',
            'search',
            'mingguKe',
            'mingguList',
            'templateLaporan',
        ));
    }

    public function storeTemplateLaporan(
        Request $request,
        PenugasanTemplateService $service
    ): RedirectResponse {
        $validated = $request->validate([
            'judul_template' => ['required', 'string', 'max:255'],
            'instansi_laporan' => ['required', Rule::in(['universitas', 'sekolah', 'semua'])],
            'file_word' => ['required', 'file', 'mimes:doc,docx', 'max:10240'],
            'ketentuan_laporan' => ['required', 'string', 'max:20000'],
        ]);

        TemplateLaporan::query()
            ->where('instansi', $validated['instansi_laporan'])
            ->where('is_active', true)
            ->update(['is_active' => false]);

        $path = $request->file('file_word')
            ->store('template-laporan', 'public');

        $template = TemplateLaporan::create([
            'user_id' => auth()->id(),
            'instansi' => $validated['instansi_laporan'],
            'judul' => $validated['judul_template'],
            'file_word' => $path,
            'ketentuan' => $validated['ketentuan_laporan'],
            'is_active' => true,
        ]);

        $updatedAssignments = $service->refreshReportTemplate($template);

        return redirect()
            ->route('admin-peserta.laporan-mingguan.index')
            ->with(
                'success',
                "Template laporan berhasil disimpan dan diterapkan ke {$updatedAssignments} penugasan laporan aktif."
            );
    }

    public function destroyTemplateLaporan(TemplateLaporan $templateLaporan): RedirectResponse
    {
        Storage::disk('public')->delete($templateLaporan->file_word);
        $templateLaporan->delete();

        return redirect()
            ->route('admin-peserta.laporan-mingguan.index')
            ->with('success', 'Template laporan berhasil dihapus.');
    }

    public function download(LaporanMingguan $laporanMingguan): BinaryFileResponse
    {
        abort_unless(
            $laporanMingguan->laporan && Storage::disk('public')->exists($laporanMingguan->laporan),
            404,
            'File laporan tidak ditemukan.'
        );

        return Storage::disk('public')->download(
            $laporanMingguan->laporan,
            "laporan-minggu-{$laporanMingguan->minggu_ke}-" . ($laporanMingguan->peserta?->user?->nama ?? 'peserta') . '.' . pathinfo($laporanMingguan->laporan, PATHINFO_EXTENSION)
        );
    }

    private function hitungMingguSaatIni(PesertaMagang $peserta): int
    {
        if (! $peserta->tgl_mulai) {
            return 1;
        }

        $selisihHari = \Carbon\Carbon::parse($peserta->tgl_mulai)->diffInDays(now());

        return max(1, (int) floor($selisihHari / 7) + 1);
    }
}
