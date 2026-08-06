@extends('layouts.portal')

@section('title', 'Kelola Jurusan')

@section('content')
<div x-data="{
    formOpen: false,
    mode: 'tambah',
    form: {
        id: null,
        nama_jurusan: '',
        tingkat: 'kuliah',
        kode: '',
        durasi_min_bulan: 1,
        durasi_max_bulan: '',
        keterangan: '',
        status: 'aktif',
    },
    action: @js(route('admin-peserta.jurusan.store')),
    bukaTambah() {
        this.mode = 'tambah';
        this.action = @js(route('admin-peserta.jurusan.store'));
        this.form = { id: null, nama_jurusan: '', tingkat: 'kuliah', kode: '', durasi_min_bulan: 1, durasi_max_bulan: '', keterangan: '', status: 'aktif' };
        this.formOpen = true;
    },
    bukaEdit(data) {
        this.mode = 'edit';
        this.action = data.action;
        this.form = {
            id: data.id,
            nama_jurusan: data.nama_jurusan,
            tingkat: data.tingkat,
            kode: data.kode,
            durasi_min_bulan: data.durasi_min_bulan,
            durasi_max_bulan: data.durasi_max_bulan ?? '',
            keterangan: data.keterangan ?? '',
            status: data.status,
        };
        this.formOpen = true;
    },
}" @keydown.escape.window="formOpen = false" x-effect="document.body.classList.toggle('overflow-hidden', formOpen)">

    {{-- Judul --}}
    <header class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="mt-5 text-2xl font-extrabold tracking-tight text-slate-950 sm:text-3xl">Kelola Jurusan</h1>
            <p class="mt-1 text-sm text-slate-500">Atur jurusan kuliah dan SMK beserta ketentuan durasi magang minimalnya. Penugasan tugas mingguan otomatis menyesuaikan daftar ini.</p>
        </div>
        <button type="button" @click="bukaTambah()" class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-sky-500 to-blue-600 px-5 py-2.5 text-sm font-bold text-white shadow-[0_10px_25px_rgba(2,132,199,0.25)] transition duration-200 hover:-translate-y-0.5 hover:from-sky-600 hover:to-blue-700">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
            Tambah Jurusan
        </button>
    </header>

    {{-- Alert --}}
    @if (session('success'))
        <div class="flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800">
            <svg class="mt-0.5 h-5 w-5 shrink-0 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if (session('error'))
        <div class="flex items-start gap-3 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-800">
            <svg class="mt-0.5 h-5 w-5 shrink-0 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    @if ($errors->any())
        <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-800">
            <div class="flex items-start gap-3">
                <svg class="mt-0.5 h-5 w-5 shrink-0 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <div>
                    <p>Periksa kembali data yang diisi:</p>
                    <ul class="mt-1 list-inside list-disc space-y-0.5 text-xs font-medium">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    @php
        $kuliah = $jurusanList->where('tingkat', 'kuliah');
        $smk = $jurusanList->where('tingkat', 'smk');
    @endphp

    {{-- Grup Jurusan Kuliah --}}
    <section class="mt-8 overflow-hidden rounded-3xl border border-sky-100/90 bg-white/95 shadow-[0_20px_50px_rgba(15,52,94,0.09)] backdrop-blur">
        <div class="flex flex-col gap-4 border-b border-sky-100 bg-gradient-to-r from-sky-50 via-blue-50 to-cyan-50 px-6 py-5 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-lg font-bold text-slate-950">Jurusan Kuliah</h2>
                <p class="mt-0.5 text-sm text-slate-500">Magang sesuai rentang bulan yang ditentukan</p>
            </div>
            <span class="w-fit rounded-xl bg-white px-4 py-2 text-xs font-bold text-sky-700 shadow-sm ring-1 ring-slate-200">{{ $kuliah->count() }} jurusan</span>
        </div>

        <div class="p-8">
            @if ($kuliah->isEmpty())
                <div class="rounded-2xl border border-dashed border-slate-200 bg-white px-5 py-16 text-center">
                    <span class="mx-auto grid h-14 w-14 place-items-center rounded-2xl bg-sky-50 text-sky-500">
                        <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </span>
                    <p class="mt-3 text-sm font-bold text-slate-800">Belum ada jurusan kuliah.</p>
                    <p class="mt-1 text-sm text-slate-500">Klik "Tambah Jurusan" untuk mulai menambahkan.</p>
                </div>
            @else
                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($kuliah as $item)
                        <article class="relative flex flex-col overflow-hidden rounded-2xl border border-slate-100 bg-gradient-to-b from-white to-sky-50/40 p-6 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:shadow-md">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0 flex-1">
                                    <h3 class="text-base font-bold text-slate-950">{{ $item->nama_jurusan }}</h3>
                                    <p class="mt-1 text-xs text-slate-500">Kode: {{ $item->kode }}</p>
                                </div>
                                <span @class([
                                    'shrink-0 rounded-full px-3 py-1 text-[11px] font-bold ring-1',
                                    'bg-emerald-50 text-emerald-700 ring-emerald-200' => $item->status === 'aktif',
                                    'bg-slate-100 text-slate-500 ring-slate-200' => $item->status !== 'aktif',
                                ])>{{ $item->status === 'aktif' ? 'Aktif' : 'Nonaktif' }}</span>
                            </div>

                            <p class="mt-3 text-sm font-semibold text-slate-700">
                                Minimal magang {{ $item->durasi_min_bulan }} bulan
                                @if ($item->durasi_max_bulan)
                                    &middot; maksimal {{ $item->durasi_max_bulan }} bulan
                                @endif
                            </p>

                            @if ($item->keterangan)
                                <p class="mt-1 line-clamp-2 text-xs text-slate-500">{{ $item->keterangan }}</p>
                            @endif

                            <div class="mt-4 flex items-center justify-between border-t border-slate-100 pt-4">
                                <span class="text-xs font-semibold text-slate-500">{{ $item->peserta_magang_count }} peserta</span>
                                <div class="flex items-center gap-1">
                                    <button type="button" class="grid h-9 w-9 place-items-center rounded-xl bg-blue-50 text-blue-700 ring-1 ring-blue-100 transition hover:bg-blue-100" title="Edit jurusan" aria-label="Edit {{ $item->nama_jurusan }}" @click="bukaEdit({
                                        id: @js($item->id_jurusan),
                                        nama_jurusan: @js($item->nama_jurusan),
                                        tingkat: @js($item->tingkat),
                                        kode: @js($item->kode),
                                        durasi_min_bulan: @js($item->durasi_min_bulan),
                                        durasi_max_bulan: @js($item->durasi_max_bulan),
                                        keterangan: @js($item->keterangan),
                                        status: @js($item->status),
                                        action: @js(route('admin-peserta.jurusan.update', $item)),
                                    })">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m5 16.5-.8 3.3 3.3-.8L18 8.5 15.5 6 5 16.5Z"/><path d="m13.8 7.7 2.5 2.5"/></svg>
                                    </button>
                                    <form method="POST" action="{{ route('admin-peserta.jurusan.destroy', $item) }}" onsubmit="return confirm('Hapus jurusan {{ $item->nama_jurusan }}? Aksi ini tidak dapat dibatalkan.')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="grid h-9 w-9 place-items-center rounded-xl bg-rose-50 text-rose-600 ring-1 ring-rose-100 transition hover:bg-rose-100" title="Hapus jurusan" aria-label="Hapus {{ $item->nama_jurusan }}">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M5 7h14M9 7V4.5h6V7M8 10v7M12 10v7M16 10v7M6.5 7l.7 12h9.6l.7-12"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    {{-- Grup Jurusan SMK --}}
    <section class="mt-8 overflow-hidden rounded-3xl border border-sky-100/90 bg-white/95 shadow-[0_20px_50px_rgba(15,52,94,0.09)] backdrop-blur">
        <div class="flex flex-col gap-4 border-b border-sky-100 bg-gradient-to-r from-sky-50 via-blue-50 to-cyan-50 px-6 py-5 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-lg font-bold text-slate-950">Jurusan SMK</h2>
                <p class="mt-0.5 text-sm text-slate-500">Magang minimal sesuai jumlah bulan yang ditentukan</p>
            </div>
            <span class="w-fit rounded-xl bg-white px-4 py-2 text-xs font-bold text-sky-700 shadow-sm ring-1 ring-slate-200">{{ $smk->count() }} jurusan</span>
        </div>

        <div class="p-8">
            @if ($smk->isEmpty())
                <div class="rounded-2xl border border-dashed border-slate-200 bg-white px-5 py-16 text-center">
                    <span class="mx-auto grid h-14 w-14 place-items-center rounded-2xl bg-sky-50 text-sky-500">
                        <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </span>
                    <p class="mt-3 text-sm font-bold text-slate-800">Belum ada jurusan SMK.</p>
                    <p class="mt-1 text-sm text-slate-500">Klik "Tambah Jurusan" untuk mulai menambahkan.</p>
                </div>
            @else
                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($smk as $item)
                        <article class="relative flex flex-col overflow-hidden rounded-2xl border border-slate-100 bg-gradient-to-b from-white to-sky-50/40 p-6 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:shadow-md">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0 flex-1">
                                    <h3 class="text-base font-bold text-slate-950">{{ $item->nama_jurusan }}</h3>
                                    <p class="mt-1 text-xs text-slate-500">Kode: {{ $item->kode }}</p>
                                </div>
                                <span @class([
                                    'shrink-0 rounded-full px-3 py-1 text-[11px] font-bold ring-1',
                                    'bg-emerald-50 text-emerald-700 ring-emerald-200' => $item->status === 'aktif',
                                    'bg-slate-100 text-slate-500 ring-slate-200' => $item->status !== 'aktif',
                                ])>{{ $item->status === 'aktif' ? 'Aktif' : 'Nonaktif' }}</span>
                            </div>

                            <p class="mt-3 text-sm font-semibold text-slate-700">
                                Minimal magang {{ $item->durasi_min_bulan }} bulan
                                @if ($item->durasi_max_bulan)
                                    &middot; maksimal {{ $item->durasi_max_bulan }} bulan
                                @endif
                            </p>

                            @if ($item->keterangan)
                                <p class="mt-1 line-clamp-2 text-xs text-slate-500">{{ $item->keterangan }}</p>
                            @endif

                            <div class="mt-4 flex items-center justify-between border-t border-slate-100 pt-4">
                                <span class="text-xs font-semibold text-slate-500">{{ $item->peserta_magang_count }} peserta</span>
                                <div class="flex items-center gap-1">
                                    <button type="button" class="grid h-9 w-9 place-items-center rounded-xl bg-blue-50 text-blue-700 ring-1 ring-blue-100 transition hover:bg-blue-100" title="Edit jurusan" aria-label="Edit {{ $item->nama_jurusan }}" @click="bukaEdit({
                                        id: @js($item->id_jurusan),
                                        nama_jurusan: @js($item->nama_jurusan),
                                        tingkat: @js($item->tingkat),
                                        kode: @js($item->kode),
                                        durasi_min_bulan: @js($item->durasi_min_bulan),
                                        durasi_max_bulan: @js($item->durasi_max_bulan),
                                        keterangan: @js($item->keterangan),
                                        status: @js($item->status),
                                        action: @js(route('admin-peserta.jurusan.update', $item)),
                                    })">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m5 16.5-.8 3.3 3.3-.8L18 8.5 15.5 6 5 16.5Z"/><path d="m13.8 7.7 2.5 2.5"/></svg>
                                    </button>
                                    <form method="POST" action="{{ route('admin-peserta.jurusan.destroy', $item) }}" onsubmit="return confirm('Hapus jurusan {{ $item->nama_jurusan }}? Aksi ini tidak dapat dibatalkan.')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="grid h-9 w-9 place-items-center rounded-xl bg-rose-50 text-rose-600 ring-1 ring-rose-100 transition hover:bg-rose-100" title="Hapus jurusan" aria-label="Hapus {{ $item->nama_jurusan }}">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M5 7h14M9 7V4.5h6V7M8 10v7M12 10v7M16 10v7M6.5 7l.7 12h9.6l.7-12"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    {{-- Modal Tambah/Edit --}}
    <div x-show="formOpen" x-cloak class="fixed inset-0 z-[9999] flex items-center justify-center overflow-y-auto bg-slate-950/60 p-4" @click.self="formOpen = false">
        <div x-show="formOpen" x-transition.scale.origin.center class="relative w-full max-w-lg rounded-3xl border border-slate-200 bg-white shadow-2xl">
            <header class="flex items-start justify-between gap-4 bg-gradient-to-r from-white via-sky-50/50 to-blue-50/40 px-6 py-5 border-b border-slate-200 rounded-t-3xl">
                <div>
                    <h3 class="mt-1 text-xl font-extrabold text-slate-950" x-text="mode === 'tambah' ? 'Tambah Jurusan' : 'Edit Jurusan'"></h3>
                    <p class="mt-0.5 text-sm text-slate-500" x-text="mode === 'tambah' ? 'Isi data jurusan baru.' : 'Perbarui data jurusan.'"></p>
                </div>
                <button type="button" @click="formOpen = false" class="grid h-10 w-10 shrink-0 place-items-center rounded-xl border border-slate-200 bg-white text-slate-500 transition hover:bg-slate-100" aria-label="Tutup modal">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="m6 6 12 12M18 6 6 18"/></svg>
                </button>
            </header>

            <form method="POST" :action="action" class="space-y-6 p-6">
                @csrf
                <template x-if="mode === 'edit'">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <div>
                    <label class="mb-1.5 block text-sm font-bold text-slate-700">Nama Jurusan</label>
                    <input type="text" name="nama_jurusan" x-model="form.nama_jurusan" required maxlength="100" class="h-11 w-full rounded-xl border border-slate-300 px-4 text-sm focus:border-sky-500 focus:ring-2 focus:ring-sky-100">
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-1.5 block text-sm font-bold text-slate-700">Tingkatan</label>
                        <select name="tingkat" x-model="form.tingkat" required class="h-11 w-full rounded-xl border border-slate-300 px-4 text-sm focus:border-sky-500 focus:ring-2 focus:ring-sky-100">
                            <option value="kuliah">Kuliah</option>
                            <option value="smk">SMK</option>
                        </select>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-bold text-slate-700">Kode</label>
                        <input type="text" name="kode" x-model="form.kode" required maxlength="20" placeholder="mis. TI, RPL" class="h-11 w-full rounded-xl border border-slate-300 px-4 text-sm uppercase focus:border-sky-500 focus:ring-2 focus:ring-sky-100">
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-1.5 block text-sm font-bold text-slate-700">Durasi Minimal (bulan)</label>
                        <input type="number" name="durasi_min_bulan" x-model="form.durasi_min_bulan" required min="1" max="24" class="h-11 w-full rounded-xl border border-slate-300 px-4 text-sm focus:border-sky-500 focus:ring-2 focus:ring-sky-100">
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-bold text-slate-700">Durasi Maksimal (bulan)</label>
                        <input type="number" name="durasi_max_bulan" x-model="form.durasi_max_bulan" min="1" max="24" placeholder="Kosongkan" class="h-11 w-full rounded-xl border border-slate-300 px-4 text-sm focus:border-sky-500 focus:ring-2 focus:ring-sky-100">
                    </div>
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-bold text-slate-700">Keterangan (opsional)</label>
                    <textarea name="keterangan" x-model="form.keterangan" rows="2" maxlength="500" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm focus:border-sky-500 focus:ring-2 focus:ring-sky-100"></textarea>
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-bold text-slate-700">Status</label>
                    <select name="status" x-model="form.status" required class="h-11 w-full rounded-xl border border-slate-300 px-4 text-sm focus:border-sky-500 focus:ring-2 focus:ring-sky-100">
                        <option value="aktif">Aktif</option>
                        <option value="nonaktif">Nonaktif</option>
                    </select>
                </div>

                <footer class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                    <button type="button" @click="formOpen = false" class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-600 transition hover:bg-slate-100">Batal</button>
                    <button type="submit" class="rounded-xl bg-gradient-to-r from-sky-500 to-blue-600 px-5 py-2.5 text-sm font-bold text-white shadow-[0_10px_24px_rgba(2,132,199,0.24)] transition hover:-translate-y-0.5">
                        <span x-text="mode === 'tambah' ? 'Simpan Jurusan' : 'Simpan Perubahan'"></span>
                    </button>
                </footer>
            </form>
        </div>
    </div>
</div>
@endsection