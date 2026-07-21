<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\PesertaMagang;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DataAbsensiController extends Controller
{
    public function index(Request $request): View
    {
        $tanggal = $request->filled('tanggal') ? $request->tanggal : now()->toDateString();

        $query = PesertaMagang::query()
            ->where('status', 'aktif') // hanya peserta yang statusnya aktif magang
            ->with([
                'user',
                'permintaan',
                // hanya ambil record absensi di tanggal yang sedang difilter
                'absensi' => fn ($q) => $q->whereDate('tanggal', $tanggal),
            ]);

        // 🔍 Search nama peserta atau instansi (nama_sekolah)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', fn ($u) => $u->where('nama', 'like', "%{$search}%"))
                  ->orWhereHas('permintaan', fn ($p) => $p->where('nama_sekolah', 'like', "%{$search}%"));
            });
        }

        // 🔽 Filter status: hadir, terlambat, izin, sakit, alfa, atau belum_absen (belum ada record)
        if ($request->filled('status')) {
            $status = $request->status;

            if ($status === 'belum_absen') {
                $query->whereDoesntHave('absensi', fn ($q) => $q->whereDate('tanggal', $tanggal));
            } else {
                $query->whereHas('absensi', fn ($q) => $q->whereDate('tanggal', $tanggal)->where('status', $status));
            }
        }

        $pesertas = $query->paginate(10)->withQueryString();

        // 📊 Statistik ringkasan untuk tanggal yang aktif
        $totalPesertaAktif = PesertaMagang::where('status', 'aktif')->count();

        $totalHadir = Absensi::whereDate('tanggal', $tanggal)->where('status', 'hadir')->count();
        $totalTerlambat = Absensi::whereDate('tanggal', $tanggal)->where('status', 'terlambat')->count();
        $totalIzinSakit = Absensi::whereDate('tanggal', $tanggal)->whereIn('status', ['izin', 'sakit'])->count();
        $totalSudahAbsen = Absensi::whereDate('tanggal', $tanggal)->count();
        $totalBelumAbsen = max($totalPesertaAktif - $totalSudahAbsen, 0);

        $persenHadir = $totalPesertaAktif > 0
            ? round(($totalHadir / $totalPesertaAktif) * 100)
            : 0;

        return view('admin.absensi.index', compact(
            'pesertas',
            'tanggal',
            'totalHadir',
            'totalTerlambat',
            'totalIzinSakit',
            'totalBelumAbsen',
            'persenHadir'
        ));
    }
}
