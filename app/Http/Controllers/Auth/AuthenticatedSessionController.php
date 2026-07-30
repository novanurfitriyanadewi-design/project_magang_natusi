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

        // 1. Cek apakah pengguna adalah Karyawan / Peserta yang BARU DISETUJUI
        // Agar mereka diarahkan ke halaman status pendaftaran dulu untuk melihat username & password baru
        $permintaanLamaran = null;
        if (class_exists('\App\Models\PermintaanLamaran')) {
            $permintaanLamaran = \App\Models\PermintaanLamaran::where('email', $user->email)
                ->latest()
                ->first();
        }

        $permintaanMagang = \App\Models\PermintaanMagang::where('email', $user->email)
            ->latest()
            ->first();

        // Jika statusnya baru disetujui (APPROVED) dan belum dikonfirmasi masuk ke dashboard
        $isNewlyApprovedKaryawan = $permintaanLamaran && in_array($permintaanLamaran->status, ['APPROVED', 'disetujui']);
        $isNewlyApprovedMagang   = $permintaanMagang && in_array($permintaanMagang->status, ['APPROVED', 'disetujui']);

        if (($user->role === 'karyawan' && $isNewlyApprovedKaryawan) || ($user->role === 'peserta' && $isNewlyApprovedMagang)) {
            // Jika dipaksa ingin melihat status pengajuan dulu
            if (!$request->session()->has('has_seen_credentials')) {
                return redirect()->route('pengajuan.status');
            }
        }

        // 2. Redirect Berdasarkan Role Pengguna
        return match ($user->role) {
            'superadmin'       => redirect()->intended(route('superadmin.dashboard')),
            'admin'            => redirect()->intended(route('admin.dashboard')),
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
