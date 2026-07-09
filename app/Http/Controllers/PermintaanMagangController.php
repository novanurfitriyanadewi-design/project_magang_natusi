<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use App\Models\PermintaanMagang;
use App\Models\PesertaMagang;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PermintaanMagangController extends ApiCrudController
{
    protected string $modelClass = PermintaanMagang::class;

    protected array $with = [
        'user',
        'peserta.user',
    ];

    protected array $files = [];

    protected array $searchable = [
        'nama_sekolah',
        'no_induk',
        'jurusan',
        'no_hp',
        'status',
    ];

    /**
     * Validasi CRUD permintaan magang.
     */
    protected function rules(?Model $model = null): array
    {
        return [
            'user_id' => [
                'nullable',
                'exists:users,id_user',
            ],

            'nama_pemohon' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'nullable',
                'email',
                'max:255',
            ],

            'nama_sekolah' => [
                'required',
                'string',
                'max:255',
            ],

            'no_induk' => [
                'required',
                'string',
                'max:100',
            ],

            'jurusan' => [
                'required',
                'string',
                'max:255',
            ],

            'no_hp' => [
                'required',
                'string',
                'max:20',
            ],

            'pesan' => [
                'nullable',
                'string',
                'max:2000',
            ],

            'status' => [
                'sometimes',
                'in:menunggu,disetujui,ditolak',
            ],
        ];
    }

    /**
     * Status permintaan baru otomatis menunggu.
     */
    protected function prepareDataForStore(
        array $data,
        Request $request
    ): array {
        $data['status'] = 'menunggu';

        return $data;
    }

    /**
     * Admin menyetujui permintaan magang.
     */
    public function setujui(
        Request $request,
        int|string $id
    ): JsonResponse {
        $data = $request->validate([
            'alamat' => [
                'required',
                'string',
                'max:1000',
            ],

            'tingkat_pendidikan' => [
                'required',
                'string',
                'max:100',
            ],

            'kelas' => [
                'nullable',
                'string',
                'max:100',
            ],

            'tgl_mulai' => [
                'required',
                'date',
            ],

            'tgl_selesai' => [
                'required',
                'date',
                'after_or_equal:tgl_mulai',
            ],

            'durasi_magang' => [
                'nullable',
                'string',
                'max:100',
            ],

            'nama_guru' => [
                'nullable',
                'string',
                'max:255',
            ],

            'no_hpguru' => [
                'nullable',
                'string',
                'max:20',
            ],
        ]);

        $hasil = DB::transaction(function () use ($id, $data) {
            /*
             * Lock agar permintaan yang sama tidak disetujui
             * dua kali secara bersamaan.
             */
            $permintaanMagang = PermintaanMagang::query()
                ->lockForUpdate()
                ->findOrFail($id);

            if ($permintaanMagang->status === 'disetujui') {
                abort(
                    422,
                    'Permintaan ini sudah pernah disetujui.'
                );
            }

            if ($permintaanMagang->status === 'ditolak') {
                abort(
                    422,
                    'Permintaan yang sudah ditolak tidak dapat langsung disetujui.'
                );
            }

            $pesertaSudahAda = PesertaMagang::query()
                ->where(
                    'permintaan_id',
                    $permintaanMagang->id_permintaan
                )
                ->exists();

            if ($pesertaSudahAda) {
                abort(
                    422,
                    'Data peserta dari permintaan ini sudah tersedia.'
                );
            }

            $username = $this->buatUsername(
                $permintaanMagang->nama_pemohon,
                $permintaanMagang->no_induk
            );

            $passwordAwal = Str::random(10);

            $email = $permintaanMagang->email;

            if (
                empty($email) ||
                User::query()->where('email', $email)->exists()
            ) {
                $email = $username . '@natusi.local';
            }

            $userPeserta = User::create([
                'nama' => $permintaanMagang->nama_pemohon,
                'email' => $email,
                'username' => $username,
                'password' => Hash::make($passwordAwal),
                'role' => 'peserta',
            ]);

            $peserta = PesertaMagang::create([
                'user_id' => $userPeserta->id_user,

                'permintaan_id' =>
                    $permintaanMagang->id_permintaan,

                'alamat' => $data['alamat'],

                'tingkat_pendidikan' =>
                    $data['tingkat_pendidikan'],

                'kelas' => $data['kelas'] ?? null,

                'tgl_mulai' => $data['tgl_mulai'],

                'tgl_selesai' => $data['tgl_selesai'],

                'durasi_magang' =>
                    $data['durasi_magang'] ?? null,

                'nama_guru' =>
                    $data['nama_guru'] ?? null,

                'no_hpguru' =>
                    $data['no_hpguru'] ?? null,

                'status' => 'aktif',
            ]);

            $permintaanMagang->update([
                'user_id' => $userPeserta->id_user,
                'status' => 'disetujui',
            ]);

            Notifikasi::create([
                'user_id' => $userPeserta->id_user,

                'judul' => 'Pengajuan Magang Diterima',

                'pesan' =>
                    'Pengajuan magang Anda telah disetujui. '
                    . "Username: {$username}. "
                    . "Password awal: {$passwordAwal}. "
                    . 'Silakan login dan segera mengganti password.',

                'kategori' => 'akun',

                'tipe' => 'sukses',

                'referensi_id' =>
                    $permintaanMagang->id_permintaan,

                'dibaca' => false,
            ]);

            return [
                'permintaan' => $permintaanMagang->fresh(),
                'peserta' => $peserta->load('user'),

                /*
                 * Kredensial hanya dikembalikan saat akun dibuat.
                 * Password di database tetap dalam bentuk hash.
                 */
                'akun' => [
                    'username' => $username,
                    'password_awal' => $passwordAwal,
                ],
            ];
        });

        return $this->successResponse(
            message: 'Permintaan magang berhasil disetujui dan akun peserta berhasil dibuat.',
            data: $hasil
        );
    }

    /**
     * Admin menolak permintaan magang.
     */
    public function tolak(
        Request $request,
        int|string $id
    ): JsonResponse {
        $data = $request->validate([
            'alasan' => [
                'required',
                'string',
                'max:1000',
            ],
        ]);

        $permintaanMagang = PermintaanMagang::findOrFail($id);

        if ($permintaanMagang->status === 'disetujui') {
            return $this->errorResponse(
                message: 'Permintaan yang sudah disetujui tidak dapat ditolak.',
                status: 422
            );
        }

        $permintaanMagang->update([
            'status' => 'ditolak',
            'pesan' => $data['alasan'],
        ]);

        /*
         * Notifikasi hanya dapat dibuat jika pemohon
         * sudah mempunyai akun user.
         */
        if ($permintaanMagang->user_id) {
            Notifikasi::create([
                'user_id' => $permintaanMagang->user_id,
                'judul' => 'Pengajuan Magang Ditolak',
                'pesan' =>
                    'Pengajuan magang Anda ditolak. Alasan: '
                    . $data['alasan'],
                'kategori' => 'pengajuan',
                'tipe' => 'peringatan',
                'referensi_id' =>
                    $permintaanMagang->id_permintaan,
                'dibaca' => false,
            ]);
        }

        return $this->successResponse(
            message: 'Permintaan magang berhasil ditolak.',
            data: $permintaanMagang->fresh()
        );
    }

    /**
     * Membuat username peserta yang unik.
     */
    private function buatUsername(
        string $namaPemohon,
        string $noInduk
    ): string {
        $nama = Str::slug($namaPemohon, '');

        $nama = Str::limit(
            strtolower($nama),
            12,
            ''
        );

        $nomorIndukBersih = preg_replace(
            '/[^a-zA-Z0-9]/',
            '',
            $noInduk
        );

        $nomorIndukBersih = strtolower(
            (string) $nomorIndukBersih
        );

        $nomorIndukAkhir = substr(
            $nomorIndukBersih,
            -5
        );

        $usernameDasar = $nama . $nomorIndukAkhir;

        if ($usernameDasar === '') {
            $usernameDasar = 'peserta';
        }

        $username = $usernameDasar;
        $nomor = 1;

        while (
            User::query()
                ->where('username', $username)
                ->exists()
        ) {
            $username = $usernameDasar . $nomor;
            $nomor++;
        }

        return $username;
    }
}