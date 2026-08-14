<?php

namespace App\Http\Controllers\AdminKaryawan;

use App\Http\Controllers\Controller;
use App\Models\Karyawan;
use App\Models\Notifikasi;
use App\Models\Pengumuman;
use App\Models\PengumumanPenerima;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        // Pengumuman untuk karyawan
        $query->whereHas('penerima', function ($q) {
            $q->where('tipe_penerima', 'karyawan');
        })

        // Pengumuman umum
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

    // Jika memilih individu, wajib pilih minimal 1 karyawan
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

    // Buat pengumuman
    $pengumuman = Pengumuman::create([
        'judul' => $validated['judul'],
        'isi' => $validated['isi'],
        'kategori' => $validated['kategori'],
        'dibuat_oleh' => Auth::id(),
        'aktif' => $request->has('aktif') ? $request->boolean('aktif') : true, // Default true jika tidak ada input
    ]);

    // Ambil daftar karyawan penerima
    $penerimaBanyak = [];
    
    if ($validated['target'] === 'individu') {
        // Target individu: gunakan karyawan yang dipilih
        $penerimaBanyak = Karyawan::whereIn('id_karyawan', $validated['karyawan_id'])
            ->get();

        foreach ($validated['karyawan_id'] as $karyawanId) {
            PengumumanPenerima::create([
                'id_pengumuman' => $pengumuman->id_pengumuman,
                'tipe_penerima' => 'karyawan',
                'id_penerima' => $karyawanId,
            ]);
        }
    } else {
        // Target umum: gunakan semua karyawan aktif
        $penerimaBanyak = Karyawan::where('status', 'aktif')->get();
    }

    // Buat notifikasi untuk setiap penerima
    foreach ($penerimaBanyak as $karyawan) {
        if ($karyawan->user) {
            Notifikasi::create([
                'user_id' => $karyawan->user->id_user,
                'judul' => $pengumuman->judul,
                'pesan' => 'Pengumuman baru: ' . $pengumuman->judul,
                // `tipe` menentukan tampilan notifikasi dan hanya menerima
                // info, peringatan, atau sukses. Pengumuman dibedakan lewat
                // kategori agar record-nya tersimpan dan dibaca oleh lonceng.
                'kategori' => 'pengumuman',
                'tipe' => 'info',
                'referensi_id' => $pengumuman->id_pengumuman,
                'dibaca' => false,
            ]);
        }
    }

    return redirect()
        ->route('admin-karyawan.pengumuman.index')
        ->with('success', 'Pengumuman berhasil dibuat.');
}


public function edit(Pengumuman $pengumuman)
{
    $karyawan = Karyawan::where('status', 'aktif')
        ->orderBy('nama_karyawan', 'asc')
        ->get();

    // Ambil semua karyawan yang sudah menjadi penerima
    $selectedKaryawan = $pengumuman->penerima()
        ->where('tipe_penerima', 'karyawan')
        ->pluck('id_penerima')
        ->map(fn ($id) => (int) $id)
        ->toArray();

    // Kalau ada penerima berarti individu,
    // kalau tidak ada berarti semua karyawan
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
        ]);


        /*
        |--------------------------------------------------------------------------
        | Target individu wajib memilih karyawan
        |--------------------------------------------------------------------------
        */

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


        /*
        |--------------------------------------------------------------------------
        | Update pengumuman
        |--------------------------------------------------------------------------
        */

        $pengumuman->update([
            'judul' => $validated['judul'],
            'isi' => $validated['isi'],
            'kategori' => $validated['kategori'],
        ]);


        /*
        |--------------------------------------------------------------------------
        | Hapus penerima lama dan notifikasi lama
        |--------------------------------------------------------------------------
        */

        $pengumuman->penerima()->delete();
        // Hapus notifikasi lama
        Notifikasi::where('referensi_id', $pengumuman->id_pengumuman)
            ->where('kategori', 'pengumuman')
            ->delete();


        /*
        |--------------------------------------------------------------------------
        | Tambahkan penerima baru dan buat notifikasi baru
        |--------------------------------------------------------------------------
        */

        $penerimaBanyak = [];

        if ($validated['target'] === 'umum') {
            // Target umum: gunakan semua karyawan aktif
            $penerimaBanyak = Karyawan::where('status', 'aktif')->get();
        } else {
            // Target individu: gunakan karyawan yang dipilih
            $penerimaBanyak = Karyawan::whereIn('id_karyawan', $validated['karyawan_id'])
                ->get();

            foreach ($validated['karyawan_id'] as $karyawanId) {
                PengumumanPenerima::create([
                    'id_pengumuman' => $pengumuman->id_pengumuman,
                    'tipe_penerima' => 'karyawan',
                    'id_penerima' => $karyawanId,
                ]);
            }
        }

        // Buat notifikasi untuk setiap penerima
        foreach ($penerimaBanyak as $karyawan) {
            if ($karyawan->user) {
                Notifikasi::create([
                    'user_id' => $karyawan->user->id_user,
                    'judul' => $pengumuman->judul,
                    'pesan' => 'Pengumuman diperbarui: ' . $pengumuman->judul,
                    'kategori' => 'pengumuman',
                    'tipe' => 'info',
                    'referensi_id' => $pengumuman->id_pengumuman,
                    'dibaca' => false,
                ]);
            }
        }


        return redirect()
            ->route('admin-karyawan.pengumuman.index')
            ->with('success', 'Pengumuman berhasil diperbarui.');
    }


    public function destroy(Pengumuman $pengumuman)
    {
        $pengumuman->delete();

        return redirect()
            ->route('admin-karyawan.pengumuman.index')
            ->with('success', 'Pengumuman berhasil dihapus.');
    }
}
