<?php

namespace App\Imports;

use App\Models\PesertaMagang;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class PesertaMagangImport implements ToCollection, SkipsEmptyRows, WithMultipleSheets
{
    private int $imported = 0;

    private int $updated = 0;

    private int $skipped = 0;

    /**
     * File lama CV Natusi memiliki beberapa sheet dengan data yang saling berulang.
     * Hanya sheet pertama yang diproses agar peserta tidak terimpor dua kali.
     */
    public function sheets(): array
    {
        return [0 => $this];
    }

    public function collection(Collection $rows): void
    {
        if ($rows->isEmpty()) {
            throw ValidationException::withMessages([
                'file_excel' => 'File Excel tidak memiliki data yang dapat diimpor.',
            ]);
        }

        $headerRow = $this->rowToArray($rows->shift());
        $headers = $this->buildHeaderMap($headerRow);

        if (! $this->hasRequiredHeaders($headers)) {
            throw ValidationException::withMessages([
                'file_excel' => 'Struktur kolom tidak dikenali. Gunakan template peserta magang yang tersedia.',
            ]);
        }

        foreach ($rows->values() as $index => $row) {
            $rowNumber = $index + 2;
            $rowValues = $this->rowToArray($row);

            if ($this->isBlankRow($rowValues)) {
                continue;
            }

            $data = $this->normalizeLegacyRow($rowValues, $headers);

            if (! $data['nama']) {
                if ($this->hasMeaningfulParticipantData($data)) {
                    $this->skipped++;
                }

                continue;
            }

            if ($this->shouldSkipRow($data)) {
                $this->skipped++;
                continue;
            }

            try {
                Validator::make($data, [
                    'nama' => ['required', 'string', 'max:255'],
                    'email' => ['required', 'email', 'max:255'],
                    'username' => ['nullable', 'string', 'max:50'],
                    'password_awal' => ['nullable', 'string', 'min:6', 'max:100'],
                    'alamat' => ['required', 'string', 'max:1000'],
                    'instansi' => ['required', 'string', 'max:255'],
                    'no_induk' => ['nullable', 'string', 'max:100'],
                    'jurusan' => ['nullable', 'string', 'max:255'],
                    'no_hp' => ['nullable', 'string', 'max:30'],
                    'tingkat_pendidikan' => ['required', 'in:SMK,Universitas'],
                    'kelas' => ['nullable', 'string', 'max:100'],
                    'tanggal_mulai' => ['nullable', 'date'],
                    'tanggal_selesai' => ['nullable', 'date', 'after_or_equal:tanggal_mulai'],
                    'durasi_magang' => ['nullable', 'string', 'max:100'],
                    'no_hp_pembimbing' => ['nullable', 'string', 'max:30'],
                    'status' => ['required', 'in:aktif,nonaktif'],
                ], [
                    'nama.required' => 'Nama Lengkap wajib diisi',
                    'alamat.required' => 'Alamat Lengkap wajib diisi',
                    'instansi.required' => 'Nama Sekolah atau Nama Universitas wajib diisi',
                    'email.required' => 'Email wajib diisi',
                    'email.email' => 'Format email tidak valid',
                    'tingkat_pendidikan.required' => 'Tingkat Pendidikan wajib diisi',
                    'tingkat_pendidikan.in' => 'Tingkat Pendidikan hanya boleh SMK atau Universitas',
                    'tanggal_selesai.after_or_equal' => 'Tanggal selesai harus setelah atau sama dengan tanggal mulai',
                ])->validate();
            } catch (ValidationException $exception) {
                $message = collect($exception->errors())->flatten()->first() ?? 'format data tidak valid';

                throw ValidationException::withMessages([
                    'file_excel' => "Baris {$rowNumber}: {$message}.",
                ]);
            }

            $this->persistRow($data, $rowNumber);
        }
    }

    public function importedCount(): int
    {
        return $this->imported;
    }

    public function updatedCount(): int
    {
        return $this->updated;
    }

    public function skippedCount(): int
    {
        return $this->skipped;
    }

    private function persistRow(array $data, int $rowNumber): void
    {
        $email = $data['email'] ? strtolower($data['email']) : null;
        $requestedUsername = $data['username'] ? $this->sanitizeUsername($data['username']) : null;

        $user = null;

        if ($email) {
            $user = User::query()->where('email', $email)->first();
        }

        if (! $user && $requestedUsername) {
            $user = User::query()->where('username', $requestedUsername)->first();
        }

        if (! $user && $data['no_induk']) {
            $user = User::query()->where('student_id', $data['no_induk'])->first();
        }

        if ($user && in_array($user->role, ['admin', 'superadmin'], true)) {
            throw ValidationException::withMessages([
                'file_excel' => "Baris {$rowNumber}: email, username, atau nomor induk sudah digunakan akun administrator.",
            ]);
        }

        $isNewParticipant = false;

        if (! $user) {
            $username = $this->uniqueUsername(
                $requestedUsername
                    ?: ($data['no_induk'] ?: Str::before((string) $email, '@'))
                    ?: $data['nama']
            );

            $email = $email ?: $this->uniqueLocalEmail($username);
            $password = $data['password_awal'] ?: ($data['no_induk'] ?: 'Natusi123!');

            $user = User::query()->create([
                'nama' => $data['nama'],
                'email' => $email,
                'username' => $username,
                'password' => Hash::make($password),
                'role' => 'peserta',
                'university' => $data['instansi'],
                'student_id' => $data['no_induk'],
                'major' => $data['jurusan'],
                'phone' => $data['no_hp'],
                'wajib_ganti_password' => true,
            ]);
        } else {
            $user->update([
                'nama' => $data['nama'],
                'role' => 'peserta',
                'university' => $data['instansi'],
                'student_id' => $data['no_induk'],
                'major' => $data['jurusan'],
                'phone' => $data['no_hp'],
            ]);
        }

        $peserta = PesertaMagang::query()
            ->where('user_id', $user->id_user)
            ->first();

        if (! $peserta) {
            $isNewParticipant = true;
            $peserta = new PesertaMagang();
            $peserta->user_id = $user->id_user;
        }

        $peserta->fill([
            'permintaan_id' => $peserta->permintaan_id,
            'alamat' => $data['alamat'],
            'tingkat_pendidikan' => $data['tingkat_pendidikan'],
            'kelas' => $data['kelas'],
            'tgl_mulai' => $data['tanggal_mulai'],
            'tgl_selesai' => $data['tanggal_selesai'],
            'durasi_magang' => $data['durasi_magang'],
            'nama_guru' => $data['nama_pembimbing'] ?: $peserta->nama_guru,
            'no_hpguru' => $data['no_hp_pembimbing'],
            'status' => $data['status'] === 'aktif' ? 'aktif' : 'selesai',
        ]);
        $peserta->save();

        $isNewParticipant ? $this->imported++ : $this->updated++;
    }

    /**
     * Mendukung template ringkas terbaru dan tetap kompatibel dengan
     * file lama Google Form CV Natusi.
     */
    private function normalizeLegacyRow(array $row, array $headers): array
    {
        $education = $this->normalizeEducation($this->textFrom($row, $headers, [
            'tingkat_pendidikan',
            'pendidikan',
        ]));

        $combinedInstitution = $this->textFrom($row, $headers, [
            'nama_sekolah_universitas',
            'nama_instansi',
            'instansi',
            'institusi',
        ]);

        $school = $this->textFrom($row, $headers, [
            'nama_sekolah',
            'asal_sekolah',
        ]);

        $university = $this->textFrom($row, $headers, [
            'nama_universitas',
            'universitas',
        ]);

        $isUniversity = $this->isUniversityLevel($education, $university);

        if (! $education) {
            $education = $university ? 'Universitas' : ($school ? 'SMK' : null);
        }

        $identityIndexes = $this->indexesFor($headers, [
            'no_induk',
            'nomor_induk',
            'nis_nim',
            'nisn_nim',
            'no_induk_siswa',
            'no_induk_mahasiswa',
        ]);

        $majorIndexes = $this->indexesFor($headers, [
            'jurusan',
            'program_studi',
            'jurusan_sekolah',
            'jurusan_universitas',
        ]);

        $schoolIdentity = $this->textAt($row, $identityIndexes[0] ?? null);
        $universityIdentity = $this->textAt($row, $identityIndexes[1] ?? null);
        $schoolMajor = $this->textAt($row, $majorIndexes[0] ?? null);
        $universityMajor = $this->textAt($row, $majorIndexes[1] ?? null);

        $status = $this->normalizeStatus(
            $this->rawFrom($row, $headers, ['status']),
            $this->rawFrom($row, $headers, ['sudah_keluar', 'sudah_keluar_'])
        );

        return [
            'nama' => $this->textFrom($row, $headers, [
                'nama_lengkap',
                'nama',
                'nama_peserta',
            ]),
            'email' => $this->textFrom($row, $headers, [
                'alamat_email',
                'email',
            ]),
            'username' => $this->textFrom($row, $headers, [
                'username',
                'nama_pengguna',
            ]),
            'password_awal' => $this->textFrom($row, $headers, [
                'password_awal',
                'password',
            ]),
            'alamat' => $this->textFrom($row, $headers, [
                'alamat_lengkap',
                'alamat',
            ]),
            'instansi' => $combinedInstitution ?: ($isUniversity
                ? ($university ?: $school)
                : ($school ?: $university)),
            'no_induk' => $isUniversity
                ? ($universityIdentity ?: $schoolIdentity)
                : ($schoolIdentity ?: $universityIdentity),
            'jurusan' => $isUniversity
                ? ($universityMajor ?: $schoolMajor)
                : ($schoolMajor ?: $universityMajor),
            'no_hp' => $this->textFrom($row, $headers, [
                'no_wa',
                'no_telepon_wa',
                'no_telepon_whatsapp',
                'no_hp',
                'nomor_hp',
                'telepon',
                'whatsapp',
            ]),
            'tingkat_pendidikan' => $education,
            'kelas' => $this->textFrom($row, $headers, [
                'kelas_semester',
                'kelas',
                'semester',
            ]),
            'tanggal_mulai' => $this->dateFrom($row, $headers, [
                'tanggal_mulai_magang',
                'tanggal_mulai',
                'tgl_mulai',
            ]),
            'tanggal_selesai' => $this->dateFrom($row, $headers, [
                'tanggal_selesai_magang',
                'tanggal_selesai',
                'tgl_selesai',
            ]),
            'durasi_magang' => $this->textFrom($row, $headers, [
                'periode_magang',
                'berapa_lama_magang_anda',
                'durasi_magang',
                'durasi',
            ]),
            'nama_pembimbing' => $this->textFrom($row, $headers, [
                'nama_pembimbing',
                'nama_guru',
                'guru_dosen_pembimbing',
            ]),
            'no_hp_pembimbing' => $this->textFrom($row, $headers, [
                'no_wa_guru_dosen',
                'no_telepon_wa_guru_dosen_pembimbing',
                'no_telepon_whatsapp_guru_dosen_pembimbing',
                'no_hp_pembimbing',
                'no_hpguru',
                'nomor_hp_pembimbing',
            ]),
            'status' => $status,
        ];
    }

    private function buildHeaderMap(array $headerRow): array
    {
        $map = [];

        foreach ($headerRow as $index => $value) {
            $normalized = $this->normalizeHeader($value);

            if ($normalized === '') {
                continue;
            }

            $map[$normalized] ??= [];
            $map[$normalized][] = $index;
        }

        return $map;
    }

    private function hasRequiredHeaders(array $headers): bool
    {
        return $this->firstIndex($headers, ['nama_lengkap', 'nama', 'nama_peserta']) !== null
            && $this->firstIndex($headers, ['alamat_lengkap', 'alamat']) !== null
            && $this->firstIndex($headers, ['tingkat_pendidikan', 'pendidikan']) !== null
            && $this->firstIndex($headers, [
                'nama_sekolah_universitas',
                'nama_sekolah',
                'nama_universitas',
                'nama_instansi',
                'instansi',
                'institusi',
            ]) !== null;
    }

    private function normalizeHeader(mixed $value): string
    {
        if ($value === null) {
            return '';
        }

        return Str::of((string) $value)
            ->lower()
            ->ascii()
            ->replaceMatches('/[^a-z0-9]+/', '_')
            ->trim('_')
            ->toString();
    }

    private function indexesFor(array $headers, array $keys): array
    {
        $indexes = [];

        foreach ($keys as $key) {
            foreach ($headers[$key] ?? [] as $index) {
                $indexes[] = $index;
            }
        }

        sort($indexes);

        return array_values(array_unique($indexes));
    }

    private function firstIndex(array $headers, array $keys): ?int
    {
        return $this->indexesFor($headers, $keys)[0] ?? null;
    }

    private function rawFrom(array $row, array $headers, array $keys): mixed
    {
        return $this->rawAt($row, $this->firstIndex($headers, $keys));
    }

    private function rawAt(array $row, ?int $index): mixed
    {
        if ($index === null || ! array_key_exists($index, $row)) {
            return null;
        }

        return $row[$index];
    }

    private function textFrom(array $row, array $headers, array $keys): ?string
    {
        return $this->textAt($row, $this->firstIndex($headers, $keys));
    }

    private function textAt(array $row, ?int $index): ?string
    {
        return $this->normalizeText($this->rawAt($row, $index));
    }

    private function normalizeText(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_bool($value)) {
            return $value ? '1' : '0';
        }

        if (is_float($value) || is_int($value)) {
            $number = (float) $value;

            if (floor($number) === $number) {
                return number_format($number, 0, '', '');
            }

            return rtrim(rtrim(number_format($number, 10, '.', ''), '0'), '.');
        }

        $text = trim((string) $value);

        return $text === '' ? null : $text;
    }

    private function dateFrom(array $row, array $headers, array $keys): ?string
    {
        $value = $this->rawFrom($row, $headers, $keys);

        if ($value === null || $value === '') {
            return null;
        }

        try {
            if ($value instanceof \DateTimeInterface) {
                return Carbon::instance(\DateTime::createFromInterface($value))->format('Y-m-d');
            }

            if (is_numeric($value)) {
                return Carbon::instance(ExcelDate::excelToDateTimeObject((float) $value))->format('Y-m-d');
            }

            $text = trim((string) $value);

            if ($text === '' || preg_match('/^#[A-Z0-9\/]+[!?]?$/i', $text)) {
                return null;
            }

            return Carbon::parse($text)->format('Y-m-d');
        } catch (\Throwable) {
            return null;
        }
    }

    private function normalizeEducation(?string $education): ?string
    {
        if (! $education) {
            return null;
        }

        $level = Str::of($education)
            ->lower()
            ->ascii()
            ->replaceMatches('/[^a-z0-9]+/', '')
            ->toString();

        if (Str::contains($level, [
            'mahasiswa',
            'universitas',
            'kuliah',
            'diploma',
            'politeknik',
            'institut',
            'akademi',
            'd1',
            'd2',
            'd3',
            'd4',
            's1',
            's2',
            's3',
        ])) {
            return 'Universitas';
        }

        if (Str::contains($level, ['smk', 'siswa', 'sekolah'])) {
            return 'SMK';
        }

        return null;
    }

    private function isUniversityLevel(?string $education, ?string $university): bool
    {
        if (in_array($education, ['Universitas', 'Mahasiswa'], true) || $university) {
            return true;
        }

        $level = Str::of((string) $education)->lower()->ascii()->toString();

        return Str::contains($level, [
            'universitas',
            'mahasiswa',
            'kuliah',
            'diploma',
            'politeknik',
            'institut',
            'akademi',
            'd1',
            'd2',
            'd3',
            'd4',
            's1',
            's2',
            's3',
        ]);
    }

    private function normalizeStatus(mixed $statusValue, mixed $leftValue): string
    {
        $status = Str::of((string) $this->normalizeText($statusValue))
            ->lower()
            ->ascii()
            ->replaceMatches('/[^a-z0-9]+/', '')
            ->toString();

        if (in_array($status, ['nonaktif', 'selesai', 'dibatalkan', 'keluar'], true)) {
            return 'nonaktif';
        }

        if (in_array($status, ['aktif', 'active'], true)) {
            return 'aktif';
        }

        if (is_bool($leftValue)) {
            return $leftValue ? 'nonaktif' : 'aktif';
        }

        $left = Str::of((string) $this->normalizeText($leftValue))
            ->lower()
            ->ascii()
            ->replaceMatches('/[^a-z0-9]+/', '')
            ->toString();

        return in_array($left, ['1', 'true', 'ya', 'yes', 'sudah', 'keluar'], true)
            ? 'nonaktif'
            : 'aktif';
    }



    private function hasMeaningfulParticipantData(array $data): bool
    {
        foreach ([
            'email',
            'alamat',
            'instansi',
            'no_induk',
            'jurusan',
            'no_hp',
            'tingkat_pendidikan',
            'tanggal_mulai',
            'tanggal_selesai',
        ] as $key) {
            if (! empty($data[$key])) {
                return true;
            }
        }

        return false;
    }

    private function isBlankRow(array $row): bool
    {
        foreach ($row as $value) {
            if ($value !== null && trim((string) $value) !== '') {
                return false;
            }
        }

        return true;
    }

    private function shouldSkipRow(array $data): bool
    {
        if (! $data['nama']) {
            return true;
        }

        $name = strtoupper(trim($data['nama']));

        return Str::startsWith($name, '#') || in_array($name, ['N/A', 'NULL'], true);
    }

    private function rowToArray(mixed $row): array
    {
        if ($row instanceof Collection) {
            return array_values($row->toArray());
        }

        return array_values((array) $row);
    }

    private function sanitizeUsername(string $value): string
    {
        return Str::of($value)
            ->lower()
            ->ascii()
            ->replaceMatches('/[^a-z0-9._-]+/', '')
            ->limit(50, '')
            ->toString();
    }

    private function uniqueUsername(string $source): string
    {
        $base = $this->sanitizeUsername($source);

        if (mb_strlen($base) < 4) {
            $base = 'peserta' . random_int(1000, 9999);
        }

        $candidate = $base;
        $suffix = 1;

        while (User::query()->where('username', $candidate)->exists()) {
            $suffixText = (string) $suffix;
            $candidate = mb_substr($base, 0, max(1, 50 - mb_strlen($suffixText))) . $suffixText;
            $suffix++;
        }

        return $candidate;
    }

    private function uniqueLocalEmail(string $username): string
    {
        $candidate = $username . '@natusi.local';
        $suffix = 1;

        while (User::query()->where('email', $candidate)->exists()) {
            $candidate = $username . $suffix . '@natusi.local';
            $suffix++;
        }

        return $candidate;
    }
}
