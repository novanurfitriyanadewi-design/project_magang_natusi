<?php

namespace App\Http\Controllers\AdminPeserta;

use App\Http\Controllers\Controller;
use App\Mail\ParticipantAccountMail;
use App\Models\Notifikasi;
use App\Models\PermintaanMagang;
use App\Models\PesertaMagang;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class PermintaanMagangController extends Controller
{
    public function index(Request $request)
    {
        $total_pendaftar = PermintaanMagang::query()->count();
        $total_disetujui = PermintaanMagang::query()->where('status', 'disetujui')->count();

        $query = PermintaanMagang::query()
            ->with(['peserta', 'anggota', 'riwayatBerkas']);

        $status = $request->string('status')->toString();

        if (in_array($status, ['menunggu', 'perlu_revisi', 'disetujui', 'ditolak'], true)) {
            $query->where('status', $status);
        }

        if ($request->filled('search')) {
            $search = trim($request->string('search')->toString());

            $query->where(function ($subQuery) use ($search) {
                $subQuery
                    ->where('nama_pemohon', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('nama_sekolah', 'like', "%{$search}%")
                    ->orWhere('no_induk', 'like', "%{$search}%")
                    ->orWhere('jurusan', 'like', "%{$search}%")
                    ->orWhere('no_hp', 'like', "%{$search}%")
                    ->orWhereHas('anggota', function ($anggotaQuery) use ($search) {
                        $anggotaQuery
                            ->where('nama', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('no_induk', 'like', "%{$search}%");
                    });
            });
        }

        $permintaan_magang = $query
            ->orderByDesc('created_at')
            ->orderByDesc('id_permintaan')
            ->paginate(10)
            ->withQueryString();

        return view('admin-peserta.permintaan-magang', compact(
            'permintaan_magang',
            'total_pendaftar',
            'total_disetujui'
        ));
    }

    public function action(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'action' => ['required', 'in:approve,reject,revision,accept'],
            'alasan_penolakan' => ['required_if:action,reject', 'nullable', 'string', 'max:2000'],
            'catatan_revisi' => ['required_if:action,revision', 'nullable', 'string', 'max:2000'],
        ]);

        $result = DB::transaction(function () use ($validated, $id): array {
            $permintaan = PermintaanMagang::query()
                ->with(['user', 'anggota'])
                ->whereKey($id)
                ->lockForUpdate()
                ->first();

            if (! $permintaan) {
                return [
                    'type' => 'error',
                    'message' => 'Data pengajuan magang tidak ditemukan.',
                ];
            }

            if (! in_array($permintaan->status, ['menunggu', 'perlu_revisi'], true)) {
                return [
                    'type' => 'error',
                    'message' => 'Pengajuan magang ini sudah pernah diproses.',
                ];
            }

            if ($validated['action'] === 'revision') {
                $permintaan->update([
                    'status' => 'perlu_revisi',
                    'catatan_revisi' => $validated['catatan_revisi'],
                ]);

                $this->kirimNotifikasiHasil(
                    $permintaan,
                    'Pengajuan Magang Perlu Revisi',
                    'Admin meminta revisi berkas: '.$validated['catatan_revisi'],
                    'peringatan'
                );

                return ['type' => 'success', 'message' => "Catatan revisi untuk {$permintaan->nama_pemohon} berhasil dikirim melalui portal dan email."];
            }

            $disetujui = in_array($validated['action'], ['approve', 'accept'], true);

            if (! $disetujui) {
                $permintaan->update([
                    'status' => 'ditolak',
                    'akun_dibuat' => false,
                    'alasan_penolakan' => $validated['alasan_penolakan'],
                ]);

                $this->kirimNotifikasiHasil(
                    $permintaan,
                    'Pengajuan Magang Belum Disetujui',
                    'Mohon maaf, pengajuan magang Anda belum dapat disetujui. Alasan: '.$validated['alasan_penolakan'],
                    'peringatan'
                );

                return [
                    'type' => 'success',
                    'message' => "Pengajuan magang atas nama {$permintaan->nama_pemohon} berhasil ditolak dan hasilnya dikirim melalui email.",
                ];
            }

            $anggota = $permintaan->anggota;
            if ($anggota->isEmpty()) {
                $anggota = collect([
                    (object) [
                        'id_anggota' => null,
                        'nama' => $permintaan->nama_pemohon,
                        'email' => $permintaan->email,
                        'no_induk' => $permintaan->no_induk,
                        'jurusan' => $permintaan->jurusan,
                        'no_hp' => $permintaan->no_hp,
                        'is_ketua' => true,
                        'user_id' => $permintaan->user_id,
                    ],
                ]);
            }

            $ketua = $anggota->first(fn ($item) => (bool) $item->is_ketua) ?? $anggota->first();
            $anggotaLain = $anggota->filter(fn ($item) => $item !== $ketua);

            foreach ($anggotaLain as $anggotaItem) {
                if (User::query()->where('email', strtolower((string) $anggotaItem->email))->exists()) {
                    return [
                        'type' => 'error',
                        'message' => "Email {$anggotaItem->email} sudah digunakan akun lain. Perbarui data anggota sebelum menyetujui pengajuan.",
                    ];
                }
            }

            $credentials = [];
            $tingkatPendidikan = $this->tentukanTingkatPendidikan($permintaan);

            foreach ($anggota as $index => $anggotaItem) {
                $isKetua = $anggotaItem === $ketua;
                $loginEmail = strtolower(trim((string) $anggotaItem->email));
                $usernameInternal = $this->buatUsernamePeserta($anggotaItem->nama);
                $passwordAwal = Str::lower(Str::random(10));

                /*
                 * PENTING: akun pendaftaran ketua/pemohon TIDAK boleh diubah
                 * menjadi akun peserta. Akun pendaftaran tetap ber-role pelamar
                 * agar halaman status/konfirmasi selalu bisa dibuka menggunakan
                 * email + password yang dibuat saat mendaftar.
                 *
                 * Setelah disetujui, seluruh anggota (termasuk ketua) memperoleh
                 * akun peserta BARU dengan email login + password awal sendiri.
                 * Untuk ketua, email akun peserta dibuat null karena email aslinya
                 * tetap dipakai oleh akun pendaftaran dan kolom users.email unik.
                 * Email ketua tetap tersimpan lengkap pada data anggota/pengajuan.
                 */
                $akunPeserta = User::query()->create([
                    'nama' => $anggotaItem->nama,
                    'email' => $isKetua ? null : strtolower((string) $anggotaItem->email),
                    'username' => $usernameInternal,
                    'password' => Hash::make($passwordAwal),
                    'role' => 'peserta',
                    'university' => $permintaan->nama_sekolah,
                    'student_id' => $anggotaItem->no_induk,
                    'major' => $anggotaItem->jurusan,
                    'phone' => $anggotaItem->no_hp,
                    'description' => $permintaan->pesan,
                    'wajib_ganti_password' => true,
                ]);

                // permintaan.user_id harus tetap menunjuk akun pendaftaran ketua.
                // Jangan diganti ke akun peserta karena dipakai untuk akses halaman status.

                $peserta = PesertaMagang::query()->create([
                    'user_id' => $akunPeserta->id_user,
                    'permintaan_id' => $permintaan->id_permintaan,
                    'alamat' => 'Belum dilengkapi',
                    'tingkat_pendidikan' => $tingkatPendidikan,
                    'kelas' => null,
                    'tgl_mulai' => null,
                    'tgl_selesai' => null,
                    'durasi_magang' => null,
                    'nama_guru' => null,
                    'no_hpguru' => null,
                    'status' => 'aktif',
                ]);

                if (! empty($anggotaItem->id_anggota)) {
                    $anggotaItem->update([
                        'user_id' => $akunPeserta->id_user,
                        'peserta_id' => $peserta->id_peserta,
                        'username_peserta' => $loginEmail,
                        'password_awal' => $passwordAwal,
                    ]);
                }

                $credentials[] = [
                    'nama' => $anggotaItem->nama,
                    'email' => $anggotaItem->email,
                    'username' => $loginEmail,
                    'login_email' => $loginEmail,
                    'internal_username' => $usernameInternal,
                    'password' => $passwordAwal,
                    'user_id' => $akunPeserta->id_user,
                ];

                Notifikasi::query()->create([
                    'user_id' => $akunPeserta->id_user,
                    'judul' => 'Selamat, Pengajuan Magang Diterima',
                    'pesan' => 'Pengajuan magang Anda diterima dan akun peserta sudah aktif. Gunakan email Anda dan password awal yang dikirim melalui email untuk masuk ke Portal Peserta Magang.', 
                    'kategori' => 'akun',
                    'tipe' => 'sukses',
                    'referensi_id' => $permintaan->id_permintaan,
                    'dibaca' => false,
                ]);
            }

            $leaderCredential = $credentials[0];
            $permintaan->update([
                'status' => 'disetujui',
                'username_peserta' => $leaderCredential['username'],
                'password_awal' => $leaderCredential['password'],
                'akun_dibuat' => true,
                'alasan_penolakan' => null,
                'catatan_revisi' => null,
            ]);

            return [
                'type' => 'success',
                'message' => count($credentials) > 1
                    ? 'Pengajuan kelompok berhasil disetujui. '.count($credentials).' akun peserta telah dibuat dan kredensial dikirim ke email masing-masing anggota.'
                    : "Pengajuan magang atas nama {$permintaan->nama_pemohon} berhasil disetujui. Kredensial akun peserta dikirim ke {$leaderCredential['email']}.",
                'credentials' => $credentials,
                'permintaan_id' => $permintaan->id_permintaan,
            ];
        });

        if ($result['type'] === 'success' && ! empty($result['credentials'])) {
            $permintaanUntukEmail = PermintaanMagang::query()->find($result['permintaan_id']);
            if ($permintaanUntukEmail) {
                $this->kirimEmailKredensialPeserta($permintaanUntukEmail, $result['credentials']);
            }
        }

        return back()->with($result['type'], $result['message']);
    }

    private function kirimEmailKredensialPeserta(PermintaanMagang $permintaan, array $credentials): void
    {
        if (! config('natusi.email_notifications_enabled')) {
            return;
        }

        $loginUrl = route('login');

        foreach ($credentials as $credential) {
            $recipient = strtolower(trim((string) ($credential['email'] ?? '')));

            if (! filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
                continue;
            }

            try {
                Mail::to($recipient)->send(new ParticipantAccountMail(
                    credential: $credential,
                    permintaan: $permintaan,
                    loginUrl: $loginUrl,
                ));
            } catch (\Throwable $exception) {
                Log::warning('Email kredensial peserta magang gagal dikirim.', [
                    'permintaan_id' => $permintaan->id_permintaan,
                    'recipient' => $recipient,
                    'error' => $exception->getMessage(),
                ]);
            }
        }
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
            $username = $namaDepan.random_int(1000, 9999);
        } while (User::query()->where('username', $username)->exists());

        return $username;
    }

    private function tentukanTingkatPendidikan(PermintaanMagang $permintaan): string
    {
        if ($permintaan->jenjang === 'smk') {
            return 'SMK';
        }

        if ($permintaan->jenjang === 'kuliah') {
            return 'Universitas';
        }

        $namaInstansi = Str::lower($permintaan->nama_sekolah);

        return Str::contains($namaInstansi, ['smk', 'sma', 'ma ', 'sekolah'])
            ? 'SMK'
            : 'Universitas';
    }
}
