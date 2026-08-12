<?php

namespace App\Http\Controllers\AdminKaryawan;

use App\Exports\AbsensiKaryawanExport;
use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class AbsensiKaryawanController extends Controller
{
    public function index(Request $request)
    {
        $tanggal = $request->input('tanggal', now()->toDateString());

        $query = Absensi::with('absentable')
            ->where('absentable_type', Karyawan::class)
            ->whereDate('tanggal', $tanggal);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('absentable', function ($q) use ($search) {
                $q->where('nama_karyawan', 'like', "%{$search}%");
            });
        }

        $absensi = $query->latest('created_at')->paginate(10)->withQueryString();

        // Data mentah hari itu buat hitung statistik (termasuk yang kefilter search biar kartu tetap akurat)
        $absensiHariIni = Absensi::where('absentable_type', Karyawan::class)
            ->whereDate('tanggal', $tanggal)
            ->get();

        $totalHadir = $absensiHariIni->where('status', 'hadir')->count();
        $totalIzinSakit = $absensiHariIni->whereIn('status', ['izin', 'sakit'])->count();

        $totalTerlambat = $absensiHariIni->filter(function ($item) {
            if (!$item->jam_masuk) return false;
            return Carbon::parse($item->jam_masuk)->format('H:i:s') > '08:15:00';
        })->count();

        $totalKaryawanAktif = Karyawan::where('status', 'aktif')->count();
        $totalAlpha = max($totalKaryawanAktif - ($totalHadir + $totalIzinSakit), 0);

        $stats = [
            'hadir'      => $totalHadir,
            'terlambat'  => $totalTerlambat,
            'izin_sakit' => $totalIzinSakit,
            'alpha'      => $totalAlpha,
        ];

        return view('admin-karyawan.absensi-karyawan.index', compact('absensi', 'stats', 'tanggal'));
    }

    public function show(Absensi $absensiKaryawan)
    {
        $absensiKaryawan->load('absentable');
        return view('admin-karyawan.absensi-karyawan.show', compact('absensiKaryawan'));
    }

    public function edit(Absensi $absensiKaryawan)
    {
        $absensiKaryawan->load('absentable');
        return view('admin-karyawan.absensi-karyawan.edit', compact('absensiKaryawan'));
    }

    public function update(Request $request, Absensi $absensiKaryawan)
    {
        $validated = $request->validate([
            'tanggal'    => 'required|date',
            'jam_masuk'  => 'nullable',
            'jam_keluar' => 'nullable',
            'status'     => 'required|in:hadir,izin,sakit,alpha',
            'keterangan' => 'nullable|string',
        ]);

        $absensiKaryawan->update($validated);

        return redirect()->route('admin-karyawan.absensi-karyawan.index')
            ->with('success', 'Absensi karyawan berhasil diperbarui.');
    }

    public function destroy(Absensi $absensiKaryawan)
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