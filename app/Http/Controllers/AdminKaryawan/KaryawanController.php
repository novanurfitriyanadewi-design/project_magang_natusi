<?php

namespace App\Http\Controllers\AdminKaryawan;

use App\Http\Controllers\Controller;
use App\Models\Karyawan;
use App\Models\Divisi;
use Illuminate\Http\Request;

class KaryawanController extends Controller
{
    public function index(Request $request)
    {
        $query = Karyawan::query()->with('divisi');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('divisi_id')) {
            $query->where('divisi_id', $request->divisi_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_karyawan', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        match ($request->get('urutan', 'nama_asc')) {
            'nama_desc' => $query->orderByDesc('nama_karyawan'),
            'nip'       => $query->orderBy('nip'),
            'tanggal'   => $query->orderBy('tanggal_bergabung'),
            default     => $query->orderBy('nama_karyawan'),
        };

        $karyawans = $query->paginate(10)->withQueryString();

        $totalKaryawan        = Karyawan::count();
        $karyawanAktif        = Karyawan::where('status', 'aktif')->count();
        $karyawanNonAktif     = Karyawan::where('status', 'nonaktif')->count();
        $karyawanBaruBulanIni = Karyawan::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $divisiList = Divisi::orderBy('nama_divisi')->get();

        return view('admin-karyawan.karyawan.index', compact(
            'karyawans', 'totalKaryawan', 'karyawanAktif',
            'karyawanNonAktif', 'karyawanBaruBulanIni', 'divisiList'
        ));
    }

    public function update(Request $request, Karyawan $karyawan)
    {
        $validated = $request->validate([
            'nama_karyawan'     => ['required', 'string', 'max:255'],
            'email'             => ['required', 'email', 'max:255', 'unique:karyawan,email,' . $karyawan->id_karyawan . ',id_karyawan'],
            'nip'               => ['nullable', 'string', 'max:50', 'unique:karyawan,nip,' . $karyawan->id_karyawan . ',id_karyawan'],
            'no_hp'             => ['nullable', 'string', 'max:20'],
            'alamat'            => ['nullable', 'string'],
            'jabatan'           => ['nullable', 'string', 'max:255'],
            'status'            => ['required', 'in:aktif,nonaktif'],
            'tanggal_bergabung' => ['nullable', 'date'],
            'divisi_id'         => ['nullable', 'exists:divisi,id_divisi'],
        ]);

        $karyawan->update($validated);

        return back()->with('success', 'Data karyawan berhasil diperbarui.');
    }
}