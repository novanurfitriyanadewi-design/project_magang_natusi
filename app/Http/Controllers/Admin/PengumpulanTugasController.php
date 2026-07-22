<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notifikasi;
use App\Models\PengumpulanTugas;
use App\Models\PenugasanPeserta;
use App\Models\PesertaMagang;
use App\Models\Tugas;
use App\Services\PenugasanTemplateService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PengumpulanTugasController extends Controller
{
    /**
     * Menampilkan dua kelompok data yang terpisah:
     * 1. Peserta yang sudah mengumpulkan, termasuk yang terlambat.
     * 2. Peserta yang belum mengumpulkan tugas yang sudah aktif/tersedia.
     */
    public function index(
        Request $request,
        PenugasanTemplateService $penugasanService
    ): View {
        $this->ensureParticipantAssignments($penugasanService);

        $jenjang = $request->string('jenjang', 'semua')->toString();
        $target = $this->targetFromFilter($jenjang);

        $submittedBase = PengumpulanTugas::query()
            ->with([
                'peserta.user',
                'peserta.permintaan',
                'tugas',
            ])
            // Kompatibel dengan status lama dan status penugasan versi baru.
            ->where(function (Builder $query): void {
                $query->whereNotNull('file_jawaban')
                    ->orWhereNotNull('dikumpulkan_pada')
                    ->orWhereIn('status', [
                        'terkumpul',
                        'dinilai',
                        'telat',
                        'Sudah Mengumpulkan',
                        'Terlambat',
                    ]);
            });

        $pendingBase = PenugasanPeserta::query()
            ->with([
                'peserta.user',
                'peserta.permintaan',
                'tugas',
            ])
            ->whereIn('status', ['aktif', 'terjadwal'])
            ->whereHas('tugas', fn (Builder $query) => $query->where('status', 'aktif'))
            ->where(function (Builder $query): void {
                $query->whereNull('tersedia_pada')
                    ->orWhere('tersedia_pada', '<=', now());
            })
            ->whereNotExists(function ($query): void {
                $query->selectRaw('1')
                    ->from('pengumpulan_tugas')
                    ->whereColumn(
                        'pengumpulan_tugas.tugas_id',
                        'penugasan_peserta.tugas_id'
                    )
                    ->whereColumn(
                        'pengumpulan_tugas.peserta_id',
                        'penugasan_peserta.peserta_id'
                    )
                    ->where(function ($submission): void {
                        $submission->whereNotNull('pengumpulan_tugas.file_jawaban')
                            ->orWhereNotNull('pengumpulan_tugas.dikumpulkan_pada')
                            ->orWhereIn('pengumpulan_tugas.status', [
                                'terkumpul',
                                'dinilai',
                                'telat',
                                'Sudah Mengumpulkan',
                                'Terlambat',
                            ]);
                    });
            });

        if ($target !== null) {
            $submittedBase->whereHas(
                'tugas',
                fn (Builder $query) => $query->where('target_peserta', $target)
            );

            $pendingBase->whereHas(
                'tugas',
                fn (Builder $query) => $query->where('target_peserta', $target)
            );
        }

        $lateStatuses = ['telat', 'Terlambat'];
        $lateCount = (clone $submittedBase)
            ->whereIn('status', $lateStatuses)
            ->count();
        $submittedTotal = (clone $submittedBase)->count();

        $stats = [
            'mengumpulkan' => max(0, $submittedTotal - $lateCount),
            'terlambat' => $lateCount,
            'tidak_mengumpulkan' => (clone $pendingBase)->count(),
        ];

        $submittedQuery = clone $submittedBase;
        $pendingQuery = clone $pendingBase;

        if ($request->filled('search')) {
            $search = trim($request->string('search')->toString());
            $this->applySearch($submittedQuery, $search);
            $this->applySearch($pendingQuery, $search);
        }

        if ($request->filled('tugas_id')) {
            $taskId = $request->integer('tugas_id');
            $submittedQuery->where('tugas_id', $taskId);
            $pendingQuery->where('tugas_id', $taskId);
        }

        $submitted = $submittedQuery
            ->orderByDesc('dikumpulkan_pada')
            ->paginate(10, ['*'], 'submitted_page')
            ->withQueryString();

        $pending = $pendingQuery
            ->orderByRaw(
                'CASE WHEN deadline IS NOT NULL AND deadline < ? THEN 0 ELSE 1 END',
                [now()]
            )
            ->orderBy('deadline')
            ->orderByDesc('id_penugasan')
            ->paginate(10, ['*'], 'pending_page')
            ->withQueryString();

        $daftarTugas = Tugas::query()
            ->where('status', 'aktif')
            ->orderBy('minggu_ke')
            ->orderBy('judul')
            ->get(['id_tugas', 'judul', 'minggu_ke']);

        return view('admin.pengumpulan_tugas', compact(
            'stats',
            'submitted',
            'pending',
            'daftarTugas',
            'jenjang'
        ));
    }

    /**
     * Membuat jadwal bagi peserta aktif yang belum mempunyai satu pun baris
     * penugasan. Ini membuat tabel "belum mengumpulkan" tetap terisi setelah
     * fitur penugasan per peserta pertama kali dipasang.
     */
    private function ensureParticipantAssignments(
        PenugasanTemplateService $penugasanService
    ): void {
        PesertaMagang::query()
            ->with(['user', 'permintaan'])
            ->where('status', 'aktif')
            ->whereNotNull('tgl_mulai')
            ->whereDoesntHave('penugasanPeserta')
            ->chunkById(50, function ($participants) use ($penugasanService): void {
                foreach ($participants as $participant) {
                    $penugasanService->syncForParticipant($participant);
                }
            }, 'id_peserta');
    }

    /** Menampilkan detail satu bukti pengumpulan. */
    public function show(PengumpulanTugas $pengumpulan): View
    {
        $pengumpulan->load([
            'peserta.user',
            'peserta.permintaan',
            'tugas',
        ]);

        $penugasan = PenugasanPeserta::query()
            ->where('tugas_id', $pengumpulan->tugas_id)
            ->where('peserta_id', $pengumpulan->peserta_id)
            ->first();

        $fileExists = filled($pengumpulan->file_jawaban)
            && Storage::disk('public')->exists($pengumpulan->file_jawaban);

        $file = null;

        if ($fileExists) {
            $path = $pengumpulan->file_jawaban;
            $file = [
                'name' => basename($path),
                'extension' => strtolower(pathinfo($path, PATHINFO_EXTENSION)),
                'size' => Storage::disk('public')->size($path),
                'mime' => Storage::disk('public')->mimeType($path),
            ];
        }

        return view('admin.pengumpulan_tugas_detail', compact(
            'pengumpulan',
            'penugasan',
            'fileExists',
            'file'
        ));
    }

    /** Membuka bukti pengumpulan melalui route admin. */
    public function file(PengumpulanTugas $pengumpulan)
    {
        abort_unless(
            filled($pengumpulan->file_jawaban)
                && Storage::disk('public')->exists($pengumpulan->file_jawaban),
            404,
            'Bukti pengumpulan tidak ditemukan.'
        );

        return Storage::disk('public')->response(
            $pengumpulan->file_jawaban,
            basename($pengumpulan->file_jawaban)
        );
    }

    /** Mengirim notifikasi peringatan kepada peserta. */
    public function remind(
        Request $request,
        PenugasanPeserta $penugasan
    ): RedirectResponse {
        $penugasan->load(['peserta.user', 'tugas']);

        $participant = $penugasan->peserta;
        $user = $participant?->user;
        $task = $penugasan->tugas;

        if (!$participant || !$user || !$task) {
            return back()->with(
                'error',
                'Pengingat gagal dikirim karena data peserta atau tugas tidak lengkap.'
            );
        }

        if ($penugasan->status === 'dilewati') {
            return back()->with(
                'error',
                'Tugas ini dilewati untuk peserta tersebut sehingga pengingat tidak dikirim.'
            );
        }

        $alreadySubmitted = PengumpulanTugas::query()
            ->where('tugas_id', $penugasan->tugas_id)
            ->where('peserta_id', $penugasan->peserta_id)
            ->where(function (Builder $query): void {
                $query->whereNotNull('file_jawaban')
                    ->orWhereNotNull('dikumpulkan_pada')
                    ->orWhereIn('status', [
                        'terkumpul',
                        'dinilai',
                        'telat',
                        'Sudah Mengumpulkan',
                        'Terlambat',
                    ]);
            })
            ->exists();

        if ($alreadySubmitted) {
            return back()->with(
                'error',
                'Peserta sudah mengumpulkan tugas ini. Muat ulang halaman untuk memperbarui data.'
            );
        }

        $recentReminderExists = Notifikasi::query()
            ->where('user_id', $user->id_user)
            ->where('kategori', 'penugasan')
            ->where('tipe', 'peringatan')
            ->where('referensi_id', $penugasan->id_penugasan)
            ->where('created_at', '>=', now()->subMinutes(5))
            ->exists();

        if ($recentReminderExists) {
            return back()->with(
                'error',
                'Pengingat untuk tugas ini baru saja dikirim. Silakan tunggu beberapa menit.'
            );
        }

        $deadline = $penugasan->deadline
            ? $penugasan->deadline->translatedFormat('d F Y, H:i') . ' WIB'
            : 'sesuai jadwal tugas';

        Notifikasi::create([
            'user_id' => $user->id_user,
            'judul' => 'Pengingat Pengumpulan Tugas',
            'pesan' => sprintf(
                'Segera kerjakan dan kumpulkan tugas Minggu %d: %s. Deadline: %s.',
                (int) ($task->minggu_ke ?: 1),
                $task->judul,
                $deadline
            ),
            'kategori' => 'penugasan',
            'tipe' => 'peringatan',
            'referensi_id' => $penugasan->id_penugasan,
            'dibaca' => false,
        ]);

        return back()->with(
            'success',
            'Peringatan berhasil dikirim kepada ' . $user->nama . '.'
        );
    }

    private function applySearch(Builder $query, string $search): void
    {
        $query->where(function (Builder $outer) use ($search): void {
            $outer->whereHas('peserta.user', function (Builder $user) use ($search): void {
                $user->where('nama', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            })
                ->orWhereHas('peserta.permintaan', function (Builder $application) use ($search): void {
                    $application->where('nama_pemohon', 'like', '%' . $search . '%')
                        ->orWhere('nama_sekolah', 'like', '%' . $search . '%')
                        ->orWhere('jurusan', 'like', '%' . $search . '%');
                })
                ->orWhereHas('tugas', function (Builder $task) use ($search): void {
                    $task->where('judul', 'like', '%' . $search . '%')
                        ->orWhere('kode_tugas', 'like', '%' . $search . '%');
                });
        });
    }

    private function targetFromFilter(string $jenjang): ?string
    {
        return match ($jenjang) {
            'smk-tkj' => 'smk_tkj',
            'smk-rpl' => 'smk_rpl',
            'universitas' => 'universitas',
            default => null,
        };
    }
}
