<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Notifikasi;
use App\Models\PermintaanMagang;
use App\Models\PermintaanLamaran;
use App\Models\User;
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
    public function create(): View
    {
        $role = session('register_role', 'pelamar');

        return view('auth.register', [
            'registerRole' => in_array(
                $role,
                ['pelamar', 'karyawan'],
                true
            ) ? $role : 'pelamar',
        ]);
    }

    public function status(Request $request): View
    {
        $user = $request->user();

        abort_unless(
            $user->role === 'pelamar',
            403,
            'Halaman status hanya tersedia untuk pelamar.'
        );

        $permintaan = null;
        if (class_exists(PermintaanLamaran::class)) {
            $permintaan = PermintaanLamaran::query()
                ->where('user_id', $user->id_user)
                ->orWhere('email', $user->email)
                ->latest('id_permintaan')
                ->first();
        }

        if (! $permintaan) {
            $permintaan = PermintaanMagang::query()
                ->where('user_id', $user->id_user)
                ->orWhere('email', $user->email)
                ->latest('id_permintaan')
                ->first();
        }

        $notifications = $user->notifikasi()
            ->latest('id_notifikasi')
            ->limit(10)
            ->get();

        $unreadNotificationCount = $user->notifikasi()
            ->where('dibaca', false)
            ->count();

        return view('auth.status-pengajuan', compact(
            'permintaan',
            'notifications',
            'unreadNotificationCount'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $roleSession = session(
            'register_role',
            $request->input('role', 'pelamar')
        );

        if (! in_array($roleSession, ['pelamar', 'karyawan'], true)) {
            $roleSession = 'pelamar';
        }

        $permintaanMasihAktif = static fn ($query) => $query
            ->whereIn('status', ['menunggu', 'disetujui']);

        $emailRules = [
            'required',
            'string',
            'lowercase',
            'email',
            'max:255',
            Rule::unique('users', 'email'),
        ];

        $studentIdRules = [
            'required',
            'string',
            'max:50',
        ];

        // Validasi unik email & no induk untuk magang
        if ($roleSession === 'pelamar') {
            $emailRules[] = Rule::unique('permintaan_magang', 'email')
                ->where($permintaanMasihAktif);
            $studentIdRules[] = Rule::unique('permintaan_magang', 'no_induk')
                ->where($permintaanMasihAktif);
        } else {
            // Validasi unik NIK untuk karyawan
            $studentIdRules[] = Rule::unique('permintaan_lamaran', 'nik')
                ->where($permintaanMasihAktif);
        }

        // Pesan error dinamis menyesuaikan role pendaftar
        $messages = [
            'full_name.required'  => 'Nama lengkap wajib diisi.',
            'email.required'      => 'Alamat email wajib diisi.',
            'email.email'         => 'Format alamat email tidak valid.',
            'email.unique'        => 'Alamat email sudah digunakan atau masih memiliki pengajuan aktif.',
            'password.required'   => 'Kata sandi akun wajib diisi.',
            'password.min'        => 'Kata sandi minimal 8 karakter.',
            'password.confirmed'  => 'Konfirmasi kata sandi tidak sama.',
            'phone.required'     => 'Nomor telepon wajib diisi.',
            'terms.accepted'      => 'Anda harus menyetujui ketentuan pendaftaran.',
        ];

        if ($roleSession === 'karyawan') {
            $messages['university.required'] = 'Pendidikan terakhir wajib diisi.';
            $messages['student_id.required'] = 'NIK (Nomor Induk Kependudukan) wajib diisi.';
            $messages['major.required']      = 'Posisi yang dilamar wajib diisi.';
        } else {
            $messages['university.required'] = 'Asal sekolah / instansi wajib diisi.';
            $messages['student_id.required'] = 'Nomor induk (NIM/NISN) wajib diisi.';
            $messages['major.required']      = 'Jurusan wajib diisi.';
        }

        $validated = $request->validate(
            [
                'full_name'   => ['required', 'string', 'max:255'],
                'email'       => $emailRules,
                'password'    => ['required', 'string', 'min:8', 'max:100', 'confirmed'],
                'university'  => ['required', 'string', 'max:255'], // Pendidikan Terakhir (Karyawan) / Sekolah (Magang)
                'student_id'  => $studentIdRules,                   // NIK (Karyawan) / NIM (Magang)
                'major'       => ['required', 'string', 'max:255'], // Posisi Dilamar (Karyawan) / Jurusan (Magang)
                'phone'       => ['required', 'string', 'max:20'],
                'description' => ['nullable', 'string', 'max:2000'],
                'terms'       => ['accepted'],
            ],
            $messages
        );

        $email = strtolower(trim($validated['email']));
        $username = $this->makeUniqueUsername(
            $validated['student_id'],
            $email
        );

        // ==========================================
        // 1. ALUR PENDAFTARAN PELAMAR MAGANG
        // ==========================================
        if ($roleSession === 'pelamar') {
            [$user, $permintaan] = DB::transaction(function () use ($validated, $email, $username) {
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

                $permintaan = PermintaanMagang::query()->create([
                    'user_id'      => $user->id_user,
                    'nama_pemohon' => trim($validated['full_name']),
                    'email'        => $email,
                    'nama_sekolah' => trim($validated['university']),
                    'no_induk'     => trim($validated['student_id']),
                    'jurusan'      => trim($validated['major']),
                    'no_hp'        => trim($validated['phone']),
                    'pesan'        => filled($validated['description'] ?? null) ? trim($validated['description']) : null,
                    'status'       => 'menunggu',
                    'akun_dibuat'  => false,
                ]);

                $this->kirimNotifikasiKeAdmin($permintaan->nama_pemohon, 'Pengajuan Magang Baru');

                Notifikasi::query()->create([
                    'user_id'      => $user->id_user,
                    'judul'        => 'Pengajuan Berhasil Dikirim',
                    'pesan'        => 'Pengajuan magang Anda telah diterima sistem dan sedang menunggu pemeriksaan Admin.',
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
        [$user, $permintaan] = DB::transaction(function () use ($validated, $email, $username) {
            $user = User::query()->create([
                'nama'                 => trim($validated['full_name']),
                'username'             => $username,
                'email'                => $email,
                'role'                 => 'pelamar',
                'university'           => trim($validated['university']), // Disimpan sebagai Pendidikan Terakhir
                'student_id'           => trim($validated['student_id']), // Disimpan sebagai NIK
                'major'                => trim($validated['major']),      // Disimpan sebagai Posisi Dilamar
                'phone'                => trim($validated['phone']),
                'description'          => filled($validated['description'] ?? null) ? trim($validated['description']) : null,
                'password'             => Hash::make($validated['password']),
                'wajib_ganti_password' => false,
            ]);

            $permintaan = null;
            if (class_exists(PermintaanLamaran::class)) {
                $permintaan = PermintaanLamaran::query()->create([
                    'user_id'            => $user->id_user,
                    'nama_pemohon'       => trim($validated['full_name']),
                    'email'              => $email,
                    'nik'                => trim($validated['student_id']),  // Sesuai field NIK Karyawan
                    'pendidikan_terakhir' => trim($validated['university']), // Sesuai Pendidikan Terakhir
                    'posisi'             => trim($validated['major']),      // Sesuai Posisi yang Dilamar
                    'no_hp'              => trim($validated['phone']),
                    'tanggal_lamar'      => now(),
                    'pesan'              => filled($validated['description'] ?? null) ? trim($validated['description']) : null,
                    'status'             => 'menunggu',
                    'akun_dibuat'        => false,
                ]);

                $this->kirimNotifikasiKeAdmin($permintaan->nama_pemohon, 'Pengajuan Lamaran Karyawan Baru');
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
            ->with('success', 'Pengajuan lamaran karyawan berhasil dikirim. Silakan tunggu pemeriksaan oleh tim HRD/Admin.');
    }

    private function kirimNotifikasiKeAdmin(string $namaPemohon, string $judul = 'Pengajuan Baru'): void
    {
        $adminIds = User::query()
            ->where('role', 'admin')
            ->pluck('id_user');

        foreach ($adminIds as $adminId) {
            Notifikasi::query()->create([
                'user_id'  => $adminId,
                'judul'    => $judul,
                'pesan'    => sprintf('%s telah mengirim pengajuan dan menunggu konfirmasi.', $namaPemohon),
                'kategori' => 'pengajuan',
                'tipe'     => 'info',
                'dibaca'   => false,
            ]);
        }
    }

    private function makeUniqueUsername(string $studentId, string $email): string
    {
        $base = Str::of($studentId)
            ->lower()
            ->replaceMatches('/[^a-z0-9_-]+/', '')
            ->limit(40, '')
            ->toString();

        if (mb_strlen($base) < 4) {
            $base = Str::of(Str::before($email, '@'))
                ->lower()
                ->replaceMatches('/[^a-z0-9_-]+/', '')
                ->limit(40, '')
                ->toString();
        }

        if (mb_strlen($base) < 4) {
            $base = 'user' . random_int(1000, 9999);
        }

        $candidate = $base;
        $suffix = 1;

        while (User::query()->where('username', $candidate)->exists()) {
            $suffixText = (string) $suffix;
            $candidate = mb_substr(
                $base,
                0,
                max(1, 50 - mb_strlen($suffixText))
            ) . $suffixText;
            $suffix++;
        }

        return $candidate;
    }
}