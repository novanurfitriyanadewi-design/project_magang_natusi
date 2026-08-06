@extends('layouts.portal')

@section('title', 'Kelola Aturan Perusahaan')

@section('content')
<div x-data="{
    createOpen: @js($errors->any() && old('form_context') === 'create'),
    editOpen: @js($errors->any() && old('form_context') === 'edit'),
    detailOpen: false,
    selectedRule: { id: '', nama: '', deskripsi: '', updated_at: '', action: '' },
    editRule: {
        id: @js(old('aturan_id', '')),
        nama: @js(old('form_context') === 'edit' ? old('nama', '') : ''),
        deskripsi: @js(old('form_context') === 'edit' ? old('deskripsi', '') : ''),
        action: @js(old('aturan_id') ? route('superadmin.aturan.update', old('aturan_id')) : '')
    },
    openDetail(rule) { this.selectedRule = rule; this.detailOpen = true; },
    openEdit(rule) { this.editRule = rule; this.detailOpen = false; this.editOpen = true; },
    closeAll() { this.createOpen = false; this.editOpen = false; this.detailOpen = false; }
}" x-effect="document.body.classList.toggle('overflow-hidden', createOpen || editOpen || detailOpen)" @keydown.escape.window="closeAll()">

    {{-- Judul halaman --}}
    <section>
        <h1 class="mt-5 text-2xl font-extrabold tracking-tight text-slate-950 sm:text-3xl">Kelola Aturan Perusahaan</h1>
        <p class="mt-1 text-sm text-slate-500">Tambah, baca, perbarui, dan hapus aturan yang berlaku bagi seluruh pengguna portal magang CV Natusi.</p>
    </section>

    {{-- Data aturan --}}
    <section class="mt-5 overflow-hidden rounded-3xl border border-sky-100/90 bg-white/95 shadow-[0_20px_50px_rgba(15,52,94,0.09)] backdrop-blur">

        {{-- Header --}}
        <div class="flex flex-col gap-4 border-b border-sky-100 bg-gradient-to-r from-sky-50 via-blue-50 to-cyan-50 px-6 py-5 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-lg font-bold text-slate-950">Data Aturan Perusahaan</h2>
                <p class="mt-0.5 text-sm text-slate-500">Semua aturan yang disimpan langsung berlaku.</p>
            </div>
            <button type="button" @click="createOpen = true" class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-sky-500 to-blue-600 px-5 py-2.5 text-sm font-bold text-white shadow-[0_10px_25px_rgba(2,132,199,0.25)] transition duration-200 hover:-translate-y-0.5 hover:from-sky-600 hover:to-blue-700">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
                Tambah Aturan Baru
            </button>
        </div>

        {{-- Grid kartu --}}
        <div class="p-6">
            <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @forelse ($rules as $rule)
                <article class="relative flex cursor-pointer flex-col overflow-hidden rounded-2xl border border-slate-100 bg-white p-5 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:shadow-md" @click="openDetail({ id: @js($rule->id_aturan), nama: @js($rule->nama), deskripsi: @js($rule->deskripsi), updated_at: @js($rule->updated_at?->translatedFormat('d M Y, H:i')), action: @js(route('superadmin.aturan.update', $rule)) })">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0 flex-1">
                            <h3 class="text-base font-bold text-slate-950">{{ $rule->nama }}</h3>
                            <p class="mt-1 line-clamp-2 text-xs text-slate-500">{{ $rule->deskripsi }}</p>
                        </div>
                        <span class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-sky-50 text-sky-600">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M6 3h9l3 3v15H6V3Zm9 0v4h4M9 11h6M9 15h6M9 19h4"/></svg>
                        </span>
                    </div>
                    <div class="mt-4 flex items-center justify-between border-t border-slate-100 pt-4">
                        <span class="text-xs font-semibold text-slate-500">{{ $rule->updated_at?->translatedFormat('d M Y') ?? '-' }} <span class="text-slate-400">· {{ $rule->updated_at?->format('H:i') }}</span></span>
                        <div class="flex items-center gap-1" @click.stop>
                            {{-- Tombol edit --}}
                            <button type="button" class="grid h-9 w-9 place-items-center rounded-xl bg-blue-50 text-blue-700 ring-1 ring-blue-100 transition hover:bg-blue-100" title="Edit aturan" aria-label="Edit {{ $rule->nama }}" @click="openEdit({ id: @js($rule->id_aturan), nama: @js($rule->nama), deskripsi: @js($rule->deskripsi), action: @js(route('superadmin.aturan.update', $rule)) })">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m5 16.5-.8 3.3 3.3-.8L18 8.5 15.5 6 5 16.5Z"/><path d="m13.8 7.7 2.5 2.5"/></svg>
                            </button>
                            {{-- Tombol hapus --}}
                            <button type="button" class="grid h-9 w-9 place-items-center rounded-xl bg-rose-50 text-rose-600 ring-1 ring-rose-100 transition hover:bg-rose-100" title="Hapus aturan" aria-label="Hapus {{ $rule->nama }}" @click="$dispatch('open-delete-confirm', { action: @js(route('superadmin.aturan.destroy', $rule)), title: 'Hapus Aturan?', name: @js($rule->nama), description: 'Aturan ini akan dihapus dari daftar kebijakan perusahaan.', confirmText: 'Ya, Hapus Aturan' })">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M5 7h14M9 7V4.5h6V7M8 10v7M12 10v7M16 10v7M6.5 7l.7 12h9.6l.7-12"/></svg>
                            </button>
                        </div>
                    </div>
                </article>
                @empty
                <div class="col-span-full rounded-2xl border border-dashed border-slate-200 bg-white px-5 py-16 text-center">
                    <span class="mx-auto grid h-14 w-14 place-items-center rounded-2xl bg-sky-50 text-sky-500">
                        <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M6 3h9l3 3v15H6V3Zm9 0v4h4M9 11h6M9 15h6"/></svg>
                    </span>
                    <p class="mt-3 text-sm font-bold text-slate-800">{{ $search ?? '' !== '' ? 'Aturan tidak ditemukan.' : 'Belum ada aturan perusahaan.' }}</p>
                    <p class="mt-1 text-sm text-slate-500">{{ $search ?? '' !== '' ? 'Coba gunakan kata pencarian lain.' : 'Klik Tambah Aturan Baru untuk membuat aturan pertama.' }}</p>
                </div>
                @endforelse
            </div>
        </div>

        {{-- Pagination --}}
        @if ($rules->hasPages())
        <div class="flex flex-col gap-3 border-t border-sky-100 bg-sky-50/50 px-6 py-4 sm:flex-row sm:items-center sm:justify-between">
            <p class="text-xs text-slate-500">Menampilkan {{ $rules->firstItem() ?? 0 }} – {{ $rules->lastItem() ?? 0 }} dari {{ $rules->total() }} aturan</p>
            <nav class="flex items-center justify-end gap-2" aria-label="Navigasi halaman aturan">
                @if ($rules->onFirstPage())
                <span class="inline-flex h-10 min-w-[42px] items-center justify-center rounded-xl border border-sky-100 bg-sky-50/70 px-3 text-sm font-semibold text-sky-300" aria-disabled="true">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 6-6 6 6 6"/></svg>
                </span>
                @else
                <a href="{{ $rules->previousPageUrl() }}" class="inline-flex h-10 min-w-[42px] items-center justify-center rounded-xl border border-sky-100 bg-sky-50 px-3 text-sm font-semibold text-sky-700 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:bg-sky-100 hover:text-sky-800" aria-label="Halaman sebelumnya">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 6-6 6 6 6"/></svg>
                </a>
                @endif

                @php $currentPage = $rules->currentPage(); $lastPage = $rules->lastPage(); $startPage = max(1, $currentPage - 2); $endPage = min($lastPage, $currentPage + 2); @endphp
                @if ($startPage > 1)
                <a href="{{ $rules->url(1) }}" class="inline-flex h-10 min-w-[42px] items-center justify-center rounded-xl border border-sky-100 bg-sky-50 px-3 text-sm font-semibold text-sky-700 shadow-sm transition hover:-translate-y-0.5 hover:bg-sky-100" aria-label="Halaman 1">1</a>
                @if ($startPage > 2) <span class="inline-flex h-10 items-center px-1 text-slate-400">…</span> @endif
                @endif

                @foreach ($rules->getUrlRange($startPage, $endPage) as $page => $url)
                @if ($page === $currentPage)
                <span class="inline-flex h-10 min-w-[42px] items-center justify-center rounded-xl bg-gradient-to-r from-sky-500 to-blue-600 px-3 text-sm font-bold text-white shadow-[0_10px_24px_rgba(14,165,233,0.28)]" aria-current="page">{{ $page }}</span>
                @else
                <a href="{{ $url }}" class="inline-flex h-10 min-w-[42px] items-center justify-center rounded-xl border border-sky-100 bg-sky-50 px-3 text-sm font-semibold text-sky-700 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:bg-sky-100 hover:text-sky-800" aria-label="Halaman {{ $page }}">{{ $page }}</a>
                @endif
                @endforeach

                @if ($endPage < $lastPage)
                @if ($endPage < $lastPage - 1) <span class="inline-flex h-10 items-center px-1 text-slate-400">…</span> @endif
                <a href="{{ $rules->url($lastPage) }}" class="inline-flex h-10 min-w-[42px] items-center justify-center rounded-xl border border-sky-100 bg-sky-50 px-3 text-sm font-semibold text-sky-700 shadow-sm transition hover:-translate-y-0.5 hover:bg-sky-100" aria-label="Halaman {{ $lastPage }}">{{ $lastPage }}</a>
                @endif

                @if ($rules->hasMorePages())
                <a href="{{ $rules->nextPageUrl() }}" class="inline-flex h-10 min-w-[42px] items-center justify-center rounded-xl border border-sky-100 bg-sky-50 px-3 text-sm font-semibold text-sky-700 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:bg-sky-100 hover:text-sky-800" aria-label="Halaman berikutnya">
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

    {{-- Modal tambah (header putih gradasi tipis, tanpa blur) --}}
    <template x-teleport="body">
        <div x-cloak x-show="createOpen" x-transition.opacity class="fixed inset-0 overflow-y-auto overscroll-contain bg-slate-950/40" style="position:fixed;inset:0;z-index:2147483647;">
            <div class="flex min-h-full items-start justify-center px-3 py-5 sm:items-center sm:px-6 sm:py-8" @click.self="createOpen = false">
                <article x-show="createOpen" x-transition.scale.origin.center role="dialog" aria-modal="true" aria-labelledby="create-rule-title" class="relative isolate my-auto flex max-h-[calc(100dvh-3rem)] w-full max-w-lg flex-col overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-2xl">
                    <header class="flex shrink-0 items-start justify-between gap-4 bg-gradient-to-r from-white via-sky-50/50 to-blue-50/40 px-6 py-5 text-slate-800 border-b border-slate-200">
                        <div>
                            <h2 id="create-rule-title" class="text-xl font-extrabold">Tambah Aturan Baru</h2>
                            <p class="mt-1 text-sm text-slate-500">Isi nama dan penjelasan aturan secara jelas.</p>
                        </div>
                        <button type="button" class="rounded-xl p-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-700" @click="createOpen = false" aria-label="Tutup modal">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="m6 6 12 12M18 6 6 18"/></svg>
                        </button>
                    </header>
                    <form method="POST" action="{{ route('superadmin.aturan.store') }}" class="flex min-h-0 flex-1 flex-col">
                        @csrf
                        <input type="hidden" name="form_context" value="create">
                        <div class="min-h-0 flex-1 space-y-4 overflow-y-auto p-6">
                            @if ($errors->any() && old('form_context') === 'create')
                            <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">Periksa kembali data yang belum valid.</div>
                            @endif
                            <div>
                                <label for="create-rule-name" class="mb-2 block text-sm font-bold text-slate-700">Nama aturan</label>
                                <input id="create-rule-name" name="nama" type="text" value="{{ old('form_context') === 'create' ? old('nama') : '' }}" required autocomplete="off" class="h-11 w-full rounded-xl border-slate-300 bg-white px-4 text-slate-700 placeholder:text-slate-400 focus:border-sky-500 focus:ring-sky-500" placeholder="Contoh: Standar Pakaian dan Atribut">
                                @if (old('form_context') === 'create') @error('nama') <p class="mt-1.5 text-xs text-rose-600">{{ $message }}</p> @enderror @endif
                            </div>
                            <div>
                                <label for="create-rule-description" class="mb-2 block text-sm font-bold text-slate-700">Isi atau penjelasan aturan</label>
                                <textarea id="create-rule-description" name="deskripsi" rows="7" required class="min-h-[210px] w-full resize-y rounded-xl border-slate-300 bg-white px-4 py-3 leading-6 text-slate-700 placeholder:text-slate-400 focus:border-sky-500 focus:ring-sky-500" placeholder="Tuliskan aturan, batasan, pengecualian, serta konsekuensi jika diperlukan...">{{ old('form_context') === 'create' ? old('deskripsi') : '' }}</textarea>
                                @if (old('form_context') === 'create') @error('deskripsi') <p class="mt-1.5 text-xs text-rose-600">{{ $message }}</p> @enderror @endif
                            </div>
                            <div class="flex items-start gap-3 rounded-2xl border border-sky-100 bg-sky-50/70 px-4 py-3">
                                <span class="mt-0.5 grid h-8 w-8 shrink-0 place-items-center rounded-xl bg-white text-sky-700 ring-1 ring-sky-100">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m8.5 12 2.2 2.2 4.8-5"/><circle cx="12" cy="12" r="8"/></svg>
                                </span>
                                <p class="text-sm leading-6 text-slate-600">Aturan otomatis langsung berlaku setelah disimpan.</p>
                            </div>
                        </div>
                        <footer class="flex shrink-0 justify-end gap-3 border-t border-slate-200 bg-white px-6 py-4">
                            <button type="button" class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-700 transition hover:bg-slate-50" @click="createOpen = false">Batal</button>
                            <button type="submit" class="rounded-xl bg-gradient-to-r from-sky-500 to-blue-600 px-5 py-2.5 text-sm font-bold text-white shadow-[0_10px_24px_rgba(2,132,199,0.24)] transition hover:-translate-y-0.5">Simpan Aturan</button>
                        </footer>
                    </form>
                </article>
            </div>
        </div>
    </template>

    {{-- Modal edit (header putih gradasi tipis, tanpa blur) --}}
    <template x-teleport="body">
        <div x-cloak x-show="editOpen" x-transition.opacity class="fixed inset-0 overflow-y-auto overscroll-contain bg-slate-950/40" style="position:fixed;inset:0;z-index:2147483647;">
            <div class="flex min-h-full items-start justify-center px-3 py-5 sm:items-center sm:px-6 sm:py-8" @click.self="editOpen = false">
                <article x-show="editOpen" x-transition.scale.origin.center role="dialog" aria-modal="true" aria-labelledby="edit-rule-title" class="relative isolate my-auto flex max-h-[calc(100dvh-3rem)] w-full max-w-lg flex-col overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-2xl">
                    <header class="flex shrink-0 items-start justify-between gap-4 bg-gradient-to-r from-white via-sky-50/50 to-blue-50/40 px-6 py-5 text-slate-800 border-b border-slate-200">
                        <div>
                            <h2 id="edit-rule-title" class="text-xl font-extrabold">Edit Aturan Perusahaan</h2>
                            <p class="mt-1 text-sm text-slate-500">Perubahan langsung berlaku setelah disimpan.</p>
                        </div>
                        <button type="button" class="rounded-xl p-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-700" @click="editOpen = false" aria-label="Tutup modal">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="m6 6 12 12M18 6 6 18"/></svg>
                        </button>
                    </header>
                    <form method="POST" :action="editRule.action" class="flex min-h-0 flex-1 flex-col">
                        @csrf @method('PUT')
                        <input type="hidden" name="form_context" value="edit">
                        <input type="hidden" name="aturan_id" :value="editRule.id">
                        <div class="min-h-0 flex-1 space-y-4 overflow-y-auto p-6">
                            @if ($errors->any() && old('form_context') === 'edit')
                            <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">Periksa kembali data yang belum valid.</div>
                            @endif
                            <div>
                                <label for="edit-rule-name" class="mb-2 block text-sm font-bold text-slate-700">Nama aturan</label>
                                <input id="edit-rule-name" name="nama" type="text" x-model="editRule.nama" required autocomplete="off" class="h-11 w-full rounded-xl border-slate-300 bg-white px-4 text-slate-700 focus:border-sky-500 focus:ring-sky-500">
                                @if (old('form_context') === 'edit') @error('nama') <p class="mt-1.5 text-xs text-rose-600">{{ $message }}</p> @enderror @endif
                            </div>
                            <div>
                                <label for="edit-rule-description" class="mb-2 block text-sm font-bold text-slate-700">Isi atau penjelasan aturan</label>
                                <textarea id="edit-rule-description" name="deskripsi" rows="7" x-model="editRule.deskripsi" required class="min-h-[210px] w-full resize-y rounded-xl border-slate-300 bg-white px-4 py-3 leading-6 text-slate-700 focus:border-sky-500 focus:ring-sky-500"></textarea>
                                @if (old('form_context') === 'edit') @error('deskripsi') <p class="mt-1.5 text-xs text-rose-600">{{ $message }}</p> @enderror @endif
                            </div>
                            <div class="flex items-start gap-3 rounded-2xl border border-sky-100 bg-sky-50/70 px-4 py-3">
                                <span class="mt-0.5 grid h-8 w-8 shrink-0 place-items-center rounded-xl bg-white text-sky-700 ring-1 ring-sky-100">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m8.5 12 2.2 2.2 4.8-5"/><circle cx="12" cy="12" r="8"/></svg>
                                </span>
                                <p class="text-sm leading-6 text-slate-600">Perubahan aturan otomatis langsung berlaku setelah disimpan.</p>
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