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
    private const DEFAULT_OPEN_TIME = '07:30';
    private const DEFAULT_CLOSE_TIME = '09:00';

    public function index(): View
    {
        $setting = $this->getSetting();
        $now = now();

        $isActive = $this->isAttendanceActive($setting, $now);
        $progress = $this->calculateProgress($setting, $now);

        $openTime = Carbon::parse($setting->jam_mulai)->format('H:i');
        $closeTime = Carbon::parse($setting->jam_selesai)->format('H:i');

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

            if ($this->isAttendanceActive($setting, now())) {
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
            ->with('success', 'Pengaturan jam absensi berhasil diperbarui.');
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

            if ($this->isAttendanceActive($setting, now())) {
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
            ->with('success', 'Jam absensi berhasil dikembalikan ke pengaturan awal.');
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

        $currentMinutes = ($now->hour * 60) + $now->minute;
        $openMinutes = $this->timeToMinutes($setting->jam_mulai);
        $closeMinutes = $this->timeToMinutes($setting->jam_selesai);

        if ($openMinutes === $closeMinutes) {
            return false;
        }

        if ($openMinutes < $closeMinutes) {
            return $currentMinutes >= $openMinutes
                && $currentMinutes <= $closeMinutes;
        }

        return $currentMinutes >= $openMinutes
            || $currentMinutes <= $closeMinutes;
    }

    private function calculateProgress(
        JamOperasional $setting,
        Carbon $now,
    ): int {
        if (! $this->isAttendanceActive($setting, $now)) {
            return 0;
        }

        $currentMinutes = ($now->hour * 60) + $now->minute;
        $openMinutes = $this->timeToMinutes($setting->jam_mulai);
        $closeMinutes = $this->timeToMinutes($setting->jam_selesai);

        if ($openMinutes < $closeMinutes) {
            $total = max(1, $closeMinutes - $openMinutes);
            $elapsed = $currentMinutes - $openMinutes;
        } else {
            $total = max(1, (1440 - $openMinutes) + $closeMinutes);
            $elapsed = $currentMinutes >= $openMinutes
                ? $currentMinutes - $openMinutes
                : (1440 - $openMinutes) + $currentMinutes;
        }

        return (int) round(
            min(100, max(0, ($elapsed / $total) * 100))
        );
    }

    private function inactiveStatusLabel(
        JamOperasional $setting,
        Carbon $now,
    ): string {
        if (! $setting->aktif) {
            return 'Jadwal Absensi Dinonaktifkan';
        }

        $currentMinutes = ($now->hour * 60) + $now->minute;
        $openMinutes = $this->timeToMinutes($setting->jam_mulai);
        $closeMinutes = $this->timeToMinutes($setting->jam_selesai);

        if ($openMinutes < $closeMinutes) {
            return $currentMinutes < $openMinutes
                ? 'Absensi Belum Dimulai'
                : 'Absensi Sudah Ditutup';
        }

        return 'Absensi Tidak Aktif';
    }

    private function timeToMinutes(string $time): int
    {
        [$hour, $minute] = array_map(
            'intval',
            explode(':', substr($time, 0, 5)),
        );

        return ($hour * 60) + $minute;
    }
}