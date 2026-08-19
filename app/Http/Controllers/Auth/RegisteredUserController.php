<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Jurusan;
use App\Models\Notifikasi;
use App\Models\PermintaanMagang;
use App\Models\PermintaanLamaran;
use App\Models\User;
use App\Models\RiwayatBerkasMagang;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | UPLOAD REVISI BERKAS MAGANG
    |--------------------------------------------------------------------------
    */

    public function uploadRevisi(
        Request $request,
        PermintaanMagang $permintaan
    ): RedirectResponse {
        abort_unless(
            $permintaan->user_id === $request->user()->id_user,
            403
        );

        abort_unless(
            $permintaan->status === 'perlu_revisi',
            422,
            'Pengajuan ini tidak sedang memerlukan revisi.'
        );

        $validated = $request->validate([
            'jenis_berkas' => [
                'required',
                'string',
                'max:100',
            ],

            'berkas' => [
                'required',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:5120',
            ],
        ]);

        $version = (int) $permintaan
            ->riwayatBerkas()
            ->max('versi') + 1;

        $path = $request
            ->file('berkas')
            ->store(
                "permintaan-magang/{$permintaan->id_permintaan}",
                'public'
            );

        RiwayatBerkasMagang::query()->create([
            'permintaan_id' => $permintaan->id_permintaan,
            'jenis_berkas' => $validated['jenis_berkas'],
            'path' => $path,
            'versi' => $version,
        ]);

        $permintaan->update([
            'status' => 'menunggu',
            'catatan_revisi' => null,
        ]);

        return back()->with(
            'success',
            'Berkas revisi berhasil dikirim dan pengajuan kembali menunggu peninjauan.'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | HALAMAN REGISTER
    |--------------------------------------------------------------------------
    */

    public function create(): View
    {
        $role = session('register_role', 'pelamar');

        return view('auth.register', [
            'registerRole' => in_array(
                $role,
                ['pelamar', 'karyawan'],
                true
            )
                ? $role
                : 'pelamar',

            'jurusanList' => Jurusan::query()
                ->aktif()
                ->orderBy('tingkat')
                ->orderBy('nama_jurusan')
                ->get(),
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | STATUS PENGAJUAN
    |--------------------------------------------------------------------------
    */

    public function status(Request $request): View
    {
        $user = $request->user();

        $permintaan = null;

        /*
        |--------------------------------------------------------------------------
        | CEK LAMARAN KARYAWAN
        |--------------------------------------------------------------------------
        */

        if (class_exists(PermintaanLamaran::class)) {
            $permintaan = PermintaanLamaran::query()
                ->where(function ($query) use ($user) {
                    $query
                        ->where('user_id', $user->id_user)
                        ->orWhere('email', $user->email)
                        ->orWhereHas(
                            'karyawan',
                            fn ($karyawan) =>
                                $karyawan->where(
                                    'user_id',
                                    $user->id_user
                                )
                        );
                })
                ->latest('id_permintaan')
                ->first();
        }

        /*
        |--------------------------------------------------------------------------
        | JIKA TIDAK ADA, CEK MAGANG
        |--------------------------------------------------------------------------
        */

        if (! $permintaan) {
            $permintaan = PermintaanMagang::query()
                ->where(function ($query) use ($user) {
                    $query
                        ->where('user_id', $user->id_user)
                        ->orWhere('email', $user->email)
                        ->orWhereHas(
                            'peserta',
                            fn ($peserta) =>
                                $peserta->where(
                                    'user_id',
                                    $user->id_user
                                )
                        );
                })
                ->latest('id_permintaan')
                ->first();
        }

        /*
        |--------------------------------------------------------------------------
        | NOTIFIKASI
        |--------------------------------------------------------------------------
        */

        $notifications = $user->notifikasi()
            ->latest('id_notifikasi')
            ->limit(10)
            ->get();

        $unreadNotificationCount = $user->notifikasi()
            ->where('dibaca', false)
            ->count();

        return view(
            'auth.status-pengajuan',
            compact(
                'permintaan',
                'notifications',
                'unreadNotificationCount'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | PROSES REGISTER
    |--------------------------------------------------------------------------
    */

    public function store(Request $request): RedirectResponse
    {
        /*
        |--------------------------------------------------------------------------
        | TENTUKAN ROLE PENDAFTARAN
        |--------------------------------------------------------------------------
        */

        $roleSession = session(
            'register_role',
            $request->input('role', 'pelamar')
        );

        if (! in_array(
            $roleSession,
            ['pelamar', 'karyawan'],
            true
        )) {
            $roleSession = 'pelamar';
        }


        /*
        |--------------------------------------------------------------------------
        | STATUS PENGAJUAN YANG MASIH AKTIF
        |--------------------------------------------------------------------------
        */

        $permintaanMasihAktif = static function ($query) {
            $query->whereIn(
                'status',
                [
                    'menunggu',
                    'disetujui',
                ]
            );
        };


        /*
        |--------------------------------------------------------------------------
        | VALIDASI EMAIL
        |--------------------------------------------------------------------------
        */

        $emailRules = [
            'required',
            'string',
            'lowercase',
            'email',
            'max:255',
            Rule::unique('users', 'email'),
        ];

        /*
        |--------------------------------------------------------------------------
        | VALIDASI BERDASARKAN ROLE
        |--------------------------------------------------------------------------
        */

        if ($roleSession === 'pelamar') {

            /*
            |--------------------------------------------------------------------------
            | PELAMAR MAGANG
            |--------------------------------------------------------------------------
            */

            $emailRules[] = Rule::unique(
                'permintaan_magang',
                'email'
            )->where($permintaanMasihAktif);

            $studentIdRules = [
                'required',
                'string',
                'max:50',

                Rule::unique(
                    'permintaan_magang',
                    'no_induk'
                )->where($permintaanMasihAktif),
            ];

            $nikRules = [
                'nullable',
                'string',
                'max:30',
            ];

            $positionRules = [
                'nullable',
                'string',
                'max:255',
            ];

        } else {

            /*
            |--------------------------------------------------------------------------
            | CALON KARYAWAN
            |--------------------------------------------------------------------------
            |
            | Karyawan:
            |
            | - NIK wajib
            | - student_id tidak digunakan
            | - position digunakan sebagai posisi lamaran
            |
            |--------------------------------------------------------------------------
            */

            $emailRules[] = Rule::unique(
                'permintaan_lamaran',
                'email'
            )->where($permintaanMasihAktif);

            $studentIdRules = [
                'nullable',
                'string',
                'max:50',
            ];

            $nikRules = [
                'required',
                'string',
                'max:30',
            ];

            $positionRules = [
                'required',
                'string',
                'max:255',
            ];
        }


        /*
        |--------------------------------------------------------------------------
        | VALIDASI BERKAS
        |--------------------------------------------------------------------------
        */

        $fileRules = [];

        if ($roleSession === 'karyawan') {

            $fileRules = [

                'surat_lamaran' => [
                    'required',
                    'file',
                    'mimes:pdf,jpg,jpeg,png',
                    'max:2048',
                ],

                'cv' => [
                    'required',
                    'file',
                    'mimes:pdf,jpg,jpeg,png',
                    'max:2048',
                ],

                'ijazah' => [
                    'required',
                    'file',
                    'mimes:pdf,jpg,jpeg,png',
                    'max:2048',
                ],

                'ktp' => [
                    'required',
                    'file',
                    'mimes:pdf,jpg,jpeg,png',
                    'max:2048',
                ],

                'pas_foto' => [
                    'required',
                    'file',
                    'mimes:jpg,jpeg,png',
                    'max:2048',
                ],

                'skck' => [
                    'required',
                    'file',
                    'mimes:pdf,jpg,jpeg,png',
                    'max:2048',
                ],

                'portfolio' => [
                    'nullable',
                    'file',
                    'mimes:pdf,jpg,jpeg,png,zip',
                    'max:5120',
                ],

                'pengalaman_kerja' => [
                    'nullable',
                    'file',
                    'mimes:pdf,jpg,jpeg,png',
                    'max:2048',
                ],
            ];
        }


        /*
        |--------------------------------------------------------------------------
        | PESAN VALIDASI
        |--------------------------------------------------------------------------
        */

        $messages = [

            'full_name.required' =>
                'Nama lengkap wajib diisi.',

            'email.required' =>
                'Alamat email wajib diisi.',

            'email.email' =>
                'Format alamat email tidak valid.',

            'email.unique' =>
                'Alamat email sudah digunakan atau masih memiliki pengajuan aktif.',

            'password.required' =>
                'Kata sandi akun wajib diisi.',

            'password.min' =>
                'Kata sandi minimal 8 karakter.',

            'password.confirmed' =>
                'Konfirmasi kata sandi tidak sama.',

            'phone.required' =>
                'Nomor telepon wajib diisi.',

            'terms.accepted' =>
                'Anda harus menyetujui ketentuan pendaftaran.',

            'nik.required' =>
                'NIK wajib diisi.',

            'nik.unique' =>
                'NIK sudah digunakan pada pengajuan lain.',

            'position.required' =>
                'Posisi yang dilamar wajib diisi.',
        ];


        /*
        |--------------------------------------------------------------------------
        | PESAN BERDASARKAN ROLE
        |--------------------------------------------------------------------------
        */

        if ($roleSession === 'karyawan') {

            $messages['university.required'] =
                'Pendidikan terakhir wajib diisi.';

        } else {

            $messages['university.required'] =
                'Asal sekolah / instansi wajib diisi.';

            $messages['student_id.required'] =
                'Nomor induk (NIM/NISN) wajib diisi.';

            $messages['major.required'] =
                'Jurusan wajib diisi.';
        }


        /*
        |--------------------------------------------------------------------------
        | VALIDASI FORM
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate(

            array_merge(

                [

                    'full_name' => [
                        'required',
                        'string',
                        'max:255',
                    ],

                    'email' => $emailRules,

                    'password' => [
                        'required',
                        'string',
                        'min:8',
                        'max:100',
                        'confirmed',
                    ],

                    'university' => [
                        'required',
                        'string',
                        'max:255',
                    ],

                    /*
                    | Untuk magang digunakan.
                    | Untuk karyawan nullable.
                    */

                    'student_id' => $studentIdRules,

                    /*
                    | NIK hanya wajib untuk karyawan.
                    */

                    'nik' => $nikRules,

                    /*
                    | Major untuk magang.
                    */

                    'major' => [
                        $roleSession === 'pelamar'
                            ? 'required'
                            : 'nullable',
                        'string',
                        'max:255',
                    ],

                    /*
                    | Position untuk karyawan.
                    */

                    'position' => $positionRules,

                    'phone' => [
                        'required',
                        'string',
                        'max:20',
                    ],

                    'description' => [
                        'nullable',
                        'string',
                        'max:2000',
                    ],

                    'terms' => [
                        'accepted',
                    ],
                ],

                $fileRules
            ),

            $messages
        );


        /*
        |--------------------------------------------------------------------------
        | NORMALISASI EMAIL
        |--------------------------------------------------------------------------
        */

        $email = strtolower(
            trim($validated['email'])
        );


        /*
        |--------------------------------------------------------------------------
        | BUAT USERNAME
        |--------------------------------------------------------------------------
        */

        if ($roleSession === 'pelamar') {

            $username = $this->makeUniqueUsername(
                $validated['student_id'],
                $email
            );

        } else {

            /*
            | Karyawan menggunakan email sebagai dasar username.
            */

            $username = $this->makeUniqueUsername(
                '',
                $email
            );
        }


        /*
        |--------------------------------------------------------------------------
        | ALUR PELAMAR MAGANG
        |--------------------------------------------------------------------------
        */

        if ($roleSession === 'pelamar') {

            [$user, $permintaan] = DB::transaction(

                function () use (
                    $validated,
                    $email,
                    $username
                ) {

                    /*
                    |--------------------------------------------------------------------------
                    | BUAT USER MAGANG
                    |--------------------------------------------------------------------------
                    */

                    $user = User::query()->create([

                        'nama' =>
                            trim(
                                $validated['full_name']
                            ),

                        'username' =>
                            $username,

                        'email' =>
                            $email,

                        'role' =>
                            'pelamar',

                        'university' =>
                            trim(
                                $validated['university']
                            ),

                        'student_id' =>
                            trim(
                                $validated['student_id']
                            ),

                        'major' =>
                            trim(
                                $validated['major']
                            ),

                        'phone' =>
                            trim(
                                $validated['phone']
                            ),

                        'description' =>
                            filled(
                                $validated['description'] ?? null
                            )
                                ? trim(
                                    $validated['description']
                                )
                                : null,

                        'password' =>
                            Hash::make(
                                $validated['password']
                            ),

                        'wajib_ganti_password' =>
                            false,
                    ]);


                    /*
                    |--------------------------------------------------------------------------
                    | BUAT PERMINTAAN MAGANG
                    |--------------------------------------------------------------------------
                    */

                    $permintaan =
                        PermintaanMagang::query()->create([

                            'user_id' =>
                                $user->id_user,

                            'nama_pemohon' =>
                                trim(
                                    $validated['full_name']
                                ),

                            'email' =>
                                $email,

                            'nama_sekolah' =>
                                trim(
                                    $validated['university']
                                ),

                            'no_induk' =>
                                trim(
                                    $validated['student_id']
                                ),

                            'jurusan' =>
                                trim(
                                    $validated['major']
                                ),

                            'no_hp' =>
                                trim(
                                    $validated['phone']
                                ),

                            'pesan' =>
                                filled(
                                    $validated['description'] ?? null
                                )
                                    ? trim(
                                        $validated['description']
                                    )
                                    : null,

                            'status' =>
                                'menunggu',

                            'akun_dibuat' =>
                                false,
                        ]);


                    /*
                    |--------------------------------------------------------------------------
                    | NOTIFIKASI ADMIN
                    |--------------------------------------------------------------------------
                    */

                    $this->kirimNotifikasiKeAdmin(
                        $permintaan->nama_pemohon,
                        'Pengajuan Magang Baru'
                    );


                    /*
                    |--------------------------------------------------------------------------
                    | NOTIFIKASI USER
                    |--------------------------------------------------------------------------
                    */

                    Notifikasi::query()->create([

                        'user_id' =>
                            $user->id_user,

                        'judul' =>
                            'Pengajuan Berhasil Dikirim',

                        'pesan' =>
                            'Pengajuan magang Anda telah diterima sistem dan sedang menunggu pemeriksaan Admin.',

                        'kategori' =>
                            'pengajuan',

                        'tipe' =>
                            'info',

                        'referensi_id' =>
                            $permintaan->id_permintaan,

                        'dibaca' =>
                            false,
                    ]);


                    return [
                        $user,
                        $permintaan,
                    ];
                }
            );


            /*
            |--------------------------------------------------------------------------
            | LOGIN USER MAGANG
            |--------------------------------------------------------------------------
            */

            event(
                new Registered($user)
            );

            Auth::login($user);

            $request->session()->regenerate();

            $request->session()->forget(
                'register_role'
            );

            return redirect()
                ->route('pengajuan.status')
                ->with(
                    'success',
                    'Pengajuan magang berhasil dikirim. Gunakan email dan kata sandi pendaftaran untuk memeriksa status.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | ALUR CALON KARYAWAN
        |--------------------------------------------------------------------------
        */

        [$user, $permintaan] = DB::transaction(

            function () use (
                $validated,
                $email,
                $username,
                $request
            ) {

                /*
                |--------------------------------------------------------------------------
                | BUAT USER CALON KARYAWAN
                |--------------------------------------------------------------------------
                */

                $user = User::query()->create([

                    'nama' =>
                        trim(
                            $validated['full_name']
                        ),

                    'username' =>
                        $username,

                    'email' =>
                        $email,

                    /*
                    | Tetap pelamar karena belum menjadi
                    | karyawan sampai diterima HRD.
                    */

                    'role' =>
                        'pelamar',

                    'university' =>
                        trim(
                            $validated['university']
                        ),

                    /*
                    | Karyawan tidak memakai student_id.
                    */

                    'student_id' =>
                        null,

                    /*
                    | Major tidak dipakai untuk karyawan.
                    */

                    'major' =>
                        null,

                    'phone' =>
                        trim(
                            $validated['phone']
                        ),

                    'description' =>
                        filled(
                            $validated['description'] ?? null
                        )
                            ? trim(
                                $validated['description']
                            )
                            : null,

                    'password' =>
                        Hash::make(
                            $validated['password']
                        ),

                    'wajib_ganti_password' =>
                        false,
                ]);


                /*
                |--------------------------------------------------------------------------
                | UPLOAD BERKAS LAMARAN
                |--------------------------------------------------------------------------
                */

                $berkasPaths = [];

                $folderBerkas =
                    'permintaan-lamaran/' .
                    Str::uuid();

                $fields = [

                    'surat_lamaran',
                    'cv',
                    'ijazah',
                    'ktp',
                    'pas_foto',
                    'skck',
                    'portfolio',
                    'pengalaman_kerja',
                ];

                foreach ($fields as $field) {

                    if ($request->hasFile($field)) {

                        $berkasPaths[
                            "{$field}_path"
                        ] =
                            $request
                                ->file($field)
                                ->store(
                                    $folderBerkas,
                                    'public'
                                );
                    }
                }


                /*
                |--------------------------------------------------------------------------
                | BUAT PERMINTAAN LAMARAN
                |--------------------------------------------------------------------------
                */

                $permintaan = null;

                if (
                    class_exists(
                        PermintaanLamaran::class
                    )
                ) {

                    $permintaan =
                        PermintaanLamaran::query()->create(

                            array_merge(

                                [

                                    'user_id' =>
                                        $user->id_user,

                                    'nama_pemohon' =>
                                        trim(
                                            $validated['full_name']
                                        ),

                                    'email' =>
                                        $email,

                                    /*
                                    |--------------------------------------------------------------------------
                                    | NIK KARYAWAN
                                    |--------------------------------------------------------------------------
                                    */

                                    'nik' =>
                                        trim(
                                            $validated['nik']
                                        ),

                                    /*
                                    |--------------------------------------------------------------------------
                                    | PENDIDIKAN TERAKHIR
                                    |--------------------------------------------------------------------------
                                    */

                                    'pendidikan_terakhir' =>
                                        trim(
                                            $validated['university']
                                        ),

                                    /*
                                    |--------------------------------------------------------------------------
                                    | POSISI YANG DILAMAR
                                    |--------------------------------------------------------------------------
                                    */

                                    'posisi' =>
                                        trim(
                                            $validated['position']
                                        ),

                                    'no_hp' =>
                                        trim(
                                            $validated['phone']
                                        ),

                                    'tanggal_lamar' =>
                                        now(),

                                    'pesan' =>
                                        filled(
                                            $validated['description'] ?? null
                                        )
                                            ? trim(
                                                $validated['description']
                                            )
                                            : null,

                                    'status' =>
                                        'menunggu',

                                    'akun_dibuat' =>
                                        false,
                                ],

                                $berkasPaths
                            )
                        );


                    /*
                    |--------------------------------------------------------------------------
                    | NOTIFIKASI ADMIN
                    |--------------------------------------------------------------------------
                    */

                    $this->kirimNotifikasiKeAdmin(
                        $permintaan->nama_pemohon,
                        'Pengajuan Lamaran Karyawan Baru'
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | NOTIFIKASI USER
                |--------------------------------------------------------------------------
                */

                Notifikasi::query()->create([

                    'user_id' =>
                        $user->id_user,

                    'judul' =>
                        'Lamaran Berhasil Dikirim',

                    'pesan' =>
                        'Pengajuan lamaran karyawan Anda telah diterima sistem dan sedang menunggu persetujuan HRD.',

                    'kategori' =>
                        'pengajuan',

                    'tipe' =>
                        'info',

                    'referensi_id' =>
                        $permintaan?->id_permintaan,

                    'dibaca' =>
                        false,
                ]);


                return [
                    $user,
                    $permintaan,
                ];
            }
        );


        /*
        |--------------------------------------------------------------------------
        | LOGIN USER KARYAWAN
        |--------------------------------------------------------------------------
        */

        event(
            new Registered($user)
        );

        Auth::login($user);

        $request->session()->regenerate();

        $request->session()->forget(
            'register_role'
        );

        return redirect()
            ->route('pengajuan.status')
            ->with(
                'success',
                'Pengajuan lamaran karyawan berhasil dikirim. Silakan tunggu pemeriksaan oleh tim HRD/Admin.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | NOTIFIKASI ADMIN
    |--------------------------------------------------------------------------
    */

    private function kirimNotifikasiKeAdmin(
        string $namaPemohon,
        string $judul = 'Pengajuan Baru'
    ): void {

        $adminIds = User::query()
            ->where('role', 'admin')
            ->pluck('id_user');

        foreach ($adminIds as $adminId) {

            Notifikasi::query()->create([

                'user_id' =>
                    $adminId,

                'judul' =>
                    $judul,

                'pesan' =>
                    sprintf(
                        '%s telah mengirim pengajuan dan menunggu konfirmasi.',
                        $namaPemohon
                    ),

                'kategori' =>
                    'pengajuan',

                'tipe' =>
                    'info',

                'dibaca' =>
                    false,
            ]);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | BUAT USERNAME UNIK
    |--------------------------------------------------------------------------
    */

    private function makeUniqueUsername(
        string $studentId,
        string $email
    ): string {

        /*
        |--------------------------------------------------------------------------
        | MAGANG
        |--------------------------------------------------------------------------
        |
        | Username berdasarkan student_id.
        |
        |--------------------------------------------------------------------------
        */

        $base = Str::of($studentId)
            ->lower()
            ->replaceMatches(
                '/[^a-z0-9_-]+/',
                ''
            )
            ->limit(
                40,
                ''
            )
            ->toString();


        /*
        |--------------------------------------------------------------------------
        | KARYAWAN
        |--------------------------------------------------------------------------
        |
        | Karena tidak memakai student_id,
        | username menggunakan bagian depan email.
        |
        |--------------------------------------------------------------------------
        */

        if (mb_strlen($base) < 4) {

            $base = Str::of(
                Str::before(
                    $email,
                    '@'
                )
            )
                ->lower()
                ->replaceMatches(
                    '/[^a-z0-9_-]+/',
                    ''
                )
                ->limit(
                    40,
                    ''
                )
                ->toString();
        }


        /*
        |--------------------------------------------------------------------------
        | FALLBACK
        |--------------------------------------------------------------------------
        */

        if (mb_strlen($base) < 4) {

            $base =
                'user' .
                random_int(
                    1000,
                    9999
                );
        }


        /*
        |--------------------------------------------------------------------------
        | CEK USERNAME
        |--------------------------------------------------------------------------
        */

        $candidate = $base;

        $suffix = 1;

        while (
            User::query()
                ->where(
                    'username',
                    $candidate
                )
                ->exists()
        ) {

            $suffixText =
                (string) $suffix;

            $candidate =
                mb_substr(
                    $base,
                    0,
                    max(
                        1,
                        50 -
                        mb_strlen(
                            $suffixText
                        )
                    )
                ) .
                $suffixText;

            $suffix++;
        }

        return $candidate;
    }
}