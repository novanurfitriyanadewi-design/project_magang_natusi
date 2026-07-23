<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notifikasi;
use App\Models\PermintaanMagang;
use App\Models\PesertaMagang;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

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

        $result = DB::transaction(function () use ($validated, $id): array {
            $permintaan = PermintaanMagang::query()
                ->whereKey($id)
                ->lockForUpdate()
                ->first();

            if (! $permintaan) {
                return [
                    'type' => 'error',
                    'message' => 'Data pengajuan magang tidak ditemukan.',
                ];
            }

            if ($permintaan->status !== 'menunggu') {
                return [
                    'type' => 'error',
                    'message' => 'Pengajuan magang ini sudah pernah diproses.',
                ];
            }

            $disetujui = in_array(
                $validated['action'],
                ['approve', 'accept'],
                true
            );

            if (! $disetujui) {
                $permintaan->update([
                    'status' => 'ditolak',
                    'akun_dibuat' => false,
                ]);

                $this->kirimNotifikasiHasil(
                    $permintaan,
                    'Pengajuan Magang Belum Disetujui',
                    'Mohon maaf, pengajuan magang Anda belum dapat disetujui. Silakan hubungi Admin untuk informasi lebih lanjut.',
                    'peringatan'
                );

                return [
                    'type' => 'success',
                    'message' => "Pengajuan magang atas nama {$permintaan->nama_pemohon} berhasil ditolak.",
                ];
            }

            $username = $this->buatUsernamePeserta(
                $permintaan->nama_pemohon
            );
            $passwordAwal = Str::lower(Str::random(10));

            $akunPeserta = User::query()->create([
                'nama' => $permintaan->nama_pemohon,
                'email' => null,
                'username' => $username,
                'password' => Hash::make($passwordAwal),
                'role' => 'peserta',
                'university' => $permintaan->nama_sekolah,
                'student_id' => $permintaan->no_induk,
                'major' => $permintaan->jurusan,
                'phone' => $permintaan->no_hp,
                'description' => $permintaan->pesan,
                'wajib_ganti_password' => true,
            ]);

            PesertaMagang::query()->create([
                'user_id' => $akunPeserta->id_user,
                'permintaan_id' => $permintaan->id_permintaan,
                'alamat' => 'Belum dilengkapi',
                'tingkat_pendidikan' => $this->tentukanTingkatPendidikan(
                    $permintaan->nama_sekolah
                ),
                'kelas' => null,
                'tgl_mulai' => null,
                'tgl_selesai' => null,
                'durasi_magang' => null,
                'nama_guru' => null,
                'no_hpguru' => null,
                'status' => 'aktif',
            ]);

            $permintaan->update([
                'status' => 'disetujui',
                'username_peserta' => $username,
                'password_awal' => $passwordAwal,
                'akun_dibuat' => true,
            ]);

            $this->kirimNotifikasiHasil(
                $permintaan,
                'Selamat, Pengajuan Magang Diterima',
                "Pengajuan Anda diterima. Akun peserta telah dibuat. Username: {$username} | Password awal: {$passwordAwal}. Simpan data ini dan segera ganti password setelah login.",
                'sukses'
            );

            Notifikasi::query()->create([
                'user_id' => $akunPeserta->id_user,
                'judul' => 'Selamat Datang sebagai Peserta Magang',
                'pesan' => 'Akun peserta Anda telah aktif. Silakan lengkapi profil dan periksa tugas yang tersedia.',
                'kategori' => 'akun',
                'tipe' => 'sukses',
                'referensi_id' => $permintaan->id_permintaan,
                'dibaca' => false,
            ]);

            return [
                'type' => 'success',
                'message' => "Pengajuan magang atas nama {$permintaan->nama_pemohon} berhasil disetujui. Akun peserta {$username} telah dibuat dan masuk ke Data Peserta Magang.",
            ];
        });

        return back()->with($result['type'], $result['message']);
    }

    private function kirimNotifikasiHasil(
        PermintaanMagang $permintaan,
        string $judul,
        string $pesan,
        string $tipe
    ): void {
        if (! $permintaan->user_id) {
            return;
        }

        Notifikasi::query()->create([
            'user_id' => $permintaan->user_id,
            'judul' => $judul,
            'pesan' => $pesan,
            'kategori' => $tipe === 'sukses' ? 'akun' : 'pengajuan',
            'tipe' => $tipe,
            'referensi_id' => $permintaan->id_permintaan,
            'dibaca' => false,
        ]);
    }

    private function buatUsernamePeserta(string $nama): string
    {
        $namaDepan = Str::of($nama)
            ->trim()
            ->before(' ')
            ->ascii()
            ->lower()
            ->replaceMatches('/[^a-z0-9]/', '')
            ->limit(24, '')
            ->toString();

        if ($namaDepan === '') {
            $namaDepan = 'peserta';
        }

        do {
            $username = $namaDepan . random_int(1000, 9999);
        } while (User::query()->where('username', $username)->exists());

        return $username;
    }

    private function tentukanTingkatPendidikan(string $instansi): string
    {
        $namaInstansi = Str::lower($instansi);

        return Str::contains($namaInstansi, [
            'smk',
            'sma',
            'ma ',
            'sekolah',
        ]) ? 'SMK' : 'Universitas';
    }
}
