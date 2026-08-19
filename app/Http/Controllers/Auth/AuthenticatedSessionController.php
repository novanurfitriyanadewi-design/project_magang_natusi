<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = Auth::user();

        if ($user->wajib_ganti_password) {
            return redirect()->route('profile.edit')->with('warning', 'Demi keamanan, silakan ganti password awal Anda sebelum melanjutkan.');
        }

        /*
         * Halaman status pengajuan hanya milik akun pendaftaran/pengaju.
         * Akun peserta (termasuk akun peserta milik ketua setelah diterima)
         * langsung masuk ke portal peserta dan tidak diarahkan ke halaman status.
         */

        // 2. Redirect Berdasarkan Role Pengguna
        return match ($user->role) {
            'superadmin'       => redirect()->intended(route('superadmin.dashboard')),
            'admin', 
            'admin_peserta'    => redirect()->intended(route('admin-peserta.dashboard')),
            'admin_karyawan'   => redirect()->intended(route('admin-karyawan.dashboard')),
            'pelamar',
            'pelamar_karyawan' => redirect()->intended(route('pengajuan.status')),
            'peserta'          => redirect()->intended(route('peserta-magang.dashboard')),
            'karyawan'         => redirect()->intended(route('karyawan.dashboard')),
            default            => redirect()->route('login')->withErrors([
                'email' => 'Role akun tidak dikenali. Silakan hubungi administrator.',
            ]),
        };
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
