<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
                true,
            )
                ? $role
                : 'pelamar',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate(
            [
                'full_name' => [
                    'required',
                    'string',
                    'max:255',
                ],
                'email' => [
                    'required',
                    'string',
                    'lowercase',
                    'email',
                    'max:255',
                    Rule::unique('users', 'email'),
                ],
                'university' => [
                    'required',
                    'string',
                    'max:255',
                ],
                'student_id' => [
                    'required',
                    'string',
                    'max:50',
                ],
                'major' => [
                    'required',
                    'string',
                    'max:255',
                ],
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
            [
                'full_name.required' => 'Nama lengkap wajib diisi.',
                'email.required' => 'Alamat email wajib diisi.',
                'email.email' => 'Format alamat email tidak valid.',
                'email.unique' => 'Alamat email sudah terdaftar.',
                'university.required' => 'Asal sekolah atau universitas wajib diisi.',
                'student_id.required' => 'Nomor induk wajib diisi.',
                'major.required' => 'Jurusan wajib diisi.',
                'phone.required' => 'Nomor telepon wajib diisi.',
                'terms.accepted' => 'Anda harus menyetujui ketentuan pendaftaran.',
            ],
        );

        $role = session(
            'register_role',
            $request->input('role', 'pelamar'),
        );

        if (! in_array($role, ['pelamar', 'karyawan'], true)) {
            $role = 'pelamar';
        }

        $username = $this->makeUniqueUsername(
            $validated['student_id'],
            $validated['email'],
        );

        $user = User::query()->create([
            'nama' => $validated['full_name'],
            'username' => $username,
            'email' => strtolower($validated['email']),
            'role' => $role,
            'university' => $validated['university'],
            'student_id' => $validated['student_id'],
            'major' => $validated['major'],
            'phone' => $validated['phone'],
            'description' => $validated['description'] ?? null,
            'password' => Hash::make($validated['student_id']),
            'wajib_ganti_password' => true,
        ]);

        event(new Registered($user));
        Auth::login($user);

        $request->session()->forget('register_role');

        return redirect()
            ->route('dashboard')
            ->with(
                'success',
                'Pendaftaran berhasil. Silakan lengkapi proses berikutnya.',
            );
    }

    private function makeUniqueUsername(
        string $studentId,
        string $email,
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

        while (
            User::query()
                ->where('username', $candidate)
                ->exists()
        ) {
            $suffixText = (string) $suffix;
            $candidate = mb_substr(
                $base,
                0,
                max(1, 50 - mb_strlen($suffixText)),
            ) . $suffixText;

            $suffix++;
        }

        return $candidate;
    }
}
