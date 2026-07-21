<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Absensi; // Pastikan nama Model Absensi kamu sudah sesuai
use Illuminate\Http\Request;

class AbsensiController extends Controller

{
    //public function index(Request $request)
    {
        // 1. Ambil input filter dari halaman browser
        $search = $request->input('search');
        $date = $request->input('date');
        $status = $request->input('status');

        // 2. Query data absensi dengan memuat relasi data peserta/user
        // Jalankan filter jika input pencarian diisi oleh admin
        $query = Absensi::with('user'); 

        if ($search) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('instansi', 'like', '%' . $search . '%');
            });
        }

        if ($date) {
            $query->whereDate('created_at', $date);
        }

        if ($status) {
            $query->where('status', $status);
        }

        // 3. Ambil data hasil filter dengan pagination (misal 10 data per halaman)
        $absensi = $query->latest()->paginate(10)->withQueryString();

        // 4. Kirim data hasil filter ke dalam view blade
        return view('admin.absensi', compact('absensi'));
    }
}
