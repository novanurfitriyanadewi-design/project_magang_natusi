<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Models\Cuti;
use App\Models\Notifikasi;
use App\Models\User;
use App\Services\LeaveLetterPdfService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CutiController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $karyawan = $user->karyawan;

        $riwayat = collect();
        if ($karyawan) {
            $riwayat = Cuti::where('karyawan_id', $karyawan->id_karyawan)->latest()->get();
        }

        return view('karyawan.cuti.index', compact('karyawan', 'riwayat'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $karyawan = $user->karyawan;

        abort_unless($karyawan, 403, 'Data karyawan tidak ditemukan.');

        $validated = $request->validate([
            'jenis_cuti' => ['required', 'in:tahunan,sakit,melahirkan,penting,lainnya'],
            'tanggal_mulai' => ['required', 'date', 'after_or_equal:today'],
            'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
            'alasan' => ['required', 'string', 'max:1000'],
            'bukti_pendukung' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
        ], [
            'tanggal_mulai.after_or_equal' => 'Tanggal mulai tidak boleh sebelum hari ini.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai tidak boleh sebelum tanggal mulai.',
        ]);

        $path = null;
        if ($request->hasFile('bukti_pendukung')) {
            $path = $request->file('bukti_pendukung')->store('bukti-cuti', 'public');
        }

        $cuti = Cuti::create([
            'karyawan_id' => $karyawan->id_karyawan, 
            'jenis_cuti' => $validated['jenis_cuti'],
            'tanggal_mulai' => $validated['tanggal_mulai'],
            'tanggal_selesai' => $validated['tanggal_selesai'],
            'alasan' => $validated['alasan'],
            'bukti_pendukung' => $path,
            'status' => 'pending',
        ]);

        // Notifikasi di header dihitung dari tabel notifikasi. Buat satu data
        // untuk setiap admin yang dapat memproses pengajuan cuti agar lencana
        // pada lonceng langsung muncul saat ada pengajuan baru.
        User::query()
            ->whereIn('role', ['admin', 'admin_karyawan'])
            ->pluck('id_user')
            ->each(function ($adminId) use ($cuti, $karyawan): void {
                Notifikasi::query()->create([
                    'user_id' => $adminId,
                    'judul' => 'Pengajuan Cuti Baru',
                    'pesan' => sprintf(
                        '%s mengajukan %s untuk periode %s sampai %s.',
                        $karyawan->nama_karyawan,
                        $cuti->jenis_label,
                        $cuti->tanggal_mulai->translatedFormat('d M Y'),
                        $cuti->tanggal_selesai->translatedFormat('d M Y'),
                    ),
                    'kategori' => 'umum',
                    'tipe' => 'info',
                    'referensi_id' => $cuti->getKey(),
                    'dibaca' => false,
                ]);
            });

        return back()->with('success', 'Pengajuan cuti berhasil dikirim dan menunggu persetujuan HRD.');
    }

    public function letter(Cuti $cuti, LeaveLetterPdfService $pdf)
    {
        abort_unless($cuti->karyawan?->user_id === Auth::id(), 403);

        return $pdf->download($cuti);
    }
}
