<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        // Terima role yang dipilih dari halaman login / session.
        $role = session('register_role', 'pelamar');

        return view('auth.register', [
            'registerRole' => $role,
        ]);
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // 1. Validasi input disesuaikan dengan form lowongan magang Anda
        $request->validate([
            'full_name'   => ['required', 'string', 'max:255'],
            'email'       => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'university'  => ['required', 'string', 'max:255'],
            'student_id'  => ['required', 'string', 'max:50'],
            'major'       => ['required', 'string', 'max:255'],
            'phone'       => ['required', 'string', 'max:20'],
            'description' => ['nullable', 'string'],
            // Karena di UI awal tidak ada input password, kita buat default password (misal: gabungan NIM & Nama) 
            // ATAU Anda bisa menambahkan input password di UI. Di sini kita buat otomatis menggunakan student_id demi kemudahan.
        ]);

        // 2. Simpan data ke tabel Users (Pastikan field ini sudah ada di $fillable pada Model User)
        $user = User::create([
            'name'        => $request->full_name,
            'email'       => $request->email,
            'role'        => session('register_role', $request->input('role', 'pelamar')), 
            'university'  => $request->university,
            'student_id'  => $request->student_id,
            'major'       => $request->major,
            'phone'       => $request->phone,
            'description' => $request->description,
            // Menggunakan student_id sebagai password default jika form tidak menyediakan field password
            'password'    => Hash::make($request->student_id),
        ]);

        event(new Registered($user));

        // 3. Otomatis login setelah mendaftar
        Auth::login($user);

        // 4. Redirect ke dashboard
        return redirect(route('dashboard', absolute: false))->with('success', 'Pendaftaran magang berhasil diajukan!');
    }
}