<?php

namespace App\Http\Controllers\AdminKaryawan;

use App\Http\Controllers\Controller;
use App\Models\Resign;
use Illuminate\Http\Request;

class ResignController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search', '');
        $status = $request->query('status', '');

        $query = Resign::with('karyawan');

        if ($search != '') {
            $query->whereHas('karyawan', function ($q) use ($search) {
                $q->where('nama_karyawan', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%");
            });
        }

        if ($status != '') {
            $query->where('status', $status);
        }

        $resigns = $query->latest()->paginate(10)->withQueryString();

        $totalPengajuan = Resign::count();
        $menunggu = Resign::where('status', 'pending')->count();
        $disetujui = Resign::where('status', 'disetujui')->count();
        $ditolak = Resign::where('status', 'ditolak')->count();

        return view('admin-karyawan.resign.index', compact(
            'resigns',
            'search',
            'status',
            'totalPengajuan',
            'menunggu',
            'disetujui',
            'ditolak'
        ));
    }

    public function approve(Resign $resign)
    {
        $resign->update([
            'status' => 'disetujui',
        ]);

        return redirect()
            ->route('admin-karyawan.resign.index')
            ->with('success', 'Pengajuan resign berhasil disetujui.');
    }

    public function reject(Request $request, Resign $resign)
    {
        $validated = $request->validate([
            'catatan_hrd' => 'required|string|max:500',
        ]);

        $resign->update([
            'status' => 'ditolak',
            'catatan_hrd' => $validated['catatan_hrd'],
        ]);

        return redirect()
            ->route('admin-karyawan.resign.index')
            ->with('success', 'Pengajuan resign berhasil ditolak.');
    }
}