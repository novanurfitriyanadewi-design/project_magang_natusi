<?php

namespace App\Services;

use App\Models\PengumpulanTugas;
use App\Models\Notifikasi;
use App\Models\PenugasanPeserta;
use App\Models\PesertaMagang;
use App\Models\TemplateLaporan;
use App\Models\Tugas;
use App\Support\JurusanKategori;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class PenugasanTemplateService
{
    private const DAYS = [
        1 => 'senin',
        2 => 'selasa',
        3 => 'rabu',
        4 => 'kamis',
        5 => 'jumat',
        6 => 'sabtu',
        7 => 'minggu',
    ];

    /**
     * Sheet resmi pada template tugas mingguan, dibentuk dinamis dari
     * daftar jurusan aktif (dikelola lewat halaman Kelola Jurusan) supaya
     * admin bisa menambah jurusan baru tanpa perlu mengubah kode ini.
     */
    private function templateSheets(): array
    {
        $sheets = [];

        foreach (JurusanKategori::semua() as $jurusan) {
            $sheets[$jurusan->nama_sheet] = [
                'target' => $jurusan->target_peserta,
                'instansi' => $jurusan->tingkat === 'smk' ? 'sekolah' : 'universitas',
                'prefix' => $jurusan->kode,
                'label' => $jurusan->nama_sheet,
                'jurusan_id' => $jurusan->id_jurusan,
            ];
        }

        return $sheets;
    }

    /**
     * Membaca template resmi yang berisi tiga sheet sekaligus, memperbarui
     * definisi tugas, lalu membuat jadwal individual dari tanggal mulai peserta.
     */
    public function import(UploadedFile $file, int $userId): array
    {
        $groups = $this->readTemplate($file);
        $batch = (string) Str::uuid();

        return DB::transaction(function () use ($groups, $batch, $userId): array {
            // Template lama yang belum memiliki segmentasi sheet dinonaktifkan
            // supaya peserta baru tidak menerima tugas ganda setelah memakai
            // template mingguan yang benar.
            Tugas::query()
                ->whereNotNull('template_batch')
                ->where('jenis_tugas', 'mingguan')
                ->where(function ($query): void {
                    $query->whereNull('target_peserta')
                        ->orWhere('target_peserta', 'semua');
                })
                ->update(['status' => 'nonaktif']);

            $importedTasks = collect();
            $taskCounts = [];

            foreach ($groups as $target => $group) {
                $codes = [];
                $groupTasks = collect();

                foreach ($group['rows'] as $rowNumber => $row) {
                    $data = $this->validateAndNormalizeTemplateRow(
                        $row,
                        $rowNumber,
                        $group
                    );

                    if (in_array($data['kode_tugas'], $codes, true)) {
                        throw ValidationException::withMessages([
                            'file_template' => "Sheet {$group['label']} baris {$rowNumber}: kode tugas ganda {$data['kode_tugas']}.",
                        ]);
                    }

                    $codes[] = $data['kode_tugas'];

                    $task = Tugas::query()->updateOrCreate(
                        [
                            'kode_tugas' => $data['kode_tugas'],
                            'jenis_tugas' => 'mingguan',
                            'instansi' => $group['instansi'],
                        ],
                        [
                            'user_id' => $userId,
                            'judul' => $data['judul'],
                            'materi' => $data['materi'],
                            'kategori_tugas' => $data['kategori_tugas'],
                            'minggu_ke' => $data['minggu_ke'],
                            'target_peserta' => $target,
                            'hari_tampil' => $data['hari_tampil'],
                            'hari_deadline' => $data['hari_deadline'],
                            'jam_deadline' => $data['jam_deadline'],
                            // Nilai relatif tetap disimpan agar kompatibel dengan
                            // tampilan dan API lama.
                            'rilis_hari_ke' => $data['rilis_hari_ke'],
                            'deadline_hari_ke' => $data['deadline_hari_ke'],
                            'hari_mulai' => 'semua',
                            'keterangan' => $data['keterangan'],
                            'template_batch' => $batch,
                            'status' => 'aktif',
                            'pengumpulan' => null,
                        ]
                    );

                    $groupTasks->push($task);
                    $importedTasks->push($task);
                }

                Tugas::query()
                    ->where('jenis_tugas', 'mingguan')
                    ->where('target_peserta', $target)
                    ->whereNotNull('template_batch')
                    ->whereNotNull('kode_tugas')
                    ->when(
                        $codes !== [],
                        fn ($query) => $query->whereNotIn('kode_tugas', $codes)
                    )
                    ->update(['status' => 'nonaktif']);

                $taskCounts[$target] = $groupTasks->count();
            }

            // Peserta aktif tetap mendapat penugasan meskipun tanggal mulai
            // belum dilengkapi. Peserta hasil approval memang dibuat dengan
            // tgl_mulai = null, jadi menunggu kolom ini membuat tugas hasil
            // import tidak pernah muncul di portal peserta baru.
            $participants = PesertaMagang::query()
                ->with(['user', 'permintaan'])
                ->where('status', 'aktif')
                ->get();

            $assignmentCount = 0;
            $unmatchedParticipants = 0;

            foreach ($participants as $participant) {
                $target = $this->participantTarget($participant);
                if ($target === null) {
                    $unmatchedParticipants++;
                    continue;
                }

                $tasks = $importedTasks
                    ->where('target_peserta', $target)
                    ->values();

                $assignmentCount += $this->syncForParticipant($participant, $tasks);
            }

            return [
                'tasks' => $importedTasks->count(),
                'tasks_by_target' => $taskCounts,
                'assignments' => $assignmentCount,
                'unmatched_participants' => $unmatchedParticipants,
                'batch' => $batch,
            ];
        });
    }

    /**
     * Membuat atau memperbarui jadwal untuk satu peserta. Method ini dipanggil
     * otomatis saat tanggal mulai, pendidikan, atau data jurusan peserta berubah.
     */
    public function syncForParticipant(
        PesertaMagang $participant,
        ?Collection $tasks = null
    ): int {
        if ($participant->status !== 'aktif') {
            return 0;
        }

        $participant->loadMissing(['user', 'permintaan']);
        $target = $this->participantTarget($participant);
        $institution = $this->participantInstitution($participant);

        $tasks ??= Tugas::query()
            ->where('status', 'aktif')
            ->where(function ($query) use ($target, $institution): void {
                // Jika jurusan peserta dikenali, ambil tugas target jurusannya.
                if ($target !== null) {
                    $query->where('target_peserta', $target)
                        ->orWhere(function ($fallback) use ($institution): void {
                            $fallback
                                ->where(function ($scope) use ($institution): void {
                                    $scope->where('instansi', $institution)
                                        ->orWhere('instansi', 'semua');
                                })
                                ->where(function ($scope): void {
                                    $scope->whereNull('target_peserta')
                                        ->orWhere('target_peserta', 'semua');
                                });
                        });
                    return;
                }

                // Data lama yang jurusannya belum terpetakan tetap boleh
                // menerima tugas umum untuk sekolah/universitas.
                $query->where(function ($scope) use ($institution): void {
                    $scope->where('instansi', $institution)
                        ->orWhere('instansi', 'semua');
                })->where(function ($scope): void {
                    $scope->whereNull('target_peserta')
                        ->orWhere('target_peserta', 'semua');
                });
            })
            ->orderBy('minggu_ke')
            ->orderBy('rilis_hari_ke')
            ->get();

        // Peserta baru dari approval belum selalu punya tgl_mulai. Supaya
        // Minggu 1 langsung tersedia, pakai tanggal pembuatan data peserta
        // sebagai acuan sementara. Saat tgl_mulai diisi, sync berikutnya akan
        // menggunakan tanggal mulai resmi.
        $startSource = $participant->tgl_mulai
            ?? $participant->created_at
            ?? now();
        $start = Carbon::parse($startSource)->startOfDay();
        $count = 0;

        foreach ($tasks as $task) {
            if ($task->status !== 'aktif'
                || !$this->taskMatchesParticipant($task, $target, $institution)) {
                continue;
            }

            // Perbaiki data hasil import lama yang dulu seluruh baris non-laporan
            // terlanjur disimpan sebagai kategori "tugas". Hanya task dari
            // template Excel yang dinormalisasi agar task manual admin tidak berubah.
            if (filled($task->template_batch)) {
                $normalizedCategory = $this->templateCategoryFromMaterial((string) $task->materi);
                if ($task->kategori_tugas !== $normalizedCategory) {
                    $task->kategori_tugas = $normalizedCategory;
                    $task->save();
                }
            }

            [$availableAt, $deadline, $isSkipped, $scheduleNote] =
                $this->scheduleForTask($task, $start);

            $submissionExists = PengumpulanTugas::query()
                ->where('tugas_id', $task->id_tugas)
                ->where('peserta_id', $participant->id_peserta)
                ->exists();

            $status = match (true) {
                $submissionExists => 'selesai',
                $isSkipped => 'dilewati',
                $availableAt && now()->greaterThanOrEqualTo($availableAt) => 'aktif',
                default => 'terjadwal',
            };

            $reportTemplate = $task->kategori_tugas === 'laporan'
                ? $this->activeReportTemplate($institution)
                : null;

            $assignment = PenugasanPeserta::query()->firstOrNew([
                'tugas_id' => $task->id_tugas,
                'peserta_id' => $participant->id_peserta,
            ]);
            $isNewAssignment = ! $assignment->exists;

            // Tugas yang sudah dikumpulkan tidak dihitung ulang agar riwayat
            // deadline saat pengerjaan tetap konsisten.
            if (!$submissionExists) {
                $assignment->fill([
                    'template_laporan_id' => $reportTemplate?->id_template_laporan,
                    'tersedia_pada' => $availableAt,
                    'deadline' => $deadline,
                    'status' => $status,
                    'keterangan' => $isSkipped
                        ? $scheduleNote
                        : trim(implode("\n", array_filter([
                            $task->keterangan,
                            $scheduleNote,
                        ]))),
                    'ketentuan_laporan' => $reportTemplate?->ketentuan,
                ]);
            } else {
                $assignment->status = 'selesai';
            }

            $assignment->save();

            if ($isNewAssignment && $status !== 'dilewati') {
                $participant->loadMissing('user');
                if ($participant->user) {
                    $deadlineText = $deadline ? $deadline->translatedFormat('d F Y H:i') : 'mengikuti jadwal di portal';
                    Notifikasi::query()->create([
                        'user_id' => $participant->user->id_user,
                        'judul' => 'Penugasan Magang Baru',
                        'pesan' => 'Tugas "'.$task->judul.'" telah dijadwalkan untuk Anda. Deadline: '.$deadlineText.'.',
                        'kategori' => 'penugasan',
                        'tipe' => 'info',
                        'referensi_id' => $task->id_tugas,
                        'dibaca' => false,
                    ]);
                }
            }

            $count++;
        }

        // Setelah semua assignment tersinkron, tegakkan kembali urutan minggu.
        // Ini penting saat sync dipanggil dari observer/import sebelum peserta
        // membuka halaman Penugasan.
        $this->refreshStatuses($participant);

        return $count;
    }

    public function refreshReportTemplate(TemplateLaporan $template): int
    {
        $assignments = PenugasanPeserta::query()
            ->with(['peserta.user', 'peserta.permintaan', 'tugas'])
            ->whereIn('status', ['terjadwal', 'aktif'])
            ->whereHas('tugas', fn ($query) => $query->where('kategori_tugas', 'laporan'))
            ->get();

        $updated = 0;
        foreach ($assignments as $assignment) {
            $institution = $this->participantInstitution($assignment->peserta);
            $activeTemplate = $this->activeReportTemplate($institution);

            if (!$activeTemplate || $activeTemplate->isNot($template)) {
                continue;
            }

            $assignment->update([
                'template_laporan_id' => $template->id_template_laporan,
                'ketentuan_laporan' => $template->ketentuan,
            ]);
            $updated++;
        }

        return $updated;
    }

    /**
     * Menyegarkan status tugas peserta dengan aturan progres berjenjang.
     *
     * Untuk tugas mingguan, peserta hanya boleh mengerjakan satu minggu pada
     * satu waktu. Minggu berikutnya baru aktif setelah SEMUA tugas pada minggu
     * sebelumnya sudah memiliki pengumpulan. Aturan ini berlaku di backend,
     * sehingga URL/form minggu berikutnya juga tidak dapat dipaksa secara manual.
     */
    public function refreshStatuses(PesertaMagang $participant): void
    {
        $assignments = PenugasanPeserta::query()
            ->with('tugas')
            ->where('peserta_id', $participant->id_peserta)
            ->get();

        if ($assignments->isEmpty()) {
            return;
        }

        // Hanya pengumpulan yang SUDAH DISETUJUI admin yang dianggap selesai.
        // Upload pertama / file revisi masih menunggu koreksi sehingga minggu
        // berikutnya belum boleh terbuka.
        $approvedTaskIds = PengumpulanTugas::query()
            ->where('peserta_id', $participant->id_peserta)
            ->where(function ($query): void {
                $query->where('status_review', 'disetujui')
                    // Kompatibilitas bila ada record lama sebelum migration.
                    ->orWhereNull('status_review');
            })
            ->pluck('tugas_id')
            ->map(fn ($id) => (int) $id)
            ->all();

        foreach ($assignments as $assignment) {
            if (!$assignment->tugas
                || !in_array($assignment->tugas->kategori_tugas, ['tugas', 'laporan'], true)) {
                continue;
            }

            $approved = in_array((int) $assignment->tugas_id, $approvedTaskIds, true);

            if ($approved && $assignment->status !== 'selesai') {
                $assignment->status = 'selesai';
                $assignment->save();
                continue;
            }

            // Jika admin meminta revisi atas tugas yang sebelumnya sempat
            // berstatus selesai, kembalikan statusnya agar progres terkunci lagi.
            if (!$approved && $assignment->status === 'selesai') {
                $assignment->status = 'terjadwal';
                $assignment->save();
            }
        }

        // Hanya item yang memang WAJIB DIKUMPULKAN yang menentukan progres
        // minggu. Materi tidak boleh membuat Minggu berikutnya terkunci selamanya.
        $weeklyCollectables = $assignments
            ->filter(fn (PenugasanPeserta $assignment) =>
                $assignment->tugas
                && $assignment->tugas->jenis_tugas === 'mingguan'
                && in_array($assignment->tugas->kategori_tugas, ['tugas', 'laporan'], true)
                && (int) $assignment->tugas->minggu_ke >= 1
                && $assignment->status !== 'dilewati'
            )
            ->groupBy(fn (PenugasanPeserta $assignment) => (int) $assignment->tugas->minggu_ke)
            ->sortKeys();

        $currentWeek = null;

        foreach ($weeklyCollectables as $week => $weekAssignments) {
            $weekCompleted = $weekAssignments->every(
                fn (PenugasanPeserta $assignment) =>
                    $assignment->status === 'selesai'
                    || in_array((int) $assignment->tugas_id, $approvedTaskIds, true)
            );

            if ($weekCompleted) {
                continue;
            }

            $currentWeek = (int) $week;
            break;
        }

        // Terapkan status ke SEMUA item mingguan, termasuk materi.
        // Materi pada minggu yang sudah/current boleh dibaca, tetapi tidak pernah
        // menjadi item yang harus dikumpulkan.
        $allWeekly = $assignments
            ->filter(fn (PenugasanPeserta $assignment) =>
                $assignment->tugas
                && $assignment->tugas->jenis_tugas === 'mingguan'
                && (int) $assignment->tugas->minggu_ke >= 1
                && $assignment->status !== 'dilewati'
            );

        foreach ($allWeekly as $assignment) {
            $task = $assignment->tugas;
            $week = (int) $task->minggu_ke;
            $isMaterial = $task->kategori_tugas === 'materi';

            if (!$isMaterial && $assignment->status === 'selesai') {
                continue;
            }

            if ($isMaterial) {
                // Materi hanya untuk dibaca. Materi minggu sekarang dan minggu
                // yang sudah lewat tetap terbuka; materi minggu depan tetap terkunci.
                $assignment->status = $currentWeek === null || $week <= $currentWeek
                    ? 'aktif'
                    : 'terjadwal';
                $assignment->save();
                continue;
            }

            if ($currentWeek === null) {
                // Semua tugas/laporan yang wajib dikumpulkan telah selesai.
                if ($assignment->status !== 'selesai') {
                    $assignment->status = 'terjadwal';
                    $assignment->save();
                }
                continue;
            }

            $assignment->status = $week === $currentWeek ? 'aktif' : 'terjadwal';
            $assignment->save();
        }

        // Tugas non-mingguan tetap mengikuti tanggal tersedia seperti sebelumnya.
        foreach ($assignments as $assignment) {
            if (!$assignment->tugas
                || $assignment->tugas->jenis_tugas === 'mingguan'
                || in_array($assignment->status, ['selesai', 'dilewati'], true)) {
                continue;
            }

            $assignment->status = (!$assignment->tersedia_pada
                || now()->greaterThanOrEqualTo($assignment->tersedia_pada))
                ? 'aktif'
                : 'terjadwal';
            $assignment->save();
        }
    }

    /**
     * Memastikan tugas tertentu benar-benar boleh dikerjakan peserta.
     * Method ini dipakai oleh controller saat submit agar penguncian tidak hanya
     * bergantung pada tampilan.
     */
    public function canSubmit(PesertaMagang $participant, PenugasanPeserta $assignment): bool
    {
        $this->refreshStatuses($participant);
        $assignment->refresh();
        $assignment->loadMissing('tugas');

        // Materi adalah bacaan, bukan pengumpulan. Yang boleh dikirim hanya
        // Tugas Materi dan Laporan Mingguan.
        if (!$assignment->tugas
            || !in_array($assignment->tugas->kategori_tugas, ['tugas', 'laporan'], true)) {
            return false;
        }

        return $assignment->status === 'aktif';
    }

    /**
     * Minggu mingguan paling awal yang belum selesai. Null berarti seluruh tugas
     * mingguan peserta sudah selesai.
     */
    public function currentSequentialWeek(PesertaMagang $participant): ?int
    {
        $this->refreshStatuses($participant);

        return PenugasanPeserta::query()
            ->where('penugasan_peserta.peserta_id', $participant->id_peserta)
            ->where('penugasan_peserta.status', 'aktif')
            ->whereHas('tugas', fn ($query) => $query
                ->where('jenis_tugas', 'mingguan')
                ->whereIn('kategori_tugas', ['tugas', 'laporan'])
                ->whereNotNull('minggu_ke'))
            ->join('tugas', 'tugas.id_tugas', '=', 'penugasan_peserta.tugas_id')
            ->min('tugas.minggu_ke');
    }

    /**
     * Menentukan jenis item dari label kolom "Materi / Laporan" template.
     */
    private function templateCategoryFromMaterial(string $material): string
    {
        $label = Str::lower(trim($material));

        if (Str::contains($label, 'laporan')) {
            return 'laporan';
        }

        if (Str::startsWith($label, 'tugas') || Str::contains($label, 'tugas materi')) {
            return 'tugas';
        }

        return 'materi';
    }

    public function participantInstitution(PesertaMagang $participant): string
    {
        $value = Str::lower(trim((string) $participant->tingkat_pendidikan));

        return Str::contains($value, [
            'universitas',
            'mahasiswa',
            'kuliah',
            'politeknik',
            'institut',
            'akademi',
            'kampus',
            'd1',
            'd2',
            'd3',
            'd4',
            's1',
            's2',
        ]) ? 'universitas' : 'sekolah';
    }

    /**
     * Menentukan sheet tugas peserta dari jurusan resminya (jurusan_id).
     * Untuk data lama yang belum punya jurusan_id, dicoba dicocokkan dari
     * teks jurusan/kelas bebas. Peserta yang jurusannya tidak dikenali
     * dalam daftar Kelola Jurusan tidak mendapat target otomatis.
     */
    public function participantTarget(PesertaMagang $participant): ?string
    {
        return $this->resolveJurusan($participant)?->target_peserta;
    }

    private function resolveJurusan(PesertaMagang $participant): ?\App\Models\Jurusan
    {
        if ($participant->jurusan_id) {
            $jurusan = JurusanKategori::cariById($participant->jurusan_id);
            if ($jurusan) {
                return $jurusan;
            }
        }

        $teks = implode(' ', array_filter([
            $participant->permintaan?->jurusan,
            $participant->user?->major,
            $participant->kelas,
        ]));

        return JurusanKategori::cariByTeks($teks);
    }

    private function activeReportTemplate(string $institution): ?TemplateLaporan
    {
        return TemplateLaporan::query()
            ->where('is_active', true)
            ->whereIn('instansi', [$institution, 'semua'])
            ->orderByRaw('CASE WHEN instansi = ? THEN 0 ELSE 1 END', [$institution])
            ->latest('id_template_laporan')
            ->first();
    }

    private function taskMatchesParticipant(
        Tugas $task,
        ?string $target,
        string $institution
    ): bool {
        if (filled($task->target_peserta)
            && $task->target_peserta !== 'semua') {
            return $target !== null && $task->target_peserta === $target;
        }

        return in_array(
            Str::lower((string) $task->instansi),
            [$institution, 'semua'],
            true
        );
    }

    /**
     * Menghitung tanggal tampil dan deadline dari Minggu Ke + nama hari.
     * Pada minggu pertama, tugas yang sudah lewat deadline dilewati; tugas yang
     * masih berada dalam masa aktif langsung tampil pada tanggal mulai peserta.
     */
    private function scheduleForTask(Tugas $task, Carbon $start): array
    {
        if (filled($task->hari_tampil) && filled($task->hari_deadline)) {
            $week = max(1, (int) $task->minggu_ke);
            $releaseWeekday = $this->dayNumber((string) $task->hari_tampil);
            $deadlineWeekday = $this->dayNumber((string) $task->hari_deadline);

            $weekAnchor = $start->copy()->startOfWeek(Carbon::MONDAY);
            // Peserta yang mulai akhir pekan memulai minggu pertama pada Senin
            // berikutnya agar seluruh rangkaian minggu pertama tetap tersedia.
            if ($start->isoWeekday() >= 6) {
                $weekAnchor->addWeek();
            }

            $release = $weekAnchor->copy()
                ->addWeeks($week - 1)
                ->addDays($releaseWeekday - 1)
                ->startOfDay();

            $deadline = $weekAnchor->copy()
                ->addWeeks($week - 1)
                ->addDays($deadlineWeekday - 1)
                ->setTimeFromTimeString($this->normalizeTime((string) ($task->jam_deadline ?: '17:00:00')));

            if ($deadline->lessThan($release)) {
                $deadline->addWeek();
            }

            if ($week === 1 && $deadline->lessThan($start)) {
                // Peserta baru tidak boleh kehilangan Minggu 1 hanya karena hari
                // masuknya berada setelah deadline template. Tugas tetap dimulai
                // pada tanggal masuk dan deadline digeser ke hari deadline
                // terdekat setelah tanggal mulai.
                $release = $start->copy();
                $deadline = $start->copy()
                    ->next($deadlineWeekday)
                    ->setTimeFromTimeString($this->normalizeTime((string) ($task->jam_deadline ?: '17:00:00')));
            }

            $availableAt = $release->lessThan($start)
                ? $start->copy()
                : $release;

            return [
                $availableAt,
                $deadline,
                false,
                sprintf(
                    'Jadwal template: tampil %s, deadline %s pukul %s.',
                    ucfirst((string) $task->hari_tampil),
                    ucfirst((string) $task->hari_deadline),
                    $deadline->format('H:i')
                ),
            ];
        }

        // Kompatibilitas untuk tugas lama yang masih memakai offset hari.
        $startDay = self::DAYS[$start->isoWeekday()];
        $allowedDays = $this->normalizeAllowedDays((string) $task->hari_mulai);
        $isSkipped = !in_array('semua', $allowedDays, true)
            && !in_array($startDay, $allowedDays, true);

        $availableAt = $isSkipped
            ? null
            : $start->copy()
                ->addDays(max(0, ((int) $task->rilis_hari_ke) - 1))
                ->startOfDay();

        $deadline = $isSkipped || !$task->deadline_hari_ke
            ? null
            : $start->copy()
                ->addDays(max(0, ((int) $task->deadline_hari_ke) - 1))
                ->endOfDay();

        return [
            $availableAt,
            $deadline,
            $isSkipped,
            $isSkipped
                ? 'Dilewati berdasarkan hari mulai magang peserta.'
                : 'Jadwal dihitung dari offset hari pada template lama.',
        ];
    }

    private function readTemplate(UploadedFile $file): array
    {
        try {
            $spreadsheet = IOFactory::load($file->getRealPath());
        } catch (\Throwable $exception) {
            throw ValidationException::withMessages([
                'file_template' => 'File Excel tidak dapat dibaca. Pastikan file tidak rusak dan berformat .xlsx.',
            ]);
        }

        $groups = [];
        $missingSheets = [];

        foreach ($this->templateSheets() as $sheetName => $profile) {
            $worksheet = $spreadsheet->getSheetByName($sheetName);
            if (!$worksheet) {
                $missingSheets[] = $sheetName;
                continue;
            }

            $rawRows = $worksheet->toArray(null, true, true, false);
            $headerIndex = null;
            $headers = [];

            foreach ($rawRows as $index => $row) {
                $candidate = array_map(
                    fn ($value) => $this->normalizeHeader((string) $value),
                    $row
                );

                $required = [
                    'minggu_ke',
                    'materi_laporan',
                    'tugas',
                    'hari_tampil',
                    'hari_deadline',
                    'jam_deadline',
                ];

                if (count(array_intersect($required, $candidate)) === count($required)) {
                    $headerIndex = $index;
                    $headers = $candidate;
                    break;
                }
            }

            if ($headerIndex === null) {
                throw ValidationException::withMessages([
                    'file_template' => "Header penugasan pada sheet {$sheetName} tidak ditemukan. Jangan mengubah nama kolom template resmi.",
                ]);
            }

            $result = [];
            $currentWeek = null;
            $sequenceByWeek = [];

            foreach (array_slice($rawRows, $headerIndex + 1) as $offset => $row) {
                $mapped = [];
                foreach ($headers as $columnIndex => $header) {
                    if ($header === '') {
                        continue;
                    }
                    $mapped[$header] = $row[$columnIndex] ?? null;
                }

                if (collect($mapped)->filter(fn ($value) => filled($value))->isEmpty()) {
                    continue;
                }

                if (filled($mapped['minggu_ke'] ?? null)) {
                    $currentWeek = (int) $mapped['minggu_ke'];
                }

                if (!$currentWeek) {
                    $excelRow = $headerIndex + $offset + 2;
                    throw ValidationException::withMessages([
                        'file_template' => "Sheet {$sheetName} baris {$excelRow}: kolom Minggu Ke belum memiliki nilai acuan.",
                    ]);
                }

                $mapped['minggu_ke'] = $currentWeek;
                $sequenceByWeek[$currentWeek] = ($sequenceByWeek[$currentWeek] ?? 0) + 1;
                $mapped['_sequence'] = $sequenceByWeek[$currentWeek];

                $excelRow = $headerIndex + $offset + 2;
                $result[$excelRow] = $mapped;
            }

            if ($result === []) {
                throw ValidationException::withMessages([
                    'file_template' => "Sheet {$sheetName} belum berisi data penugasan.",
                ]);
            }

            $groups[$profile['target']] = array_merge($profile, [
                'rows' => $result,
            ]);
        }

        if ($missingSheets !== []) {
            throw ValidationException::withMessages([
                'file_template' => 'Sheet wajib tidak ditemukan: '.implode(', ', $missingSheets).'. Gunakan file template resmi tanpa mengganti nama sheet.',
            ]);
        }

        return $groups;
    }

    private function validateAndNormalizeTemplateRow(
        array $row,
        int $rowNumber,
        array $group
    ): array {
        $week = (int) ($row['minggu_ke'] ?? 0);
        $sequence = (int) ($row['_sequence'] ?? 0);
        $material = trim((string) ($row['materi_laporan'] ?? ''));
        $title = trim((string) ($row['tugas'] ?? ''));
        $releaseDay = $this->normalizeDay((string) ($row['hari_tampil'] ?? ''));
        $deadlineDay = $this->normalizeDay((string) ($row['hari_deadline'] ?? ''));
        $deadlineTime = $this->excelTimeToString($row['jam_deadline'] ?? null);

        $errors = [];
        if ($week < 1) {
            $errors[] = 'Minggu Ke minimal 1';
        }
        if ($sequence < 1) {
            $errors[] = 'urutan tugas tidak valid';
        }
        if ($material === '') {
            $errors[] = 'Materi & Laporan wajib diisi';
        }
        if ($title === '') {
            $errors[] = 'Tugas wajib diisi';
        }
        if ($releaseDay === null) {
            $errors[] = 'Hari Tampil tidak dikenali';
        }
        if ($deadlineDay === null) {
            $errors[] = 'Hari Deadline tidak dikenali';
        }
        if ($deadlineTime === null) {
            $errors[] = 'Jam Deadline harus berupa waktu yang valid';
        }

        if ($errors !== []) {
            throw ValidationException::withMessages([
                'file_template' => "Sheet {$group['label']} baris {$rowNumber}: ".implode('; ', $errors).'.',
            ]);
        }

        $releaseNumber = $this->dayNumber($releaseDay);
        $deadlineNumber = $this->dayNumber($deadlineDay);
        $relativeRelease = (($week - 1) * 7) + $releaseNumber;
        $relativeDeadline = (($week - 1) * 7) + $deadlineNumber;
        if ($relativeDeadline < $relativeRelease) {
            $relativeDeadline += 7;
        }

        // Bedakan baris template dengan tegas:
        // - "Materi 1"        => materi, hanya dibaca (tidak dikumpulkan)
        // - "Tugas Materi 1"  => tugas, wajib dikumpulkan
        // - "Laporan Mingguan"=> laporan, wajib dikumpulkan
        $category = $this->templateCategoryFromMaterial($material);

        return [
            'kode_tugas' => sprintf(
                '%s-M%02d-%02d',
                $group['prefix'],
                $week,
                $sequence
            ),
            'judul' => $title,
            'materi' => $material,
            'kategori_tugas' => $category,
            'minggu_ke' => $week,
            'hari_tampil' => $releaseDay,
            'hari_deadline' => $deadlineDay,
            'jam_deadline' => $deadlineTime,
            'rilis_hari_ke' => $relativeRelease,
            'deadline_hari_ke' => $relativeDeadline,
            'keterangan' => sprintf(
                '%s · Minggu %d · %s sampai %s pukul %s',
                $group['label'],
                $week,
                ucfirst($releaseDay),
                ucfirst($deadlineDay),
                substr($deadlineTime, 0, 5)
            ),
        ];
    }

    private function normalizeAllowedDays(string $days): array
    {
        $normalized = Str::of($days)
            ->lower()
            ->replace(['/', ';', '|'], ',')
            ->explode(',')
            ->map(fn ($day) => $this->normalizeDay((string) $day) ?? trim((string) $day))
            ->filter()
            ->values()
            ->all();

        return $normalized === [] ? ['semua'] : $normalized;
    }

    private function normalizeDay(string $day): ?string
    {
        $value = Str::of($day)
            ->lower()
            ->ascii()
            ->replace("'", '')
            ->replaceMatches('/[^a-z]+/', '')
            ->toString();

        return match ($value) {
            'senin', 'monday' => 'senin',
            'selasa', 'tuesday' => 'selasa',
            'rabu', 'wednesday' => 'rabu',
            'kamis', 'thursday' => 'kamis',
            'jumat', 'friday' => 'jumat',
            'sabtu', 'saturday' => 'sabtu',
            'minggu', 'ahad', 'sunday' => 'minggu',
            default => null,
        };
    }

    private function dayNumber(string $day): int
    {
        $normalized = $this->normalizeDay($day);
        $number = array_search($normalized, self::DAYS, true);

        if ($number === false) {
            throw ValidationException::withMessages([
                'file_template' => "Nama hari {$day} tidak dikenali.",
            ]);
        }

        return (int) $number;
    }

    private function excelTimeToString(mixed $value): ?string
    {
        if ($value instanceof \DateTimeInterface) {
            return $value->format('H:i:s');
        }

        if (is_numeric($value)) {
            try {
                return ExcelDate::excelToDateTimeObject((float) $value)->format('H:i:s');
            } catch (\Throwable) {
                return null;
            }
        }

        $text = trim((string) $value);
        if ($text === '') {
            return null;
        }

        $text = str_replace('.', ':', $text);
        foreach (['H:i:s', 'H:i', 'G:i:s', 'G:i'] as $format) {
            $date = \DateTimeImmutable::createFromFormat('!'.$format, $text);
            if ($date !== false) {
                return $date->format('H:i:s');
            }
        }

        return null;
    }

    private function normalizeTime(string $time): string
    {
        return $this->excelTimeToString($time) ?? '17:00:00';
    }

    private function normalizeHeader(string $header): string
    {
        $header = Str::of($header)
            ->lower()
            ->ascii()
            ->replaceMatches('/[^a-z0-9]+/', '_')
            ->trim('_')
            ->toString();

        return match ($header) {
            'minggu', 'pekan', 'pekan_ke' => 'minggu_ke',
            'materi_dan_laporan', 'materi_laporan', 'materi' => 'materi_laporan',
            'judul_tugas', 'nama_tugas' => 'tugas',
            'tanggal_tampil' => 'hari_tampil',
            'tanggal_deadline' => 'hari_deadline',
            'waktu_deadline' => 'jam_deadline',
            default => $header,
        };
    }
}
