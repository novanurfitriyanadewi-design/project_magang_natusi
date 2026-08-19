@extends('layouts.portal')

@section('title', 'Kelola Sertifikat')

@section('content')
<div
    class="space-y-6"
    x-data="{
        issueOpen: false,
        showOpen: false,
        editOpen: false,
        selected: {},
        openShow(data) {
            this.selected = { ...data };
            this.showOpen = true;
        },
        openEdit(data) {
            this.selected = { ...data };
            this.editOpen = true;
        },
        closeAll() {
            this.issueOpen = false;
            this.showOpen = false;
            this.editOpen = false;
        }
    }"
    @keydown.escape.window="closeAll()"
>
    <header class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="mt-5 text-2xl font-extrabold tracking-tight text-slate-950 sm:mt-0 sm:text-3xl">Kelola Sertifikat</h1>
            <p class="mt-1 text-sm text-slate-500">Admin menerbitkan, melihat, dan mengedit data sertifikat. Unduh PDF hanya tersedia di akun peserta.</p>
        </div>
        <button
            type="button"
            @click="issueOpen = true"
            class="inline-flex h-10 w-fit items-center justify-center gap-2 rounded-xl bg-sky-600 px-4 text-sm font-bold text-white shadow-sm transition hover:bg-sky-700"
        >
            <span class="material-symbols-outlined text-[18px]">add</span>
            Terbitkan Sertifikat
        </button>
    </header>

    @if (session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-700">
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
            <p class="font-bold">Periksa kembali data sertifikat:</p>
            <ul class="mt-1 list-inside list-disc">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div class="rounded-2xl border border-sky-100 bg-white p-5 shadow-sm">
            <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-slate-400">Sertifikat Terbit</p>
            <p class="mt-2 text-2xl font-extrabold text-sky-700">{{ $stats['total_terbit'] ?? 0 }}</p>
        </div>
        <div class="rounded-2xl border border-indigo-100 bg-white p-5 shadow-sm">
            <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-slate-400">Belum Disertifikasi</p>
            <p class="mt-2 text-2xl font-extrabold text-indigo-700">{{ $stats['belum_disertifikasi'] ?? 0 }}</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-slate-400">Riwayat Dicabut</p>
            <p class="mt-2 text-2xl font-extrabold text-slate-700">{{ $stats['total_dicabut'] ?? 0 }}</p>
        </div>
    </section>

    <section class="overflow-hidden rounded-3xl border border-sky-100 bg-white shadow-[0_18px_45px_rgba(15,52,94,0.08)]">
        <div class="flex flex-col gap-4 border-b border-sky-100 bg-gradient-to-r from-sky-50 via-blue-50 to-cyan-50 px-5 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
            <div>
                <h2 class="text-base font-extrabold text-slate-950">Daftar Sertifikat</h2>
                <p class="mt-0.5 text-xs text-slate-500">Aksi admin per sertifikat hanya Show dan Edit.</p>
            </div>

            <form method="GET" action="{{ route('admin-peserta.sertifikat.index') }}" class="flex flex-wrap items-center gap-2">
                <input
                    type="search"
                    name="search"
                    value="{{ $search }}"
                    placeholder="Cari nama / nomor..."
                    class="h-10 w-52 rounded-xl border border-slate-200 bg-white px-3 text-sm focus:border-sky-500 focus:ring-2 focus:ring-sky-100"
                >
                <select
                    name="status"
                    onchange="this.form.submit()"
                    class="h-10 rounded-xl border border-slate-200 bg-white px-3 text-sm focus:border-sky-500 focus:ring-2 focus:ring-sky-100"
                >
                    <option value="">Semua Status</option>
                    <option value="terbit" @selected($status === 'terbit')>Terbit</option>
                    <option value="dicabut" @selected($status === 'dicabut')>Dicabut</option>
                </select>
                <button type="submit" class="h-10 rounded-xl bg-sky-600 px-4 text-sm font-bold text-white hover:bg-sky-700">Cari</button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[980px] border-collapse text-left">
                <thead>
                    <tr class="border-b border-slate-200 bg-sky-50/70 text-[10px] font-bold uppercase tracking-[0.12em] text-slate-500">
                        <th class="px-6 py-4">Nomor</th>
                        <th class="px-5 py-4">Peserta</th>
                        <th class="px-5 py-4">Divisi</th>
                        <th class="px-5 py-4">Predikat</th>
                        <th class="px-5 py-4">Tanggal Terbit</th>
                        <th class="px-5 py-4 text-center">Status</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($riwayat as $item)
                        @php
                            $payload = [
                                'nama' => $item->peserta?->user?->nama ?? '-',
                                'instansi' => $item->peserta?->permintaan?->nama_sekolah ?? '-',
                                'nomor' => $item->nomor_sertifikat,
                                'divisi' => $item->divisi?->nama_divisi ?? '-',
                                'divisi_id' => (string) $item->divisi_id,
                                'predikat' => $item->predikat,
                                'judul' => $item->judul,
                                'tanggal' => $item->tanggal_terbit?->format('Y-m-d'),
                                'tanggal_label' => $item->tanggal_terbit?->translatedFormat('d F Y'),
                                'status' => $item->status,
                                'catatan' => $item->catatan ?? '',
                                'update_url' => route('admin-peserta.sertifikat.update', $item),
                            ];
                        @endphp
                        <tr class="transition hover:bg-sky-50/40">
                            <td class="px-6 py-4 text-xs font-mono text-slate-500">{{ $item->nomor_sertifikat }}</td>
                            <td class="px-5 py-4">
                                <p class="text-sm font-extrabold text-slate-900">{{ $item->peserta?->user?->nama ?? '-' }}</p>
                                <p class="mt-0.5 text-xs text-slate-500">{{ $item->peserta?->permintaan?->nama_sekolah ?? '-' }}</p>
                            </td>
                            <td class="px-5 py-4 text-sm font-medium text-slate-700">{{ $item->divisi?->nama_divisi ?? '-' }}</td>
                            <td class="px-5 py-4 text-sm text-slate-700">{{ $item->predikat }}</td>
                            <td class="px-5 py-4 text-sm text-slate-700">{{ $item->tanggal_terbit?->translatedFormat('d M Y') ?? '-' }}</td>
                            <td class="px-5 py-4 text-center">
                                <span @class([
                                    'inline-flex rounded-full px-3 py-1 text-xs font-bold',
                                    'bg-emerald-100 text-emerald-700' => $item->status === 'terbit',
                                    'bg-slate-100 text-slate-500' => $item->status !== 'terbit',
                                ])>
                                    {{ $item->status === 'terbit' ? 'Terbit' : 'Dicabut' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <button
                                        type="button"
                                        @click="openShow(@js($payload))"
                                        class="inline-flex h-9 items-center gap-1.5 rounded-xl border border-sky-200 bg-sky-50 px-3 text-xs font-bold text-sky-700 transition hover:bg-sky-100"
                                    >
                                        <span class="material-symbols-outlined text-[17px]">visibility</span>
                                        Show
                                    </button>
                                    <button
                                        type="button"
                                        @click="openEdit(@js($payload))"
                                        class="inline-flex h-9 items-center gap-1.5 rounded-xl border border-indigo-200 bg-indigo-50 px-3 text-xs font-bold text-indigo-700 transition hover:bg-indigo-100"
                                    >
                                        <span class="material-symbols-outlined text-[17px]">edit</span>
                                        Edit
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-16 text-center text-sm text-slate-500">Belum ada sertifikat yang diterbitkan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($riwayat->hasPages())
            <div class="border-t border-slate-100 px-6 py-4">
                {{ $riwayat->links() }}
            </div>
        @endif
    </section>

    {{-- Modal Terbitkan --}}
    <template x-teleport="body">
        <div x-show="issueOpen" x-cloak x-transition.opacity class="fixed inset-0 z-[9999] flex items-center justify-center overflow-y-auto bg-slate-950/65 p-4" @click.self="issueOpen = false">
            <section class="my-auto flex w-full max-w-lg flex-col overflow-hidden rounded-3xl bg-white shadow-2xl" style="max-height:90vh">
                <header class="flex items-center justify-between border-b border-sky-100 bg-gradient-to-r from-sky-50 to-blue-50 px-5 py-4">
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-sky-600">Sertifikat Baru</p>
                        <h3 class="mt-1 text-lg font-extrabold text-slate-950">Terbitkan Sertifikat</h3>
                    </div>
                    <button type="button" @click="issueOpen = false" class="grid h-9 w-9 place-items-center rounded-xl border border-slate-200 bg-white text-slate-500 hover:bg-rose-50 hover:text-rose-600">
                        <span class="material-symbols-outlined text-[20px]">close</span>
                    </button>
                </header>

                <form method="POST" action="{{ route('admin-peserta.sertifikat.store') }}" class="min-h-0 flex-1 space-y-4 overflow-y-auto p-5">
                    @csrf
                    <label class="block text-sm font-bold text-slate-700">
                        Peserta
                        <select name="peserta_id" required class="mt-1.5 h-11 w-full rounded-xl border border-slate-200 text-sm focus:border-sky-500 focus:ring-sky-100">
                            <option value="">Pilih peserta</option>
                            @foreach ($pesertaBisaDisertifikasi as $peserta)
                                <option value="{{ $peserta->id_peserta }}">{{ $peserta->user?->nama ?? 'Tanpa nama' }} — {{ $peserta->permintaan?->nama_sekolah ?? '-' }}</option>
                            @endforeach
                        </select>
                    </label>

                    <label class="block text-sm font-bold text-slate-700">
                        Divisi
                        <select name="divisi_id" required class="mt-1.5 h-11 w-full rounded-xl border border-slate-200 text-sm focus:border-sky-500 focus:ring-sky-100">
                            <option value="">Pilih divisi</option>
                            @foreach ($divisiList as $divisi)
                                <option value="{{ $divisi->id_divisi }}">{{ $divisi->nama_divisi }}</option>
                            @endforeach
                        </select>
                    </label>

                    <div class="grid gap-3 sm:grid-cols-2">
                        <label class="block text-sm font-bold text-slate-700">
                            Predikat
                            <select name="predikat" required class="mt-1.5 h-11 w-full rounded-xl border border-slate-200 text-sm">
                                @foreach ($predikatOptions as $predikat)
                                    <option value="{{ $predikat }}" @selected($predikat === 'Sangat Baik')>{{ $predikat }}</option>
                                @endforeach
                            </select>
                        </label>
                        <label class="block text-sm font-bold text-slate-700">
                            Tanggal Terbit
                            <input type="date" name="tanggal_terbit" value="{{ now()->format('Y-m-d') }}" required class="mt-1.5 h-11 w-full rounded-xl border border-slate-200 text-sm">
                        </label>
                    </div>

                    <label class="block text-sm font-bold text-slate-700">
                        Judul
                        <input type="text" name="judul" value="Sertifikat Magang" required class="mt-1.5 h-11 w-full rounded-xl border border-slate-200 text-sm">
                    </label>

                    <label class="block text-sm font-bold text-slate-700">
                        Catatan
                        <textarea name="catatan" rows="3" class="mt-1.5 w-full rounded-xl border border-slate-200 text-sm"></textarea>
                    </label>

                    <div class="flex justify-end gap-2 border-t border-slate-100 pt-4">
                        <button type="button" @click="issueOpen = false" class="h-10 rounded-xl border border-slate-200 bg-white px-4 text-sm font-bold text-slate-600 hover:bg-slate-50">Batal</button>
                        <button type="submit" class="h-10 rounded-xl bg-sky-600 px-5 text-sm font-bold text-white hover:bg-sky-700">Terbitkan</button>
                    </div>
                </form>
            </section>
        </div>
    </template>

    {{-- Modal Show --}}
    <template x-teleport="body">
        <div x-show="showOpen" x-cloak x-transition.opacity class="fixed inset-0 z-[9999] flex items-center justify-center overflow-y-auto bg-slate-950/65 p-4" @click.self="showOpen = false">
            <section class="my-auto flex w-full max-w-2xl flex-col overflow-hidden rounded-3xl bg-white shadow-2xl" style="max-height:90vh">
                <header class="flex items-start justify-between gap-4 border-b border-sky-100 bg-gradient-to-r from-sky-50 via-blue-50 to-cyan-50 px-6 py-5">
                    <div>
                        <span class="rounded-full bg-white px-3 py-1 text-[10px] font-bold uppercase tracking-[0.14em] text-sky-700 ring-1 ring-sky-200">Detail Sertifikat</span>
                        <h3 class="mt-3 text-xl font-extrabold text-slate-950" x-text="selected.nama || '-'">-</h3>
                        <p class="mt-1 text-sm text-slate-500">Data sertifikat peserta.</p>
                    </div>
                    <button type="button" @click="showOpen = false" class="grid h-10 w-10 place-items-center rounded-xl border border-slate-200 bg-white text-slate-500 hover:bg-rose-50 hover:text-rose-600">
                        <span class="material-symbols-outlined text-[20px]">close</span>
                    </button>
                </header>

                <div class="min-h-0 flex-1 overflow-y-auto p-6">
                    <div class="grid gap-3 sm:grid-cols-2">
                        <template x-for="row in [
                            ['Nomor Sertifikat', selected.nomor],
                            ['Instansi', selected.instansi],
                            ['Divisi', selected.divisi],
                            ['Predikat', selected.predikat],
                            ['Tanggal Terbit', selected.tanggal_label],
                            ['Status', selected.status],
                            ['Judul', selected.judul],
                            ['Catatan', selected.catatan || '-']
                        ]" :key="row[0]">
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                <p class="text-[10px] font-bold uppercase tracking-[0.12em] text-slate-400" x-text="row[0]"></p>
                                <p class="mt-2 break-words text-sm font-bold text-slate-800" x-text="row[1] || '-'"></p>
                            </div>
                        </template>
                    </div>
                </div>
            </section>
        </div>
    </template>

    {{-- Modal Edit --}}
    <template x-teleport="body">
        <div x-show="editOpen" x-cloak x-transition.opacity class="fixed inset-0 z-[9999] flex items-center justify-center overflow-y-auto bg-slate-950/65 p-4" @click.self="editOpen = false">
            <section class="my-auto flex w-full max-w-lg flex-col overflow-hidden rounded-3xl bg-white shadow-2xl" style="max-height:90vh">
                <header class="flex items-center justify-between border-b border-indigo-100 bg-gradient-to-r from-indigo-50 to-sky-50 px-5 py-4">
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-indigo-600">Edit Data</p>
                        <h3 class="mt-1 text-lg font-extrabold text-slate-950">Edit Sertifikat</h3>
                    </div>
                    <button type="button" @click="editOpen = false" class="grid h-9 w-9 place-items-center rounded-xl border border-slate-200 bg-white text-slate-500 hover:bg-rose-50 hover:text-rose-600">
                        <span class="material-symbols-outlined text-[20px]">close</span>
                    </button>
                </header>

                <form :action="selected.update_url" method="POST" class="min-h-0 flex-1 space-y-4 overflow-y-auto p-5">
                    @csrf
                    @method('PUT')

                    <label class="block text-sm font-bold text-slate-700">
                        Divisi
                        <select name="divisi_id" x-model="selected.divisi_id" required class="mt-1.5 h-11 w-full rounded-xl border border-slate-200 text-sm">
                            @foreach ($divisiList as $divisi)
                                <option value="{{ $divisi->id_divisi }}">{{ $divisi->nama_divisi }}</option>
                            @endforeach
                        </select>
                    </label>

                    <div class="grid gap-3 sm:grid-cols-2">
                        <label class="block text-sm font-bold text-slate-700">
                            Predikat
                            <select name="predikat" x-model="selected.predikat" required class="mt-1.5 h-11 w-full rounded-xl border border-slate-200 text-sm">
                                @foreach ($predikatOptions as $predikat)
                                    <option value="{{ $predikat }}">{{ $predikat }}</option>
                                @endforeach
                            </select>
                        </label>
                        <label class="block text-sm font-bold text-slate-700">
                            Tanggal Terbit
                            <input type="date" name="tanggal_terbit" x-model="selected.tanggal" required class="mt-1.5 h-11 w-full rounded-xl border border-slate-200 text-sm">
                        </label>
                    </div>

                    <label class="block text-sm font-bold text-slate-700">
                        Judul
                        <input type="text" name="judul" x-model="selected.judul" required class="mt-1.5 h-11 w-full rounded-xl border border-slate-200 text-sm">
                    </label>

                    <label class="block text-sm font-bold text-slate-700">
                        Catatan
                        <textarea name="catatan" x-model="selected.catatan" rows="3" class="mt-1.5 w-full rounded-xl border border-slate-200 text-sm"></textarea>
                    </label>

                    <div class="flex justify-end gap-2 border-t border-slate-100 pt-4">
                        <button type="button" @click="editOpen = false" class="h-10 rounded-xl border border-slate-200 bg-white px-4 text-sm font-bold text-slate-600 hover:bg-slate-50">Batal</button>
                        <button type="submit" class="h-10 rounded-xl bg-indigo-600 px-5 text-sm font-bold text-white hover:bg-indigo-700">Simpan Perubahan</button>
                    </div>
                </form>
            </section>
        </div>
    </template>
</div>
@endsection
