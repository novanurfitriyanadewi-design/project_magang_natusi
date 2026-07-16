<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PesertaMagang;
use App\Models\PermintaanMagang;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LaporanPesertaController extends Controller
{
    /**
     * Halaman laporan: statistik, grafik tren, dan tabel peserta.
     */
    public function index(Request $request): View
    {
        $search       = $request->get('search');
        $statusFilter = $request->get('status_filter', 'overall'); // overall | active | non-active
        $year         = (int) $request->get('year', now()->year);

        $query = PesertaMagang::with(['user', 'permintaan']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($u) use ($search) {
                    $u->where('nama', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                })
                ->orWhereHas('permintaan', function ($p) use ($search) {
                    $p->where('nama_sekolah', 'like', "%{$search}%")
                      ->orWhere('jurusan', 'like', "%{$search}%");
                });
            });
        }

        if ($statusFilter === 'active') {
            $query->where('status', 'aktif');
        } elseif ($statusFilter === 'non-active') {
            $query->whereIn('status', ['selesai', 'dibatalkan']);
        }

        if ($year) {
            $query->whereYear('tgl_mulai', $year);
        }

        $peserta = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        // Statistik (mengikuti filter tahun yang dipilih)
        $stats = [
            'total'    => PesertaMagang::whereYear('tgl_mulai', $year)->count(),
            'aktif'    => PesertaMagang::whereYear('tgl_mulai', $year)->where('status', 'aktif')->count(),
            'nonaktif' => PesertaMagang::whereYear('tgl_mulai', $year)->whereIn('status', ['selesai', 'dibatalkan'])->count(),
        ];

        // Data grafik tren bulanan (Universitas vs SMK)
        [$universitas, $smk] = $this->getTrendData($year);
        $chartMax = max(1, max(array_merge($universitas, $smk)));

        $pointsUniversitas = $this->buildPoints($universitas, $chartMax);
        $pointsSmk         = $this->buildPoints($smk, $chartMax);

        // Daftar tahun yang tersedia untuk dropdown filter
        $availableYears = PesertaMagang::selectRaw('YEAR(tgl_mulai) as th')
            ->whereNotNull('tgl_mulai')
            ->distinct()
            ->orderByDesc('th')
            ->pluck('th');

        if ($availableYears->isEmpty()) {
            $availableYears = collect([now()->year]);
        }

        // Data untuk dropdown di modal Tambah/Edit
        $users      = User::orderBy('nama')->get();
        $permintaan = PermintaanMagang::orderBy('nama_pemohon')->get();

        return view('admin.laporan.peserta', compact(
            'peserta',
            'stats',
            'search',
            'statusFilter',
            'year',
            'pointsUniversitas',
            'pointsSmk',
            'availableYears',
            'users',
            'permintaan'
        ));
    }

    /**
     * Hitung total peserta per bulan, dipecah Universitas vs SMK.
     *
     * @return array{0: array<int,int>, 1: array<int,int>}
     */
    private function getTrendData(int $year): array
    {
        $rows = PesertaMagang::selectRaw('MONTH(tgl_mulai) as bulan, tingkat_pendidikan, COUNT(*) as total')
            ->whereYear('tgl_mulai', $year)
            ->groupBy('bulan', 'tingkat_pendidikan')
            ->get();

        $universitas = array_fill(1, 12, 0);
        $smk         = array_fill(1, 12, 0);

        foreach ($rows as $row) {
            if (!$row->bulan) {
                continue;
            }

            if (strtoupper(trim((string) $row->tingkat_pendidikan)) === 'SMK') {
                $smk[$row->bulan] += $row->total;
            } else {
                $universitas[$row->bulan] += $row->total;
            }
        }

        return [$universitas, $smk];
    }

    /**
     * Konversi array nilai bulanan jadi koordinat x,y buat digambar di SVG (viewBox 1200x220).
     *
     * @param array<int,int> $values
     * @return array<int, array{x:int, y:int}>
     */
    private function buildPoints(array $values, int $max, int $width = 1200, int $height = 220, int $topPad = 10): array
    {
        $points = [];
        $count  = count($values);
        $stepX  = $count > 1 ? $width / ($count - 1) : $width;
        $i      = 0;

        foreach ($values as $val) {
            $x = (int) round($i * $stepX);
            $y = (int) round($height - $topPad - (($val / $max) * ($height - ($topPad * 2))));
            $points[] = ['x' => $x, 'y' => $y];
            $i++;
        }

        return $points;
    }

    /**
     * Simpan peserta baru (dari modal Tambah).
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'user_id'            => 'required|exists:users,id_user',
            'permintaan_id'      => 'nullable|exists:permintaan_magang,id_permintaan',
            'alamat'             => 'required|string',
            'tingkat_pendidikan' => 'required|string|max:100',
            'kelas'              => 'nullable|string|max:100',
            'tgl_mulai'          => 'nullable|date',
            'tgl_selesai'        => 'nullable|date|after_or_equal:tgl_mulai',
            'durasi_magang'      => 'nullable|string|max:100',
            'nama_guru'          => 'nullable|string|max:255',
            'no_hpguru'          => 'nullable|string|max:20',
            'status'             => 'required|in:aktif,selesai,dibatalkan',
        ]);

        PesertaMagang::create($validated);

        return redirect()
            ->route('admin.laporan-peserta')
            ->with('success', 'Peserta magang berhasil ditambahkan.');
    }

    /**
     * Update peserta (dari modal Edit).
     */
    public function update(Request $request, PesertaMagang $pesertaMagang): RedirectResponse
    {
        $validated = $request->validate([
            'user_id'            => 'required|exists:users,id_user',
            'permintaan_id'      => 'nullable|exists:permintaan_magang,id_permintaan',
            'alamat'             => 'required|string',
            'tingkat_pendidikan' => 'required|string|max:100',
            'kelas'              => 'nullable|string|max:100',
            'tgl_mulai'          => 'nullable|date',
            'tgl_selesai'        => 'nullable|date|after_or_equal:tgl_mulai',
            'durasi_magang'      => 'nullable|string|max:100',
            'nama_guru'          => 'nullable|string|max:255',
            'no_hpguru'          => 'nullable|string|max:20',
            'status'             => 'required|in:aktif,selesai,dibatalkan',
        ]);

        $pesertaMagang->update($validated);

        return redirect()
            ->route('admin.laporan.peserta')
            ->with('success', 'Data peserta berhasil diperbarui.');
    }

    /**
     * Hapus peserta.
     */
    public function destroy(PesertaMagang $pesertaMagang): RedirectResponse
    {
        $pesertaMagang->delete();

        return redirect()
            ->route('admin.laporan.peserta')
            ->with('success', 'Peserta berhasil dihapus.');
    }
}