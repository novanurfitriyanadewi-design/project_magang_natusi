@extends('layouts.portal')

@section('title', 'Kelola Divisi')

@section('content')
<div x-data="{
    addOpen: @js($errors->any() && old('form_context') === 'create'),
    editOpen: @js($errors->any() && old('form_context') === 'edit'),
    detailOpen: false,
    deleteOpen: false,
    selectedDivisi: { id: '', nama_divisi: '', keterangan: '', updated_at: '', action: '' },
    editDivisi: {
        id: @js(old('divisi_id', '')),
        nama_divisi: @js(old('form_context') === 'edit' ? old('nama_divisi', '') : ''),
        keterangan: @js(old('form_context') === 'edit' ? old('keterangan', '') : ''),
        action: @js(old('divisi_id') ? route('superadmin.divisi.update', old('divisi_id')) : '')
    },
    deleteDivisi: { action: '', nama: '' },
    openDetail(d) { this.selectedDivisi = d; this.detailOpen = true; },
    openEdit(d) { this.editDivisi = d; this.detailOpen = false; this.editOpen = true; },
    openDelete(d) { this.deleteDivisi = d; this.deleteOpen = true; },
    closeAll() { this.addOpen = false; this.editOpen = false; this.detailOpen = false; this.deleteOpen = false; }
}" x-effect="document.body.classList.toggle('overflow-hidden', addOpen || editOpen || detailOpen || deleteOpen)" @keydown.escape.window="closeAll()">

    {{-- Judul --}}
    <section>
        <h1 class="mt-5 text-2xl font-extrabold tracking-tight text-slate-950 sm:text-3xl">Kelola Divisi</h1>
        <p class="mt-1 text-sm text-slate-500">Atur pembagian divisi untuk seluruh karyawan CV Natusi.</p>
    </section>

    {{-- Data divisi --}}
    <section class="mt-5 overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">

        {{-- Header Data Divisi (gradasi biru seperti di aturan) --}}
        <div class="flex flex-col gap-4 border-b border-sky-100 bg-gradient-to-r from-sky-50 via-blue-50 to-cyan-50 px-6 py-5 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-lg font-bold text-slate-950">Data Divisi</h2>
                <p class="mt-0.5 text-sm text-slate-500">Semua divisi yang tersimpan aktif digunakan.</p>
            </div>
            <button type="button" @click="addOpen = true" class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-sky-500 to-blue-600 px-5 py-2.5 text-sm font-bold text-white shadow-[0_10px_25px_rgba(2,132,199,0.25)] transition duration-200 hover:-translate-y-0.5 hover:from-sky-600 hover:to-blue-700">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
                Tambah Divisi
            </button>
        </div>

        {{-- Grid kartu (dengan gradasi tipis) --}}
        <div class="p-6">
            <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @forelse ($divisiList as $divisi)
                <article class="relative flex cursor-pointer flex-col overflow-hidden rounded-2xl border border-slate-100 bg-gradient-to-b from-white to-sky-50/40 p-5 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:shadow-md" @click="openDetail({ id: @js($divisi->id_divisi), nama_divisi: @js($divisi->nama_divisi), keterangan: @js($divisi->keterangan), updated_at: @js($divisi->updated_at?->translatedFormat('d M Y, H:i')), action: @js(route('superadmin.divisi.update', $divisi)) })">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0 flex-1">
                            <h3 class="text-base font-bold text-slate-950">{{ $divisi->nama_divisi }}</h3>
                            <p class="mt-1 line-clamp-2 text-xs text-slate-500">{{ $divisi->keterangan ?: 'Tidak ada keterangan' }}</p>
                        </div>
                        <span class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-sky-50 text-sky-600">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 21v-2a4 4 0 0 1 4-4h4a4 4 0 0 1 4 4v2M17 3.13a4 4 0 0 1 0 7.75M21 21v-2a4 4 0 0 0-3-3.87"/><circle cx="9" cy="8" r="3.2"/></svg>
                        </span>
                    </div>
                    <div class="mt-4 flex items-center justify-between border-t border-slate-100 pt-4">
                        <span class="text-xs font-semibold text-slate-500">{{ $divisi->karyawan_count ?? 0 }} karyawan</span>
                        <div class="flex items-center gap-1" @click.stop>
                            {{-- Tombol edit --}}
                            <button type="button" class="grid h-9 w-9 place-items-center rounded-xl bg-blue-50 text-blue-700 ring-1 ring-blue-100 transition hover:bg-blue-100" title="Edit" aria-label="Edit {{ $divisi->nama_divisi }}" @click="openEdit({ id: @js($divisi->id_divisi), nama_divisi: @js($divisi->nama_divisi), keterangan: @js($divisi->keterangan), action: @js(route('superadmin.divisi.update', $divisi)) })">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m5 16.5-.8 3.3 3.3-.8L18 8.5 15.5 6 5 16.5Z"/><path d="m13.8 7.7 2.5 2.5"/></svg>
                            </button>
                            {{-- Tombol hapus --}}
                            <button type="button" class="grid h-9 w-9 place-items-center rounded-xl bg-rose-50 text-rose-600 ring-1 ring-rose-100 transition hover:bg-rose-100" title="Hapus" aria-label="Hapus {{ $divisi->nama_divisi }}" @click="openDelete({ action: @js(route('superadmin.divisi.destroy', $divisi)), nama: @js($divisi->nama_divisi) })">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M5 7h14M9 7V4.5h6V7M8 10v7M12 10v7M16 10v7M6.5 7l.7 12h9.6l.7-12"/></svg>
                            </button>
                        </div>
                    </div>
                </article>
                @empty
                <div class="col-span-full rounded-2xl border border-dashed border-slate-200 bg-white px-5 py-16 text-center">
                    <span class="mx-auto grid h-14 w-14 place-items-center rounded-2xl bg-sky-50 text-sky-500">
                        <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 21v-2a4 4 0 0 1 4-4h4a4 4 0 0 1 4 4v2M17 3.13a4 4 0 0 1 0 7.75M21 21v-2a4 4 0 0 0-3-3.87"/><circle cx="9" cy="8" r="3.2"/></svg>
                    </span>
                    <p class="mt-3 text-sm font-bold text-slate-800">Belum ada divisi.</p>
                    <p class="mt-1 text-sm text-slate-500">Klik "Tambah Divisi" untuk mulai membuat struktur organisasi.</p>
                </div>
                @endforelse
            </div>
        </div>

        {{-- Pagination --}}
        @if ($divisiList->hasPages())
        <div class="flex flex-col gap-3 border-t border-sky-100 bg-sky-50/50 px-6 py-4 sm:flex-row sm:items-center sm:justify-between">
            <p class="text-xs text-slate-500">Menampilkan {{ $divisiList->firstItem() ?? 0 }} – {{ $divisiList->lastItem() ?? 0 }} dari {{ $divisiList->total() }} divisi</p>
            <nav class="flex items-center justify-end gap-2" aria-label="Navigasi halaman divisi">
                @if ($divisiList->onFirstPage())
                <span class="inline-flex h-10 min-w-[42px] items-center justify-center rounded-xl border border-sky-100 bg-sky-50/70 px-3 text-sm font-semibold text-sky-300" aria-disabled="true">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 6-6 6 6 6"/></svg>
                </span>
                @else
                <a href="{{ $divisiList->previousPageUrl() }}" class="inline-flex h-10 min-w-[42px] items-center justify-center rounded-xl border border-sky-100 bg-sky-50 px-3 text-sm font-semibold text-sky-700 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:bg-sky-100 hover:text-sky-800" aria-label="Halaman sebelumnya">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 6-6 6 6 6"/></svg>
                </a>
                @endif

                @php
                    $currentPage = $divisiList->currentPage();
                    $lastPage = $divisiList->lastPage();
                    $startPage = max(1, $currentPage - 2);
                    $endPage = min($lastPage, $currentPage + 2);
                @endphp

                @if ($startPage > 1)
                <a href="{{ $divisiList->url(1) }}" class="inline-flex h-10 min-w-[42px] items-center justify-center rounded-xl border border-sky-100 bg-sky-50 px-3 text-sm font-semibold text-sky-700 shadow-sm transition hover:-translate-y-0.5 hover:bg-sky-100" aria-label="Halaman 1">1</a>
                @if ($startPage > 2) <span class="inline-flex h-10 items-center px-1 text-slate-400">…</span> @endif
                @endif

                @foreach ($divisiList->getUrlRange($startPage, $endPage) as $page => $url)
                @if ($page === $currentPage)
                <span class="inline-flex h-10 min-w-[42px] items-center justify-center rounded-xl bg-gradient-to-r from-sky-500 to-blue-600 px-3 text-sm font-bold text-white shadow-[0_10px_24px_rgba(14,165,233,0.28)]" aria-current="page">{{ $page }}</span>
                @else
                <a href="{{ $url }}" class="inline-flex h-10 min-w-[42px] items-center justify-center rounded-xl border border-sky-100 bg-sky-50 px-3 text-sm font-semibold text-sky-700 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:bg-sky-100 hover:text-sky-800" aria-label="Halaman {{ $page }}">{{ $page }}</a>
                @endif
                @endforeach

                @if ($endPage < $lastPage)
                @if ($endPage < $lastPage - 1) <span class="inline-flex h-10 items-center px-1 text-slate-400">…</span> @endif
                <a href="{{ $divisiList->url($lastPage) }}" class="inline-flex h-10 min-w-[42px] items-center justify-center rounded-xl border border-sky-100 bg-sky-50 px-3 text-sm font-semibold text-sky-700 shadow-sm transition hover:-translate-y-0.5 hover:bg-sky-100" aria-label="Halaman {{ $lastPage }}">{{ $lastPage }}</a>
                @endif

                @if ($divisiList->hasMorePages())
                <a href="{{ $divisiList->nextPageUrl() }}" class="inline-flex h-10 min-w-[42px] items-center justify-center rounded-xl border border-sky-100 bg-sky-50 px-3 text-sm font-semibold text-sky-700 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:bg-sky-100 hover:text-sky-800" aria-label="Halaman berikutnya">
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

    {{-- MODAL TAMBAH (header putih gradasi tipis) --}}
    <template x-teleport="body">
        <div x-cloak x-show="addOpen" x-transition.opacity class="fixed inset-0 overflow-y-auto overscroll-contain bg-slate-950/40" style="position:fixed;inset:0;z-index:2147483647;">
            <div class="flex min-h-full items-start justify-center px-3 py-5 sm:items-center sm:px-6 sm:py-8" @click.self="addOpen = false">
                <article x-show="addOpen" x-transition.scale.origin.center role="dialog" aria-modal="true" aria-labelledby="add-divisi-title" class="relative isolate my-auto flex max-h-[calc(100dvh-3rem)] w-full max-w-lg flex-col overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-2xl">
                    <header class="flex shrink-0 items-start justify-between gap-4 bg-gradient-to-r from-white via-sky-50/50 to-blue-50/40 px-6 py-5 text-slate-800 border-b border-slate-200">
                        <div>
                            <h2 id="add-divisi-title" class="text-xl font-extrabold">Tambah Divisi</h2>
                            <p class="mt-1 text-sm text-slate-500">Isi nama dan keterangan divisi.</p>
                        </div>
                        <button type="button" class="rounded-xl p-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-700" @click="addOpen = false" aria-label="Tutup modal">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="m6 6 12 12M18 6 6 18"/></svg>
                        </button>
                    </header>
                    <form method="POST" action="{{ route('superadmin.divisi.store') }}" class="flex min-h-0 flex-1 flex-col">
                        @csrf
                        <input type="hidden" name="form_context" value="create">
                        <div class="min-h-0 flex-1 space-y-4 overflow-y-auto p-6">
                            @if ($errors->any() && old('form_context') === 'create')
                            <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">Periksa kembali data yang belum valid.</div>
                            @endif
                            <div>
                                <label for="add-divisi-name" class="mb-2 block text-sm font-bold text-slate-700">Nama Divisi</label>
                                <input id="add-divisi-name" name="nama_divisi" type="text" value="{{ old('form_context') === 'create' ? old('nama_divisi') : '' }}" required autocomplete="off" class="h-11 w-full rounded-xl border-slate-300 bg-white px-4 text-slate-700 placeholder:text-slate-400 focus:border-sky-500 focus:ring-sky-500" placeholder="Contoh: Programmer">
                                @if (old('form_context') === 'create') @error('nama_divisi') <p class="mt-1.5 text-xs text-rose-600">{{ $message }}</p> @enderror @endif
                            </div>
                            <div>
                                <label for="add-divisi-desc" class="mb-2 block text-sm font-bold text-slate-700">Keterangan (opsional)</label>
                                <textarea id="add-divisi-desc" name="keterangan" rows="4" class="min-h-[120px] w-full resize-y rounded-xl border-slate-300 bg-white px-4 py-3 leading-6 text-slate-700 placeholder:text-slate-400 focus:border-sky-500 focus:ring-sky-500" placeholder="Tuliskan deskripsi singkat tentang divisi ini...">{{ old('form_context') === 'create' ? old('keterangan') : '' }}</textarea>
                                @if (old('form_context') === 'create') @error('keterangan') <p class="mt-1.5 text-xs text-rose-600">{{ $message }}</p> @enderror @endif
                            </div>
                            <div class="flex items-start gap-3 rounded-2xl border border-sky-100 bg-sky-50/70 px-4 py-3">
                                <span class="mt-0.5 grid h-8 w-8 shrink-0 place-items-center rounded-xl bg-white text-sky-700 ring-1 ring-sky-100">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m8.5 12 2.2 2.2 4.8-5"/><circle cx="12" cy="12" r="8"/></svg>
                                </span>
                                <p class="text-sm leading-6 text-slate-600">Divisi otomatis tersedia untuk semua karyawan setelah disimpan.</p>
                            </div>
                        </div>
                        <footer class="flex shrink-0 justify-end gap-3 border-t border-slate-200 bg-white px-6 py-4">
                            <button type="button" class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-700 transition hover:bg-slate-50" @click="addOpen = false">Batal</button>
                            <button type="submit" class="rounded-xl bg-gradient-to-r from-sky-500 to-blue-600 px-5 py-2.5 text-sm font-bold text-white shadow-[0_10px_24px_rgba(2,132,199,0.24)] transition hover:-translate-y-0.5">Simpan Divisi</button>
                        </footer>
                    </form>
                </article>
            </div>
        </div>
    </template>

    {{-- MODAL EDIT (header putih gradasi tipis) --}}
    <template x-teleport="body">
        <div x-cloak x-show="editOpen" x-transition.opacity class="fixed inset-0 overflow-y-auto overscroll-contain bg-slate-950/40" style="position:fixed;inset:0;z-index:2147483647;">
            <div class="flex min-h-full items-start justify-center px-3 py-5 sm:items-center sm:px-6 sm:py-8" @click.self="editOpen = false">
                <article x-show="editOpen" x-transition.scale.origin.center role="dialog" aria-modal="true" aria-labelledby="edit-divisi-title" class="relative isolate my-auto flex max-h-[calc(100dvh-3rem)] w-full max-w-lg flex-col overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-2xl">
                    <header class="flex shrink-0 items-start justify-between gap-4 bg-gradient-to-r from-white via-sky-50/50 to-blue-50/40 px-6 py-5 text-slate-800 border-b border-slate-200">
                        <div>
                            <h2 id="edit-divisi-title" class="text-xl font-extrabold">Edit Divisi</h2>
                            <p class="mt-1 text-sm text-slate-500">Perubahan langsung berlaku untuk semua karyawan.</p>
                        </div>
                        <button type="button" class="rounded-xl p-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-700" @click="editOpen = false" aria-label="Tutup modal">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="m6 6 12 12M18 6 6 18"/></svg>
                        </button>
                    </header>
                    <form method="POST" :action="editDivisi.action" class="flex min-h-0 flex-1 flex-col">
                        @csrf @method('PUT')
                        <input type="hidden" name="form_context" value="edit">
                        <input type="hidden" name="divisi_id" :value="editDivisi.id">
                        <div class="min-h-0 flex-1 space-y-4 overflow-y-auto p-6">
                            @if ($errors->any() && old('form_context') === 'edit')
                            <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">Periksa kembali data yang belum valid.</div>
                            @endif
                            <div>
                                <label for="edit-divisi-name" class="mb-2 block text-sm font-bold text-slate-700">Nama Divisi</label>
                                <input id="edit-divisi-name" name="nama_divisi" type="text" x-model="editDivisi.nama_divisi" required autocomplete="off" class="h-11 w-full rounded-xl border-slate-300 bg-white px-4 text-slate-700 focus:border-sky-500 focus:ring-sky-500">
                                @if (old('form_context') === 'edit') @error('nama_divisi') <p class="mt-1.5 text-xs text-rose-600">{{ $message }}</p> @enderror @endif
                            </div>
                            <div>
                                <label for="edit-divisi-desc" class="mb-2 block text-sm font-bold text-slate-700">Keterangan (opsional)</label>
                                <textarea id="edit-divisi-desc" name="keterangan" rows="4" x-model="editDivisi.keterangan" class="min-h-[120px] w-full resize-y rounded-xl border-slate-300 bg-white px-4 py-3 leading-6 text-slate-700 focus:border-sky-500 focus:ring-sky-500"></textarea>
                                @if (old('form_context') === 'edit') @error('keterangan') <p class="mt-1.5 text-xs text-rose-600">{{ $message }}</p> @enderror @endif
                            </div>
                            <div class="flex items-start gap-3 rounded-2xl border border-sky-100 bg-sky-50/70 px-4 py-3">
                                <span class="mt-0.5 grid h-8 w-8 shrink-0 place-items-center rounded-xl bg-white text-sky-700 ring-1 ring-sky-100">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m8.5 12 2.2 2.2 4.8-5"/><circle cx="12" cy="12" r="8"/></svg>
                                </span>
                                <p class="text-sm leading-6 text-slate-600">Perubahan langsung berlaku untuk semua karyawan.</p>
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

    {{-- MODAL HAPUS --}}
    <template x-teleport="body">
        <div x-cloak x-show="deleteOpen" x-transition.opacity class="fixed inset-0 overflow-y-auto overscroll-contain bg-slate-950/40" style="position:fixed;inset:0;z-index:2147483647;">
            <div class="flex min-h-full items-start justify-center px-3 py-5 sm:items-center sm:px-6 sm:py-8" @click.self="deleteOpen = false">
                <form method="POST" :action="deleteDivisi.action" class="relative w-full max-w-sm rounded-2xl bg-white shadow-2xl">
                    @csrf @method('DELETE')
                    <div class="px-6 py-6 text-center">
                        <div class="mx-auto grid h-12 w-12 place-items-center rounded-full bg-rose-50 text-rose-500">
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2m3 0-1 14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2L4 6"/></svg>
                        </div>
                        <h3 class="mt-4 text-lg font-bold text-slate-950">Hapus Divisi?</h3>
                        <p class="mt-1 text-sm text-slate-500">
                            Yakin mau hapus divisi <span class="font-semibold" x-text="deleteDivisi.nama"></span>? Tindakan ini tidak bisa dibatalkan.
                        </p>
                    </div>
                    <div class="flex justify-center gap-3 border-t border-slate-200 px-6 py-4">
                        <button type="button" @click="deleteOpen = false" class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-700 transition hover:bg-slate-50">Batal</button>
                        <button type="submit" class="rounded-xl bg-rose-600 px-5 py-2.5 text-sm font-bold text-white hover:bg-rose-700">Ya, Hapus</button>
                    </div>
                </form>
            </div>
        </div>
    </template>
</div>
@endsection