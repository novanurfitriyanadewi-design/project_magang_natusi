<?php

namespace App\Http\Controllers\AdminKaryawan;

use App\Http\Controllers\Controller;
use App\Mail\EmployeeAccountMail;
use Carbon\Carbon;
use App\Models\Karyawan;
use App\Models\Notifikasi;
use App\Models\PermintaanLamaran;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Throwable;

class PermintaanLamaranController extends Controller
{
    public function index(Request $request)
    {
        $total_pendaftar = DB::table('permintaan_lamaran')
            ->count();

        $total_disetujui = DB::table('permintaan_lamaran')
            ->where('status', 'disetujui')
            ->where(function ($query) {
                $query->whereNotExists(function ($subQuery) {
                    $subQuery->selectRaw('1')
                        ->from('karyawan')
                        ->whereColumn('karyawan.permintaan_id', 'permintaan_lamaran.id_permintaan');
                })->orWhereExists(function ($subQuery) {
                    $subQuery->selectRaw('1')
                        ->from('karyawan')
                        ->whereColumn('karyawan.permintaan_id', 'permintaan_lamaran.id_permintaan')
                        ->where('karyawan.status', 'aktif');
                });
            })
            ->count();

        $total_interview = DB::table('permintaan_lamaran')
            ->where('status', 'interview')
            ->count();

        $total_ditolak = DB::table('permintaan_lamaran')
            ->where('status', 'ditolak')
            ->count();

        $query = DB::table('permintaan_lamaran as pl')
            ->leftJoin('karyawan as k', 'k.permintaan_id', '=', 'pl.id_permintaan')
            ->select([
                'pl.*',
                'k.alamat',
                'k.status as status_karyawan',
            ]);

        $status = $request->string('status')->toString();

        if ($status === 'nonaktif') {
            $query->where('k.status', 'nonaktif');
        } elseif (in_array($status, ['menunggu', 'interview', 'disetujui', 'ditolak'], true)) {
            $query->where('pl.status', $status)->where(function ($subQuery) {
                $subQuery->whereNull('k.status')->orWhere('k.status', 'aktif');
            });
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
            ->get();

        return view('admin-karyawan.karyawan.permintaan-lamaran', compact(
            'permintaan_lamaran',
            'total_pendaftar',
            'total_disetujui',
            'total_interview',
            'total_ditolak'
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

                    Notifikasi::query()->create([
                        'user_id'      => $user->id_user,
                        'judul'        => 'Jadwal Interview Lamaran Karyawan',
                        'pesan'        => "Halo {$user->nama}, berdasarkan hasil peninjauan lamaran Anda, kami mengundang Anda untuk mengikuti interview pada {$jadwal} WIB di {$validated['lokasi_interview']}. Mohon hadir 15 menit lebih awal dan membawa dokumen pendukung yang relevan. Informasi lengkap juga telah kami kirimkan ke email terdaftar Anda.",
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
                    Notifikasi::query()->create([
                        'user_id'      => $user->id_user,
                        'judul'        => 'Status Lamaran Karyawan',
                        'pesan'        => 'Terima kasih atas ketertarikan Anda untuk bergabung bersama CV Natusi. Setelah melalui proses peninjauan, lamaran Anda belum dapat kami lanjutkan pada tahap ini. Alasan: '.$validated['alasan_penolakan']. ' Detail keputusan juga telah kami kirimkan ke email terdaftar Anda.',
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

        $credentials = DB::transaction(function () use ($id, $pendaftar, $user) {
            // Promosikan akun pendaftar yang sama agar username dan email tetap konsisten.
            $usernameBaru = $user->username ?: $pendaftar->email;
            $passwordBaru = 'Karyawan#' . rand(10000, 99999);

            $user->update([
                'username'             => $usernameBaru,
                'email'                => $user->email ?: $pendaftar->email,
                'password'             => bcrypt($passwordBaru),
                'role'                 => 'karyawan',
                'wajib_ganti_password' => false,
            ]);

            $karyawanUserId = $user->id_user;

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
            //    FIX: nip di-copy dari data pelamar (nik).
            //    FIX: posisi yang dipilih pelamar saat mengisi form dipetakan ke divisi,
            //         bukan disimpan sebagai jabatan. Admin masih bisa mengubahnya via Edit.
            $posisiLamaran = trim((string) ($pendaftar->posisi ?? ''));

            $divisiIdLamaran = null;
            if ($posisiLamaran !== '') {
                $divisiIdLamaran = DB::table('divisi')
                    ->where('nama_divisi', 'like', "%{$posisiLamaran}%")
                    ->value('id_divisi');
            }

            $karyawanRecord = DB::table('karyawan')
                ->where('permintaan_id', $id)
                ->orWhere('user_id', $karyawanUserId)
                ->first();

            if (! $karyawanRecord) {
                DB::table('karyawan')->insert([
                    'user_id'           => $karyawanUserId,
                    'permintaan_id'     => $id,
                    'nama_karyawan'     => $pendaftar->nama_pemohon,
                    'email'             => $pendaftar->email,
                    'no_hp'             => $pendaftar->no_hp ?? null,
                    'nip'               => $pendaftar->nik ?? null,     // <-- FIX: auto-fill NIK
                    'divisi_id'         => $divisiIdLamaran,             // <-- FIX: posisi form -> divisi
                    'alamat'            => $pendaftar->alamat ?? null,   // <-- FIX: alamat dari form
                    'status'            => 'aktif',
                    'tanggal_bergabung' => today(),
                    'created_at'        => now(),
                    'updated_at'        => now(),
                ]);
            } else {
                DB::table('karyawan')
                    ->where('id_karyawan', $karyawanRecord->id_karyawan)
                    ->update([
                        'user_id'           => $karyawanUserId,
                        'status'            => 'aktif',
                        // Jangan timpa kalau admin sudah pernah isi manual sebelumnya
                        'nip'               => $karyawanRecord->nip ?? ($pendaftar->nik ?? null),
                        'divisi_id'         => $karyawanRecord->divisi_id ?? $divisiIdLamaran,
                        'alamat'            => filled($karyawanRecord->alamat ?? null) ? $karyawanRecord->alamat : ($pendaftar->alamat ?? null),
                        'tanggal_bergabung' => $karyawanRecord->tanggal_bergabung ?? today(),
                        'updated_at'        => now(),
                    ]);
            }

            // 4. Kirim notifikasi ke akun pelamar
            Notifikasi::query()->create([
                'user_id'      => $user->id_user,
                'judul'        => 'Selamat! Lamaran Karyawan Disetujui',
                'pesan'        => "Selamat, {$user->nama}! Lamaran Anda telah disetujui dan akun karyawan Anda sudah aktif. Kredensial login baru telah dikirim ke email {$user->email}. Gunakan username {$usernameBaru} atau email tersebut, beserta password yang tercantum pada email. Silakan periksa kotak masuk atau folder Spam/Promosi.",
                'kategori'     => 'pengajuan',
                'tipe'         => 'sukses',
                'referensi_id' => $id,
                'dibaca'       => false,
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);

            return [
                'username' => $usernameBaru,
                'password' => $passwordBaru,
                'email' => $user->email,
            ];
        });

        if (config('natusi.email_notifications_enabled') && filter_var($credentials['email'], FILTER_VALIDATE_EMAIL)) {
            try {
                Mail::to($credentials['email'])->send(new EmployeeAccountMail(
                    $pendaftar,
                    $credentials['username'],
                    $credentials['password'],
                    route('login'),
                ));
            } catch (Throwable $exception) {
                Log::warning('Email kredensial akun karyawan gagal dikirim.', [
                    'permintaan_id' => $id,
                    'recipient' => $credentials['email'],
                    'error' => $exception->getMessage(),
                ]);
            }
        }

        return back()->with(
            'success',
            "Pengajuan lamaran atas nama {$pendaftar->nama_pemohon} berhasil disetujui. Kredensial akun Karyawan baru telah dibuat."
        );
    }

    public function destroy(int $id): RedirectResponse
    {
        $pendaftar = PermintaanLamaran::query()->whereKey($id)->first();

        if (! $pendaftar) {
            return back()->with('error', 'Data pengajuan lamaran tidak ditemukan.');
        }

        if ($pendaftar->status !== 'ditolak') {
            return back()->with('error', 'Hanya pengajuan yang ditolak yang dapat dihapus.');
        }

        $filePaths = collect([
            'surat_lamaran_path',
            'cv_path',
            'ijazah_path',
            'ktp_path',
            'pas_foto_path',
            'skck_path',
            'portfolio_path',
            'pengalaman_kerja_path',
        ])->map(fn (string $column) => $pendaftar->{$column})->filter();

        DB::transaction(function () use ($pendaftar, $filePaths): void {
            Notifikasi::query()->where('referensi_id', $pendaftar->id_permintaan)->delete();

            $pendaftar->delete();

            if ($pendaftar->user_id) {
                Notifikasi::query()->where('user_id', $pendaftar->user_id)->delete();
                User::query()->whereKey($pendaftar->user_id)->delete();
            }

            foreach ($filePaths as $filePath) {
                Storage::disk('public')->delete($filePath);
            }
        });

        return back()->with('success', "Data lamaran {$pendaftar->nama_pemohon} berhasil dihapus. Pelamar dapat mendaftar kembali.");
    }
}