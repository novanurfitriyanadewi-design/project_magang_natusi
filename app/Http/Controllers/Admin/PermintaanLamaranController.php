<?php

namespace App\Http\Controllers\Admin;
 
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PermintaanLamaranController extends Controller
{
    public function index(Request $request)
    {
        $total_pendaftar = DB::table('permintaan_lamaran')
            ->where('status', '!=', 'ditolak')
            ->count();

        $total_disetujui = DB::table('permintaan_lamaran')
            ->where('status', 'disetujui')
            ->count();

        $query = DB::table('permintaan_lamaran as pl')
            ->leftJoin('karyawan as k', 'k.permintaan_id', '=', 'pl.id_permintaan')
            ->where('pl.status', '!=', 'ditolak')
            ->select([
                'pl.*',
                'k.alamat',
            ]);

        $status = $request->string('status')->toString();

        if (in_array($status, ['menunggu', 'disetujui'], true)) {
            $query->where('pl.status', $status);
        }

        if ($request->filled('search')) {
            $search = trim($request->string('search')->toString());

            $query->where(function ($subQuery) use ($search) {
                $subQuery
                    ->where('pl.nama_pemohon', 'like', "%{$search}%")
                    ->orWhere('pl.email', 'like', "%{$search}%")
                    ->orWhere('pl.posisi', 'like', "%{$search}%")
                    ->orWhere('pl.no_hp', 'like', "%{$search}%");
            });
        }

        $permintaan_lamaran = $query
            ->orderByDesc('pl.created_at')
            ->orderByDesc('pl.id_permintaan')
            ->paginate(10)
            ->withQueryString();

        // DENGAN SUBFOLDER KARYAWAN (BENAR)
        return view('admin.karyawan.permintaan-lamaran', compact(
            'permintaan_lamaran',
            'total_pendaftar',
            'total_disetujui'
));
    }

    public function action(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'action' => ['required', 'in:approve,reject,accept'],
        ]);

        $pendaftar = DB::table('permintaan_lamaran')
            ->where('id_permintaan', $id)
            ->first();

        if (! $pendaftar) {
            return back()->with('error', 'Data pengajuan lamaran tidak ditemukan.');
        }

        if (($pendaftar->status ?? 'menunggu') !== 'menunggu') {
            return back()->with('error', 'Pengajuan lamaran ini sudah pernah diproses.');
        }

        $disetujui = in_array($validated['action'], ['approve', 'accept'], true);

        if (! $disetujui) {
            DB::table('permintaan_lamaran')
                ->where('id_permintaan', $id)
                ->delete();

            return back()->with(
                'success',
                "Pengajuan lamaran atas nama {$pendaftar->nama_pemohon} berhasil ditolak dan dihapus."
            );
        }

        DB::table('permintaan_lamaran')
            ->where('id_permintaan', $id)
            ->update([
                'status' => 'disetujui',
                'updated_at' => now(),
            ]);

        return back()->with(
            'success',
            "Pengajuan lamaran atas nama {$pendaftar->nama_pemohon} berhasil disetujui."
        );
    }
}