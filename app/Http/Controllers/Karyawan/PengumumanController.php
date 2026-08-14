<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Models\Pengumuman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PengumumanController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $karyawan = $user->karyawan;

        $query = Pengumuman::with('pembuat')
            ->where('aktif', true)
            ->where(function ($query) use ($karyawan) {

                // Pengumuman umum
                $query->whereDoesntHave('penerima')

                    // Pengumuman khusus karyawan ini
                    ->orWhereHas('penerima', function ($q) use ($karyawan) {

                        $q->where('tipe_penerima', 'karyawan')
                            ->where(
                                'id_penerima',
                                $karyawan?->id_karyawan
                            );
                    });
            });


        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {

            $search = $request->search;

            $query->where(function ($q) use ($search) {

                $q->where('judul', 'like', '%' . $search . '%')
                    ->orWhere('isi', 'like', '%' . $search . '%');
            });
        }


        $pengumuman = $query
            ->latest()
            ->get();


        return view(
            'karyawan.pengumuman.index',
            compact('pengumuman')
        );
    }
}