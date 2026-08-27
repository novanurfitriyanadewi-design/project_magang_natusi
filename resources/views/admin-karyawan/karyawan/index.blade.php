@extends('layouts.portal')

@section('title', 'Data Karyawan')

@section('content')
<div
    x-data="{
        detailOpen: false,
        editOpen: false,
        deleteOpen: false,
        detailKaryawan: { berkas: [] },
        editKaryawan: {
            action: '', nama_karyawan: '', nik: '', email: '',
            no_hp: '', alamat: '', status: 'aktif',
            tanggal_bergabung: '', divisi_id: ''
        },
        deleteKaryawan: { action: '', nama: '' },
        openDetail(k) { this.detailKaryawan = k; this.detailOpen = true; },
        openEdit(k) { this.editKaryawan = k; this.editOpen = true; },
        openDelete(k) { this.deleteKaryawan = k; this.deleteOpen = true; },
        closeModals() { this.detailOpen = false; this.editOpen = false; this.deleteOpen = false; }
    }"
    @keydown.escape.window="closeModals()"
    x-effect="document.body.classList.toggle('overflow-hidden', detailOpen || editOpen || deleteOpen)"
>
    <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <span class="inline-flex items-center gap-2 rounded-full bg-sky-100 px-3 py-1 text-[10px] font-bold uppercase tracking-[0.16em] text-sky-700">
                <span class="h-1.5 w-1.5 rounded-full bg-sky-500"></span>
                Manajemen Personil
            </span>
            <h1 class="mt-3 text-2xl font-extrabold tracking-tight text-slate-950 sm:text-3xl">Data Karyawan</h1>
            <p class="mt-1 text-sm text-slate-500">Kelola informasi seluruh personil CV Natusi secara efisien.</p>
        </div>
    </div>

    <section class="mt-5 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <article class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-indigo-500 to-indigo-600 p-5 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <p class="text-[11px] font-bold uppercase tracking-[0.14em] text-indigo-100">Total Karyawan</p>
                <span class="grid h-9 w-9 place-items-center rounded-xl bg-white/20">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none"><circle cx="9" cy="8" r="3" stroke="currentColor" stroke-width="1.8"/><path d="M3.5 19c.5-3.5 2.3-5.2 5.5-5.2s5 1.7 5.5 5.2M16 7.5h5M18.5 5v5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                </span>
            </div>
            <p class="mt-3 text-3xl font-extrabold">{{ $totalKaryawan }}</p>
            <p class="mt-1 text-xs text-indigo-100">Seluruh personil terdaftar</p>
            <span class="pointer-events-none absolute -bottom-4 -right-4 h-20 w-20 rounded-full bg-white/10"></span>
        </article>

        <article class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-emerald-500 to-emerald-600 p-5 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <p class="text-[11px] font-bold uppercase tracking-[0.14em] text-emerald-100">Karyawan Aktif</p>
                <span class="grid h-9 w-9 place-items-center rounded-xl bg-white/20">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none"><path d="M5 13l4 4L19 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </span>
            </div>
            <p class="mt-3 text-3xl font-extrabold">{{ $karyawanAktif }}</p>
            <p class="mt-1 text-xs text-emerald-100">Sedang bekerja aktif</p>
            <span class="pointer-events-none absolute -bottom-4 -right-4 h-20 w-20 rounded-full bg-white/10"></span>
        </article>

        <article class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-amber-500 to-amber-600 p-5 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <p class="text-[11px] font-bold uppercase tracking-[0.14em] text-amber-100">Karyawan Non-Aktif</p>
                <span class="grid h-9 w-9 place-items-center rounded-xl bg-white/20">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none"><path d="M6 6l12 12M18 6L6 18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                </span>
            </div>
            <p class="mt-3 text-3xl font-extrabold">{{ $karyawanNonAktif }}</p>
            <p class="mt-1 text-xs text-amber-100">Tidak lagi bertugas</p>
            <span class="pointer-events-none absolute -bottom-4 -right-4 h-20 w-20 rounded-full bg-white/10"></span>
        </article>

        <article class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-violet-600 to-purple-700 p-5 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <p class="text-[11px] font-bold uppercase tracking-[0.14em] text-violet-100">Baru Bulan Ini</p>
                <span class="grid h-9 w-9 place-items-center rounded-xl bg-white/20">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none"><path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"/></svg>
                </span>
            </div>
            <p class="mt-3 text-3xl font-extrabold">{{ $karyawanBaruBulanIni }}</p>
            <p class="mt-1 text-xs text-violet-100">Bergabung bulan ini</p>
            <span class="pointer-events-none absolute -bottom-4 -right-4 h-20 w-20 rounded-full bg-white/10"></span>
        </article>
    </section>

    <form method="GET" class="mt-5 flex flex-wrap items-end gap-4 rounded-2xl border border-slate-100 bg-slate-50 p-4">
        <div class="min-w-[180px] flex-1">
            <label class="mb-1 block text-[10px] font-bold uppercase tracking-wide text-slate-400">Cari</label>
            <input
                type="text" name="search" value="{{ request('search') }}"
                placeholder="Cari nama, NIK, atau email..."
                class="w-full rounded-lg border-slate-300 text-sm focus:border-sky-500 focus:ring-sky-500"
            >
        </div>

        <div class="min-w-[160px]">
            <label class="mb-1 block text-[10px] font-bold uppercase tracking-wide text-slate-400">Divisi</label>
            <select name="divisi_id" class="w-full rounded-lg border-slate-300 text-sm focus:border-sky-500 focus:ring-sky-500">
                <option value="">Semua Divisi</option>
                @foreach ($divisiList as $divisi)
                    <option value="{{ $divisi->id_divisi }}" @selected((string) request('divisi_id') === (string) $divisi->id_divisi)>
                        {{ $divisi->nama_divisi }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="min-w-[150px]">
            <label class="mb-1 block text-[10px] font-bold uppercase tracking-wide text-slate-400">Status</label>
            <select name="status" class="w-full rounded-lg border-slate-300 text-sm focus:border-sky-500 focus:ring-sky-500">
                <option value="">Semua Status</option>
                <option value="aktif" @selected(request('status') === 'aktif')>Aktif</option>
                <option value="nonaktif" @selected(request('status') === 'nonaktif')>Non-Aktif</option>
            </select>
        </div>

        <div class="min-w-[170px]">
            <label class="mb-1 block text-[10px] font-bold uppercase tracking-wide text-slate-400">Urutan</label>
            <select name="urutan" class="w-full rounded-lg border-slate-300 text-sm focus:border-sky-500 focus:ring-sky-500">
                <option value="nama_asc" @selected(request('urutan', 'nama_asc') === 'nama_asc')>Nama (A-Z)</option>
                <option value="nama_desc" @selected(request('urutan') === 'nama_desc')>Nama (Z-A)</option>
                <option value="nip" @selected(request('urutan') === 'nip')>NIK</option>
                <option value="tanggal" @selected(request('urutan') === 'tanggal')>Tanggal Bergabung</option>
            </select>
        </div>

        <button type="submit" class="rounded-lg bg-sky-600 px-4 py-2 text-sm font-bold text-white hover:bg-sky-700">Terapkan</button>
        <a href="{{ route('admin-karyawan.karyawan.index') }}" class="px-2 text-sm font-bold text-sky-700 hover:underline">Reset Filter</a>
    </form>

    <section class="mt-5 overflow-hidden rounded-3xl border border-sky-100/90 bg-white/95 shadow-[0_20px_50px_rgba(15,52,94,0.09)]">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-gradient-to-r from-sky-50 via-blue-50 to-cyan-50">
                    <tr>
                        <th class="px-5 py-3.5 text-left text-[11px] font-bold uppercase tracking-[0.09em] text-slate-500">Nama Karyawan</th>
                        <th class="px-5 py-3.5 text-left text-[11px] font-bold uppercase tracking-[0.09em] text-slate-500">NIK</th>
                        <th class="px-5 py-3.5 text-left text-[11px] font-bold uppercase tracking-[0.09em] text-slate-500">Divisi</th>
                        <th class="px-5 py-3.5 text-left text-[11px] font-bold uppercase tracking-[0.09em] text-slate-500">No. HP</th>
                        <th class="px-5 py-3.5 text-left text-[11px] font-bold uppercase tracking-[0.09em] text-slate-500">Alamat</th>
                        <th class="px-5 py-3.5 text-left text-[11px] font-bold uppercase tracking-[0.09em] text-slate-500">Tgl Bergabung</th>
                        <th class="px-5 py-3.5 text-left text-[11px] font-bold uppercase tracking-[0.09em] text-slate-500">Status</th>
                        <th class="px-5 py-3.5 text-center text-[11px] font-bold uppercase tracking-[0.09em] text-slate-500">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($karyawans as $karyawan)
                        @php($meta = $karyawan->statusMeta())
                        <tr class="transition hover:bg-sky-50/70">
                            <td class="whitespace-nowrap px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <span class="grid h-10 w-10 shrink-0 place-items-center rounded-full bg-sky-100 text-sm font-bold text-sky-700">
                                        {{ $karyawan->initials() }}
                                    </span>
                                    <div class="min-w-0">
                                        <p class="truncate font-bold text-slate-800">{{ $karyawan->nama_karyawan }}</p>
                                        <p class="truncate text-[11px] text-slate-400">{{ $karyawan->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="whitespace-nowrap px-5 py-4 text-sm font-medium text-slate-600">{{ $karyawan->nip ?? '-' }}</td>
                            <td class="whitespace-nowrap px-5 py-4">
                                @if ($karyawan->divisi)
                                    <span class="inline-flex rounded-full bg-sky-50 px-2.5 py-1 text-[11px] font-semibold text-sky-700">
                                        {{ $karyawan->divisi->nama_divisi }}
                                    </span>
                                @else
                                    <span class="text-xs text-slate-400">Belum ada</span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-5 py-4 text-sm text-slate-600">{{ $karyawan->no_hp ?? '-' }}</td>
                            <td class="max-w-[220px] truncate px-5 py-4 text-sm text-slate-600" title="{{ $karyawan->alamat ?? '-' }}">{{ $karyawan->alamat ?? '-' }}</td>
                            <td class="whitespace-nowrap px-5 py-4 text-sm text-slate-600">
                                {{ $karyawan->tanggal_bergabung?->translatedFormat('d M Y') ?? '-' }}
                            </td>
                            <td class="whitespace-nowrap px-5 py-4">
                                <span class="inline-flex items-center gap-1.5 text-xs font-semibold {{ $meta['text'] }}">
                                    <span class="h-2 w-2 rounded-full {{ $meta['dot'] }}"></span>
                                    {{ $meta['label'] }}
                                </span>
                            </td>
                            <td class="whitespace-nowrap px-5 py-4">
                                <div class="flex justify-center gap-2">
                                    <button
                                        type="button"
                                        title="Detail"
                                        class="rounded-lg p-2 text-sky-600 hover:bg-sky-50"
                                        {{-- [UBAH] jabatan dihapus dari data detail --}}
                                        @click="openDetail({
                                            nama: @js($karyawan->nama_karyawan),
                                            nip: @js($karyawan->nip ?? '-'),
                                            email: @js($karyawan->email),
                                            no_hp: @js($karyawan->no_hp ?? '-'),
                                            alamat: @js($karyawan->alamat ?? '-'),
                                            divisi: @js($karyawan->divisi->nama_divisi ?? 'Belum ada'),
                                            status: @js($meta['label']),
                                            statusValue: @js($karyawan->status),
                                            tanggal: @js(optional($karyawan->tanggal_bergabung)->translatedFormat('d M Y') ?? '-'),
                                            berkas: @js(collect([
                                                ['label' => 'Surat Lamaran Kerja', 'path' => $karyawan->permintaanLamaran->surat_lamaran_path ?? null],
                                                ['label' => 'CV (Curriculum Vitae)', 'path' => $karyawan->permintaanLamaran->cv_path ?? null],
                                                ['label' => 'Ijazah & Transkrip Nilai', 'path' => $karyawan->permintaanLamaran->ijazah_path ?? null],
                                                ['label' => 'Fotokopi KTP', 'path' => $karyawan->permintaanLamaran->ktp_path ?? null],
                                                ['label' => 'Pas Foto', 'path' => $karyawan->permintaanLamaran->pas_foto_path ?? null],
                                                ['label' => 'SKCK', 'path' => $karyawan->permintaanLamaran->skck_path ?? null],
                                                ['label' => 'Portofolio', 'path' => $karyawan->permintaanLamaran->portfolio_path ?? null],
                                                ['label' => 'Pengalaman Kerja', 'path' => $karyawan->permintaanLamaran->pengalaman_kerja_path ?? null],
                                            ])->map(fn ($b) => [
                                                'label' => $b['label'],
                                                'url'   => $b['path'] ? \Illuminate\Support\Facades\Storage::url($b['path']) : null,
                                            ])->filter(fn ($b) => $b['url'])->values()),
                                        })"
                                    >
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7Z" stroke="currentColor" stroke-width="1.7"/><circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.7"/></svg>
                                    </button>
                                    <button
                                        type="button"
                                        title="Edit"
                                        class="rounded-lg p-2 text-slate-500 hover:bg-slate-100"
                                        @click="openEdit({
                                            action: @js(route('admin-karyawan.karyawan.update', $karyawan->id_karyawan)),
                                            nama_karyawan: @js($karyawan->nama_karyawan),
                                            nip: @js($karyawan->nip),
                                            email: @js($karyawan->email),
                                            no_hp: @js($karyawan->no_hp),
                                            alamat: @js($karyawan->alamat),
                                            status: @js($karyawan->status),
                                            divisi_id: @js($karyawan->divisi_id ?? ''),
                                        })"
                                    >
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none"><path d="M11 4H6a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-5M18.5 2.5a2.12 2.12 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5Z" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    </button>
                                    <button
                                        type="button"
                                        title="Hapus"
                                        class="rounded-lg p-2 text-rose-600 hover:bg-rose-50"
                                        @click="openDelete({
                                            action: @js(route('admin-karyawan.karyawan.destroy', $karyawan->id_karyawan)),
                                            nama: @js($karyawan->nama_karyawan)
                                        })"
                                    >
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none"><path d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M10 11v6M14 11v6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-5 py-10 text-center text-sm text-slate-400">Belum ada data karyawan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="flex flex-col gap-3 border-t border-sky-100 px-5 py-4 text-xs text-slate-500 sm:flex-row sm:items-center sm:justify-between">
            <span>
                Menampilkan {{ $karyawans->firstItem() ?? 0 }} – {{ $karyawans->lastItem() ?? 0 }}
                dari {{ $karyawans->total() }} karyawan
            </span>
            {{ $karyawans->links() }}
        </div>
    </section>

    {{-- ============================================================ --}}
    {{-- MODAL DETAIL — didesain ulang, tanpa field Jabatan            --}}
    {{-- ============================================================ --}}
    <div x-cloak x-show="detailOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="detailOpen = false"></div>

        <div class="relative flex max-h-[90vh] w-full max-w-lg flex-col overflow-hidden rounded-3xl bg-white shadow-2xl">

            {{-- Header gradient dengan avatar & status --}}
            <div class="relative shrink-0 overflow-hidden bg-gradient-to-br from-sky-600 via-blue-600 to-indigo-700 px-6 pb-14 pt-6 text-white">
                <span class="pointer-events-none absolute -right-8 -top-8 h-32 w-32 rounded-full bg-white/10"></span>
                <span class="pointer-events-none absolute -bottom-10 right-10 h-20 w-20 rounded-full bg-white/10"></span>

                <div class="relative flex items-start justify-between">
                    <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-sky-100">Profil Karyawan</p>
                    <button type="button" @click="detailOpen = false" class="grid h-8 w-8 place-items-center rounded-full bg-white/15 text-white hover:bg-white/25">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none"><path d="M6 6l12 12M18 6L6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                    </button>
                </div>

                <div class="relative mt-3 flex items-center gap-4">
                    <span class="grid h-14 w-14 shrink-0 place-items-center rounded-2xl bg-white/20 text-xl font-extrabold ring-2 ring-white/30" x-text="(detailKaryawan.nama || '').split(' ').map(w => w[0]).slice(0,2).join('').toUpperCase()"></span>
                    <div class="min-w-0">
                        <h3 class="truncate text-xl font-extrabold leading-tight" x-text="detailKaryawan.nama"></h3>
                        <p class="truncate text-sm text-sky-100" x-text="detailKaryawan.divisi"></p>
                    </div>
                </div>
            </div>

            {{-- Body (scrollable) --}}
            <div class="-mt-8 flex-1 overflow-y-auto px-6 pb-2">

                {{-- Kartu status kepegawaian --}}
                <div class="flex items-center justify-between rounded-2xl border border-slate-100 bg-white px-5 py-4 shadow-[0_10px_30px_rgba(15,52,94,0.08)]">
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-slate-400">Status Kepegawaian</p>
                        <p class="mt-1 text-base font-extrabold text-slate-800" x-text="detailKaryawan.status"></p>
                    </div>
                    <span
                        class="h-3.5 w-3.5 rounded-full"
                        :class="detailKaryawan.statusValue === 'aktif' ? 'bg-emerald-500' : 'bg-rose-500'"
                    ></span>
                </div>

                {{-- Informasi utama --}}
                <p class="mb-2 mt-5 text-[11px] font-bold uppercase tracking-[0.14em] text-slate-400">Informasi Utama</p>
                <div class="grid grid-cols-2 gap-3">
                    <div class="rounded-xl border border-slate-100 bg-slate-50/70 px-4 py-3">
                        <p class="text-[10px] font-bold uppercase tracking-wide text-slate-400">Nama Lengkap</p>
                        <p class="mt-0.5 truncate text-sm font-bold text-slate-800" x-text="detailKaryawan.nama"></p>
                    </div>
                    <div class="rounded-xl border border-slate-100 bg-slate-50/70 px-4 py-3">
                        <p class="text-[10px] font-bold uppercase tracking-wide text-slate-400">NIK</p>
                        <p class="mt-0.5 truncate text-sm font-bold text-slate-800" x-text="detailKaryawan.nip"></p>
                    </div>
                    <div class="col-span-2 rounded-xl border border-slate-100 bg-slate-50/70 px-4 py-3">
                        <p class="text-[10px] font-bold uppercase tracking-wide text-slate-400">Email</p>
                        <p class="mt-0.5 truncate text-sm font-bold text-slate-800" x-text="detailKaryawan.email"></p>
                    </div>
                    <div class="rounded-xl border border-slate-100 bg-slate-50/70 px-4 py-3">
                        <p class="text-[10px] font-bold uppercase tracking-wide text-slate-400">Divisi</p>
                        <p class="mt-0.5 truncate text-sm font-bold text-slate-800" x-text="detailKaryawan.divisi"></p>
                    </div>
                    <div class="rounded-xl border border-slate-100 bg-slate-50/70 px-4 py-3">
                        <p class="text-[10px] font-bold uppercase tracking-wide text-slate-400">Nomor HP</p>
                        <p class="mt-0.5 truncate text-sm font-bold text-slate-800" x-text="detailKaryawan.no_hp"></p>
                    </div>
                    <div class="col-span-2 rounded-xl border border-slate-100 bg-slate-50/70 px-4 py-3">
                        <p class="text-[10px] font-bold uppercase tracking-wide text-slate-400">Tanggal Bergabung</p>
                        <p class="mt-0.5 truncate text-sm font-bold text-slate-800" x-text="detailKaryawan.tanggal"></p>
                    </div>
                </div>

                {{-- Alamat --}}
                <p class="mb-2 mt-5 text-[11px] font-bold uppercase tracking-[0.14em] text-slate-400">Alamat</p>
                <div class="flex items-start gap-2.5 rounded-xl border border-slate-100 bg-slate-50/70 px-4 py-3">
                    <svg class="mt-0.5 h-4 w-4 shrink-0 text-slate-400" viewBox="0 0 24 24" fill="none"><path d="M12 21s7-6.5 7-11.5A7 7 0 0 0 5 9.5C5 14.5 12 21 12 21Z" stroke="currentColor" stroke-width="1.7"/><circle cx="12" cy="9.5" r="2.4" stroke="currentColor" stroke-width="1.7"/></svg>
                    <p class="text-sm font-semibold text-slate-700" x-text="detailKaryawan.alamat"></p>
                </div>

                {{-- Berkas lamaran --}}
                <template x-if="detailKaryawan.berkas && detailKaryawan.berkas.length">
                    <div class="mt-5">
                        <p class="mb-2 text-[11px] font-bold uppercase tracking-[0.14em] text-slate-400">Berkas Lamaran</p>
                        <div class="grid grid-cols-2 gap-2.5">
                            <template x-for="file in detailKaryawan.berkas" :key="file.label">
                                <div class="flex items-center justify-between gap-2 rounded-xl border border-slate-100 bg-white px-3 py-2.5 shadow-sm">
                                    <span class="truncate text-xs font-semibold text-slate-700" x-text="file.label"></span>
                                    <div class="flex shrink-0 items-center gap-1.5">
                                        <a :href="file.url" target="_blank" title="Lihat" class="grid h-7 w-7 place-items-center rounded-full bg-sky-100 text-sky-600 transition hover:bg-sky-500 hover:text-white">
                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7Z" stroke="currentColor" stroke-width="1.7"/><circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.7"/></svg>
                                        </a>
                                        <a :href="file.url" download title="Unduh" class="grid h-7 w-7 place-items-center rounded-full bg-slate-100 text-slate-600 transition hover:bg-slate-700 hover:text-white">
                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none"><path d="M12 3v12m0 0-4-4m4 4 4-4M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-2" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        </a>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>
                <template x-if="!detailKaryawan.berkas || !detailKaryawan.berkas.length">
                    <p class="mt-5 rounded-xl border border-dashed border-slate-200 px-4 py-4 text-center text-xs text-slate-400">
                        Tidak ada berkas lamaran tersimpan.
                    </p>
                </template>

                <div class="h-2"></div>
            </div>

            {{-- Footer --}}
            <div class="shrink-0 border-t border-slate-100 px-6 py-4 text-right">
                <button type="button" @click="detailOpen = false" class="rounded-xl bg-slate-100 px-5 py-2.5 text-sm font-bold text-slate-700 hover:bg-slate-200">Tutup</button>
            </div>
        </div>
    </div>

    {{-- MODAL EDIT --}}
    <div x-cloak x-show="editOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/40" @click="editOpen = false"></div>
        <form method="POST" :action="editKaryawan.action" class="relative w-full max-w-lg rounded-2xl bg-white shadow-2xl">
            @csrf
            @method('PUT')
            <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4">
                <h3 class="text-lg font-bold text-slate-950">Edit Karyawan</h3>
                <button type="button" @click="editOpen = false" class="text-slate-400 hover:text-slate-700">✕</button>
            </div>
            <div class="grid gap-4 px-6 py-5 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label class="mb-1 block text-sm font-bold text-slate-700">Nama Karyawan</label>
                    <input type="text" name="nama_karyawan" x-model="editKaryawan.nama_karyawan" required class="w-full rounded-xl border-slate-300 focus:border-sky-500 focus:ring-sky-500">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-bold text-slate-700">NIK</label>
                    <input type="text" name="nip" x-model="editKaryawan.nip" placeholder="Diambil otomatis dari form pelamar" class="w-full rounded-xl border-slate-300 focus:border-sky-500 focus:ring-sky-500">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-bold text-slate-700">Email</label>
                    <input type="email" name="email" x-model="editKaryawan.email" required class="w-full rounded-xl border-slate-300 focus:border-sky-500 focus:ring-sky-500">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-bold text-slate-700">No. HP</label>
                    <input type="text" name="no_hp" x-model="editKaryawan.no_hp" class="w-full rounded-xl border-slate-300 focus:border-sky-500 focus:ring-sky-500">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-bold text-slate-700">Divisi</label>
                    <select
                        name="divisi_id"
                        x-model="editKaryawan.divisi_id"
                        class="w-full rounded-xl border-slate-300 focus:border-sky-500 focus:ring-sky-500"
                    >
                        <option value="">Belum ada divisi</option>
                        @foreach ($divisiList as $divisi)
                            <option value="{{ $divisi->id_divisi }}">{{ $divisi->nama_divisi }}</option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-[11px] text-slate-400">Berdasarkan posisi yang dipilih saat mengisi form (tetap dapat diubah).</p>
                </div>
                <div class="sm:col-span-2">
                    <label class="mb-1 block text-sm font-bold text-slate-700">Alamat</label>
                    <textarea name="alamat" x-model="editKaryawan.alamat" rows="2" class="w-full rounded-xl border-slate-300 focus:border-sky-500 focus:ring-sky-500"></textarea>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-bold text-slate-700">Status</label>
                    <select name="status" x-model="editKaryawan.status" class="w-full rounded-xl border-slate-300 focus:border-sky-500 focus:ring-sky-500">
                        <option value="aktif">Aktif</option>
                        <option value="nonaktif">Non-Aktif</option>
                    </select>
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
        <form method="POST" :action="deleteKaryawan.action" class="relative w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl">
            @csrf
            @method('DELETE')
            <div class="flex items-center gap-4">
                <div class="grid h-12 w-12 shrink-0 place-items-center rounded-full bg-rose-100 text-rose-600">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none"><path d="M12 9v4m0 4h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-slate-950">Hapus Data Karyawan</h3>
                    <p class="mt-1 text-sm text-slate-500">Apakah Anda yakin ingin menghapus <span class="font-bold text-slate-800" x-text="deleteKaryawan.nama"></span>? Tindakan ini tidak dapat dibatalkan.</p>
                </div>
            </div>
            <div class="mt-6 flex justify-end gap-3">
                <button type="button" @click="deleteOpen = false" class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-700 hover:bg-slate-50">Batal</button>
                <button type="submit" class="rounded-xl bg-rose-600 px-5 py-2.5 text-sm font-bold text-white shadow hover:bg-rose-700">Hapus Karyawan</button>
            </div>
        </form>
    </div>
</div>
@endsection