<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminController extends Controller
{
    private const ADMIN_ROLES = [
        'admin',
        'admin_karyawan',
        'admin_peserta',
    ];

    // Menampilkan seluruh akun administrator.
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));

        $admins = User::query()
            ->whereIn('role', self::ADMIN_ROLES)
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('nama', 'like', "%{$search}%")
                        ->orWhere('username', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->latest('id_user')
            ->paginate(5)
            ->withQueryString();

        $totalAdmins = User::query()
            ->whereIn('role', self::ADMIN_ROLES)
            ->count();

        $adminsThisMonth = User::query()
            ->whereIn('role', self::ADMIN_ROLES)
            ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->count();

        $latestAdmin = User::query()
            ->whereIn('role', self::ADMIN_ROLES)
            ->latest('id_user')
            ->first(['id_user', 'nama', 'created_at']);

        return view('superadmin.admin', compact(
            'admins',
            'search',
            'totalAdmins',
            'adminsThisMonth',
            'latestAdmin',
        ));
    }

    // Menyimpan akun administrator baru.
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'username' => [
                'required',
                'string',
                'min:4',
                'max:50',
                'alpha_dash',
                Rule::unique('users', 'username'),
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:100',
                Rule::unique('users', 'email'),
            ],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'string', Rule::in(self::ADMIN_ROLES)],
        ], [
            'nama.required' => 'Nama admin wajib diisi.',
            'username.required' => 'Username wajib diisi.',
            'username.alpha_dash' => 'Username hanya boleh berisi huruf, angka, tanda hubung, dan garis bawah.',
            'username.unique' => 'Username tersebut sudah digunakan.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email belum valid.',
            'email.unique' => 'Email tersebut sudah digunakan.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak sama.',
            'role.required' => 'Role admin wajib dipilih.',
            'role.in' => 'Role yang dipilih tidak valid.',
        ]);

        User::query()->create([
            'nama' => $validated['nama'],
            'username' => strtolower($validated['username']),
            'email' => strtolower($validated['email']),
            'password' => $validated['password'],
            'role' => $validated['role'],
            'wajib_ganti_password' => false,
        ]);

        $roleLabel = match ($validated['role']) {
            'admin_karyawan' => 'Admin Karyawan',
            'admin_peserta' => 'Admin Peserta Magang',
            default => 'Admin',
        };

        return redirect()
            ->route('superadmin.admin')
            ->with('success', "Akun {$roleLabel} berhasil ditambahkan dan langsung dapat digunakan.");
    }

    // Memperbarui data administrator.
    public function update(Request $request, User $admin): RedirectResponse
    {
        abort_unless(in_array($admin->role, self::ADMIN_ROLES), 404);

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'username' => [
                'required',
                'string',
                'min:4',
                'max:50',
                'alpha_dash',
                Rule::unique('users', 'username')->ignore($admin->id_user, 'id_user'),
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:100',
                Rule::unique('users', 'email')->ignore($admin->id_user, 'id_user'),
            ],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'string', Rule::in(self::ADMIN_ROLES)],
        ], [
            'nama.required' => 'Nama admin wajib diisi.',
            'username.required' => 'Username wajib diisi.',
            'username.alpha_dash' => 'Username hanya boleh berisi huruf, angka, tanda hubung, dan garis bawah.',
            'username.unique' => 'Username tersebut sudah digunakan.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email belum valid.',
            'email.unique' => 'Email tersebut sudah digunakan.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak sama.',
            'role.required' => 'Role admin wajib dipilih.',
            'role.in' => 'Role yang dipilih tidak valid.',
        ]);

        $admin->fill([
            'nama' => $validated['nama'],
            'username' => strtolower($validated['username']),
            'email' => strtolower($validated['email']),
        ]);

        if (! empty($validated['password'])) {
            $admin->password = $validated['password'];
        }

        $admin->role = $validated['role'];
        $admin->save();

        return redirect()
            ->route('superadmin.admin')
            ->with('success', 'Data admin berhasil diperbarui.');
    }

    // Menghapus akun administrator.
    public function destroy(User $admin): RedirectResponse
    {
        abort_unless(in_array($admin->role, self::ADMIN_ROLES), 404);

        $adminName = $admin->nama;
        $admin->delete();

        return redirect()
            ->route('superadmin.admin')
            ->with('success', "Akun {$adminName} berhasil dihapus.");
    }
}
