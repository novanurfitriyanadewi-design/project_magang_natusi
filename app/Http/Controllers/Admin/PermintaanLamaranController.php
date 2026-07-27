<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use App\Models\Karyawan;
use App\Models\PermintaanLamaran;
use App\Models\User;
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
            'jadwal_interview' => ['required_if:action,interview', 'nullable', 'date', 'after_or_equal:today'],
            'lokasi_interview' => ['required_if:action,interview', 'nullable', 'string', 'max:255'],
            'alasan_penolakan' => ['required_if:action,reject', 'nullable', 'string', 'max:2000'],
        ], [
            'jadwal_interview.after_or_equal' => 'Jadwal interview tidak boleh menggunakan tanggal yang sudah berlalu.',
            'jadwal_interview.required_if' => 'Jadwal interview wajib diisi jika memilih aksi Interview.',
            'lokasi_interview.required_if' => 'Lokasi interview wajib diisi jika memilih aksi Interview.',
        ]);

        $pendaftar = PermintaanLamaran::query()->whereKey($id)->first();

        if (! $pendaftar) {
            return back()->with('error', 'Data pengajuan lamaran tidak ditemukan.');
        }

        if (in_array($pendaftar->status ?? 'menunggu', ['disetujui', 'ditolak'], true)) {
            return back()->with('error', 'Pengajuan lamaran ini sudah selesai diproses (Disetujui/Ditolak).');
        }

        $user = User::query()->find($pendaftar->user_id) ?? User::query()->where('email', $pendaftar->email)->first();

        // ==========================================
        // 1. AKSI: INTERVIEW
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
                        'user_id'      => $user->id_user ?? $user->id,
                        'judul'        => 'Jadwal Interview Lamaran Karyawan',
                        'pesan'        => "Anda diundang untuk interview pada {$jadwal} WIB di {$validated['lokasi_interview']}. Mohon hadir tepat waktu.",
                        'kategori'     => 'pengajuan',
                        'tipe'         => 'info',
                        'referensi_id' => $id,
                        'dibaca'       => false,
                        'created_at'   => now(),
                        'updated_at'   => now(),
                    ]);
                }
            });

            return back()->with(
                'success',
                "Jadwal interview untuk {$pendaftar->nama_pemohon} berhasil diperbarui dan dikirim."
            );
        }

        $disetujui = in_array($validated['action'], ['approve', 'accept'], true);

        // ==========================================
        // 2. AKSI: REJECT / DITOLAK
        // ==========================================
        if (! $disetujui) {
            DB::transaction(function () use ($id, $user, $validated) {
                DB::table('permintaan_lamaran')
                    ->where('id_permintaan', $id)
                    ->update([
                        'status' => 'ditolak',
                        'alasan_penolakan' => $validated['alasan_penolakan'],
                        'updated_at' => now(),
                    ]);

                if ($user) {
                    DB::table('notifikasi')->insert([
                        'user_id'      => $user->id_user ?? $user->id,
                        'judul'        => 'Status Lamaran Karyawan',
                        'pesan'        => 'Mohon maaf, pengajuan lamaran karyawan Anda belum dapat kami terima. Alasan: '.$validated['alasan_penolakan'],
                        'kategori'     => 'pengajuan',
                        'tipe'         => 'peringatan',
                        'referensi_id' => $id,
                        'dibaca'       => false,
                        'created_at'   => now(),
                        'updated_at'   => now(),
                    ]);
                }
            });

            return back()->with(
                'success',
                "Pengajuan lamaran atas nama {$pendaftar->nama_pemohon} berhasil ditolak."
            );
        }

        // ==========================================
        // 3. AKSI: APPROVE / DISETUJUI
        // ==========================================
        if (! $user) {
            return back()->with(
                'error',
                "Gagal menyetujui. Akun user dengan email {$pendaftar->email} tidak ditemukan dalam sistem."
            );
        }

        DB::transaction(function () use ($id, $pendaftar, $user) {
            $userIdPelamar = $user->id_user ?? $user->id;

            // Generate Username & Password Baru untuk Karyawan
            $namaSlug = \Illuminate\Support\Str::of($pendaftar->nama_pemohon)->slug('_')->lower()->toString();
            if (empty($namaSlug)) {
                $namaSlug = 'karyawan';
            }

            do {
                $usernameBaru = $namaSlug . '_' . rand(1000, 9999);
            } while (DB::table('users')->where('username', $usernameBaru)->exists());

            $passwordBaru = 'Karyawan#' . rand(10000, 99999);

            // 1. Buat User account khusus Karyawan (role = 'karyawan')
            $karyawanUserId = DB::table('users')->insertGetId([
                'nama'                 => $pendaftar->nama_pemohon,
                'username'             => $usernameBaru,
                'email'                => null, // Null agar tidak bentrok dengan email pelamar
                'password'             => bcrypt($passwordBaru),
                'role'                 => 'karyawan',
                'phone'                => $pendaftar->no_hp ?? null,
                'university'           => $pendaftar->pendidikan_terakhir ?? null,
                'student_id'           => $pendaftar->nik ?? null,
                'major'                => $pendaftar->posisi ?? null,
                'wajib_ganti_password' => false,
                'created_at'           => now(),
                'updated_at'           => now(),
            ], 'id_user');

            // 2. Update status lamaran & simpan username/password baru
            DB::table('permintaan_lamaran')
                ->where('id_permintaan', $id)
                ->update([
                    'status'            => 'disetujui',
                    'username_karyawan' => $usernameBaru,
                    'password_karyawan' => $passwordBaru,
                    'akun_dibuat'        => true,
                    'updated_at'        => now(),
                ]);

            // 3. Tambahkan record ke tabel karyawan
            $karyawanRecord = DB::table('karyawan')
                ->where('permintaan_id', $id)
                ->orWhere('user_id', $karyawanUserId)
                ->first();

            if (! $karyawanRecord) {
                DB::table('karyawan')->insert([
                    'user_id'       => $karyawanUserId,
                    'permintaan_id' => $id,
                    'nama_karyawan' => $pendaftar->nama_pemohon,
                    'email'         => $pendaftar->email,
                    'no_hp'         => $pendaftar->no_hp ?? null,
                    'jabatan'       => $pendaftar->posisi ?? $pendaftar->jabatan ?? null,
                    'status'        => 'aktif',
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]);
            } else {
                DB::table('karyawan')
                    ->where('id_karyawan', $karyawanRecord->id_karyawan)
                    ->update([
                        'user_id'    => $karyawanUserId,
                        'status'     => 'aktif',
                        'updated_at' => now(),
                    ]);
            }

            // 4. Kirim notifikasi ke akun pelamar
            DB::table('notifikasi')->insert([
                'user_id'      => $userIdPelamar,
                'judul'        => 'Selamat! Lamaran Karyawan Disetujui',
                'pesan'        => "Pengajuan lamaran Anda telah disetujui. Akun Karyawan baru Anda: Username: {$usernameBaru} | Password: {$passwordBaru}. Silakan login menggunakan kredensial baru tersebut.",
                'kategori'     => 'pengajuan',
                'tipe'         => 'sukses',
                'referensi_id' => $id,
                'dibaca'       => false,
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);
        });

        return back()->with(
            'success',
            "Pengajuan lamaran atas nama {$pendaftar->nama_pemohon} berhasil disetujui. Kredensial akun Karyawan baru telah dibuat."
        );
    }
}
