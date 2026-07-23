<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
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

        $total_interview = DB::table('permintaan_lamaran')
            ->where('status', 'interview')
            ->count();

        $query = DB::table('permintaan_lamaran as pl')
            ->leftJoin('karyawan as k', 'k.permintaan_id', '=', 'pl.id_permintaan')
            ->where('pl.status', '!=', 'ditolak')
            ->select([
                'pl.*',
                'k.alamat',
            ]);

        $status = $request->string('status')->toString();

        if (in_array($status, ['menunggu', 'interview', 'disetujui'], true)) {
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

        return view('admin.karyawan.permintaan-lamaran', compact(
            'permintaan_lamaran',
            'total_pendaftar',
            'total_disetujui',
            'total_interview'
        ));
    }

    public function action(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'action' => ['required', 'in:interview,approve,reject,accept'],
            'jadwal_interview' => ['required_if:action,interview', 'nullable', 'date'],
            'lokasi_interview' => ['required_if:action,interview', 'nullable', 'string', 'max:255'],
        ]);

        $pendaftar = DB::table('permintaan_lamaran')
            ->where('id_permintaan', $id)
            ->first();

        if (! $pendaftar) {
            return back()->with('error', 'Data pengajuan lamaran tidak ditemukan.');
        }

        // Yang sudah final (disetujui/ditolak) tidak boleh diproses ulang.
        // Status 'menunggu' maupun 'interview' masih boleh lanjut diproses.
        if (in_array($pendaftar->status ?? 'menunggu', ['disetujui', 'ditolak'], true)) {
            return back()->with('error', 'Pengajuan lamaran ini sudah pernah diproses.');
        }

        // Cari user terkait berdasarkan email
        $user = DB::table('users')->where('email', $pendaftar->email)->first();

        // ==========================================
        // 0. JIKA DIJADWALKAN INTERVIEW
        // ==========================================
        if ($validated['action'] === 'interview') {
            DB::transaction(function () use ($id, $user, $validated) {
                DB::table('permintaan_lamaran')
                    ->where('id_permintaan', $id)
                    ->update([
                        'status' => 'interview',
                        'jadwal_interview' => $validated['jadwal_interview'],
                        'lokasi_interview' => $validated['lokasi_interview'],
                        'updated_at' => now(),
                    ]);

                if ($user) {
                    $jadwal = Carbon::parse($validated['jadwal_interview'])->translatedFormat('d M Y, H:i');

                    DB::table('notifikasi')->insert([
                        'user_id'     => $user->id_user,
                        'judul'       => 'Jadwal Interview Lamaran Karyawan',
                        'pesan'       => "Anda diundang untuk interview pada {$jadwal} di {$validated['lokasi_interview']}. Mohon datang tepat waktu.",
                        'kategori'    => 'pengajuan',
                        'tipe'        => 'info',
                        'referensi_id'=> $id,
                        'dibaca'      => false,
                        'created_at'  => now(),
                        'updated_at'  => now(),
                    ]);
                }
            });

            return back()->with(
                'success',
                "Jadwal interview untuk {$pendaftar->nama_pemohon} berhasil dikirim."
            );
        }

        $disetujui = in_array($validated['action'], ['approve', 'accept'], true);

        // ==========================================
        // 1. JIKA LAMARAN DITOLAK
        // ==========================================
        if (! $disetujui) {
            DB::transaction(function () use ($id, $user) {
                DB::table('permintaan_lamaran')
                    ->where('id_permintaan', $id)
                    ->update([
                        'status' => 'ditolak',
                        'updated_at' => now(),
                    ]);

                if ($user) {
                    DB::table('notifikasi')->insert([
                        'user_id'     => $user->id_user,
                        'judul'       => 'Status Lamaran Karyawan',
                        'pesan'       => 'Mohon maaf, pengajuan lamaran karyawan Anda belum dapat kami terima saat ini.',
                        'kategori'    => 'pengajuan',
                        'tipe'        => 'peringatan',
                        'referensi_id'=> $id,
                        'dibaca'      => false,
                        'created_at'  => now(),
                        'updated_at'  => now(),
                    ]);
                }
            });

            return back()->with(
                'success',
                "Pengajuan lamaran atas nama {$pendaftar->nama_pemohon} berhasil ditolak."
            );
        }

        // ==========================================
        // 2. JIKA LAMARAN DISETUJUI
        // ==========================================
        DB::transaction(function () use ($id, $pendaftar, $user) {
            DB::table('permintaan_lamaran')
                ->where('id_permintaan', $id)
                ->update([
                    'status'     => 'disetujui',
                    'updated_at' => now(),
                ]);

            if ($user) {
                // Ubah role akun user menjadi 'karyawan' resmi.
                DB::table('users')
                    ->where('id_user', $user->id_user)
                    ->update([
                        'role'       => 'karyawan',
                        'updated_at' => now(),
                    ]);

                $karyawanExists = DB::table('karyawan')
                    ->where('permintaan_id', $id)
                    ->orWhere('user_id', $user->id_user)
                    ->exists();

                if (! $karyawanExists) {
                    DB::table('karyawan')->insert([
                        'user_id'       => $user->id_user,
                        'permintaan_id' => $id,
                        'nama_karyawan' => $pendaftar->nama_pemohon,
                        'email'         => $pendaftar->email,
                        'no_hp'         => $pendaftar->no_hp ?? null,
                        'jabatan'       => $pendaftar->posisi ?? null,
                        'status'        => 'aktif',
                        'created_at'    => now(),
                        'updated_at'    => now(),
                    ]);
                }

                DB::table('notifikasi')->insert([
                    'user_id'     => $user->id_user,
                    'judul'       => 'Selamat! Lamaran Karyawan Disetujui',
                    'pesan'       => 'Pengajuan lamaran Anda telah disetujui. Anda sekarang sudah aktif sebagai karyawan dan dapat mengakses Portal Internal.',
                    'kategori'    => 'pengajuan',
                    'tipe'        => 'sukses',
                    'referensi_id'=> $id,
                    'dibaca'      => false,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);
            }
        });

        return back()->with(
            'success',
            "Pengajuan lamaran atas nama {$pendaftar->nama_pemohon} berhasil disetujui dan role user resmi diubah menjadi Karyawan."
        );
    }
}