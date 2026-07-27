<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use App\Models\PengumpulanTugas;
use App\Models\PenugasanPeserta;
use App\Models\PesertaMagang;
use App\Services\PenugasanTemplateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class TugasController extends Controller
{
    public function index(
        Request $request,
        PenugasanTemplateService $service
    ) {
        $peserta = $this->currentParticipant($request);
        $service->refreshStatuses($peserta);

        $penugasan = PenugasanPeserta::query()
            ->with(['tugas', 'templateLaporan'])
            ->where('peserta_id', $peserta->id_peserta)
            ->whereHas('tugas', function ($query) use ($request): void {
                if ($request->filled('jenis_tugas')) {
                    $query->where('jenis_tugas', $request->string('jenis_tugas'));
                }
            })
            ->orderByRaw("CASE status WHEN 'aktif' THEN 0 WHEN 'terjadwal' THEN 1 WHEN 'selesai' THEN 2 ELSE 3 END")
            ->orderBy('deadline')
            ->get();

        $pengumpulan = PengumpulanTugas::query()
            ->where('peserta_id', $peserta->id_peserta)
            ->whereIn('tugas_id', $penugasan->pluck('tugas_id'))
            ->get()
            ->keyBy('tugas_id');

        return view('peserta.tugas', compact('peserta', 'penugasan', 'pengumpulan'));
    }

    public function downloadTask(Request $request, PenugasanPeserta $penugasan)
    {
        $this->ensureOwnership($request, $penugasan);
        $task = $penugasan->tugas;

        abort_unless($task && $task->file_tugas, 404, 'File tugas tidak tersedia.');
        abort_unless(Storage::disk('public')->exists($task->file_tugas), 404, 'File tugas tidak ditemukan.');

        $extension = pathinfo($task->file_tugas, PATHINFO_EXTENSION);
        $name = Str::slug($task->judul).($extension ? '.'.$extension : '');

        return Storage::disk('public')->download($task->file_tugas, $name);
    }

    public function downloadReportTemplate(Request $request, PenugasanPeserta $penugasan)
    {
        $this->ensureOwnership($request, $penugasan);
        $template = $penugasan->templateLaporan;

        abort_unless(
            $penugasan->tugas?->kategori_tugas === 'laporan' && $template,
            404,
            'Template laporan belum tersedia.'
        );
        abort_unless(
            Storage::disk('public')->exists($template->file_word),
            404,
            'File template laporan tidak ditemukan.'
        );

        $extension = pathinfo($template->file_word, PATHINFO_EXTENSION);
        $name = Str::slug($template->judul).($extension ? '.'.$extension : '.docx');

        return Storage::disk('public')->download($template->file_word, $name);
    }

    public function submit(
        Request $request,
        PenugasanPeserta $penugasan
    ) {
        $peserta = $this->ensureOwnership($request, $penugasan);

        $request->validate([
            'file_jawaban' => [
                'required',
                'file',
                'mimes:pdf,doc,docx,xls,xlsx,zip',
                'max:10240',
            ],
        ]);

        if ($penugasan->status === 'dilewati') {
            throw ValidationException::withMessages([
                'file_jawaban' => 'Tugas ini dilewati berdasarkan tanggal mulai magang Anda.',
            ]);
        }

        if ($penugasan->tersedia_pada && now()->lessThan($penugasan->tersedia_pada)) {
            throw ValidationException::withMessages([
                'file_jawaban' => 'Tugas belum tersedia untuk dikumpulkan.',
            ]);
        }

        $existing = PengumpulanTugas::query()
            ->where('tugas_id', $penugasan->tugas_id)
            ->where('peserta_id', $peserta->id_peserta)
            ->exists();

        if ($existing) {
            throw ValidationException::withMessages([
                'file_jawaban' => 'Tugas ini sudah pernah dikumpulkan.',
            ]);
        }

        $path = $request->file('file_jawaban')
            ->store('jawaban-tugas', 'public');

        $late = $penugasan->deadline && now()->greaterThan($penugasan->deadline);

        PengumpulanTugas::create([
            'tugas_id' => $penugasan->tugas_id,
            'peserta_id' => $peserta->id_peserta,
            'file_jawaban' => $path,
            'dikumpulkan_pada' => now(),
            'status' => $late ? 'telat' : 'terkumpul',
        ]);

        $penugasan->update(['status' => 'selesai']);

        return redirect()
            ->route('peserta.tugas.index')
            ->with(
                'success',
                $late
                    ? 'Tugas berhasil dikumpulkan, tetapi melewati deadline.'
                    : 'Tugas berhasil dikumpulkan.'
            );
    }

    private function currentParticipant(Request $request): PesertaMagang
    {
        return PesertaMagang::query()
            ->where('user_id', $request->user()->id_user)
            ->firstOrFail();
    }

    private function ensureOwnership(
        Request $request,
        PenugasanPeserta $penugasan
    ): PesertaMagang {
        $peserta = $this->currentParticipant($request);
        abort_unless($penugasan->peserta_id === $peserta->id_peserta, 403);

        $penugasan->loadMissing(['tugas', 'templateLaporan']);

        return $peserta;
    }
}
