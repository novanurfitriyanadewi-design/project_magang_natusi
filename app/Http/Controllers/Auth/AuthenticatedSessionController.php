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

        // Redirect berdasarkan Role User yang sudah disesuaikan dengan Route milikmu
        return match ($user->role) {
            'superadmin' => redirect()->intended(route('superadmin.dashboard')),
            'admin'      => redirect()->intended(route('admin.dashboard')),
            'pelamar'    => redirect()->intended(route('pengajuan.status')),

            // FIX: pendaftar karyawan (termasuk yang statusnya masih interview)
            // pakai role 'pelamar_karyawan', dulu belum ada case-nya jadi kepentok default.
            'pelamar_karyawan' => redirect()->intended(route('pengajuan.status')),

            // PERBAIKAN DI SINI (Sesuaikan ke nama route peserta-magang yang ada)
            'peserta'    => redirect()->intended(route('peserta-magang.dashboard')), // atau route('peserta-magang.tugas.index')

            'karyawan'   => redirect()->intended(route('dashboard')),
            default      => redirect()->route('login')->withErrors([
                'email'  => 'Role akun tidak dikenali. Silakan hubungi administrator.',
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