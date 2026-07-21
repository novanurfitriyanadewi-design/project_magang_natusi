<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PengumpulanTugas;
use App\Models\PesertaMagang;
use App\Models\Tugas;
use Illuminate\Http\Request;

class PengumpulanTugasController extends Controller
{
    public function index(Request $request)
    {
        // -------------------------------------------------------------------
        // 1. HITUNG STATISTIK (BENTO GRID)
        // -------------------------------------------------------------------
        $totalPeserta = PesertaMagang::count();
        
        $pesertaTrend = 12; 

        $tugasTerkumpul = PengumpulanTugas::where('status', 'Sudah Mengumpulkan')->count();

        $persentaseBerhasil = $totalPeserta > 0 
            ? round(($tugasTerkumpul / $totalPeserta) * 100, 1) 
            : 0;

        $tugasTerlambat = PengumpulanTugas::where('status', 'Terlambat')->count();

        $belumMengumpulkan = PengumpulanTugas::where('status', 'Belum Mengumpulkan')->count();

        $stats = [
            'total_peserta'       => $totalPeserta,
            'peserta_trend'       => $pesertaTrend,
            'tugas_terkumpul'     => $tugasTerkumpul,
            'persentase_berhasil' => $persentaseBerhasil,
            'tugas_terlambat'     => $tugasTerlambat,
            'belum_mengumpulkan'  => $belumMengumpulkan,
        ];

        // -------------------------------------------------------------------
        // 2. QUERY & FILTER DATA PENGUMPULAN TUGAS
        // -------------------------------------------------------------------
        $query = PengumpulanTugas::with(['peserta', 'tugas']);

        // Filter: Pencarian berdasarkan Nama Peserta
        if ($request->filled('search')) {
            $query->whereHas('peserta', function ($q) use ($request) {
                $q->where('nama_peserta', 'like', '%' . $request->search . '%');
            });
        }

        // Filter: Status Pengumpulan
        if ($request->filled('status') && $request->status !== 'Semua Status') {
            $query->where('status', $request->status);
        }

        // Filter: Tugas tertentu
        if ($request->filled('tugas_id') && $request->tugas_id !== 'Semua Tugas') {
            $query->where('tugas_id', $request->tugas_id);
        }

        // Filter: Kategori/Instansi (Universitas / SMK)
        if ($request->filled('kategori') && $request->kategori !== 'Semua Kategori') {
            $query->whereHas('peserta', function ($q) use ($request) {
                $q->where('instansi', 'like', '%' . $request->kategori . '%');
            });
        }

        // Ambil data dengan Paginasi (10 item per halaman)
        $submissions = $query->latest('dikumpulkan_pada')->paginate(10)->withQueryString();

        // -------------------------------------------------------------------
        // 3. AMBIL DATA DROP DOWN FILTER (PERBAIKAN DI SINI)
        // -------------------------------------------------------------------
        // Menggunakan Tugas::all() agar terhindar dari error nama kolom yang tidak pas
        $daftarTugas = Tugas::all();

        return view('admin.pengumpulan_tugas', compact('stats', 'submissions', 'daftarTugas'));
    }
}