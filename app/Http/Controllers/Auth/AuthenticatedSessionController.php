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
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        $user = $request->user();

        return match ($user->role) {
            'superadmin' => redirect()->intended(route('superadmin.dashboard')),
            'admin' => redirect()->intended(route('admin.dashboard')),
            'pelamar' => redirect()->intended(route('pengajuan.status')),
            'peserta' => redirect()->intended(route('peserta.tugas.index')),
            'karyawan' => redirect()->intended(route('dashboard')),
            default => redirect()->route('login')->withErrors([
                'email' => 'Role akun tidak dikenali. Silakan hubungi administrator.',
            ]),
        };
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
