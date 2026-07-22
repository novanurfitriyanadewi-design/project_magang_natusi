<?php

namespace App\Services;

use App\Models\PengumpulanTugas;
use App\Models\PenugasanPeserta;
use App\Models\PesertaMagang;
use App\Models\TemplateLaporan;
use App\Models\Tugas;
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
     * Sheet resmi pada template tugas mingguan.
     */
    private const TEMPLATE_SHEETS = [
        'SMK RPL' => [
            'target' => 'smk_rpl',
            'instansi' => 'sekolah',
            'prefix' => 'RPL',
            'label' => 'SMK RPL',
        ],
        'SMK TKJ' => [
            'target' => 'smk_tkj',
            'instansi' => 'sekolah',
            'prefix' => 'TKJ',
            'label' => 'SMK TKJ',
        ],
        'Universitas' => [
            'target' => 'universitas',
            'instansi' => 'universitas',
            'prefix' => 'UNI',
            'label' => 'Universitas',
        ],
    ];

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

            $participants = PesertaMagang::query()
                ->with(['user', 'permintaan'])
                ->where('status', 'aktif')
                ->whereNotNull('tgl_mulai')
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
        if (!$participant->tgl_mulai || $participant->status !== 'aktif') {
            return 0;
        }

        $participant->loadMissing(['user', 'permintaan']);
        $target = $this->participantTarget($participant);
        if ($target === null) {
            return 0;
        }

        $institution = $this->participantInstitution($participant);

        $tasks ??= Tugas::query()
            ->where('status', 'aktif')
            ->where(function ($query) use ($target, $institution): void {
                $query
                    ->where('target_peserta', $target)
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
            })
            ->orderBy('minggu_ke')
            ->orderBy('rilis_hari_ke')
            ->get();

        $start = Carbon::parse($participant->tgl_mulai)->startOfDay();
        $count = 0;

        foreach ($tasks as $task) {
            if ($task->status !== 'aktif'
                || !$this->taskMatchesParticipant($task, $target, $institution)) {
                continue;
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
            $count++;
        }

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

    public function refreshStatuses(PesertaMagang $participant): void
    {
        PenugasanPeserta::query()
            ->where('peserta_id', $participant->id_peserta)
            ->where('status', 'terjadwal')
            ->whereNotNull('tersedia_pada')
            ->where('tersedia_pada', '<=', now())
            ->update(['status' => 'aktif']);
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
     * Menentukan sheet tugas peserta dari pendidikan dan jurusannya.
     */
    public function participantTarget(PesertaMagang $participant): ?string
    {
        if ($this->participantInstitution($participant) === 'universitas') {
            return 'universitas';
        }

        $major = Str::of(implode(' ', array_filter([
            $participant->permintaan?->jurusan,
            $participant->user?->major,
            $participant->kelas,
        ])))
            ->lower()
            ->ascii()
            ->replaceMatches('/[^a-z0-9]+/', ' ')
            ->squish()
            ->toString();

        if (Str::contains($major, [
            'rpl',
            'rekayasa perangkat lunak',
            'pengembangan perangkat lunak dan gim',
            'pplg',
            'software engineering',
        ])) {
            return 'smk_rpl';
        }

        if (Str::contains($major, [
            'tkj',
            'teknik komputer dan jaringan',
            'teknik jaringan komputer dan telekomunikasi',
            'tjkt',
            'komputer jaringan',
        ])) {
            return 'smk_tkj';
        }

        return null;
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
        string $target,
        string $institution
    ): bool {
        if (filled($task->target_peserta)
            && $task->target_peserta !== 'semua') {
            return $task->target_peserta === $target;
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
                return [
                    null,
                    null,
                    true,
                    sprintf(
                        'Dilewati karena deadline %s pukul %s sudah lewat sebelum tanggal mulai magang.',
                        ucfirst((string) $task->hari_deadline),
                        $deadline->format('H:i')
                    ),
                ];
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

        foreach (self::TEMPLATE_SHEETS as $sheetName => $profile) {
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

        $category = Str::contains(Str::lower($material), 'laporan')
            ? 'laporan'
            : 'tugas';

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
