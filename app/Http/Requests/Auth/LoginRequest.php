<?php

namespace App\Http\Requests\Auth;

use App\Models\PermintaanMagangAnggota;
use App\Models\PesertaMagang;
use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
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
            'email' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Login portal mendukung:
     * - pengaju/pelamar: WAJIB email + password pendaftaran;
     * - peserta magang: email masing-masing + password awal/baru;
     * - role internal lain: email atau username seperti sebelumnya.
     *
     * Akun peserta milik ketua sengaja dapat memiliki users.email = null
     * karena email ketua tetap dipakai akun pengajuan. Karena itu email
     * peserta ketua dicocokkan melalui permintaan_magang_anggota atau
     * permintaan_magang -> peserta_magang.
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $login = Str::lower(trim((string) $this->input('email')));
        $password = (string) $this->input('password');
        $remember = $this->boolean('remember');
        $isEmail = filter_var($login, FILTER_VALIDATE_EMAIL) !== false;

        $user = null;

        if ($isEmail) {
            // 1) Akun normal yang memang menyimpan email pada tabel users.
            $candidate = User::query()->whereRaw('LOWER(email) = ?', [$login])->first();
            if ($candidate && Hash::check($password, $candidate->password)) {
                $user = $candidate;
            }

            // 2) Jika password bukan milik akun pengajuan, cari akun peserta
            //    berdasarkan email anggota. Ini membuat ketua tetap dapat
            //    memakai email yang sama untuk akun peserta dengan password
            //    peserta yang berbeda.
            if (! $user) {
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

            // 3) Fallback untuk pengajuan individu/data lama yang belum punya
            //    record anggota kelompok lengkap.
            if (! $user) {
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
        } else {
            // Username tetap dipertahankan untuk admin/karyawan/akun internal.
            $candidate = User::query()->where('username', trim((string) $this->input('email')))->first();
            if ($candidate && Hash::check($password, $candidate->password)) {
                $user = $candidate;
            }
        }

        if (! $user) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => 'Email/username atau kata sandi yang Anda masukkan salah. Untuk pengaju dan peserta magang, gunakan alamat email masing-masing.',
            ]);
        }

        // Halaman status/verifikasi tetap hanya boleh memakai email ketua/pengaju.
        if (in_array($user->role, ['pelamar', 'pelamar_karyawan'], true) && ! $isEmail) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => 'Untuk memeriksa status pengajuan, gunakan email ketua/pengaju dan kata sandi yang dibuat saat pendaftaran.',
            ]);
        }

        if (isset($user->status)) {
            if (in_array($user->status, ['pending', 'menunggu'], true)) {
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
