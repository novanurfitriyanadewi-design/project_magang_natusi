<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PermintaanMagangController extends Controller
{
    public function index(Request $request)
    {
        $total_pendaftar = DB::table('permintaan_magang')
            ->where('status', '!=', 'ditolak')
            ->count();

        $total_disetujui = DB::table('permintaan_magang')
            ->where('status', 'disetujui')
            ->count();

        $query = DB::table('permintaan_magang as pm')
            ->leftJoin('peserta_magang as ps', 'ps.permintaan_id', '=', 'pm.id_permintaan')
            ->where('pm.status', '!=', 'ditolak')
            ->select([
                'pm.*',
                'ps.alamat',
            ]);

        $status = $request->string('status')->toString();

        if (in_array($status, ['menunggu', 'disetujui'], true)) {
            $query->where('pm.status', $status);
        }

        if ($request->filled('search')) {
            $search = trim($request->string('search')->toString());

            $query->where(function ($subQuery) use ($search) {
                $subQuery
                    ->where('pm.nama_pemohon', 'like', "%{$search}%")
                    ->orWhere('pm.email', 'like', "%{$search}%")
                    ->orWhere('pm.nama_sekolah', 'like', "%{$search}%")
                    ->orWhere('pm.no_induk', 'like', "%{$search}%")
                    ->orWhere('pm.jurusan', 'like', "%{$search}%")
                    ->orWhere('pm.no_hp', 'like', "%{$search}%");
            });
        }

        $permintaan_magang = $query
            ->orderByDesc('pm.created_at')
            ->orderByDesc('pm.id_permintaan')
            ->paginate(10)
            ->withQueryString();

        return view('admin.permintaan-magang', compact(
            'permintaan_magang',
            'total_pendaftar',
            'total_disetujui'
        ));
    }

    public function action(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'action' => ['required', 'in:approve,reject,accept'],
        ]);

        $pendaftar = DB::table('permintaan_magang')
            ->where('id_permintaan', $id)
            ->first();

        if (! $pendaftar) {
            return back()->with('error', 'Data pengajuan magang tidak ditemukan.');
        }

        if (($pendaftar->status ?? 'menunggu') !== 'menunggu') {
            return back()->with('error', 'Pengajuan magang ini sudah pernah diproses.');
        }

        $disetujui = in_array($validated['action'], ['approve', 'accept'], true);

        if (! $disetujui) {
            DB::table('permintaan_magang')
                ->where('id_permintaan', $id)
                ->delete();

            return back()->with(
                'success',
                "Pengajuan magang atas nama {$pendaftar->nama_pemohon} berhasil ditolak dan dihapus."
            );
        }

        DB::table('permintaan_magang')
            ->where('id_permintaan', $id)
            ->update([
                'status' => 'disetujui',
                'updated_at' => now(),
            ]);

        return back()->with(
            'success',
            "Pengajuan magang atas nama {$pendaftar->nama_pemohon} berhasil disetujui."
        );
    }
}
