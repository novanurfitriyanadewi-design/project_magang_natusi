<?php

namespace App\Http\Controllers\AdminPeserta;

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
        if (session()->has('admin_peserta')) {
            return redirect()->route('admin-peserta.dashboard');
        }

        return view('admin-peserta.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate(['username' => ['required', 'string'], 'password' => ['required', 'string']]);
        $user = User::where('username', $credentials['username'])->where('role', 'admin_peserta')->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return back()->withErrors(['username' => 'Username atau password tidak valid.'])->onlyInput('username');
        }

        $request->session()->regenerate();
        $request->session()->put('admin_peserta', $user->getKey());
        return redirect()->route('admin-peserta.dashboard');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->session()->forget('admin_peserta');
        return redirect()->route('admin-peserta.login');
    }
}
