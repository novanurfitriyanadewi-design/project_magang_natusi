<?php

namespace App\Http\Controllers\PesertaMagang;

use App\Http\Controllers\Controller;
use App\Models\Tugas;
use App\Models\PengumpulanTugas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PenugasanController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();
        $selectedMinggu = $request->get('minggu', 'all');

        $query = Tugas::with(['pengumpulanTugas' => function ($q) use ($userId) {
            $q->where('id_user', $userId);
        }]);

        if ($selectedMinggu !== 'all') {
            $query->where('minggu_ke', $selectedMinggu);
        }

        $semuaTugas = $query->orderBy('pengumpulan', 'desc')->get();

        $tugasAktif = $semuaTugas->reject(function ($tugas) {
            $pengumpulan = $tugas->pengumpulanTugas->first();
            return $pengumpulan && $pengumpulan->status_pengumpulan === 'dinilai';
        });

        $riwayatTugas = $semuaTugas->filter(function ($tugas) {
            $pengumpulan = $tugas->pengumpulanTugas->first();
            return $pengumpulan && $pengumpulan->status_pengumpulan === 'dinilai';
        });

        $selectedTugasId = $request->get('tugas_id', $tugasAktif->first()?->id_tugas);
        $detailTugas = $semuaTugas->firstWhere('id_tugas', $selectedTugasId);

        // Memanggil blade 'peserta-magang.penugasan' sesuai gambar folder
        return view('peserta-magang.penugasan', compact(
            'tugasAktif',
            'riwayatTugas',
            'detailTugas',
            'selectedMinggu'
        ));
    }

    public function store(Request $request, $id_tugas)
    {
        $request->validate([
            'file_pengumpulan' => 'nullable|file|mimes:pdf,zip,rar,docx,doc|max:25600',
            'catatan'          => 'nullable|string',
            'link_external'    => 'nullable|url',
        ]);

        $userId = Auth::id();

        $pengumpulan = PengumpulanTugas::firstOrNew([
            'id_tugas' => $id_tugas,
            'id_user'  => $userId,
        ]);

        if ($request->hasFile('file_pengumpulan')) {
            if ($pengumpulan->file_pengumpulan && Storage::disk('public')->exists($pengumpulan->file_pengumpulan)) {
                Storage::disk('public')->delete($pengumpulan->file_pengumpulan);
            }

            $path = $request->file('file_pengumpulan')->store('pengumpulan-tugas', 'public');
            $pengumpulan->file_pengumpulan = $path;
        }

        $pengumpulan->catatan = $request->catatan;
        $pengumpulan->link_external = $request->link_external;
        $pengumpulan->tanggal_dikumpul = now();
        $pengumpulan->status_pengumpulan = $request->input('action') === 'draft' ? 'draft' : 'dikumpul';
        $pengumpulan->save();

        $pesan = $pengumpulan->status_pengumpulan === 'draft' 
            ? 'Draft tugas berhasil disimpan.' 
            : 'Tugas berhasil dikumpulkan!';

        return redirect()->route('peserta-magang.penugasan.index', ['tugas_id' => $id_tugas])
            ->with('success', $pesan);
    }
}