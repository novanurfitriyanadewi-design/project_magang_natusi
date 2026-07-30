<?php

namespace App\Http\Controllers\AdminKaryawan;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function create(): View|RedirectResponse
    {
        if (session()->has('admin_karyawan')) {
            return redirect()->route('admin-karyawan.dashboard');
        }

        return view('admin-karyawan.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate(['username' => ['required', 'string'], 'password' => ['required', 'string']]);
        $user = User::where('username', $credentials['username'])->where('role', 'admin_karyawan')->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return back()->withErrors(['username' => 'Username atau password tidak valid.'])->onlyInput('username');
        }

        $request->session()->regenerate();
        $request->session()->put('admin_karyawan', $user->getKey());
        return redirect()->route('admin-karyawan.dashboard');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->session()->forget('admin_karyawan');
        return redirect()->route('admin-karyawan.login');
    }
}
