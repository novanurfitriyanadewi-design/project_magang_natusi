<?php

namespace App\Http\Requests\Auth;

use App\Models\PermintaanMagangAnggota;
use App\Models\PesertaMagang;
use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email'    => ['required', 'string', 'max:255'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Login portal mendukung:
     * - pengaju/pelamar: email + password pendaftaran;
     * - peserta magang: email masing-masing + password awal/baru;
     * - karyawan baru: username/email + password baru dari kartu status;
     * - role internal lain: email atau username.
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $login = Str::lower(trim((string) $this->input('email')));
        $password = (string) $this->input('password');
        $remember = $this->boolean('remember');
        $isEmail = filter_var($login, FILTER_VALIDATE_EMAIL) !== false;

        $user = null;

        // 1) Cari akun utama berdasarkan EMAIL atau USERNAME di tabel users
        $candidate = User::query()
            ->whereRaw('LOWER(email) = ?', [$login])
            ->orWhereRaw('LOWER(username) = ?', [$login])
            ->first();

        if ($candidate && Hash::check($password, $candidate->password)) {
            $user = $candidate;
        }

        // 2) KARYAWAN BARU: Fallback ke tabel permintaan_lamaran
        // Jika password di tabel users belum cocok, cek kredensial karyawan yang dibuatkan oleh admin
        if (! $user) {
            $lamaran = DB::table('permintaan_lamaran')
                ->where('status', 'disetujui')
                ->where(function ($query) use ($login) {
                    $query->whereRaw('LOWER(username_karyawan) = ?', [$login])
                          ->orWhereRaw('LOWER(email) = ?', [$login]);
                })
                ->where('password_karyawan', $password)
                ->first();

            if ($lamaran) {
                $candidateKaryawan = User::query()
                    ->where('id_user', $lamaran->user_id)
                    ->orWhereRaw('LOWER(email) = ?', [Str::lower($lamaran->email)])
                    ->first();

                if ($candidateKaryawan) {
                    // Update password & role di tabel users agar selanjutnya bisa login biasa
                    $candidateKaryawan->update([
                        'password' => bcrypt($password),
                        'role'     => 'karyawan',
                    ]);

                    $user = $candidateKaryawan;
                }
            }
        }

        // 3) PESERTA MAGANG: Cek berdasarkan email anggota kelompok
        if (! $user && $isEmail) {
            $anggota = PermintaanMagangAnggota::query()
                ->whereRaw('LOWER(email) = ?', [$login])
                ->whereNotNull('user_id')
                ->latest('id_anggota')
                ->get();

            foreach ($anggota as $item) {
                $participant = User::query()
                    ->whereKey($item->user_id)
                    ->where('role', 'peserta')
                    ->first();

                if ($participant && Hash::check($password, $participant->password)) {
                    $user = $participant;
                    break;
                }
            }
        }

        // 4) FALLBACK PESERTA MAGANG (Individu / Data Lama)
        if (! $user && $isEmail) {
            $pesertaCandidates = PesertaMagang::query()
                ->whereHas('permintaan', function ($query) use ($login) {
                    $query->whereRaw('LOWER(email) = ?', [$login]);
                })
                ->with('user')
                ->latest('id_peserta')
                ->get();

            foreach ($pesertaCandidates as $peserta) {
                $participant = $peserta->user;
                if ($participant && $participant->role === 'peserta' && Hash::check($password, $participant->password)) {
                    $user = $participant;
                    break;
                }
            }
        }

        // Jika tidak ada skema login yang cocok
        if (! $user) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => 'Email/username atau kata sandi yang Anda masukkan salah. Untuk pengaju dan peserta magang, gunakan alamat email masing-masing.',
            ]);
        }

        // Validasi khusus untuk pelamar
        if (in_array($user->role, ['pelamar', 'pelamar_karyawan'], true) && ! $isEmail) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => 'Untuk memeriksa status pengajuan, gunakan email ketua/pengaju dan kata sandi yang dibuat saat pendaftaran.',
            ]);
        }

        // Validasi Status Akun
        if (isset($user->status)) {
            $isPelamar = in_array($user->role, ['pelamar', 'pelamar_karyawan'], true);

            if (! $isPelamar && in_array($user->status, ['pending', 'menunggu'], true)) {
                throw ValidationException::withMessages([
                    'email' => 'Akun Anda masih dalam proses peninjauan oleh Admin. Silakan periksa status pengajuan Anda secara berkala.',
                ]);
            }

            if (in_array($user->status, ['ditolak', 'nonaktif'], true)) {
                throw ValidationException::withMessages([
                    'email' => 'Akun Anda telah ditolak atau dinonaktifkan. Silakan hubungi Administrator.',
                ]);
            }
        }

        Auth::login($user, $remember);
        RateLimiter::clear($this->throttleKey());
    }

    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    public function throttleKey(): string
    {
        return Str::transliterate(
            Str::lower((string) $this->input('email')).'|'.$this->ip()
        );
    }
}