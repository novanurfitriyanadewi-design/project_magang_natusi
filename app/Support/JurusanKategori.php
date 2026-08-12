<?php

namespace App\Support;

use App\Models\Jurusan;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

/**
 * Jembatan ke tabel `jurusan` (dikelola lewat halaman Kelola Jurusan).
 * Dipakai oleh:
 * - PenugasanTemplateService, untuk menentukan sheet/target tugas mingguan.
 * - Validasi durasi magang minimal saat data peserta disimpan.
 */
class JurusanKategori
{
    private const CACHE_KEY = 'jurusan_kategori_aktif';

    /**
     * Semua jurusan aktif, di-cache singkat supaya tidak query berulang
     * dalam satu request/import Excel yang memproses banyak baris.
     */
    public static function semua(): Collection
    {
        return Cache::remember(self::CACHE_KEY, 300, function () {
            return Jurusan::query()->aktif()->get();
        });
    }

    public static function lupakanCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    public static function cariById(?int $jurusanId): ?Jurusan
    {
        if ($jurusanId === null) {
            return null;
        }

        return self::semua()->firstWhere('id_jurusan', $jurusanId);
    }

    /**
     * Cocokkan teks jurusan bebas (data lama/legacy) ke jurusan resmi.
     * Dipakai sebagai fallback untuk peserta yang belum punya jurusan_id.
     */
    public static function cariByTeks(?string $jurusanText): ?Jurusan
    {
        $normalized = self::normalize($jurusanText);
        if ($normalized === '') {
            return null;
        }

        return self::semua()->first(function (Jurusan $jurusan) use ($normalized) {
            $needle = self::normalize($jurusan->nama_jurusan);
            $kode = self::normalize($jurusan->kode);

            return $normalized === $needle
                || $normalized === $kode
                || Str::contains(" {$normalized} ", " {$needle} ")
                || Str::contains(" {$normalized} ", " {$kode} ");
        });
    }

    /**
     * Validasi apakah jumlah bulan magang sesuai ketentuan jurusan.
     * Mengembalikan pesan error (string) jika tidak valid, atau null jika
     * valid atau jurusannya tidak dikenali (tidak dibatasi).
     */
    public static function validasiDurasi(?Jurusan $jurusan, float $bulan): ?string
    {
        if ($jurusan === null) {
            return null;
        }

        if ($bulan < $jurusan->durasi_min_bulan) {
            return sprintf(
                'Durasi magang untuk jurusan %s minimal %d bulan. Durasi yang dimasukkan sekitar %s bulan.',
                $jurusan->nama_jurusan,
                $jurusan->durasi_min_bulan,
                self::formatBulan($bulan)
            );
        }

        if ($jurusan->durasi_max_bulan !== null && $bulan > $jurusan->durasi_max_bulan) {
            return sprintf(
                'Durasi magang untuk jurusan %s maksimal %d bulan. Durasi yang dimasukkan sekitar %s bulan.',
                $jurusan->nama_jurusan,
                $jurusan->durasi_max_bulan,
                self::formatBulan($bulan)
            );
        }

        return null;
    }

    private static function formatBulan(float $bulan): string
    {
        return rtrim(rtrim(number_format($bulan, 1), '0'), '.');
    }

    private static function normalize(?string $value): string
    {
        return (string) Str::of((string) $value)
            ->lower()
            ->ascii()
            ->replaceMatches('/[^a-z0-9]+/', ' ')
            ->squish();
    }
}
