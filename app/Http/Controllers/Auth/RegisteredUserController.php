<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Notifikasi;
use App\Models\PermintaanMagang;
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

    /**
     * Menampilkan status pengajuan milik pelamar yang sedang login.
     */
    public function status(Request $request): View
    {
        $user = $request->user();

        abort_unless(
            $user->role === 'pelamar',
            403,
            'Halaman status hanya tersedia untuk pelamar magang.'
        );

        $permintaan = PermintaanMagang::query()
            ->where('user_id', $user->id_user)
            ->latest('id_permintaan')
            ->firstOrFail();

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
        $role = session(
            'register_role',
            $request->input('role', 'pelamar')
        );

        if (! in_array($role, ['pelamar', 'karyawan'], true)) {
            $role = 'pelamar';
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

        if ($role === 'pelamar') {
            $emailRules[] = Rule::unique('permintaan_magang', 'email')
                ->where($permintaanMasihAktif);
            $studentIdRules[] = Rule::unique('permintaan_magang', 'no_induk')
                ->where($permintaanMasihAktif);
        }

        $validated = $request->validate(
            [
                'full_name' => ['required', 'string', 'max:255'],
                'email' => $emailRules,
                'password' => [
                    'required',
                    'string',
                    'min:8',
                    'max:100',
                    'confirmed',
                ],
                'university' => ['required', 'string', 'max:255'],
                'student_id' => $studentIdRules,
                'major' => ['required', 'string', 'max:255'],
                'phone' => ['required', 'string', 'max:20'],
                'description' => ['nullable', 'string', 'max:2000'],
                'terms' => ['accepted'],
            ],
            [
                'full_name.required' => 'Nama lengkap wajib diisi.',
                'email.required' => 'Alamat email wajib diisi.',
                'email.email' => 'Format alamat email tidak valid.',
                'email.unique' => 'Alamat email sudah digunakan atau masih memiliki pengajuan aktif.',
                'password.required' => 'Kata sandi akun wajib diisi.',
                'password.min' => 'Kata sandi minimal 8 karakter.',
                'password.confirmed' => 'Konfirmasi kata sandi tidak sama.',
                'university.required' => 'Asal sekolah atau universitas wajib diisi.',
                'student_id.required' => 'Nomor induk wajib diisi.',
                'student_id.unique' => 'Pengajuan dengan NIS/NIM ini masih menunggu atau sudah disetujui.',
                'major.required' => 'Jurusan wajib diisi.',
                'phone.required' => 'Nomor telepon wajib diisi.',
                'terms.accepted' => 'Anda harus menyetujui ketentuan pendaftaran.',
            ]
        );

        $email = strtolower(trim($validated['email']));
        $username = $this->makeUniqueUsername(
            $validated['student_id'],
            $email
        );

        if ($role === 'pelamar') {
            [$user, $permintaan] = DB::transaction(function () use (
                $validated,
                $email,
                $username
            ) {
                $user = User::query()->create([
                    'nama' => trim($validated['full_name']),
                    'username' => $username,
                    'email' => $email,
                    'role' => 'pelamar',
                    'university' => trim($validated['university']),
                    'student_id' => trim($validated['student_id']),
                    'major' => trim($validated['major']),
                    'phone' => trim($validated['phone']),
                    'description' => filled($validated['description'] ?? null)
                        ? trim($validated['description'])
                        : null,
                    'password' => Hash::make($validated['password']),
                    'wajib_ganti_password' => false,
                ]);

                $permintaan = PermintaanMagang::query()->create([
                    'user_id' => $user->id_user,
                    'nama_pemohon' => trim($validated['full_name']),
                    'email' => $email,
                    'nama_sekolah' => trim($validated['university']),
                    'no_induk' => trim($validated['student_id']),
                    'jurusan' => trim($validated['major']),
                    'no_hp' => trim($validated['phone']),
                    'pesan' => filled($validated['description'] ?? null)
                        ? trim($validated['description'])
                        : null,
                    'status' => 'menunggu',
                    'akun_dibuat' => false,
                ]);

                $this->kirimNotifikasiPengajuanKeAdmin($permintaan);

                Notifikasi::query()->create([
                    'user_id' => $user->id_user,
                    'judul' => 'Pengajuan Berhasil Dikirim',
                    'pesan' => 'Pengajuan magang Anda telah diterima sistem dan sedang menunggu pemeriksaan Admin.',
                    'kategori' => 'pengajuan',
                    'tipe' => 'info',
                    'referensi_id' => $permintaan->id_permintaan,
                    'dibaca' => false,
                ]);

                return [$user, $permintaan];
            });

            event(new Registered($user));
            Auth::login($user);
            $request->session()->regenerate();
            $request->session()->forget('register_role');

            return redirect()
                ->route('pengajuan.status')
                ->with(
                    'success',
                    'Pengajuan magang berhasil dikirim. Gunakan email dan kata sandi pendaftaran untuk memeriksa status pengajuan.'
                );
        }

        $user = User::query()->create([
            'nama' => trim($validated['full_name']),
            'username' => $username,
            'email' => $email,
            'role' => 'karyawan',
            'university' => trim($validated['university']),
            'student_id' => trim($validated['student_id']),
            'major' => trim($validated['major']),
            'phone' => trim($validated['phone']),
            'description' => filled($validated['description'] ?? null)
                ? trim($validated['description'])
                : null,
            'password' => Hash::make($validated['password']),
            'wajib_ganti_password' => false,
        ]);

        event(new Registered($user));
        Auth::login($user);
        $request->session()->regenerate();
        $request->session()->forget('register_role');

        return redirect()
            ->route('dashboard')
            ->with('success', 'Pendaftaran akun berhasil.');
    }

    private function kirimNotifikasiPengajuanKeAdmin(
        PermintaanMagang $permintaan
    ): void {
        $adminIds = User::query()
            ->where('role', 'admin')
            ->pluck('id_user');

        foreach ($adminIds as $adminId) {
            Notifikasi::query()->create([
                'user_id' => $adminId,
                'judul' => 'Pengajuan Magang Baru',
                'pesan' => sprintf(
                    '%s dari %s telah mengirim pengajuan magang dan menunggu konfirmasi.',
                    $permintaan->nama_pemohon,
                    $permintaan->nama_sekolah
                ),
                'kategori' => 'pengajuan',
                'tipe' => 'info',
                'referensi_id' => $permintaan->id_permintaan,
                'dibaca' => false,
            ]);
        }
    }

    private function makeUniqueUsername(
        string $studentId,
        string $email
    ): string {
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
