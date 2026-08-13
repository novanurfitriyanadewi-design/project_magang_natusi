<?php

namespace App\Http\Controllers\AdminKaryawan;

use App\Http\Controllers\Controller;
use App\Models\Cuti;
use App\Models\Notifikasi;
use Illuminate\Http\Request;

class CutiController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search', '');
        $status = $request->query('status', '');

        $query = Cuti::with('karyawan');

        if ($search != '') {
            $query->whereHas('karyawan', function ($q) use ($search) {
                $q->where('nama_karyawan', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%");
            });
        }

        if ($status != '') {
            $query->where('status', $status);
        }

        $cutis = $query->latest()->paginate(10)->withQueryString();

        $totalPengajuan = Cuti::count();
        $menunggu = Cuti::where('status', 'pending')->count();
        $disetujui = Cuti::where('status', 'disetujui')->count();
        $ditolak = Cuti::where('status', 'ditolak')->count();

        return view('admin-karyawan.cuti.index', compact(
            'cutis', 'search', 'status', 'totalPengajuan', 'menunggu', 'disetujui', 'ditolak'
        ));
    }

    public function approve(Cuti $cuti)
    {
        $cuti->update(['status' => 'disetujui']);

        $karyawan = $cuti->karyawan;
        if ($karyawan && $karyawan->user_id) {
            Notifikasi::create([
                'user_id' => $karyawan->user_id,
                'judul' => 'Pengajuan Cuti Disetujui',
                'pesan' => sprintf(
                    'Pengajuan cuti Anda (%s - %s) telah disetujui.',
                    $cuti->tanggal_mulai->translatedFormat('d M Y'),
                    $cuti->tanggal_selesai->translatedFormat('d M Y')
                ),
                'kategori' => 'cuti',
                'tipe' => 'sukses',
                'referensi_id' => $cuti->id,
            ]);
        }

        return redirect()->route('admin-karyawan.cuti.index')->with('success', 'Pengajuan cuti berhasil disetujui.');
    }

    public function reject(Request $request, Cuti $cuti)
    {
        $validated = $request->validate([
            'catatan_hrd' => 'required|string|max:500',
        ]);

        $cuti->update([
            'status' => 'ditolak',
            'catatan_hrd' => $validated['catatan_hrd'],
        ]);

        $karyawan = $cuti->karyawan;
        if ($karyawan && $karyawan->user_id) {
            Notifikasi::create([
                'user_id' => $karyawan->user_id,
                'judul' => 'Pengajuan Cuti Ditolak',
                'pesan' => 'Pengajuan cuti Anda ditolak. Alasan: ' . $validated['catatan_hrd'],
                'kategori' => 'cuti',
                'tipe' => 'peringatan',
                'referensi_id' => $cuti->id,
            ]);
        }

        return redirect()->route('admin-karyawan.cuti.index')->with('success', 'Pengajuan cuti berhasil ditolak.');
    }
}