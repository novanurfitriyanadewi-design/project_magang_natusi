@extends('layouts.portal')

@section('title', 'Kelola Divisi')

@section('content')
<div
    x-data="{
        addOpen: false,
        editOpen: false,
        deleteOpen: false,
        editDivisi: { action: '', nama_divisi: '', keterangan: '' },
        deleteDivisi: { action: '', nama: '' },
        openEdit(d) { this.editDivisi = d; this.editOpen = true; },
        openDelete(d) { this.deleteDivisi = d; this.deleteOpen = true; },
        closeModals() { this.addOpen = false; this.editOpen = false; this.deleteOpen = false; }
    }"
    @keydown.escape.window="closeModals()"
    x-effect="document.body.classList.toggle('overflow-hidden', addOpen || editOpen || deleteOpen)"
>
    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <span class="inline-flex items-center gap-2 rounded-full bg-sky-100 px-3 py-1 text-[10px] font-bold uppercase tracking-[0.16em] text-sky-700">
                <span class="h-1.5 w-1.5 rounded-full bg-sky-500"></span>
                Struktur Organisasi
            </span>
            <h1 class="mt-3 text-2xl font-extrabold tracking-tight text-slate-950 sm:text-3xl">Kelola Divisi</h1>
            <p class="mt-1 text-sm text-slate-500">Atur pembagian divisi untuk seluruh karyawan CV Natusi.</p>
        </div>
        <button
            type="button"
            @click="addOpen = true"
            class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-sky-600 to-blue-600 px-4 py-2.5 text-sm font-bold text-white shadow hover:-translate-y-0.5"
        >
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none"><path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
            Tambah Divisi
        </button>
    </div>

    <section class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @forelse ($divisiList as $divisi)
            <article class="relative overflow-hidden rounded-2xl border border-slate-100 bg-white p-5 shadow-sm">
                <div class="flex items-start justify-between">
                    <div>
                        <h3 class="text-base font-bold text-slate-950">{{ $divisi->nama_divisi }}</h3>
                        <p class="mt-1 text-xs text-slate-500">{{ $divisi->keterangan ?: 'Tidak ada keterangan' }}</p>
                    </div>
                    <span class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-sky-50 text-sky-600">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none"><path d="M4 21v-2a4 4 0 0 1 4-4h4a4 4 0 0 1 4 4v2M17 3.13a4 4 0 0 1 0 7.75M21 21v-2a4 4 0 0 0-3-3.87" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/><circle cx="9" cy="8" r="3.2" stroke="currentColor" stroke-width="1.7"/></svg>
                    </span>
                </div>

                <div class="mt-4 flex items-center justify-between border-t border-slate-100 pt-4">
                    <span class="text-sm font-semibold text-slate-600">
                        {{ $divisi->karyawan_count }} karyawan
                    </span>
                    <div class="flex gap-1">
                        <button
                            type="button"
                            title="Edit"
                            class="rounded-lg p-2 text-slate-500 hover:bg-slate-100"
                            @click="openEdit({
                                action: @js(route('superadmin.divisi.update', $divisi->id_divisi)),
                                nama_divisi: @js($divisi->nama_divisi),
                                keterangan: @js($divisi->keterangan),
                            })"
                        >
                            <svg class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="none"><path d="M11 4H6a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-5M18.5 2.5a2.12 2.12 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5Z" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </button>
                        <button
                            type="button"
                            title="Hapus"
                            class="rounded-lg p-2 text-rose-500 hover:bg-rose-50"
                            @click="openDelete({
                                action: @js(route('superadmin.divisi.destroy', $divisi->id_divisi)),
                                nama: @js($divisi->nama_divisi),
                            })"
                        >
                            <svg class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="none"><path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2m3 0-1 14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2L4 6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </button>
                    </div>
                </div>
            </article>
        @empty
            <div class="col-span-full rounded-2xl border border-dashed border-slate-200 bg-white p-10 text-center text-sm text-slate-400">
                Belum ada divisi. Klik "Tambah Divisi" untuk mulai membuat struktur organisasi.
            </div>
        @endforelse
    </section>

    {{-- MODAL TAMBAH --}}
    <div x-cloak x-show="addOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/40" @click="addOpen = false"></div>
        <form method="POST" action="{{ route('superadmin.divisi.store') }}" class="relative w-full max-w-md rounded-2xl bg-white shadow-2xl">
            @csrf
            <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4">
                <h3 class="text-lg font-bold text-slate-950">Tambah Divisi</h3>
                <button type="button" @click="addOpen = false" class="text-slate-400 hover:text-slate-700">✕</button>
            </div>
            <div class="space-y-4 px-6 py-5">
                <div>
                    <label class="mb-1 block text-sm font-bold text-slate-700">Nama Divisi</label>
                    <input type="text" name="nama_divisi" required placeholder="Contoh: Programmer" class="w-full rounded-xl border-slate-300 focus:border-sky-500 focus:ring-sky-500">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-bold text-slate-700">Keterangan (opsional)</label>
                    <textarea name="keterangan" rows="2" class="w-full rounded-xl border-slate-300 focus:border-sky-500 focus:ring-sky-500"></textarea>
                </div>
            </div>
            <div class="flex justify-end gap-3 border-t border-slate-100 px-6 py-4">
                <button type="button" @click="addOpen = false" class="rounded-xl border border-sky-100 bg-sky-50 px-4 py-2.5 text-sm font-bold text-sky-700 hover:bg-sky-100">Batal</button>
                <button type="submit" class="rounded-xl bg-gradient-to-r from-sky-600 to-blue-600 px-5 py-2.5 text-sm font-bold text-white shadow hover:-translate-y-0.5">Simpan Divisi</button>
            </div>
        </form>
    </div>

    {{-- MODAL EDIT --}}
    <div x-cloak x-show="editOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/40" @click="editOpen = false"></div>
        <form method="POST" :action="editDivisi.action" class="relative w-full max-w-md rounded-2xl bg-white shadow-2xl">
            @csrf
            @method('PUT')
            <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4">
                <h3 class="text-lg font-bold text-slate-950">Edit Divisi</h3>
                <button type="button" @click="editOpen = false" class="text-slate-400 hover:text-slate-700">✕</button>
            </div>
            <div class="space-y-4 px-6 py-5">
                <div>
                    <label class="mb-1 block text-sm font-bold text-slate-700">Nama Divisi</label>
                    <input type="text" name="nama_divisi" x-model="editDivisi.nama_divisi" required class="w-full rounded-xl border-slate-300 focus:border-sky-500 focus:ring-sky-500">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-bold text-slate-700">Keterangan (opsional)</label>
                    <textarea name="keterangan" x-model="editDivisi.keterangan" rows="2" class="w-full rounded-xl border-slate-300 focus:border-sky-500 focus:ring-sky-500"></textarea>
                </div>
            </div>
            <div class="flex justify-end gap-3 border-t border-slate-100 px-6 py-4">
                <button type="button" @click="editOpen = false" class="rounded-xl border border-sky-100 bg-sky-50 px-4 py-2.5 text-sm font-bold text-sky-700 hover:bg-sky-100">Batal</button>
                <button type="submit" class="rounded-xl bg-gradient-to-r from-sky-600 to-blue-600 px-5 py-2.5 text-sm font-bold text-white shadow hover:-translate-y-0.5">Simpan Perubahan</button>
            </div>
        </form>
    </div>

    {{-- MODAL HAPUS --}}
    <div x-cloak x-show="deleteOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/40" @click="deleteOpen = false"></div>
        <form method="POST" :action="deleteDivisi.action" class="relative w-full max-w-sm rounded-2xl bg-white shadow-2xl">
            @csrf
            @method('DELETE')
            <div class="px-6 py-6 text-center">
                <div class="mx-auto grid h-12 w-12 place-items-center rounded-full bg-rose-50 text-rose-500">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none"><path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2m3 0-1 14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2L4 6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </div>
                <h3 class="mt-4 text-lg font-bold text-slate-950">Hapus Divisi?</h3>
                <p class="mt-1 text-sm text-slate-500">
                    Yakin mau hapus divisi <span class="font-semibold" x-text="deleteDivisi.nama"></span>? Tindakan ini tidak bisa dibatalkan.
                </p>
            </div>
            <div class="flex justify-center gap-3 border-t border-slate-100 px-6 py-4">
                <button type="button" @click="deleteOpen = false" class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-bold text-slate-600 hover:bg-slate-50">Batal</button>
                <button type="submit" class="rounded-xl bg-rose-600 px-5 py-2.5 text-sm font-bold text-white hover:bg-rose-700">Ya, Hapus</button>
            </div>
        </form>
    </div>
</div>
@endsection