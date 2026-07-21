<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PermintaanMagangController extends Controller
{
    public function index(Request $request)
    {
        $total_pendaftar = DB::table('permintaan_magang')->count();
        $total_menunggu   = DB::table('permintaan_magang')->where('status', 'menunggu')->count();
        $total_diterima   = DB::table('permintaan_magang')->where('status', 'diterima')->count();

        $query = DB::table('permintaan_magang');

        if ($request->has('status') && $request->input('status') !== 'all') {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('institusi', 'like', "%{$search}%");
            });
        }

        $permintaan_magang = $query->orderBy('id', 'desc')->paginate(10);

        return view('admin.permintaan-magang', compact(
            'permintaan_magang',
            'total_pendaftar',
            'total_menunggu',
            'total_diterima'
        ));
    }

    public function action(Request $request, int $id)
    {
        $action = $request->input('action');
        $pendaftar = DB::table('permintaan_magang')->where('id', $id)->first();

        if (! $pendaftar) {
            return redirect()->back()->with('error', 'Data pendaftar tidak ditemukan.');
        }

        $statusBaru = ($action === 'accept') ? 'diterima' : 'ditolak';
        $pesanText  = ($action === 'accept') ? 'DITERIMA' : 'DITOLAK';

        DB::table('permintaan_magang')
            ->where('id', $id)
            ->update(['status' => $statusBaru]);

        return redirect()->back()->with('success', "Akses pendaftaran {$pendaftar->nama} berhasil di-{$pesanText}.");
    }
}