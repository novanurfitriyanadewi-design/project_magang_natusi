<?php

namespace App\Http\Controllers\Admin;

use App\Exports\AbsensiKaryawanExport;
use App\Http\Controllers\Controller;
use App\Models\AbsensiKaryawan;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class AbsensiKaryawanController extends Controller
{
    public function index(Request $request)
    {
        $tanggal = $request->input('tanggal', now()->toDateString());

        $query = AbsensiKaryawan::with('karyawan')->whereDate('tanggal', $tanggal);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('karyawan', function ($q) use ($search) {
                $q->where('nama_karyawan', 'like', "%{$search}%");
            });
        }

        $absensi = $query->latest('created_at')->paginate(10)->withQueryString();

        $baseStats = AbsensiKaryawan::whereDate('tanggal', $tanggal);
        $stats = [
            'hadir' => (clone $baseStats)->where('status', 'hadir')->count(),
            'terlambat' => (clone $baseStats)
                ->whereNotNull('jam_masuk')
                ->whereTime('jam_masuk', '>', '08:00:00')
                ->count(),
            'izin_sakit' => (clone $baseStats)->whereIn('status', ['izin', 'sakit'])->count(),
            'alpha' => (clone $baseStats)->where('status', 'alpha')->count(),
        ];

        $karyawanList = Karyawan::orderBy('nama_karyawan')->get();

        return view('admin.absensi-karyawan.index', compact('absensi', 'stats', 'tanggal', 'karyawanList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_karyawan' => 'required|exists:karyawan,id_karyawan',
            'tanggal' => 'required|date',
            'jam_masuk' => 'nullable',
            'jam_pulang' => 'nullable',
            'status' => 'required|in:hadir,izin,sakit,alpha',
            'keterangan' => 'nullable|string',
        ]);

        AbsensiKaryawan::create($validated);

        return back()->with('success', 'Absensi karyawan berhasil ditambahkan.');
    }

    public function destroy(AbsensiKaryawan $absensiKaryawan)
    {
        $absensiKaryawan->delete();

        return back()->with('success', 'Absensi karyawan berhasil dihapus.');
    }

    public function export(Request $request)
    {
        $tanggal = $request->input('tanggal', now()->toDateString());
        $search = $request->input('search');

        $namaFile = 'absensi-karyawan-' . $tanggal . '.xlsx';

        return Excel::download(new AbsensiKaryawanExport($tanggal, $search), $namaFile);
    }
}