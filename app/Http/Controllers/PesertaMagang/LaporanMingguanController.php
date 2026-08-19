<?php

namespace App\Http\Controllers\PesertaMagang;

use App\Http\Controllers\Controller;
use App\Models\LaporanMingguan;
use App\Models\TemplateLaporan;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LaporanMingguanController extends Controller
{
    /**
     * Halaman ini khusus menampilkan template dan aturan laporan mingguan.
     * Pengumpulan laporan dilakukan melalui menu Penugasan.
     */
    public function index(Request $request): View|RedirectResponse
    {
        $user = Auth::user();
        $peserta = $user?->pesertaMagang;

        if (! $peserta) {
            return redirect()
                ->route('peserta-magang.dashboard')
                ->with('error', 'Data peserta magang Anda belum terdaftar di sistem. Hubungi admin.');
        }

        $templateLaporan = TemplateLaporan::query()
            ->with('user')
            ->where('is_active', true)
            ->latest('id_template_laporan')
            ->first();

        return view('peserta-magang.laporan-mingguan', compact('templateLaporan'));
    }

    /**
     * Download template melalui Laravel agar tidak bergantung pada /storage URL.
     */
    public function downloadTemplate(TemplateLaporan $templateLaporan): StreamedResponse
    {
        $user = Auth::user();
        abort_unless($user?->pesertaMagang, 403, 'Akses hanya untuk peserta magang.');

        abort_unless(
            $templateLaporan->is_active &&
            $templateLaporan->file_word &&
            Storage::disk('public')->exists($templateLaporan->file_word),
            404,
            'Template laporan tidak ditemukan atau sudah tidak aktif.'
        );

        $extension = pathinfo($templateLaporan->file_word, PATHINFO_EXTENSION) ?: 'docx';
        $baseName = Str::slug($templateLaporan->judul ?: 'template-laporan-mingguan');
        $downloadName = ($baseName !== '' ? $baseName : 'template-laporan-mingguan') . '.' . $extension;

        return Storage::disk('public')->download(
            $templateLaporan->file_word,
            $downloadName
        );
    }

    /**
     * Dipertahankan untuk kompatibilitas route lama.
     * UI baru tidak lagi melakukan upload dari halaman ini.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $peserta = $user?->pesertaMagang;

        if (! $peserta) {
            return redirect()
                ->route('peserta-magang.dashboard')
                ->with('error', 'Data peserta magang Anda belum terdaftar di sistem. Hubungi admin.');
        }

        $mingguSaatIni = $this->hitungMingguSaatIni($peserta);

        $validated = $request->validate([
            'laporan' => ['required', 'file', 'mimes:pdf,doc,docx', 'max:10240'],
        ]);

        $path = $request->file('laporan')->store('laporan-mingguan', 'public');

        LaporanMingguan::updateOrCreate(
            [
                'peserta_id' => $peserta->id_peserta,
                'minggu_ke' => $mingguSaatIni,
            ],
            [
                'laporan' => $path,
                'dikumpulkan_pada' => Carbon::now(),
            ]
        );

        return redirect()
            ->route('peserta-magang.penugasan.index')
            ->with('success', 'Laporan berhasil dikirim. Pengumpulan selanjutnya dilakukan melalui menu Penugasan.');
    }

    private function hitungMingguSaatIni($peserta): int
    {
        if (! $peserta->tgl_mulai) {
            return 1;
        }

        $selisihHari = Carbon::parse($peserta->tgl_mulai)->diffInDays(Carbon::now());

        return max(1, (int) floor($selisihHari / 7) + 1);
    }
}
