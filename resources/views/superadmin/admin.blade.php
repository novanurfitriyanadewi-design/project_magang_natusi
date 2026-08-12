@extends('layouts.portal')

@section('title', 'Kelola Admin')

@section('content')
@php
    // Fallback jika $admins tidak ada atau bukan paginator
    if (!isset($admins) || !method_exists($admins, 'firstItem')) {
        $admins = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 6, 1);
    }
@endphp

<div x-data="{
    addOpen: @js($errors->any() && old('form_context') === 'create'),
    editOpen: @js($errors->any() && old('form_context') === 'edit'),
    editAdmin: {
        action: @js(old('admin_id') ? route('superadmin.admin.update', old('admin_id')) : ''),
        id: @js(old('admin_id', '')),
        nama: @js(old('form_context') === 'edit' ? old('nama', '') : ''),
        username: @js(old('form_context') === 'edit' ? old('username', '') : ''),
        email: @js(old('form_context') === 'edit' ? old('email', '') : ''),
        role: @js(old('form_context') === 'edit' ? old('role', '') : ''),
    },
    openEdit(admin) {
        this.editAdmin = admin;
        this.editOpen = true;
    },
    closeModals() {
        this.addOpen = false;
        this.editOpen = false;
    }
}" @keydown.escape.window="closeModals()" x-effect="document.body.classList.toggle('overflow-hidden', addOpen || editOpen)">

    {{-- Judul --}}
    <section class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="mt-5 text-2xl font-extrabold tracking-tight text-slate-950 sm:text-3xl">Daftar Administrator</h1>
            <p class="mt-1 text-sm text-slate-500">Kelola akun admin yang dapat mengoperasikan portal magang CV Natusi.</p>
        </div>
        <button type="button" @click="addOpen = true" class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-sky-500 to-blue-600 px-5 py-2.5 text-sm font-bold text-white shadow-[0_10px_25px_rgba(2,132,199,0.25)] transition duration-200 hover:-translate-y-0.5 hover:from-sky-600 hover:to-blue-700">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
            Tambah Admin Baru
        </button>
    </section>

    {{-- Tabel data --}}
    <section class="mt-5 overflow-hidden rounded-3xl border border-sky-100/90 bg-white/95 shadow-[0_20px_50px_rgba(15,52,94,0.09)] backdrop-blur">

        {{-- Header gradasi biru --}}
        <div class="flex flex-col gap-4 border-b border-sky-100 bg-gradient-to-r from-sky-50 via-blue-50 to-cyan-50 px-6 py-5 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-lg font-bold text-slate-950">Data Administrator</h2>
                <p class="mt-0.5 text-sm text-slate-500">Semua admin memiliki hak akses yang sama dan langsung aktif setelah dibuat.</p>
            </div>
        </div>

        {{-- Tabel --}}
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead>
                    <tr>
                        <th scope="col" class="px-5 py-3.5 text-left text-[11px] font-bold uppercase tracking-[0.09em] text-slate-500">Nama Admin</th>
                        <th scope="col" class="px-5 py-3.5 text-left text-[11px] font-bold uppercase tracking-[0.09em] text-slate-500">Username</th>
                        <th scope="col" class="px-5 py-3.5 text-left text-[11px] font-bold uppercase tracking-[0.09em] text-slate-500">Role</th>
                        <th scope="col" class="px-5 py-3.5 text-left text-[11px] font-bold uppercase tracking-[0.09em] text-slate-500">Tanggal Dibuat</th>
                        <th scope="col" class="px-5 py-3.5 text-center text-[11px] font-bold uppercase tracking-[0.09em] text-slate-500">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white/80">
                    @forelse ($admins as $admin)
                    <tr class="transition duration-200 hover:bg-sky-50/80">
                        <td class="whitespace-nowrap px-5 py-4">
                            <div class="flex items-center gap-3">
                                <span class="grid h-10 w-10 shrink-0 place-items-center rounded-2xl bg-gradient-to-br from-sky-100 to-cyan-100 text-xs font-extrabold text-sky-700 ring-1 ring-sky-200/80">
                                    {{ strtoupper(mb_substr(trim($admin->nama ?: 'AD'), 0, 2)) }}
                                </span>
                                <div class="min-w-0">
                                    <p class="max-w-64 truncate text-sm font-bold text-slate-900">{{ $admin->nama }}</p>
                                    <p class="max-w-64 truncate text-xs text-slate-500">{{ $admin->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="whitespace-nowrap px-5 py-4">
                            <span class="inline-flex rounded-full bg-sky-50 px-3 py-1 text-xs font-bold text-sky-700 ring-1 ring-sky-100">{{ '@'.$admin->username }}</span>
                        </td>
                        <td class="whitespace-nowrap px-5 py-4">
                            @php
                                $roleLabel = match ($admin->role) {
                                    'admin_karyawan' => 'Admin Karyawan',
                                    'admin_peserta' => 'Admin Peserta Magang',
                                    default => 'Admin',
                                };
                                $roleColor = match ($admin->role) {
                                    'admin_karyawan' => 'bg-emerald-50 text-emerald-700 ring-emerald-100',
                                    'admin_peserta' => 'bg-violet-50 text-violet-700 ring-violet-100',
                                    default => 'bg-sky-50 text-sky-700 ring-sky-100',
                                };
                            @endphp
                            <span class="inline-flex rounded-full {{ $roleColor }} px-3 py-1 text-xs font-bold ring-1">{{ $roleLabel }}</span>
                        </td>
                        <td class="whitespace-nowrap px-5 py-4 text-sm text-slate-600">
                            {{ $admin->created_at?->translatedFormat('d M Y') ?? '-' }}
                            <span class="block text-xs text-slate-400">{{ $admin->created_at?->format('H:i') }}</span>
                        </td>
                        <td class="whitespace-nowrap px-5 py-4">
                            <div class="flex items-center justify-center gap-1">
                                <button type="button" title="Edit admin" aria-label="Edit {{ $admin->nama }}" class="grid h-9 w-9 place-items-center rounded-xl bg-sky-50 text-sky-700 ring-1 ring-sky-100 transition hover:bg-sky-100" @click="openEdit({
                                    action: @js(route('superadmin.admin.update', $admin)),
                                    id: @js($admin->id_user),
                                    nama: @js($admin->nama),
                                    username: @js($admin->username),
                                    email: @js($admin->email),
                                    role: @js($admin->role),
                                })">
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m5 16.5-.8 3.3 3.3-.8L18 8.5 15.5 6 5 16.5Z"/><path d="m13.8 7.7 2.5 2.5"/></svg>
                                </button>
                                <button type="button" title="Hapus admin" aria-label="Hapus {{ $admin->nama }}" class="grid h-9 w-9 place-items-center rounded-xl bg-rose-50 text-rose-600 ring-1 ring-rose-100 transition hover:bg-rose-100" @click="$dispatch('open-delete-confirm', {
                                    action: @js(route('superadmin.admin.destroy', $admin)),
                                    title: 'Hapus Admin?',
                                    name: @js($admin->nama),
                                    description: 'Akun admin ini akan dihapus dari portal dan tidak bisa digunakan untuk masuk lagi.',
                                    confirmText: 'Ya, Hapus Admin',
                                })">
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M5 7h14M9 7V4.5h6V7M8 10v7M12 10v7M16 10v7M6.5 7l.7 12h9.6l.7-12"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-5 py-16 text-center">
                            <span class="mx-auto grid h-14 w-14 place-items-center rounded-2xl bg-sky-50 text-sky-500">
                                <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><circle cx="9" cy="8" r="3"/><path d="M3.5 19c.5-3.5 2.3-5.2 5.5-5.2s5 1.7 5.5 5.2M16 10h5"/></svg>
                            </span>
                            <p class="mt-3 text-sm font-bold text-slate-800">{{ ($search ?? request('search', '')) !== '' ? 'Admin tidak ditemukan.' : 'Belum ada akun admin.' }}</p>
                            <p class="mt-1 text-sm text-slate-500">{{ ($search ?? request('search', '')) !== '' ? 'Coba gunakan nama admin yang berbeda.' : 'Klik Tambah Admin Baru untuk membuat akun pertama.' }}</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($admins->hasPages())
        <div class="flex flex-col gap-3 border-t border-sky-100 bg-sky-50/50 px-6 py-4 sm:flex-row sm:items-center sm:justify-between">
            <p class="text-xs text-slate-500">Menampilkan {{ $admins->firstItem() ?? 0 }} – {{ $admins->lastItem() ?? 0 }} dari {{ $admins->total() }} admin</p>
            <nav class="flex items-center justify-end gap-2" aria-label="Navigasi halaman admin">
                @if ($admins->onFirstPage())
                <span class="inline-flex h-10 min-w-[42px] items-center justify-center rounded-xl border border-sky-100 bg-sky-50/70 px-3 text-sm font-semibold text-sky-300" aria-disabled="true">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 6-6 6 6 6"/></svg>
                </span>
                @else
                <a href="{{ $admins->previousPageUrl() }}" class="inline-flex h-10 min-w-[42px] items-center justify-center rounded-xl border border-sky-100 bg-sky-50 px-3 text-sm font-semibold text-sky-700 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:bg-sky-100 hover:text-sky-800" aria-label="Halaman sebelumnya">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 6-6 6 6 6"/></svg>
                </a>
                @endif

                @php
                    $currentPage = $admins->currentPage();
                    $lastPage = $admins->lastPage();
                    $startPage = max(1, $currentPage - 2);
                    $endPage = min($lastPage, $currentPage + 2);
                @endphp

                @if ($startPage > 1)
                <a href="{{ $admins->url(1) }}" class="inline-flex h-10 min-w-[42px] items-center justify-center rounded-xl border border-sky-100 bg-sky-50 px-3 text-sm font-semibold text-sky-700 shadow-sm transition hover:-translate-y-0.5 hover:bg-sky-100" aria-label="Halaman 1">1</a>
                @if ($startPage > 2) <span class="inline-flex h-10 items-center px-1 text-slate-400">…</span> @endif
                @endif

                @foreach ($admins->getUrlRange($startPage, $endPage) as $page => $url)
                @if ($page === $currentPage)
                <span class="inline-flex h-10 min-w-[42px] items-center justify-center rounded-xl bg-gradient-to-r from-sky-500 to-blue-600 px-3 text-sm font-bold text-white shadow-[0_10px_24px_rgba(14,165,233,0.28)]" aria-current="page">{{ $page }}</span>
                @else
                <a href="{{ $url }}" class="inline-flex h-10 min-w-[42px] items-center justify-center rounded-xl border border-sky-100 bg-sky-50 px-3 text-sm font-semibold text-sky-700 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:bg-sky-100 hover:text-sky-800" aria-label="Halaman {{ $page }}">{{ $page }}</a>
                @endif
                @endforeach

                @if ($endPage < $lastPage)
                @if ($endPage < $lastPage - 1) <span class="inline-flex h-10 items-center px-1 text-slate-400">…</span> @endif
                <a href="{{ $admins->url($lastPage) }}" class="inline-flex h-10 min-w-[42px] items-center justify-center rounded-xl border border-sky-100 bg-sky-50 px-3 text-sm font-semibold text-sky-700 shadow-sm transition hover:-translate-y-0.5 hover:bg-sky-100" aria-label="Halaman {{ $lastPage }}">{{ $lastPage }}</a>
                @endif

                @if ($admins->hasMorePages())
                <a href="{{ $admins->nextPageUrl() }}" class="inline-flex h-10 min-w-[42px] items-center justify-center rounded-xl border border-sky-100 bg-sky-50 px-3 text-sm font-semibold text-sky-700 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:bg-sky-100 hover:text-sky-800" aria-label="Halaman berikutnya">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 6 6 6-6 6"/></svg>
                </a>
                @else
                <span class="inline-flex h-10 min-w-[42px] items-center justify-center rounded-xl border border-sky-100 bg-sky-50/70 px-3 text-sm font-semibold text-sky-300" aria-disabled="true">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 6 6 6-6 6"/></svg>
                </span>
                @endif
            </nav>
        </div>
        @endif
    </section>

    {{-- MODAL TAMBAH (header putih gradasi tipis, tanpa blur) --}}
    <template x-teleport="body">
        <div x-cloak x-show="addOpen" x-transition.opacity class="fixed inset-0 overflow-y-auto overscroll-contain bg-slate-950/40" style="position:fixed;inset:0;z-index:2147483647;">
            <div class="flex min-h-full items-start justify-center px-3 py-5 sm:items-center sm:px-6 sm:py-8" @click.self="addOpen = false">
                <article x-show="addOpen" x-transition.scale.origin.center role="dialog" aria-modal="true" aria-labelledby="create-admin-title" class="relative isolate my-auto flex max-h-[calc(100dvh-3rem)] w-full max-w-lg flex-col overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-2xl">
                    <header class="flex shrink-0 items-start justify-between gap-4 bg-gradient-to-r from-white via-sky-50/50 to-blue-50/40 px-6 py-5 text-slate-800 border-b border-slate-200">
                        <div>
                            <h2 id="create-admin-title" class="text-xl font-extrabold">Tambah Admin Baru</h2>
                            <p class="mt-1 text-sm text-slate-500">Akun langsung aktif setelah disimpan.</p>
                        </div>
                        <button type="button" class="rounded-xl p-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-700" @click="addOpen = false" aria-label="Tutup modal">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="m6 6 12 12M18 6 6 18"/></svg>
                        </button>
                    </header>
                    <form method="POST" action="{{ route('superadmin.admin.store') }}" class="flex min-h-0 flex-1 flex-col">
                        @csrf
                        <input type="hidden" name="form_context" value="create">
                        <div class="min-h-0 flex-1 space-y-4 overflow-y-auto p-6">
                            @if ($errors->any() && old('form_context') === 'create')
                            <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">Periksa kembali data yang masih belum valid.</div>
                            @endif
                            <div>
                                <label for="create-nama" class="mb-1.5 block text-sm font-bold text-slate-700">Nama lengkap</label>
                                <input id="create-nama" name="nama" type="text" autocomplete="name" value="{{ old('form_context') === 'create' ? old('nama') : '' }}" required class="w-full rounded-xl border-slate-300 focus:border-sky-500 focus:ring-sky-500" placeholder="Nama admin">
                                @if (old('form_context') === 'create') @error('nama') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror @endif
                            </div>
                            <div>
                                <label for="create-username" class="mb-1.5 block text-sm font-bold text-slate-700">Username</label>
                                <input id="create-username" name="username" type="text" autocomplete="username" value="{{ old('form_context') === 'create' ? old('username') : '' }}" required class="w-full rounded-xl border-slate-300 focus:border-sky-500 focus:ring-sky-500" placeholder="contoh: adminnatusi">
                                @if (old('form_context') === 'create') @error('username') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror @endif
                            </div>
                            <div>
                                <label for="create-email" class="mb-1.5 block text-sm font-bold text-slate-700">Email</label>
                                <input id="create-email" name="email" type="email" autocomplete="email" value="{{ old('form_context') === 'create' ? old('email') : '' }}" required class="w-full rounded-xl border-slate-300 focus:border-sky-500 focus:ring-sky-500" placeholder="admin@contoh.com">
                                @if (old('form_context') === 'create') @error('email') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror @endif
                            </div>
                            <div>
                                <label for="create-role" class="mb-1.5 block text-sm font-bold text-slate-700">Role Administrator</label>
                                <select id="create-role" name="role" required class="w-full rounded-xl border-slate-300 focus:border-sky-500 focus:ring-sky-500">
                                    <option value="" disabled {{ old('form_context') !== 'create' || old('role') === '' ? 'selected' : '' }}>Pilih role admin</option>
                                    <option value="admin" {{ old('form_context') === 'create' && old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                                    <option value="admin_karyawan" {{ old('form_context') === 'create' && old('role') === 'admin_karyawan' ? 'selected' : '' }}>Admin Karyawan</option>
                                    <option value="admin_peserta" {{ old('form_context') === 'create' && old('role') === 'admin_peserta' ? 'selected' : '' }}>Admin Peserta Magang</option>
                                </select>
                                @if (old('form_context') === 'create') @error('role') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror @endif
                            </div>
                            <div class="grid gap-4 sm:grid-cols-2">
                                <div>
                                    <label for="create-password" class="mb-1.5 block text-sm font-bold text-slate-700">Kata sandi</label>
                                    <input id="create-password" name="password" type="password" autocomplete="new-password" required class="w-full rounded-xl border-slate-300 focus:border-sky-500 focus:ring-sky-500" placeholder="Minimal 8 karakter">
                                    @if (old('form_context') === 'create') @error('password') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror @endif
                                </div>
                                <div>
                                    <label for="create-password-confirmation" class="mb-1.5 block text-sm font-bold text-slate-700">Konfirmasi</label>
                                    <input id="create-password-confirmation" name="password_confirmation" type="password" autocomplete="new-password" required class="w-full rounded-xl border-slate-300 focus:border-sky-500 focus:ring-sky-500" placeholder="Ulangi kata sandi">
                                </div>
                            </div>
                        </div>
                        <footer class="flex shrink-0 justify-end gap-3 border-t border-slate-200 bg-white px-6 py-4">
                            <button type="button" class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-700 transition hover:bg-slate-50" @click="addOpen = false">Batal</button>
                            <button type="submit" class="rounded-xl bg-gradient-to-r from-sky-500 to-blue-600 px-5 py-2.5 text-sm font-bold text-white shadow-[0_10px_24px_rgba(2,132,199,0.24)] transition hover:-translate-y-0.5">Simpan Admin</button>
                        </footer>
                    </form>
                </article>
            </div>
        </div>
    </template>

    {{-- MODAL EDIT (header putih gradasi tipis, tanpa blur) --}}
    <template x-teleport="body">
        <div x-cloak x-show="editOpen" x-transition.opacity class="fixed inset-0 overflow-y-auto overscroll-contain bg-slate-950/40" style="position:fixed;inset:0;z-index:2147483647;">
            <div class="flex min-h-full items-start justify-center px-3 py-5 sm:items-center sm:px-6 sm:py-8" @click.self="editOpen = false">
                <article x-show="editOpen" x-transition.scale.origin.center role="dialog" aria-modal="true" aria-labelledby="edit-admin-title" class="relative isolate my-auto flex max-h-[calc(100dvh-3rem)] w-full max-w-lg flex-col overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-2xl">
                    <header class="flex shrink-0 items-start justify-between gap-4 bg-gradient-to-r from-white via-sky-50/50 to-blue-50/40 px-6 py-5 text-slate-800 border-b border-slate-200">
                        <div>
                            <h2 id="edit-admin-title" class="text-xl font-extrabold">Edit Data Admin</h2>
                            <p class="mt-1 text-sm text-slate-500">Kosongkan password jika tidak ingin menggantinya.</p>
                        </div>
                        <button type="button" class="rounded-xl p-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-700" @click="editOpen = false" aria-label="Tutup modal">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="m6 6 12 12M18 6 6 18"/></svg>
                        </button>
                    </header>
                    <form method="POST" :action="editAdmin.action" class="flex min-h-0 flex-1 flex-col">
                        @csrf @method('PUT')
                        <input type="hidden" name="form_context" value="edit">
                        <input type="hidden" name="admin_id" :value="editAdmin.id">
                        <div class="min-h-0 flex-1 space-y-4 overflow-y-auto p-6">
                            @if ($errors->any() && old('form_context') === 'edit')
                            <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">Periksa kembali data yang masih belum valid.</div>
                            @endif
                            <div>
                                <label for="edit-nama" class="mb-1.5 block text-sm font-bold text-slate-700">Nama lengkap</label>
                                <input id="edit-nama" name="nama" type="text" autocomplete="name" x-model="editAdmin.nama" required class="w-full rounded-xl border-slate-300 focus:border-sky-500 focus:ring-sky-500">
                                @if (old('form_context') === 'edit') @error('nama') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror @endif
                            </div>
                            <div>
                                <label for="edit-username" class="mb-1.5 block text-sm font-bold text-slate-700">Username</label>
                                <input id="edit-username" name="username" type="text" autocomplete="username" x-model="editAdmin.username" required class="w-full rounded-xl border-slate-300 focus:border-sky-500 focus:ring-sky-500">
                                @if (old('form_context') === 'edit') @error('username') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror @endif
                            </div>
                            <div>
                                <label for="edit-email" class="mb-1.5 block text-sm font-bold text-slate-700">Email</label>
                                <input id="edit-email" name="email" type="email" autocomplete="email" x-model="editAdmin.email" required class="w-full rounded-xl border-slate-300 focus:border-sky-500 focus:ring-sky-500">
                                @if (old('form_context') === 'edit') @error('email') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror @endif
                            </div>
                            <div>
                                <label for="edit-role" class="mb-1.5 block text-sm font-bold text-slate-700">Role Administrator</label>
                                <select id="edit-role" name="role" x-model="editAdmin.role" required class="w-full rounded-xl border-slate-300 focus:border-sky-500 focus:ring-sky-500">
                                    <option value="" disabled>Pilih role admin</option>
                                    <option value="admin">Admin</option>
                                    <option value="admin_karyawan">Admin Karyawan</option>
                                    <option value="admin_peserta">Admin Peserta Magang</option>
                                </select>
                                @if (old('form_context') === 'edit') @error('role') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror @endif
                            </div>
                            <div class="grid gap-4 sm:grid-cols-2">
                                <div>
                                    <label for="edit-password" class="mb-1.5 block text-sm font-bold text-slate-700">Password baru</label>
                                    <input id="edit-password" name="password" type="password" autocomplete="new-password" class="w-full rounded-xl border-slate-300 focus:border-sky-500 focus:ring-sky-500" placeholder="Opsional">
                                    @if (old('form_context') === 'edit') @error('password') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror @endif
                                </div>
                                <div>
                                    <label for="edit-password-confirmation" class="mb-1.5 block text-sm font-bold text-slate-700">Konfirmasi</label>
                                    <input id="edit-password-confirmation" name="password_confirmation" type="password" autocomplete="new-password" class="w-full rounded-xl border-slate-300 focus:border-sky-500 focus:ring-sky-500" placeholder="Ulangi password">
                                </div>
                            </div>
                        </div>
                        <footer class="flex shrink-0 justify-end gap-3 border-t border-slate-200 bg-white px-6 py-4">
                            <button type="button" class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-700 transition hover:bg-slate-50" @click="editOpen = false">Batal</button>
                            <button type="submit" class="rounded-xl bg-gradient-to-r from-sky-500 to-blue-600 px-5 py-2.5 text-sm font-bold text-white shadow-[0_10px_24px_rgba(2,132,199,0.24)] transition hover:-translate-y-0.5">Simpan Perubahan</button>
                        </footer>
                    </form>
                </article>
            </div>
        </div>
    </template>
</div>
@endsection