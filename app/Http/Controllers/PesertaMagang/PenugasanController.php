<?php

namespace App\Http\Controllers\PesertaMagang;

use App\Http\Controllers\Controller;
use App\Models\PengumpulanTugas;
use App\Models\PenugasanPeserta;
use App\Models\PesertaMagang;
use App\Services\AdminMagangNotificationService;
use App\Services\PenugasanTemplateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class PenugasanController extends Controller
{
    public function index(Request $request, PenugasanTemplateService $service)
    {
        $peserta = $this->currentParticipant($request);

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

        if ($assignment->tugas?->kategori_tugas === 'materi') {
            throw ValidationException::withMessages([
                'file_jawaban' => 'Item ini adalah materi pembelajaran dan tidak memerlukan pengumpulan file.',
            ]);
        }

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

        // Pengumpulan ulang hanya boleh jika admin memang meminta revisi.
        if ($existing && $existing->status_review !== 'perlu_revisi') {
            throw ValidationException::withMessages([
                'file_jawaban' => $existing->status_review === 'menunggu_review'
                    ? 'File sudah dikirim dan sedang menunggu koreksi Admin.'
                    : 'Tugas ini sudah disetujui Admin dan tidak perlu dikumpulkan ulang.',
            ]);
        }

        $path = $request->file('file_jawaban')->store('jawaban-tugas', 'public');
        $late = $assignment->deadline && now()->greaterThan($assignment->deadline);

        try {
            if ($existing) {
                $oldPath = $existing->file_jawaban;

                $existing->update([
                    'file_jawaban' => $path,
                    'dikumpulkan_pada' => now(),
                    'status_review' => 'menunggu_review',
                    'reviewed_at' => null,
                    'reviewed_by' => null,
                    'revisi_ke' => ((int) $existing->revisi_ke) + 1,
                ]);

                if ($oldPath && $oldPath !== $path) {
                    Storage::disk('public')->delete($oldPath);
                }
            } else {
                PengumpulanTugas::create([
                    'tugas_id' => $assignment->tugas_id,
                    'peserta_id' => $peserta->id_peserta,
                    'file_jawaban' => $path,
                    'dikumpulkan_pada' => now(),
                    'status' => $late ? 'telat' : 'terkumpul',
                    'status_review' => 'menunggu_review',
                    'revisi_ke' => 0,
                ]);
            }
        } catch (\Throwable $exception) {
            Storage::disk('public')->delete($path);
            throw $exception;
        }

        // Belum dianggap selesai sebelum admin menyetujui.
        $assignment->update(['status' => 'aktif']);
        $service->refreshStatuses($peserta);

        $isRevision = (bool) $existing;
        app(AdminMagangNotificationService::class)->notify(
            $isRevision ? 'File Revisi Tugas Dikirim' : 'Pengumpulan Tugas Baru',
            sprintf(
                '%s mengirim %s untuk tugas "%s". Silakan buka Data Pengumpulan Tugas untuk melakukan koreksi.',
                $peserta->user?->nama ?? 'Peserta magang',
                $isRevision ? 'file revisi' : 'file tugas',
                $assignment->tugas?->judul ?? 'Penugasan'
            ),
            'penugasan',
            $assignment->id_penugasan,
            'info'
        );

        return redirect()
            ->route('peserta-magang.penugasan.index', ['penugasan_id' => $assignment->id_penugasan])
            ->with(
                'success',
                $isRevision
                    ? 'File revisi berhasil dikirim. Tunggu koreksi Admin sebelum minggu berikutnya terbuka.'
                    : 'Tugas berhasil dikumpulkan dan sedang menunggu koreksi Admin.'
            );
    }

    /** Membuka file pengumpulan milik peserta sendiri. */
    public function file(Request $request, PengumpulanTugas $pengumpulan)
    {
        $peserta = $this->currentParticipant($request);

        abort_unless((int) $pengumpulan->peserta_id === (int) $peserta->id_peserta, 403);
        abort_unless(
            filled($pengumpulan->file_jawaban)
                && Storage::disk('public')->exists($pengumpulan->file_jawaban),
            404,
            'File pengumpulan tidak ditemukan.'
        );

        return Storage::disk('public')->response(
            $pengumpulan->file_jawaban,
            basename($pengumpulan->file_jawaban)
        );
    }

    private function currentParticipant(Request $request): PesertaMagang
    {
        return PesertaMagang::query()
            ->with('user')
            ->where('user_id', $request->user()->id_user)
            ->firstOrFail();
    }
}
