<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\RegisteredUserController; // <-- Tambahkan import ini di atas
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

// Halaman pertama langsung ke Login
Route::get('/', function () {
    return redirect()->route('login');
});



Route::get('/register/pelamar', function (Request $request) {
    session(['register_role' => 'pelamar']);
    return redirect()->route('register');
})->middleware('guest')->name('register.pelamar');

Route::get('/register/karyawan', function (Request $request) {
    session(['register_role' => 'karyawan']);
    return redirect()->route('register');
})->middleware('guest')->name('register.karyawan');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    // Jika Anda ingin mengubah profile menggunakan nama lengkap, dst:
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';