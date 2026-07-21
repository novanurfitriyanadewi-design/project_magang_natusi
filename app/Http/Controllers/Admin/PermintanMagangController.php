<?php

namespace App\Http\Controllers\Admin; // <--- PASTIKAN BARIS INI ADA DAN TULISANNYA SAMA PERSIS

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
// ... kode fungsi index dan handleAction kemarin ...

class PermintaanMagangController extends Controller
{
    // Fungsi Utama untuk Tampilan & Filter
    public function index(Request $request)
    {
        // 1. Ambil hitungan metrik langsung dari database
        $total_pendaftar = PermintaanMagangController::count();
        $total_menunggu   = PermintaanMagangController::where('status', 'menunggu')->count();
        $total_diterima   = PermintaanMagangController::where('status', 'diterima')->count();

        // 2. Filter data tabel berdasarkan tombol status yang diklik
        $query = PermintaanMagangController::query();

        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // 3. Ambil data dengan pagination bawaan laravel (misal 10 data per halaman)
        $permintaan_magang = $query->latest()->paginate(10);

        // Kirim semua variabel ke view blade
        return view('admin.permintaan-magang', compact(
            'permintaan_magang', 
            'total_pendaftar', 
            'total_menunggu', 
            'total_diterima'
        ));
    }

    // Fungsi Pengubah Akses (Ketika tombol Terima / Tolak diklik)
    public function handleAction(Request $request, $id)
    {
        $request->validate([
            'action' => 'required|in:accept,reject'
        ]);

        // Cari data pendaftar di database berdasarkan ID
        $pendaftar = PermintaanMagang::findOrFail($id);

        if ($request->action === 'accept') {
            $pendaftar->status = 'diterima';
            $message = "Akses magang {$pendaftar->nama} berhasil DITERIMA.";
        } else {
            $pendaftar->status = 'ditolak';
            $message = "Akses magang {$pendaftar->nama} telah DITOLAK.";
        }

        // Simpan perubahan ke database
        $pendaftar->save();

        // Kembalikan ke halaman sebelumnya dengan pesan sukses hijau di atas
        return redirect()->back()->with('success', $message);
    }
}