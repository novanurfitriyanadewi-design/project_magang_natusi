<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PesertaMagang;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PesertaMagangController extends Controller
{
    /**
     * Daftar peserta magang.
     */
    public function index(Request $request): View
    {
        $search = $request->get('search');

        $query = PesertaMagang::with([
            'user',
            'permintaan'
        ]);

        // Search berdasarkan data user dan permintaan
        if ($search) {

            $query->where(function ($q) use ($search) {

                $q->whereHas('user', function ($user) use ($search) {

                    $user->where('nama', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");

                })

                ->orWhereHas('permintaan', function ($permintaan) use ($search) {

                    $permintaan->where('nama_pemohon', 'like', "%{$search}%")
                               ->orWhere('nama_sekolah', 'like', "%{$search}%")
                               ->orWhere('jurusan', 'like', "%{$search}%");

                });

            });

        }

        $peserta = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $stats = [

            'total' => PesertaMagang::count(),

            'aktif' => PesertaMagang::where('status', 'aktif')->count(),

            'selesai' => PesertaMagang::where('status', 'selesai')->count(),

            'dibatalkan' => PesertaMagang::where('status', 'dibatalkan')->count(),

        ];

        return view(
            'admin.peserta_magang',
            compact(
                'peserta',
                'stats',
                'search'
            )
        );
    }

    /**
     * Simpan peserta.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([

            'user_id' => 'required|exists:users,id_user',

            'permintaan_id' => 'nullable|exists:permintaan_magang,id_permintaan',

            'alamat' => 'required|string',

            'tingkat_pendidikan' => 'required|string|max:100',

            'kelas' => 'nullable|string|max:100',

            'tgl_mulai' => 'nullable|date',

            'tgl_selesai' => 'nullable|date|after_or_equal:tgl_mulai',

            'durasi_magang' => 'nullable|string|max:100',

            'nama_guru' => 'nullable|string|max:255',

            'no_hpguru' => 'nullable|string|max:20',

            'status' => 'required|in:aktif,selesai,dibatalkan',

        ]);

        PesertaMagang::create($validated);

        return redirect()
            ->route('admin.peserta.index')
            ->with('success', 'Peserta magang berhasil ditambahkan.');
    }

    /**
     * Detail peserta.
     */
    public function show(PesertaMagang $pesertaMagang): View
    {
        $pesertaMagang->load([
            'user',
            'permintaan',
            'absensi',
            'laporanMingguan',
            'pembayaran',
            'pengumpulanTugas'
        ]);

        return view(
            'admin.peserta.show',
            compact('pesertaMagang')
        );
    }

    /**
     * Update peserta.
     */
    public function update(Request $request, PesertaMagang $pesertaMagang): RedirectResponse
    {
        $validated = $request->validate([

            'alamat' => 'required|string',

            'tingkat_pendidikan' => 'required|string|max:100',

            'kelas' => 'nullable|string|max:100',

            'tgl_mulai' => 'nullable|date',

            'tgl_selesai' => 'nullable|date|after_or_equal:tgl_mulai',

            'durasi_magang' => 'nullable|string|max:100',

            'nama_guru' => 'nullable|string|max:255',

            'no_hpguru' => 'nullable|string|max:20',

            'status' => 'required|in:aktif,selesai,dibatalkan',

        ]);

        $pesertaMagang->update($validated);

        return redirect()
            ->route('admin.peserta.index')
            ->with('success', 'Data peserta berhasil diperbarui.');
    }

    /**
     * Hapus peserta.
     */
    public function destroy(PesertaMagang $pesertaMagang): RedirectResponse
    {
        $pesertaMagang->delete();

        return redirect()
            ->route('admin.peserta.index')
            ->with('success', 'Peserta berhasil dihapus.');
    }
}