<?php

namespace App\Http\Controllers\AdminKaryawan;

use App\Http\Controllers\Controller;
use App\Models\Karyawan;
use App\Models\Divisi;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class KaryawanController extends Controller
{
    public function index(Request $request)
    {
        // 1. Statistik (dihitung dari SELURUH data, tidak terpengaruh filter)
        $totalKaryawan = Karyawan::count();

        $karyawanAktif = Karyawan::whereRaw('LOWER(status) = ?', ['aktif'])->count();

        $karyawanNonAktif = $totalKaryawan - $karyawanAktif;

        $karyawanBaruBulanIni = Karyawan::whereMonth('tanggal_bergabung', now()->month)
            ->whereYear('tanggal_bergabung', now()->year)
            ->count();

        // 2. Query data karyawan dengan relasinya
        $query = Karyawan::query()
            ->with([
                'divisi',
                'permintaanLamaran',
            ]);

        // 3. Filter Pencarian Teks
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_karyawan', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%")
                  ->orWhere('jabatan', 'like', "%{$search}%");
            });
        }

        // 4. Filter berdasarkan Divisi (Dropdown Filter)
        if ($divisiId = $request->input('divisi_id')) {
            $query->where('divisi_id', $divisiId);
        }

        // 5. Filter berdasarkan Status (sebelumnya tidak diterapkan sama sekali)
        if ($status = $request->input('status')) {
            if ($status === 'aktif') {
                $query->whereRaw('LOWER(status) = ?', ['aktif']);
            } elseif ($status === 'nonaktif') {
                $query->whereRaw('LOWER(status) != ?', ['aktif']);
            }
        }

        // 6. Sorting Data — key form adalah "urutan", bukan "sort"
        switch ($request->input('urutan', 'nama_asc')) {
            case 'nama_desc':
                $query->orderByDesc('nama_karyawan');
                break;

            case 'nip':
                $query->orderBy('nip');
                break;

            case 'tanggal':
                $query->orderByDesc('tanggal_bergabung');
                break;

            case 'nama_asc':
            default:
                $query->orderBy('nama_karyawan');
                break;
        }

        // 7. Paginasi Database
        $karyawans = $query
            ->paginate(10)
            ->withQueryString();

//8. maping alpjs

$karyawans->getCollection()->transform(function ($item) {
    // 💡 Tambahkan field pendukung untuk AlpineJS Modal
    $item->nama_divisi = optional($item->divisi)->nama_divisi ?? '-';
    $item->action = route('admin-karyawan.karyawan.update', $item->id_karyawan ?? $item->id); // sesuaikan route update Anda

    $item->berkas = [
        [
            'key'   => 'surat_lamaran',
            'label' => 'Surat Lamaran Kerja',
            'icon'  => 'document',
            'url'   => optional($item->permintaanLamaran)->surat_lamaran_path ? asset('storage/' . $item->permintaanLamaran->surat_lamaran_path) : null,
        ],
        [
            'key'   => 'cv',
            'label' => 'CV (Curriculum Vitae)',
            'icon'  => 'document',
            'url'   => optional($item->permintaanLamaran)->cv_path ? asset('storage/' . $item->permintaanLamaran->cv_path) : null,
        ],
        [
            'key'   => 'ijazah',
            'label' => 'Ijazah & Transkrip Nilai',
            'icon'  => 'document',
            'url'   => optional($item->permintaanLamaran)->ijazah_path ? asset('storage/' . $item->permintaanLamaran->ijazah_path) : null,
        ],
        [
            'key'   => 'ktp',
            'label' => 'Fotokopi KTP',
            'icon'  => 'document',
            'url'   => optional($item->permintaanLamaran)->ktp_path ? asset('storage/' . $item->permintaanLamaran->ktp_path) : null,
        ],
    ];
    
    return $item;
});

        // 9. Ambil semua list divisi untuk kebutuhan dropdown filter di Blade
        $divisiList = Divisi::all();

        // 10. Kirim semua variabel ke view
        return view('admin-karyawan.karyawan.index', compact(
            'karyawans',
            'divisiList',
            'totalKaryawan',
            'karyawanAktif',
            'karyawanNonAktif',
            'karyawanBaruBulanIni'
        ));
    }

public function update(Request $request, $id)
{
    // Validasi
    $request->validate([
        'nama_karyawan' => 'required|string|max:255',
        'email'         => "required|email|max:255|unique:users,email,{$id},id_user",
        'no_hp'         => 'nullable|string|max:20',
        'jabatan'       => 'nullable|string|max:100',
        'divisi_id'     => 'nullable',
        'status'        => 'required|string',
        'alamat'        => 'nullable|string',
    ]);

    // Cari Karyawan
    $karyawan = \App\Models\User::where('id_user', $id)->firstOrFail(); 

    // Eksekusi Update ke Database
    $karyawan->update([
        'nama'              => $request->nama_karyawan, // mencocokkan input nama_karyawan ke kolom 'nama'
        'email'             => $request->email,
        'nik'               => $request->nik,
        'no_hp'             => $request->no_hp,
        'jabatan'           => $request->jabatan,
        'divisi_id'         => $request->divisi_id ?: null, // set null jika divisi kosong
        'status'            => $request->status,
        'tanggal_bergabung' => $request->tanggal_bergabung,
        'alamat'            => $request->alamat,
    ]);

    return redirect()->back()->with('success', 'Data karyawan berhasil diperbarui!');
}

}