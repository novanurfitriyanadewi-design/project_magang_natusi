<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Divisi;
use App\Models\Jurusan;
use App\Models\Notifikasi;
use App\Models\PermintaanLamaran;
use App\Models\PermintaanMagang;
use App\Models\PermintaanMagangAnggota;
use App\Models\RiwayatBerkasMagang;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | HALAMAN REGISTER
    |--------------------------------------------------------------------------
    */

    public function create(): View
    {
        $role = session('register_role', 'pelamar');

        return view('auth.register', [
            'registerRole' => in_array($role, ['pelamar', 'karyawan'], true) ? $role : 'pelamar',
            'divisiList'   => Divisi::query()
                ->orderBy('nama_divisi')
                ->get(),
            'jurusanList'  => Jurusan::query()
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

        abort_unless(
            in_array($user->role, ['pelamar', 'pelamar_karyawan'], true),
            403,
            'Halaman status pengajuan hanya dapat diakses oleh akun pengaju.'
        );

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
                        ->orWhereHas('karyawan', function ($karyawan) use ($user) {
                            $karyawan->where('user_id', $user->id_user);
                        });
                })
                ->latest('id_permintaan')
                ->first();
        }

        /*
        |--------------------------------------------------------------------------
        | JIKA LAMARAN KARYAWAN TIDAK ADA, CEK MAGANG
        |--------------------------------------------------------------------------
        */

        if (! $permintaan) {
            $permintaan = PermintaanMagang::query()
                ->where(function ($query) use ($user) {
                    $query
                        ->where('user_id', $user->id_user)
                        ->orWhere('email', $user->email)
                        ->orWhereHas('peserta', function ($peserta) use ($user) {
                            $peserta->where('user_id', $user->id_user);
                        });
                })
                ->with(['anggota', 'riwayatBerkas'])
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

        return view('auth.status-pengajuan', compact('permintaan', 'notifications', 'unreadNotificationCount'));
    }


    /*
    |--------------------------------------------------------------------------
    | UPLOAD REVISI BERKAS MAGANG
    |--------------------------------------------------------------------------
    */

    public function uploadRevisi(Request $request, PermintaanMagang $permintaan): RedirectResponse
    {
        abort_unless($permintaan->user_id === $request->user()->id_user, 403);

        abort_unless(
            $permintaan->status === 'perlu_revisi',
            422,
            'Pengajuan ini tidak sedang memerlukan revisi.'
        );

        $validated = $request->validate([
            'jenis_berkas' => ['required', 'string', 'max:100'],
            'berkas'       => ['required', 'file', 'mimes:pdf,doc,docx,jpg,jpeg,png', 'max:5120'],
        ]);

        $version = (int) $permintaan->riwayatBerkas()->max('versi') + 1;
        $path = $request->file('berkas')->store("permintaan-magang/{$permintaan->id_permintaan}", 'public');

        RiwayatBerkasMagang::query()->create([
            'permintaan_id' => $permintaan->id_permintaan,
            'jenis_berkas'  => $validated['jenis_berkas'],
            'path'          => $path,
            'versi'         => $version,
        ]);

        $permintaan->update([
            'status' => 'menunggu',
            'catatan_revisi' => null,
        ]);

        return back()->with('success', 'Berkas revisi berhasil dikirim dan pengajuan kembali menunggu peninjauan.');
    }


    /*
    |--------------------------------------------------------------------------
    | PROSES REGISTER
    |--------------------------------------------------------------------------
    */

    public function store(Request $request): RedirectResponse
    {
        $roleSession = session('register_role', $request->input('role', 'pelamar'));

        if (! in_array($roleSession, ['pelamar', 'karyawan'], true)) {
            $roleSession = 'pelamar';
        }

        $permintaanMasihAktif = static function ($query) {
            $query->whereIn('status', ['menunggu', 'perlu_revisi', 'disetujui']);
        };

        $emailRules = [
            'required', 'string', 'lowercase', 'email', 'max:255',
            Rule::unique('users', 'email'),
        ];

        if ($roleSession === 'pelamar') {
            $emailRules[] = Rule::unique('permintaan_magang', 'email')->where($permintaanMasihAktif);
            $studentIdRules = ['required', 'string', 'max:50', Rule::unique('permintaan_magang', 'no_induk')->where($permintaanMasihAktif)];
            $nikRules = ['nullable', 'string', 'max:30'];
            $positionRules = ['nullable', 'string', 'max:255'];
        } else {
            $emailRules[] = Rule::unique('permintaan_lamaran', 'email')->where($permintaanMasihAktif);
            $studentIdRules = ['nullable', 'string', 'max:50'];
            $nikRules = ['required', 'string', 'max:30'];
            $positionRules = ['required', 'string', 'max:255', Rule::exists('divisi', 'nama_divisi')];
        }

        $fileRules = [];
        $magangRules = [];

        if ($roleSession === 'karyawan') {
            $fileRules = [
                'surat_lamaran' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
                'cv'            => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
                'ijazah'        => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
                'ktp'           => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
            ];
        } else {
            $fileRules = [
                'cv_magang'       => ['required', 'file', 'mimes:pdf,doc,docx', 'max:5120'],
                'surat_pengajuan' => ['required', 'file', 'mimes:pdf,doc,docx,jpg,jpeg,png', 'max:5120'],
            ];

            $magangRules = [
                'jenjang'                   => ['required', Rule::in(['smk', 'kuliah'])],
                'tipe_pengajuan'            => ['required', Rule::in(['individu', 'kelompok'])],
                'jumlah_anggota'            => ['required', 'integer', 'min:1', 'max:10'],
                'anggota'                   => ['nullable', 'array', 'max:9'],
                'anggota.*.nama'            => ['required', 'string', 'max:255'],
                'anggota.*.email'           => ['required', 'email', 'max:255', 'distinct'],
                'anggota.*.no_induk'        => ['required', 'string', 'max:100', 'distinct'],
                'anggota.*.jurusan'         => ['required', 'string', 'max:255'],
                'anggota.*.no_hp'            => ['required', 'string', 'max:20'],
                'anggota.*.cv_magang'       => ['required', 'file', 'mimes:pdf,doc,docx', 'max:5120'],
                'anggota.*.surat_pengajuan' => ['required', 'file', 'mimes:pdf,doc,docx,jpg,jpeg,png', 'max:5120'],
            ];
        }

        $messages = [
            'full_name.required' => 'Nama lengkap wajib diisi.',
            'email.required'     => 'Alamat email wajib diisi.',
            'email.email'        => 'Format alamat email tidak valid.',
            'email.unique'       => 'Alamat email sudah digunakan atau masih memiliki pengajuan aktif.',
            'password.required'  => 'Kata sandi akun wajib diisi.',
            'password.min'       => 'Kata sandi minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak sama.',
            'phone.required'     => 'Nomor telepon wajib diisi.',
            'terms.accepted'     => 'Anda harus menyetujui ketentuan pendaftaran.',
            'nik.required'       => 'NIK wajib diisi.',
            'position.required'  => 'Posisi yang dilamar wajib diisi.',
            'university.required' => $roleSession === 'karyawan' ? 'Pendidikan terakhir wajib diisi.' : 'Asal sekolah / instansi wajib diisi.',
            'student_id.required' => 'Nomor induk (NIM/NISN) wajib diisi.',
            'major.required'      => 'Jurusan wajib diisi.',
            'position.exists'     => 'Posisi yang dilamar harus dipilih dari divisi yang tersedia.',
            'anggota.*.cv_magang.required'       => 'CV setiap anggota kelompok wajib diunggah.',
            'anggota.*.surat_pengajuan.required' => 'Surat pengantar setiap anggota kelompok wajib diunggah.',
            'anggota.*.cv_magang.mimes'          => 'CV anggota harus berupa PDF, DOC, atau DOCX.',
            'anggota.*.surat_pengajuan.mimes'    => 'Surat pengantar anggota harus berupa PDF, DOC, DOCX, JPG, JPEG, atau PNG.',
        ];


        if ($roleSession === 'karyawan') {
            $messages['university.required'] = 'Pendidikan terakhir wajib diisi.';
            $messages['major.required']      = 'Bidang / keahlian utama wajib diisi.';
        } else {
            $messages['university.required']                 = 'Asal sekolah / instansi wajib diisi.';
            $messages['student_id.required']                 = 'Nomor induk (NIM/NISN) wajib diisi.';
            $messages['major.required']                      = 'Jurusan wajib diisi.';
            $messages['anggota.*.cv_magang.required']        = 'CV setiap anggota kelompok wajib diunggah.';
            $messages['anggota.*.surat_pengajuan.required']  = 'Surat pengantar setiap anggota kelompok wajib diunggah.';
            $messages['anggota.*.cv_magang.mimes']           = 'CV anggota harus berupa PDF, DOC, atau DOCX.';
            $messages['anggota.*.surat_pengajuan.mimes']     = 'Surat pengantar anggota harus berupa PDF, DOC, DOCX, JPG, JPEG, atau PNG.';
        }

        $fileRules = [];
        $magangRules = [];

        if ($roleSession === 'karyawan') {
            $fileRules = [
    'surat_lamaran' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
    'cv'            => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
    'ijazah'        => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
    'ktp'           => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
];
        } else {
            $fileRules = [
                'cv_magang'       => ['required', 'file', 'mimes:pdf,doc,docx', 'max:5120'],
                'surat_pengajuan' => ['required', 'file', 'mimes:pdf,doc,docx,jpg,jpeg,png', 'max:5120'],
            ];
            $magangRules = [
                'jenjang'                   => ['required', Rule::in(['smk', 'kuliah'])],
                'tipe_pengajuan'            => ['required', Rule::in(['individu', 'kelompok'])],
                'jumlah_anggota'            => ['required', 'integer', 'min:1', 'max:10'],
                'anggota'                   => ['nullable', 'array', 'max:9'],
                'anggota.*.nama'            => ['required', 'string', 'max:255'],
                'anggota.*.email'           => ['required', 'email', 'max:255', 'distinct'],
                'anggota.*.no_induk'        => ['required', 'string', 'max:100', 'distinct'],
                'anggota.*.jurusan'         => ['required', 'string', 'max:255'],
                'anggota.*.no_hp'           => ['required', 'string', 'max:20'],
                'anggota.*.cv_magang'       => ['required', 'file', 'mimes:pdf,doc,docx', 'max:5120'],
                'anggota.*.surat_pengajuan' => ['required', 'file', 'mimes:pdf,doc,docx,jpg,jpeg,png', 'max:5120'],
            ];
        }


        $validated = $request->validate(
            array_merge(
                [
                    'full_name'   => ['required', 'string', 'max:255'],
                    'email'       => $emailRules,
                    'password'    => ['required', 'string', 'min:8', 'max:100', 'confirmed'],
                    'university'  => ['required', 'string', 'max:255'],
                    'student_id'  => $studentIdRules,
                    'nik'         => $nikRules,
                    'major'       => [$roleSession === 'pelamar' ? 'required' : 'nullable', 'string', 'max:255'],
                    'position'    => $positionRules,
                    'phone'       => ['required', 'string', 'max:20'],
                    'description' => ['nullable', 'string', 'max:2000'],
                    'terms'       => ['accepted'],
                ],
                $magangRules,
                $fileRules
            ),
            $messages
        );

        if ($roleSession === 'pelamar') {
            $validated['tipe_pengajuan'] = $validated['tipe_pengajuan'] ?? 'individu';
            $validated['jumlah_anggota'] = $validated['tipe_pengajuan'] === 'individu' ? 1 : (int) ($validated['jumlah_anggota'] ?? 1);

            if ($validated['tipe_pengajuan'] === 'kelompok' && $validated['jumlah_anggota'] < 2) {
                throw ValidationException::withMessages([
                    'jumlah_anggota' => 'Pengajuan kelompok minimal terdiri dari 2 orang.',
                ]);
            }

            $anggotaTambahan = array_values($validated['anggota'] ?? []);
            $expectedAdditional = max(0, $validated['jumlah_anggota'] - 1);

            if (count($anggotaTambahan) !== $expectedAdditional) {
                throw ValidationException::withMessages([
                    'jumlah_anggota' => "Jumlah data anggota tambahan harus {$expectedAdditional} orang.",
                ]);
            }

            $allEmails = collect([$validated['email']])
                ->merge(collect($anggotaTambahan)->pluck('email'))
                ->map(fn ($value) => strtolower(trim((string) $value)));

            $allStudentIds = collect([$validated['student_id']])
                ->merge(collect($anggotaTambahan)->pluck('no_induk'))
                ->map(fn ($value) => trim((string) $value));

            if ($allEmails->unique()->count() !== $allEmails->count()) {
                throw ValidationException::withMessages(['anggota' => 'Email setiap anggota kelompok harus berbeda.']);
            }

            if ($allStudentIds->unique()->count() !== $allStudentIds->count()) {
                throw ValidationException::withMessages(['anggota' => 'NIS/NIM setiap anggota kelompok harus berbeda.']);
            }

            $additionalEmails = collect($anggotaTambahan)->pluck('email')->map(fn ($value) => strtolower(trim((string) $value)))->filter()->values();

            if ($additionalEmails->isNotEmpty() && User::query()->whereIn('email', $additionalEmails->all())->exists()) {
                throw ValidationException::withMessages(['anggota' => 'Salah satu email anggota sudah digunakan oleh akun lain.']);
            }

            if ($additionalEmails->isNotEmpty() && PermintaanMagang::query()->whereIn('email', $additionalEmails->all())->whereIn('status', ['menunggu', 'perlu_revisi', 'disetujui'])->exists()) {
                throw ValidationException::withMessages(['anggota' => 'Salah satu email anggota sudah memiliki pengajuan magang aktif.']);
            }

            $additionalStudentIds = collect($anggotaTambahan)->pluck('no_induk')->map(fn ($value) => trim((string) $value))->filter()->values();

            if ($additionalStudentIds->isNotEmpty() && PermintaanMagang::query()->whereIn('no_induk', $additionalStudentIds->all())->whereIn('status', ['menunggu', 'perlu_revisi', 'disetujui'])->exists()) {
                throw ValidationException::withMessages(['anggota' => 'Salah satu NIS/NIM anggota sudah memiliki pengajuan magang aktif.']);
            }

            $activeMemberEmailExists = PermintaanMagangAnggota::query()
                ->whereIn('email', $allEmails->all())
                ->whereHas('permintaan', fn ($query) => $query->whereIn('status', ['menunggu', 'perlu_revisi', 'disetujui']))
                ->exists();

            if ($activeMemberEmailExists) {
                throw ValidationException::withMessages(['anggota' => 'Salah satu email anggota tercatat dalam kelompok magang lain yang masih aktif.']);
            }
        }


        // Simpan Data User
        $user = User::create([
            'name'     => $validated['full_name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role'     => $roleSession === 'karyawan' ? 'pelamar_karyawan' : 'pelamar',
        ]);

        $magangBerkas = [];
        $anggotaBerkas = [];
        if ($roleSession === 'pelamar') {
            $folderMagang = 'permintaan-magang/'.Str::uuid();
            $magangBerkas = [
                'cv_path'              => $request->file('cv_magang')->store($folderMagang.'/ketua', 'public'),
                'surat_pengajuan_path' => $request->file('surat_pengajuan')->store($folderMagang.'/ketua', 'public'),
            ];

            foreach (array_values($validated['anggota'] ?? []) as $index => $anggota) {
                $cv = $request->file("anggota.{$index}.cv_magang");
                $surat = $request->file("anggota.{$index}.surat_pengajuan");

                if (! $cv || ! $surat) {
                    throw ValidationException::withMessages([
                        "anggota.{$index}.cv_magang" => 'CV dan surat pengantar setiap anggota wajib diunggah.',
                    ]);
                }

                $folderAnggota = $folderMagang.'/anggota-'.($index + 2);
                $anggotaBerkas[$index] = [
                    'cv_path'              => $cv->store($folderAnggota, 'public'),
                    'surat_pengajuan_path' => $surat->store($folderAnggota, 'public'),
                ];
            }
        }

        $email = strtolower(trim($validated['email']));

        $username = $roleSession === 'pelamar'
            ? $this->makeApplicantUsernameFromEmail($email)
            : $this->makeUniqueUsername($validated['student_id'], $email);

        // ==========================================
        // 1. ALUR PENDAFTARAN PELAMAR MAGANG
        // ==========================================
        if ($roleSession === 'pelamar') {
            [$user, $permintaan] = DB::transaction(function () use ($validated, $email, $username, $magangBerkas, $anggotaBerkas) {
                $user = User::query()->create([
                    'nama'                 => trim($validated['full_name']),
                    'username'             => $username,
                    'email'                => $email,
                    'role'                 => 'pelamar',
                    'university'           => trim($validated['university']),
                    'student_id'           => trim($validated['student_id']),
                    'major'                => trim($validated['major']),
                    'phone'                => trim($validated['phone']),
                    'description'          => filled($validated['description'] ?? null) ? trim($validated['description']) : null,
                    'password'             => Hash::make($validated['password']),
                    'wajib_ganti_password' => false,
                ]);

                $permintaan = PermintaanMagang::query()->create(array_merge([
                    'user_id'        => $user->id_user,
                    'nama_pemohon'   => trim($validated['full_name']),
                    'email'          => $email,
                    'nama_sekolah'   => trim($validated['university']),
                    'no_induk'       => trim($validated['student_id']),
                    'jurusan'        => trim($validated['major']),
                    'jenjang'        => $validated['jenjang'],
                    'tipe_pengajuan' => $validated['tipe_pengajuan'],
                    'jumlah_anggota' => $validated['jumlah_anggota'],
                    'no_hp'          => trim($validated['phone']),
                    'pesan'          => filled($validated['description'] ?? null) ? trim($validated['description']) : null,
                    'status'         => 'menunggu',
                    'akun_dibuat'    => false,
                ], $magangBerkas));

                $permintaan->anggota()->create([
                    'user_id'              => $user->id_user,
                    'nama'                 => trim($validated['full_name']),
                    'email'                => $email,
                    'no_induk'             => trim($validated['student_id']),
                    'jurusan'              => trim($validated['major']),
                    'no_hp'                => trim($validated['phone']),
                    'cv_path'              => $magangBerkas['cv_path'],
                    'surat_pengajuan_path' => $magangBerkas['surat_pengajuan_path'],
                    'is_ketua'             => true,
                ]);

                foreach (array_values($validated['anggota'] ?? []) as $index => $anggota) {
                    $permintaan->anggota()->create([
                        'nama'                 => trim($anggota['nama']),
                        'email'                => strtolower(trim($anggota['email'])),
                        'no_induk'             => trim($anggota['no_induk']),
                        'jurusan'              => trim($anggota['jurusan']),
                        'no_hp'                => trim($anggota['no_hp']),
                        'cv_path'              => $anggotaBerkas[$index]['cv_path'] ?? null,
                        'surat_pengajuan_path' => $anggotaBerkas[$index]['surat_pengajuan_path'] ?? null,
                        'is_ketua'             => false,
                    ]);
                }

                $this->kirimNotifikasiKeAdmin(
                    $permintaan->nama_pemohon,
                    'Pengajuan Magang Baru',
                    ['admin', 'admin_peserta', 'superadmin'],
                    $permintaan->id_permintaan,
                    $permintaan->jumlah_anggota
                );

                Notifikasi::query()->create([
                    'user_id'      => $user->id_user,
                    'judul'        => 'Pengajuan Berhasil Dikirim',
                    'pesan'        => $permintaan->jumlah_anggota > 1
                        ? "Pengajuan magang kelompok untuk {$permintaan->jumlah_anggota} orang telah diterima sistem dan sedang menunggu pemeriksaan Admin."
                        : 'Pengajuan magang Anda telah diterima sistem dan sedang menunggu pemeriksaan Admin.',
                    'kategori'     => 'pengajuan',
                    'tipe'         => 'info',
                    'referensi_id' => $permintaan->id_permintaan,
                    'dibaca'       => false,
                ]);

                return [$user, $permintaan];
            });

            event(new Registered($user));
            Auth::login($user);
            $request->session()->regenerate();
            $request->session()->forget('register_role');

            return redirect()
                ->route('pengajuan.status')
                ->with('success', 'Pengajuan magang berhasil dikirim. Gunakan email dan kata sandi pendaftaran untuk memeriksa status.');
        }

        // ==========================================
        // 2. ALUR PENDAFTARAN CALON KARYAWAN
        // ==========================================
        [$user, $permintaan] = DB::transaction(function () use ($validated, $email, $username, $request) {
            $user = User::query()->create([
                'nama'                 => trim($validated['full_name']),
                'username'             => $username,
                'email'                => $email,
                'role'                 => 'pelamar',
                'university'           => trim($validated['university']),
                'student_id'           => trim($validated['student_id']),
                'major'                => trim($validated['major']),
                'phone'                => trim($validated['phone']),
                'description'          => filled($validated['description'] ?? null) ? trim($validated['description']) : null,
                'password'             => Hash::make($validated['password']),
                'wajib_ganti_password' => false,
            ]);

            $berkasPaths = [];
            $folderBerkas = 'permintaan-lamaran/' . Str::uuid();

            foreach (['surat_lamaran', 'cv', 'ijazah', 'ktp'] as $field) {
                if ($request->hasFile($field)) {
                    $berkasPaths["{$field}_path"] = $request->file($field)->store($folderBerkas, 'public');
                }
            }

            $permintaan = null;
            if (class_exists(PermintaanLamaran::class)) {
                $permintaan = PermintaanLamaran::query()->create(array_merge([
                    'user_id'             => $user->id_user,
                    'nama_pemohon'        => trim($validated['full_name']),
                    'email'               => $email,
                    'pendidikan_terakhir' => trim($validated['university']),
                    'posisi'              => trim($validated['position']),
                    'bidang_keahlian'     => trim($validated['major']),
                    'no_hp'               => trim($validated['phone']),
                    'tanggal_lamar'       => now(),
                    'pesan'               => filled($validated['description'] ?? null) ? trim($validated['description']) : null,
                    'status'              => 'menunggu',
                    'akun_dibuat'         => false,
                ], $berkasPaths));

                $this->kirimNotifikasiKeAdmin(
                    $permintaan->nama_pemohon,
                    'Pengajuan Lamaran Karyawan Baru',
                    ['admin', 'admin_karyawan', 'superadmin'],
                    $permintaan->id_permintaan
                );
            }

            Notifikasi::query()->create([
                'user_id'      => $user->id_user,
                'judul'        => 'Lamaran Berhasil Dikirim',
                'pesan'        => 'Pengajuan lamaran karyawan Anda telah diterima sistem dan sedang menunggu persetujuan HRD.',
                'kategori'     => 'pengajuan',
                'tipe'         => 'info',
                'referensi_id' => $permintaan?->id_permintaan ?? null,
                'dibaca'       => false,
            ]);

            return [$user, $permintaan];
        });


        event(new Registered($user));
        Auth::login($user);
        $request->session()->regenerate();
        $request->session()->forget('register_role');

        return redirect()
            ->route('pengajuan.status')
            ->with('success', 'Lamaran berhasil dikirim. Gunakan email dan kata sandi pendaftaran untuk memeriksa status.');
    }
}