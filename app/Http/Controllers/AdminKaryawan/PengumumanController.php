<?php

namespace App\Http\Controllers\AdminKaryawan;

use App\Http\Controllers\Controller;
use App\Models\Karyawan;
use App\Models\Notifikasi;
use App\Models\Pengumuman;
use App\Models\PengumumanPenerima;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PengumumanController extends Controller
{
    public function index()
    {
        $pengumuman = Pengumuman::with([
            'penerima' => function ($query) {
                $query->where('tipe_penerima', 'karyawan')
                    ->with('karyawan');
            }
        ])
        ->where(function ($query) {
            $query->whereHas('penerima', function ($q) {
                $q->where('tipe_penerima', 'karyawan');
            })
            ->orWhereDoesntHave('penerima');
        })
        ->latest()
        ->paginate(10);

        return view(
            'admin-karyawan.pengumuman.index',
            compact('pengumuman')
        );
    }


    public function create()
    {
        $karyawan = Karyawan::where('status', 'aktif')
            ->orderBy('nama_karyawan', 'asc')
            ->get();

        return view(
            'admin-karyawan.pengumuman.create',
            compact('karyawan')
        );
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'kategori' => 'required|in:umum,penting,acara',
            'target' => 'required|in:umum,individu',
            'karyawan_id' => 'nullable|array',
            'karyawan_id.*' => 'exists:karyawan,id_karyawan',
            'isi' => 'required|string',
            'aktif' => 'nullable|boolean',
        ]);

        if (
            $validated['target'] === 'individu' &&
            empty($validated['karyawan_id'])
        ) {
            return back()
                ->withErrors([
                    'karyawan_id' => 'Silakan pilih minimal satu karyawan.'
                ])
                ->withInput();
        }

        DB::transaction(function () use ($request, $validated) {

            $pengumuman = Pengumuman::create([
                'judul' => $validated['judul'],
                'isi' => $validated['isi'],
                'kategori' => $validated['kategori'],
                'dibuat_oleh' => Auth::id(),
                'aktif' => $request->boolean('aktif'),
            ]);

            /*
            |--------------------------------------------------------------------------
            | Tentukan penerima
            |--------------------------------------------------------------------------
            */

            if ($validated['target'] === 'individu') {

                $karyawan = Karyawan::whereIn(
                    'id_karyawan',
                    $validated['karyawan_id']
                )->get();

            } else {

                $karyawan = Karyawan::where('status', 'aktif')
                    ->get();
            }


            /*
            |--------------------------------------------------------------------------
            | Simpan penerima + buat notifikasi
            |--------------------------------------------------------------------------
            */

            foreach ($karyawan as $item) {

                // Kalau individu, simpan penerima
                // Kalau umum, tidak wajib menyimpan penerima
                if ($validated['target'] === 'individu') {
                    PengumumanPenerima::create([
                        'id_pengumuman' => $pengumuman->id_pengumuman,
                        'tipe_penerima' => 'karyawan',
                        'id_penerima' => $item->id_karyawan,
                    ]);
                }


                /*
                |--------------------------------------------------------------------------
                | Buat notifikasi untuk user pemilik karyawan
                |--------------------------------------------------------------------------
                */

                if ($item->user_id) {

                    Notifikasi::create([
                        'user_id' => $item->user_id,
                        'judul' => 'Pengumuman Baru',
                        'pesan' => $pengumuman->judul,
                        'kategori' => 'pengumuman',
                        'tipe' => $pengumuman->kategori === 'penting'
                            ? 'peringatan'
                            : 'info',
                        'referensi_id' => $pengumuman->id_pengumuman,
                        'dibaca' => false,
                    ]);
                }
            }
        });

        return redirect()
            ->route('admin-karyawan.pengumuman.index')
            ->with('success', 'Pengumuman berhasil dibuat.');
    }


    public function edit(Pengumuman $pengumuman)
    {
        $karyawan = Karyawan::where('status', 'aktif')
            ->orderBy('nama_karyawan', 'asc')
            ->get();

        $selectedKaryawan = $pengumuman->penerima()
            ->where('tipe_penerima', 'karyawan')
            ->pluck('id_penerima')
            ->map(fn ($id) => (int) $id)
            ->toArray();

        $target = count($selectedKaryawan) > 0
            ? 'individu'
            : 'umum';

        return view(
            'admin-karyawan.pengumuman.edit',
            compact(
                'pengumuman',
                'karyawan',
                'selectedKaryawan',
                'target'
            )
        );
    }


    public function update(Request $request, Pengumuman $pengumuman)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'isi' => 'required|string',
            'kategori' => 'required|in:umum,penting,acara',
            'target' => 'required|in:umum,individu',
            'karyawan_id' => 'nullable|array',
            'karyawan_id.*' => 'exists:karyawan,id_karyawan',
            'aktif' => 'nullable|boolean',
        ]);

        if (
            $validated['target'] === 'individu' &&
            empty($validated['karyawan_id'])
        ) {
            return back()
                ->withErrors([
                    'karyawan_id' => 'Silakan pilih minimal satu karyawan.'
                ])
                ->withInput();
        }

        DB::transaction(function () use ($request, $validated, $pengumuman) {

            /*
            |--------------------------------------------------------------------------
            | Update pengumuman
            |--------------------------------------------------------------------------
            */

            $pengumuman->update([
                'judul' => $validated['judul'],
                'isi' => $validated['isi'],
                'kategori' => $validated['kategori'],
                'aktif' => $request->boolean('aktif'),
            ]);


            /*
            |--------------------------------------------------------------------------
            | Hapus penerima lama
            |--------------------------------------------------------------------------
            */

            $pengumuman->penerima()->delete();


            /*
            |--------------------------------------------------------------------------
            | Hapus notifikasi lama dari pengumuman ini
            |--------------------------------------------------------------------------
            */

            Notifikasi::where('kategori', 'pengumuman')
                ->where('referensi_id', $pengumuman->id_pengumuman)
                ->delete();


            /*
            |--------------------------------------------------------------------------
            | Tentukan penerima baru
            |--------------------------------------------------------------------------
            */

            if ($validated['target'] === 'individu') {

                $karyawan = Karyawan::whereIn(
                    'id_karyawan',
                    $validated['karyawan_id']
                )->get();

            } else {

                $karyawan = Karyawan::where('status', 'aktif')
                    ->get();
            }


            /*
            |--------------------------------------------------------------------------
            | Simpan penerima dan buat notifikasi
            |--------------------------------------------------------------------------
            */

            foreach ($karyawan as $item) {

                if ($validated['target'] === 'individu') {

                    PengumumanPenerima::create([
                        'id_pengumuman' => $pengumuman->id_pengumuman,
                        'tipe_penerima' => 'karyawan',
                        'id_penerima' => $item->id_karyawan,
                    ]);
                }


                if ($item->user_id) {

                    Notifikasi::create([
                        'user_id' => $item->user_id,
                        'judul' => 'Pengumuman Diperbarui',
                        'pesan' => $pengumuman->judul,
                        'kategori' => 'pengumuman',
                        'tipe' => $pengumuman->kategori === 'penting'
                            ? 'peringatan'
                            : 'info',
                        'referensi_id' => $pengumuman->id_pengumuman,
                        'dibaca' => false,
                    ]);
                }
            }
        });

        return redirect()
            ->route('admin-karyawan.pengumuman.index')
            ->with('success', 'Pengumuman berhasil diperbarui.');
    }


    public function destroy(Pengumuman $pengumuman)
    {
        DB::transaction(function () use ($pengumuman) {

            Notifikasi::where('kategori', 'pengumuman')
                ->where('referensi_id', $pengumuman->id_pengumuman)
                ->delete();

            $pengumuman->delete();
        });

        return redirect()
            ->route('admin-karyawan.pengumuman.index')
            ->with('success', 'Pengumuman berhasil dihapus.');
    }
}