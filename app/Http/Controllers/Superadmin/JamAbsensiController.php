<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\JamOperasional;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class JamAbsensiController extends Controller
{
    private const ATTENDANCE_TIMEZONE = 'Asia/Jakarta';

    private const DEFAULT_OPEN_TIME = '07:30';
    private const DEFAULT_CLOSE_TIME = '09:00';

    public function index(): View
    {
        $setting = $this->getSetting();
        $now = $this->currentTime();

        $isActive = $this->isAttendanceActive($setting, $now);
        $progress = $this->calculateProgress($setting, $now);

        $openTime = $this->formatTime($setting->jam_mulai);
        $closeTime = $this->formatTime($setting->jam_selesai);

        $statusLabel = $isActive
            ? 'Absensi Sedang Berlangsung'
            : $this->inactiveStatusLabel($setting, $now);

        return view('superadmin.jam-absensi', compact(
            'setting',
            'now',
            'isActive',
            'progress',
            'openTime',
            'closeTime',
            'statusLabel',
        ));
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate(
            [
                'jam_buka' => ['required', 'date_format:H:i'],
                'jam_tutup' => ['required', 'date_format:H:i'],
            ],
            [
                'jam_buka.required' => 'Jam buka absensi wajib diisi.',
                'jam_buka.date_format' => 'Format jam buka harus HH:MM.',
                'jam_tutup.required' => 'Jam tutup absensi wajib diisi.',
                'jam_tutup.date_format' => 'Format jam tutup harus HH:MM.',
            ],
        );

        if ($validated['jam_buka'] === $validated['jam_tutup']) {
            throw ValidationException::withMessages([
                'jam_tutup' => 'Jam tutup harus berbeda dari jam buka.',
            ]);
        }

        DB::transaction(function () use ($validated): void {
            $setting = JamOperasional::query()
                ->lockForUpdate()
                ->first();

            if (! $setting) {
                $setting = $this->createDefaultSetting();
            }

            if (
                $this->isAttendanceActive(
                    $setting,
                    $this->currentTime(),
                )
            ) {
                throw ValidationException::withMessages([
                    'jam_buka' => 'Jam absensi tidak dapat diubah saat absensi sedang berlangsung.',
                ]);
            }

            $setting->update([
                'jam_mulai' => $validated['jam_buka'] . ':00',
                'jam_selesai' => $validated['jam_tutup'] . ':00',
            ]);
        });

        return redirect()
            ->route('superadmin.jam-absensi.index')
            ->with(
                'success',
                'Pengaturan jam absensi berhasil diperbarui.',
            );
    }

    public function reset(): RedirectResponse
    {
        DB::transaction(function (): void {
            $setting = JamOperasional::query()
                ->lockForUpdate()
                ->first();

            if (! $setting) {
                $setting = $this->createDefaultSetting();
            }

            if (
                $this->isAttendanceActive(
                    $setting,
                    $this->currentTime(),
                )
            ) {
                throw ValidationException::withMessages([
                    'jam_buka' => 'Pengaturan tidak dapat direset saat absensi sedang berlangsung.',
                ]);
            }

            $setting->update([
                'jam_mulai' => self::DEFAULT_OPEN_TIME . ':00',
                'jam_selesai' => self::DEFAULT_CLOSE_TIME . ':00',
            ]);
        });

        return redirect()
            ->route('superadmin.jam-absensi.index')
            ->with(
                'success',
                'Jam absensi berhasil dikembalikan ke pengaturan awal.',
            );
    }

    private function currentTime(): Carbon
    {
        /*
         * Jam operasional selalu memakai WIB, tidak bergantung
         * pada timezone komputer/server tempat Laravel dijalankan.
         */
        return Carbon::now(self::ATTENDANCE_TIMEZONE);
    }

    private function getSetting(): JamOperasional
    {
        return JamOperasional::query()->first()
            ?? $this->createDefaultSetting();
    }

    private function createDefaultSetting(): JamOperasional
    {
        return JamOperasional::query()->create([
            'jam_mulai' => self::DEFAULT_OPEN_TIME . ':00',
            'jam_selesai' => self::DEFAULT_CLOSE_TIME . ':00',
            'aktif' => true,
        ]);
    }

    private function isAttendanceActive(
        JamOperasional $setting,
        Carbon $now,
    ): bool {
        if (! $setting->aktif) {
            return false;
        }

        [$openAt, $closeAt] = $this->attendanceWindow(
            $setting,
            $now,
        );

        if ($openAt->equalTo($closeAt)) {
            return false;
        }

        /*
         * Jam tutup bersifat eksklusif.
         * Contoh: tutup 09:00 berarti tepat 09:00 sudah tidak aktif.
         */
        return $now->greaterThanOrEqualTo($openAt)
            && $now->lessThan($closeAt);
    }

    private function calculateProgress(
        JamOperasional $setting,
        Carbon $now,
    ): int {
        if (! $this->isAttendanceActive($setting, $now)) {
            return 0;
        }

        [$openAt, $closeAt] = $this->attendanceWindow(
            $setting,
            $now,
        );

        $totalSeconds = max(
            1,
            (int) $openAt->diffInSeconds($closeAt),
        );

        $elapsedSeconds = max(
            0,
            (int) $openAt->diffInSeconds($now),
        );

        return (int) round(
            min(
                100,
                max(
                    0,
                    ($elapsedSeconds / $totalSeconds) * 100,
                ),
            ),
        );
    }

    /**
     * Membuat tanggal dan waktu pembukaan/penutupan yang benar.
     *
     * Contoh rentang lintas hari 19:30–09:00:
     * - pukul 02:00 memakai pembukaan hari sebelumnya;
     * - pukul 10:00 memakai pembukaan hari ini;
     * - tepat pukul 09:00 periode sudah berakhir.
     *
     * @return array{0: Carbon, 1: Carbon}
     */
    private function attendanceWindow(
        JamOperasional $setting,
        Carbon $now,
    ): array {
        [$openHour, $openMinute, $openSecond] = $this->timeParts(
            $setting->jam_mulai,
        );

        [$closeHour, $closeMinute, $closeSecond] = $this->timeParts(
            $setting->jam_selesai,
        );

        $openAt = $now->copy()->setTime(
            $openHour,
            $openMinute,
            $openSecond,
        );

        $closeAt = $now->copy()->setTime(
            $closeHour,
            $closeMinute,
            $closeSecond,
        );

        if ($openAt->lessThan($closeAt)) {
            return [$openAt, $closeAt];
        }

        if ($openAt->equalTo($closeAt)) {
            return [$openAt, $closeAt];
        }

        /*
         * Rentang melewati tengah malam.
         * Contoh: 19:30 sampai 09:00.
         */
        $currentSeconds = $this->secondsFromMidnight($now);
        $closeSeconds = $this->timeToSeconds(
            $setting->jam_selesai,
        );

        if ($currentSeconds < $closeSeconds) {
            $openAt->subDay();
        } else {
            $closeAt->addDay();
        }

        return [$openAt, $closeAt];
    }

    private function inactiveStatusLabel(
        JamOperasional $setting,
        Carbon $now,
    ): string {
        if (! $setting->aktif) {
            return 'Jadwal Absensi Dinonaktifkan';
        }

        $currentSeconds = $this->secondsFromMidnight($now);
        $openSeconds = $this->timeToSeconds(
            $setting->jam_mulai,
        );
        $closeSeconds = $this->timeToSeconds(
            $setting->jam_selesai,
        );

        if ($openSeconds < $closeSeconds) {
            return $currentSeconds < $openSeconds
                ? 'Absensi Belum Dimulai'
                : 'Absensi Sudah Ditutup';
        }

        if ($openSeconds > $closeSeconds) {
            return $currentSeconds >= $closeSeconds
                && $currentSeconds < $openSeconds
                    ? 'Absensi Sudah Ditutup'
                    : 'Absensi Belum Dimulai';
        }

        return 'Absensi Tidak Aktif';
    }

    private function formatTime(string $time): string
    {
        return Carbon::createFromFormat(
            'H:i:s',
            $this->normalizeTime($time),
            self::ATTENDANCE_TIMEZONE,
        )->format('H:i');
    }

    /**
     * @return array{0: int, 1: int, 2: int}
     */
    private function timeParts(string $time): array
    {
        [$hour, $minute, $second] = array_map(
            'intval',
            explode(':', $this->normalizeTime($time)),
        );

        return [$hour, $minute, $second];
    }

    private function timeToSeconds(string $time): int
    {
        [$hour, $minute, $second] = $this->timeParts($time);

        return ($hour * 3600)
            + ($minute * 60)
            + $second;
    }

    private function secondsFromMidnight(Carbon $time): int
    {
        return ($time->hour * 3600)
            + ($time->minute * 60)
            + $time->second;
    }

    private function normalizeTime(string $time): string
    {
        $time = trim($time);

        if (preg_match('/^\d{2}:\d{2}$/', $time) === 1) {
            return $time . ':00';
        }

        return substr($time, 0, 8);
    }
}