<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Resign;
use Illuminate\Http\Request;

class ResignController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));
        $status = $request->query('status', '');

        $query = Resign::with('karyawan');

        if ($search !== '') {
            $query->whereHas('karyawan', function ($q) use ($search) {
                $q->where('nama_karyawan', 'like', "%{$search}%")
                    ->orWhere('nip', 'like', "%{$search}%");
            });
        }

        if ($status !== '') {
            $query->where('status', $status);
        }

        $pengajuan = $query->latest()->paginate(10)->withQueryString();

        $totalPengajuan = Resign::count();
        $menunggu = Resign::where('status', 'pending')->count();
        $disetujui = Resign::where('status', 'disetujui')->count();
        $ditolak = Resign::where('status', 'ditolak')->count();

        return view('admin.resign.index', [
            'pengajuan' => $pengajuan,
            'search' => $search,
            'status' => $status,
            'totalPengajuan' => $totalPengajuan,
            'menunggu' => $menunggu,
            'disetujui' => $disetujui,
            'ditolak' => $ditolak,
        ]);
    }

    public function setujui(Resign $resign)
    {
        $resign->update([
            'status' => 'disetujui',
            'catatan_hrd' => 'Pengajuan resign disetujui HRD.',
        ]);

        return back()->with('success', 'Pengajuan resign berhasil disetujui.');
    }

    public function tolak(Request $request, Resign $resign)
    {
        $validated = $request->validate([
            'catatan_hrd' => 'required|string|max:1000',
        ]);

        $resign->update([
            'status' => 'ditolak',
            'catatan_hrd' => $validated['catatan_hrd'],
        ]);

        return back()->with('success', 'Pengajuan resign berhasil ditolak.');
    }
}
