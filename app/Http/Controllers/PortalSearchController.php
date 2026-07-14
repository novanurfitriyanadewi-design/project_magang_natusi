<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\LaporanMingguan;
use App\Models\PermintaanMagang;
use App\Models\PesertaMagang;
use App\Models\Tugas;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PortalSearchController extends Controller
{
    public function index(Request $request): View
    {
        $query = trim((string) $request->query('q', ''));
        $type = trim((string) $request->query('type', ''));
        $user = $request->user();

        $results = mb_strlen($query) >= 2
            ? $this->collectResults($user, $query, 60)
            : collect();

        if ($type !== '') {
            $results = $results
                ->filter(fn (array $result) => $result['type'] === $type)
                ->values();
        }

        return view('search.index', [
            'query' => $query,
            'results' => $results,
            'groupedResults' => $results->groupBy('category'),
            'searchDescription' => $this->searchDescription($user?->role),
        ]);
    }

    public function suggestions(Request $request): JsonResponse
    {
        $query = trim((string) $request->query('q', ''));

        if (mb_strlen($query) < 2) {
            return response()->json([
                'query' => $query,
                'results' => [],
            ]);
        }

        $results = $this->collectResults($request->user(), $query, 10);

        return response()->json([
            'query' => $query,
            'results' => $results->values(),
        ]);
    }

    // search roll
    private function collectResults(?User $user, string $query, int $limit): Collection
    {
        if (! $user) {
            return collect();
        }

        return match ($user->role) {
            'superadmin' => $this->searchForSuperadmin($query, $limit),
            'admin' => $this->searchForAdmin($query, $limit),
            default => $this->searchForPeserta($user, $query, $limit),
        };
    }

    // search admin
    private function searchForSuperadmin(string $query, int $limit): Collection
    {
        if (! Schema::hasTable('users')) {
            return collect();
        }

        return User::query()
            ->where('role', 'admin')
            ->where(function ($builder) use ($query) {
                $builder
                    ->where('nama', 'like', "%{$query}%")
                    ->orWhere('username', 'like', "%{$query}%")
                    ->orWhere('email', 'like', "%{$query}%");
            })
            ->orderBy('nama')
            ->limit($limit)
            ->get()
            ->map(function (User $admin) use ($query) {
                $destination = $this->destination(
                    'superadmin.admin',
                    ['search' => $admin->username ?: $admin->nama],
                    $query,
                    'admin'
                );

                return $this->result(
                    key: 'admin-'.$admin->id_user,
                    type: 'admin',
                    category: 'Administrator',
                    title: $admin->nama,
                    subtitle: collect([
                        $admin->username ? '@'.$admin->username : null,
                        $admin->email,
                    ])->filter()->implode(' • '),
                    meta: 'Akun admin operasional',
                    destination: $destination,
                );
            });
    }

    private function searchForAdmin(string $query, int $limit): Collection
    {
        $perGroup = max(3, (int) ceil($limit / 3));
        $results = collect();

        if (Schema::hasTable('users')) {
            $pesertaQuery = User::query()
                ->where('role', 'peserta')
                ->where(function ($builder) use ($query) {
                    $builder
                        ->where('nama', 'like', "%{$query}%")
                        ->orWhere('username', 'like', "%{$query}%")
                        ->orWhere('email', 'like', "%{$query}%");
                })
                ->orderBy('nama')
                ->limit($perGroup);

            if (Schema::hasTable('peserta_magang')) {
                $pesertaQuery->with('pesertaMagang:id_peserta,user_id,status,tingkat_pendidikan');
            }

            $results = $results->concat(
                $pesertaQuery->get()->map(function (User $peserta) use ($query) {
                    $destination = $this->destination(
                        'admin.peserta.index',
                        ['search' => $peserta->username ?: $peserta->nama],
                        $query,
                        'peserta'
                    );

                    $status = $peserta->relationLoaded('pesertaMagang')
                        ? $peserta->pesertaMagang?->status
                        : null;

                    return $this->result(
                        key: 'peserta-'.$peserta->id_user,
                        type: 'peserta',
                        category: 'Peserta Magang',
                        title: $peserta->nama,
                        subtitle: collect([
                            $peserta->username ? '@'.$peserta->username : null,
                            $peserta->email,
                        ])->filter()->implode(' • '),
                        meta: $status ? 'Status: '.Str::headline($status) : 'Akun peserta magang',
                        destination: $destination,
                    );
                })
            );
        }

        if (Schema::hasTable('permintaan_magang')) {
            $results = $results->concat(
                PermintaanMagang::query()
                    ->where(function ($builder) use ($query) {
                        $builder
                            ->where('nama_pemohon', 'like', "%{$query}%")
                            ->orWhere('email', 'like', "%{$query}%")
                            ->orWhere('nama_sekolah', 'like', "%{$query}%")
                            ->orWhere('no_induk', 'like', "%{$query}%")
                            ->orWhere('jurusan', 'like', "%{$query}%")
                            ->orWhere('no_hp', 'like', "%{$query}%")
                            ->orWhere('status', 'like', "%{$query}%");
                    })
                    ->latest('id_permintaan')
                    ->limit($perGroup)
                    ->get()
                    ->map(function (PermintaanMagang $permintaan) use ($query) {
                        $destination = $this->destination(
                            'admin.permintaan.index',
                            ['search' => $permintaan->nama_pemohon],
                            $query,
                            'permintaan'
                        );

                        return $this->result(
                            key: 'permintaan-'.$permintaan->id_permintaan,
                            type: 'permintaan',
                            category: 'Permintaan Magang',
                            title: $permintaan->nama_pemohon,
                            subtitle: collect([
                                $permintaan->nama_sekolah,
                                $permintaan->jurusan,
                            ])->filter()->implode(' • '),
                            meta: 'Status: '.Str::headline((string) $permintaan->status),
                            destination: $destination,
                        );
                    })
            );
        }

        if (Schema::hasTable('tugas')) {
            $results = $results->concat(
                Tugas::query()
                    ->where(function ($builder) use ($query) {
                        $builder
                            ->where('judul', 'like', "%{$query}%")
                            ->orWhere('materi', 'like', "%{$query}%")
                            ->orWhere('jenis_tugas', 'like', "%{$query}%")
                            ->orWhere('status', 'like', "%{$query}%");
                    })
                    ->latest('id_tugas')
                    ->limit($perGroup)
                    ->get()
                    ->map(function (Tugas $tugas) use ($query) {
                        $destination = $this->destination(
                            'admin.tugas.index',
                            ['search' => $tugas->judul],
                            $query,
                            'tugas'
                        );

                        $minggu = $tugas->minggu_ke
                            ? 'Minggu '.$tugas->minggu_ke
                            : null;

                        return $this->result(
                            key: 'tugas-'.$tugas->id_tugas,
                            type: 'tugas',
                            category: 'Tugas',
                            title: $tugas->judul,
                            subtitle: collect([
                                Str::headline((string) $tugas->jenis_tugas),
                                $minggu,
                            ])->filter()->implode(' • '),
                            meta: Str::limit(strip_tags((string) ($tugas->materi ?: 'Status: '.Str::headline((string) $tugas->status))), 90),
                            destination: $destination,
                        );
                    })
            );
        }

        return $results->take($limit)->values();
    }

    private function searchForPeserta(User $user, string $query, int $limit): Collection
    {
        $perGroup = max(3, (int) ceil($limit / 3));
        $results = collect();

        $peserta = Schema::hasTable('peserta_magang')
            ? PesertaMagang::query()->where('user_id', $user->id_user)->first()
            : null;

        if (Schema::hasTable('tugas')) {
            $results = $results->concat(
                Tugas::query()
                    ->where('status', 'aktif')
                    ->where(function ($builder) use ($query) {
                        $builder
                            ->where('judul', 'like', "%{$query}%")
                            ->orWhere('materi', 'like', "%{$query}%")
                            ->orWhere('jenis_tugas', 'like', "%{$query}%");
                    })
                    ->latest('id_tugas')
                    ->limit($perGroup)
                    ->get()
                    ->map(function (Tugas $tugas) use ($query) {
                        $destination = $this->destination(
                            'peserta.tugas.index',
                            ['search' => $tugas->judul],
                            $query,
                            'tugas'
                        );

                        return $this->result(
                            key: 'tugas-'.$tugas->id_tugas,
                            type: 'tugas',
                            category: 'Tugas Saya',
                            title: $tugas->judul,
                            subtitle: collect([
                                Str::headline((string) $tugas->jenis_tugas),
                                $tugas->minggu_ke ? 'Minggu '.$tugas->minggu_ke : null,
                            ])->filter()->implode(' • '),
                            meta: Str::limit(strip_tags((string) ($tugas->materi ?: 'Tugas aktif')), 90),
                            destination: $destination,
                        );
                    })
            );
        }

        if ($peserta && Schema::hasTable('laporan_mingguan')) {
            $results = $results->concat(
                LaporanMingguan::query()
                    ->where('peserta_id', $peserta->id_peserta)
                    ->where(function ($builder) use ($query) {
                        $builder
                            ->where('minggu_ke', 'like', "%{$query}%")
                            ->orWhere('laporan', 'like', "%{$query}%");
                    })
                    ->latest('id_laporan')
                    ->limit($perGroup)
                    ->get()
                    ->map(function (LaporanMingguan $laporan) use ($query) {
                        $destination = $this->destination(
                            'peserta.laporan.index',
                            ['search' => 'minggu '.$laporan->minggu_ke],
                            $query,
                            'laporan'
                        );

                        return $this->result(
                            key: 'laporan-'.$laporan->id_laporan,
                            type: 'laporan',
                            category: 'Laporan Mingguan',
                            title: 'Laporan Minggu '.$laporan->minggu_ke,
                            subtitle: $laporan->dikumpulkan_pada
                                ? 'Dikumpulkan '.$laporan->dikumpulkan_pada->translatedFormat('d M Y, H:i')
                                : 'Belum dikumpulkan',
                            meta: basename((string) $laporan->laporan),
                            destination: $destination,
                        );
                    })
            );
        }

        if ($peserta && Schema::hasTable('absensi')) {
            $results = $results->concat(
                Absensi::query()
                    ->where('peserta_id', $peserta->id_peserta)
                    ->where(function ($builder) use ($query) {
                        $builder
                            ->where('tanggal', 'like', "%{$query}%")
                            ->orWhere('status', 'like', "%{$query}%")
                            ->orWhere('keterangan', 'like', "%{$query}%");
                    })
                    ->latest('tanggal')
                    ->limit($perGroup)
                    ->get()
                    ->map(function (Absensi $absensi) use ($query) {
                        $destination = $this->destination(
                            'peserta.absensi.index',
                            ['search' => $absensi->tanggal?->format('Y-m-d')],
                            $query,
                            'absensi'
                        );

                        return $this->result(
                            key: 'absensi-'.$absensi->id_absensi,
                            type: 'absensi',
                            category: 'Absensi Saya',
                            title: $absensi->tanggal?->translatedFormat('d F Y') ?? 'Data Absensi',
                            subtitle: collect([
                                Str::headline((string) $absensi->status),
                                $absensi->jam ? substr((string) $absensi->jam, 0, 5) : null,
                            ])->filter()->implode(' • '),
                            meta: $absensi->keterangan ?: 'Catatan kehadiran peserta',
                            destination: $destination,
                        );
                    })
            );
        }

        return $results->take($limit)->values();
    }

    private function result(
        string $key,
        string $type,
        string $category,
        string $title,
        string $subtitle,
        string $meta,
        array $destination,
    ): array {
        return [
            'key' => $key,
            'type' => $type,
            'category' => $category,
            'title' => $title,
            'subtitle' => $subtitle,
            'meta' => $meta,
            'url' => $destination['url'],
            'can_open' => $destination['can_open'],
        ];
    }

    private function destination(
        string $routeName,
        array $parameters,
        string $query,
        string $type,
    ): array {
        if (Route::has($routeName)) {
            return [
                'url' => route($routeName, $parameters),
                'can_open' => true,
            ];
        }
        return [
            'url' => route('search.index', [
                'q' => $query,
                'type' => $type,
            ]),
            'can_open' => false,
        ];
    }

    private function searchDescription(?string $role): string
    {
        return match ($role) {
            'superadmin' => 'Pencarian Super Admin dibatasi hanya untuk data akun administrator.',
            'admin' => 'Pencarian mencakup peserta magang, permintaan magang, dan tugas.',
            default => 'Pencarian mencakup tugas aktif, laporan mingguan, dan riwayat absensi Anda.',
        };
    }
}
