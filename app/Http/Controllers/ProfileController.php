<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate(
            [
                'nama' => [
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
                    Rule::unique($user->getTable(), 'email')
                        ->ignore(
                            $user->getKey(),
                            $user->getKeyName(),
                        ),
                ],
            ],
            [
                'nama.required' => 'Nama lengkap wajib diisi.',
                'nama.max' => 'Nama lengkap maksimal 255 karakter.',
                'email.required' => 'Alamat email wajib diisi.',
                'email.email' => 'Format alamat email tidak valid.',
                'email.unique' => 'Alamat email sudah digunakan.',
            ],
        );

        $emailChanged = $user->email !== $validated['email'];

        $user->nama = $validated['nama'];
        $user->email = $validated['email'];

        if (
            $emailChanged
            && array_key_exists('email_verified_at', $user->getAttributes())
        ) {
            $user->email_verified_at = null;
        }

        $user->save();

        return redirect()
            ->route('profile.edit')
            ->with('status', 'profile-updated');
    }

    public function showPhoto(Request $request): StreamedResponse
    {
        $user = $request->user();

        abort_unless(
            Schema::hasColumn($user->getTable(), 'foto_profil'),
            404,
        );

        $path = $this->normalizePhotoPath($user->foto_profil);

        abort_if(
            $path === null
            || ! Storage::disk('public')->exists($path),
            404,
            'Foto profil tidak ditemukan.',
        );

        return Storage::disk('public')->response(
            $path,
            basename($path),
            [
                'Cache-Control' => 'private, max-age=3600',
            ],
        );
    }

    public function updatePhoto(Request $request): RedirectResponse
    {
        $user = $request->user();

        if (! Schema::hasColumn($user->getTable(), 'foto_profil')) {
            return redirect()
                ->route('profile.edit')
                ->withErrors([
                    'foto_profil' => 'Kolom foto profil belum tersedia. Jalankan php artisan migrate terlebih dahulu.',
                ]);
        }

        $validated = $request->validate(
            [
                'foto_profil' => [
                    'required',
                    'file',
                    'image',
                    'mimes:jpg,jpeg,png,webp',
                    'max:2048',
                ],
            ],
            [
                'foto_profil.required' => 'Pilih foto profil terlebih dahulu.',
                'foto_profil.file' => 'Foto profil gagal dibaca sebagai file.',
                'foto_profil.image' => 'File harus berupa gambar.',
                'foto_profil.mimes' => 'Foto harus berformat JPG, PNG, atau WEBP.',
                'foto_profil.max' => 'Ukuran foto maksimal 2 MB.',
            ],
        );

        $oldPhoto = $this->normalizePhotoPath($user->foto_profil);

        $newPhoto = $validated['foto_profil']->store(
            'profile-photos',
            'public',
        );

        if (! $newPhoto) {
            return redirect()
                ->route('profile.edit')
                ->withErrors([
                    'foto_profil' => 'Foto gagal disimpan ke penyimpanan aplikasi.',
                ]);
        }

        try {
            $user->foto_profil = $newPhoto;
            $user->save();
        } catch (Throwable $exception) {
            Storage::disk('public')->delete($newPhoto);

            throw $exception;
        }

        if (
            $oldPhoto
            && $oldPhoto !== $newPhoto
            && Storage::disk('public')->exists($oldPhoto)
        ) {
            Storage::disk('public')->delete($oldPhoto);
        }

        return redirect()
            ->route('profile.edit')
            ->with('status', 'photo-updated');
    }

    public function destroyPhoto(Request $request): RedirectResponse
    {
        $user = $request->user();

        if (! Schema::hasColumn($user->getTable(), 'foto_profil')) {
            return redirect()
                ->route('profile.edit');
        }

        $oldPhoto = $this->normalizePhotoPath($user->foto_profil);

        $user->foto_profil = null;
        $user->save();

        if ($oldPhoto && Storage::disk('public')->exists($oldPhoto)) {
            Storage::disk('public')->delete($oldPhoto);
        }

        return redirect()
            ->route('profile.edit')
            ->with('status', 'photo-removed');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => [
                'required',
                'current_password',
            ],
        ]);

        $user = $request->user();
        $oldPhoto = Schema::hasColumn($user->getTable(), 'foto_profil')
            ? $this->normalizePhotoPath($user->foto_profil)
            : null;

        auth()->logout();

        $user->delete();

        if ($oldPhoto && Storage::disk('public')->exists($oldPhoto)) {
            Storage::disk('public')->delete($oldPhoto);
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    private function normalizePhotoPath(?string $path): ?string
    {
        $normalized = trim((string) $path);

        if ($normalized === '') {
            return null;
        }

        $normalized = str_replace('\\', '/', $normalized);
        $normalized = ltrim($normalized, '/');
        $normalized = preg_replace(
            '#^(public/|storage/)#',
            '',
            $normalized,
        );

        return $normalized !== '' ? $normalized : null;
    }
}
