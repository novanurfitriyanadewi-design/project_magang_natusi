<?php

namespace App\Http\Controllers\PesertaMagang;

use App\Http\Controllers\Controller;
use App\Models\PengumpulanTugas;
use App\Models\PenugasanPeserta;
use App\Models\PesertaMagang;
use App\Services\PenugasanTemplateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class PenugasanController extends Controller
{
    public function index(Request $request, PenugasanTemplateService $service)
    {
        $peserta = $this->currentParticipant($request);

        // Pastikan jadwal terbaru sudah terbentuk dan status minggu dikunci
        // secara berjenjang sebelum halaman ditampilkan.
        $service->syncForParticipant($peserta);
        $service->refreshStatuses($peserta);

        $selectedMinggu = $request->query('minggu', 'all');

        $assignments = PenugasanPeserta::query()
            ->with(['tugas', 'templateLaporan'])
            ->where('peserta_id', $peserta->id_peserta)
            ->whereHas('tugas', function ($query) use ($selectedMinggu): void {
                if ($selectedMinggu !== 'all') {
                    $query->where('minggu_ke', (int) $selectedMinggu);
                }
            })
            ->get()
            ->filter(fn (PenugasanPeserta $assignment) => $assignment->tugas !== null)
            ->sortBy(fn (PenugasanPeserta $assignment) => sprintf(
                '%04d-%020d-%020d',
                (int) ($assignment->tugas?->minggu_ke ?? 999),
                (int) ($assignment->deadline?->timestamp ?? PHP_INT_MAX),
                (int) $assignment->id_penugasan
            ))
            ->values();

        $submissions = PengumpulanTugas::query()
            ->where('peserta_id', $peserta->id_peserta)
            ->whereIn('tugas_id', $assignments->pluck('tugas_id'))
            ->get()
            ->keyBy('tugas_id');

        $currentWeek = $service->currentSequentialWeek($peserta);

        $selectedAssignmentId = (int) $request->query('penugasan_id', 0);
        $detailAssignment = $assignments->firstWhere('id_penugasan', $selectedAssignmentId)
            ?? $assignments->first(fn (PenugasanPeserta $assignment) => $assignment->status === 'aktif')
            ?? $assignments->first();

        $availableWeeks = PenugasanPeserta::query()
            ->where('peserta_id', $peserta->id_peserta)
            ->whereHas('tugas', fn ($query) => $query->whereNotNull('minggu_ke'))
            ->join('tugas', 'tugas.id_tugas', '=', 'penugasan_peserta.tugas_id')
            ->select('tugas.minggu_ke')
            ->distinct()
            ->orderBy('tugas.minggu_ke')
            ->pluck('tugas.minggu_ke');

        return view('peserta-magang.penugasan', compact(
            'peserta',
            'assignments',
            'submissions',
            'detailAssignment',
            'selectedMinggu',
            'currentWeek',
            'availableWeeks'
        ));
    }

    public function store(
        Request $request,
        $id_tugas,
        PenugasanTemplateService $service
    ) {
        $peserta = $this->currentParticipant($request);

        $assignment = PenugasanPeserta::query()
            ->with('tugas')
            ->where('peserta_id', $peserta->id_peserta)
            ->where('tugas_id', (int) $id_tugas)
            ->firstOrFail();

        // Materi hanya untuk dibaca, bukan dikumpulkan.
        if ($assignment->tugas?->kategori_tugas === 'materi') {
            throw ValidationException::withMessages([
                'file_jawaban' => 'Item ini adalah materi pembelajaran dan tidak memerlukan pengumpulan file.',
            ]);
        }

        // Keamanan backend: minggu yang masih terkunci tetap ditolak walaupun
        // user mencoba mengirim form secara manual dari DevTools/URL.
        if (!$service->canSubmit($peserta, $assignment)) {
            $week = (int) ($assignment->tugas?->minggu_ke ?? 0);
            $currentWeek = $service->currentSequentialWeek($peserta);

            throw ValidationException::withMessages([
                'file_jawaban' => $week > 0 && $currentWeek && $week > $currentWeek
                    ? "Minggu {$week} masih terkunci. Selesaikan seluruh tugas Minggu {$currentWeek} terlebih dahulu."
                    : 'Tugas atau laporan ini belum dapat dikumpulkan saat ini.',
            ]);
        }

        $request->validate([
            'file_jawaban' => [
                'required',
                'file',
                'mimes:pdf,doc,docx,xls,xlsx,zip,rar',
                'max:25600',
            ],
        ]);

        $existing = PengumpulanTugas::query()
            ->where('tugas_id', $assignment->tugas_id)
            ->where('peserta_id', $peserta->id_peserta)
            ->first();

        if ($existing) {
            throw ValidationException::withMessages([
                'file_jawaban' => 'Tugas ini sudah pernah dikumpulkan.',
            ]);
        }

        $path = $request->file('file_jawaban')
            ->store('jawaban-tugas', 'public');

        $late = $assignment->deadline && now()->greaterThan($assignment->deadline);

        try {
            PengumpulanTugas::create([
                'tugas_id' => $assignment->tugas_id,
                'peserta_id' => $peserta->id_peserta,
                'file_jawaban' => $path,
                'dikumpulkan_pada' => now(),
                'status' => $late ? 'telat' : 'terkumpul',
            ]);
        } catch (\Throwable $exception) {
            Storage::disk('public')->delete($path);
            throw $exception;
        }

        $assignment->update(['status' => 'selesai']);

        // Setelah tugas terakhir di minggu ini selesai, method ini otomatis
        // membuka minggu berikutnya dan tetap mengunci minggu setelahnya.
        $service->refreshStatuses($peserta);
        $nextWeek = $service->currentSequentialWeek($peserta);
        $finishedWeek = (int) ($assignment->tugas?->minggu_ke ?? 0);

        $message = $late
            ? 'Tugas berhasil dikumpulkan, tetapi melewati deadline.'
            : 'Tugas berhasil dikumpulkan.';

        if ($finishedWeek > 0 && $nextWeek && $nextWeek > $finishedWeek) {
            $message .= " Seluruh tugas Minggu {$finishedWeek} selesai. Minggu {$nextWeek} sekarang terbuka.";
        } elseif ($finishedWeek > 0 && $nextWeek === null) {
            $message .= ' Seluruh tugas mingguan Anda sudah selesai.';
        }

        return redirect()
            ->route('peserta-magang.penugasan.index')
            ->with('success', $message);
    }

    private function currentParticipant(Request $request): PesertaMagang
    {
        return PesertaMagang::query()
            ->where('user_id', $request->user()->id_user)
            ->firstOrFail();
    }
}
