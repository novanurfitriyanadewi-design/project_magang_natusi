@extends('layouts.portal')

@section('title', 'Absensi Karyawan')

@section('content')
@php
    $tanggal = $tanggal ?? now()->toDateString();
    $stats = $stats ?? ['hadir' => 0, 'terlambat' => 0, 'izin_sakit' => 0, 'alpha' => 0];
    $karyawanList = $karyawanList ?? collect();
@endphp

<div class="space-y-6">
    <section class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
        <div>
            <h1 class="mt-5 text-2xl font-extrabold tracking-tight text-slate-950 sm:text-3xl">
                Absensi Karyawan
            </h1>
            <p class="mt-1 max-w-3xl text-sm leading-6 text-slate-500">
                Pantau dan kelola data kehadiran karyawan setiap hari.
            </p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.absensi-karyawan.create') }}" class="flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-bold text-white shadow-md transition hover:bg-emerald-700 active:scale-95"><span class="material-symbols-outlined text-[20px]">add</span>Tambah</a>
            <a href="{{ route('admin.absensi-karyawan.export', ['tanggal' => $tanggal, 'search' => request('search')]) }}" class="flex items-center gap-2 rounded-lg bg-sky-700 px-4 py-2.5 text-sm font-bold text-white shadow-md transition hover:bg-sky-800 active:scale-95"><span class="material-symbols-outlined text-[20px]">download</span>Export</a>
        </div>
    </section>

    @if(session('success'))
        <div class="flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700 shadow-sm">
            <span class="material-symbols-outlined text-[21px]">check_circle</span>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="flex items-start gap-3 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-700 shadow-sm">
            <span class="material-symbols-outlined text-[21px]">error</span>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    {{-- Kartu Statistik --}}
    <section class="grid gap-4 md:grid-cols-4">
        <article class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-sky-600 to-blue-500 p-5 text-white shadow-[0_16px_36px_rgba(2,132,199,0.18)] transition duration-200 hover:-translate-y-0.5">
            <div class="absolute -bottom-12 -right-8 h-36 w-36 rounded-full border-[22px] border-white/10"></div>
            <div class="relative flex items-start justify-between gap-4">
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-[0.15em] text-sky-100">Hadir</p>
                    <p class="mt-3 text-4xl font-extrabold">{{ number_format($stats['hadir']) }}</p>
                    <p class="mt-1 text-sm text-sky-100">Karyawan hadir hari ini</p>
                </div>
                <span class="grid h-12 w-12 place-items-center rounded-2xl bg-white/15 ring-1 ring-white/20 transition group-hover:scale-105">
                    <span class="material-symbols-outlined text-[26px]">task_alt</span>
                </span>
            </div>
        </article>

        <article class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-rose-600 to-red-500 p-5 text-white shadow-[0_16px_36px_rgba(225,29,72,0.18)] transition duration-200 hover:-translate-y-0.5">
            <div class="absolute -bottom-12 -right-8 h-36 w-36 rounded-full border-[22px] border-white/10"></div>
            <div class="relative flex items-start justify-between gap-4">
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-[0.15em] text-rose-100">Terlambat</p>
                    <p class="mt-3 text-4xl font-extrabold">{{ number_format($stats['terlambat']) }}</p>
                    <p class="mt-1 text-sm text-rose-100">Masuk setelah jam 08:00</p>
                </div>
                <span class="grid h-12 w-12 place-items-center rounded-2xl bg-white/15 ring-1 ring-white/20 transition group-hover:scale-105">
                    <span class="material-symbols-outlined text-[26px]">schedule</span>
                </span>
            </div>
        </article>

        <article class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-amber-500 to-orange-500 p-5 text-white shadow-[0_16px_36px_rgba(245,158,11,0.20)] transition duration-200 hover:-translate-y-0.5">
            <div class="absolute -bottom-20 left-8 h-40 w-40 rounded-full bg-white/10 blur-2xl"></div>
            <div class="relative flex items-start justify-between gap-4">
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-[0.15em] text-amber-50">Izin / Sakit</p>
                    <p class="mt-3 text-4xl font-extrabold">{{ number_format($stats['izin_sakit']) }}</p>
                    <p class="mt-1 text-sm text-amber-50">Pengajuan izin & sakit</p>
                </div>
                <span class="grid h-12 w-12 place-items-center rounded-2xl bg-white/15 ring-1 ring-white/20 transition group-hover:scale-105">
                    <span class="material-symbols-outlined text-[26px]">medical_services</span>
                </span>
            </div>
        </article>

        <article class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-slate-700 to-slate-900 p-5 text-white shadow-[0_16px_36px_rgba(15,23,42,0.20)] transition duration-200 hover:-translate-y-0.5">
            <div class="absolute -bottom-12 -right-8 h-36 w-36 rounded-full border-[22px] border-white/10"></div>
            <div class="relative flex items-start justify-between gap-4">
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-[0.15em] text-slate-300">Alpha</p>
                    <p class="mt-3 text-4xl font-extrabold">{{ number_format($stats['alpha']) }}</p>
                    <p class="mt-1 text-sm text-slate-300">Tidak ada keterangan</p>
                </div>
                <span class="grid h-12 w-12 place-items-center rounded-2xl bg-white/15 ring-1 ring-white/20 transition group-hover:scale-105">
                    <span class="material-symbols-outlined text-[26px]">person_off</span>
                </span>
            </div>
        </article>
    </section>

    {{-- Form Tambah Absensi Manual (gaya clean seperti Stitch) --}}
    <section class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm" style="border-left: 4px solid #006191;">
        <div class="mb-6 flex items-center gap-3">
            <div class="rounded-lg bg-sky-100 p-2 text-sky-700">
                <span class="material-symbols-outlined">edit_calendar</span>
            </div>
            <div>
                <h3 class="text-lg font-bold text-slate-900">Tambah Absensi Manual</h3>
                <p class="text-xs text-slate-500">Input data kehadiran, izin, atau sakit karyawan</p>
            </div>
        </div>

        <form action="{{ route('admin.absensi-karyawan.store') }}" method="POST" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div class="space-y-1">
                    <label class="text-sm font-semibold text-slate-600">Nama Karyawan</label>
                    <select name="id_karyawan" required class="w-full rounded-lg border border-slate-200 p-2.5 text-sm text-slate-800 outline-none transition-all focus:border-sky-600 focus:ring-1 focus:ring-sky-600">
                        <option value="">Pilih karyawan</option>
                        @foreach($karyawanList as $karyawan)
                            <option value="{{ $karyawan->id_karyawan }}">{{ $karyawan->nama_karyawan }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="space-y-1">
                    <label class="text-sm font-semibold text-slate-600">Tanggal</label>
                    <input type="date" name="tanggal" required value="{{ $tanggal }}" class="w-full rounded-lg border border-slate-200 p-2.5 text-sm text-slate-800 outline-none transition-all focus:border-sky-600 focus:ring-1 focus:ring-sky-600">
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                <div class="space-y-1">
                    <label class="text-sm font-semibold text-slate-600">Jam Masuk</label>
                    <input type="time" name="jam_masuk" class="w-full rounded-lg border border-slate-200 p-2.5 text-sm text-slate-800 outline-none transition-all focus:border-sky-600 focus:ring-1 focus:ring-sky-600">
                </div>
                <div class="space-y-1">
                    <label class="text-sm font-semibold text-slate-600">Jam Pulang</label>
                    <input type="time" name="jam_pulang" class="w-full rounded-lg border border-slate-200 p-2.5 text-sm text-slate-800 outline-none transition-all focus:border-sky-600 focus:ring-1 focus:ring-sky-600">
                </div>
                <div class="space-y-1">
                    <label class="text-sm font-semibold text-slate-600">Status</label>
                    <select name="status" required class="w-full rounded-lg border border-slate-200 p-2.5 text-sm text-slate-800 outline-none transition-all focus:border-sky-600 focus:ring-1 focus:ring-sky-600">
                        <option value="hadir">Hadir</option>
                        <option value="izin">Izin</option>
                        <option value="sakit">Sakit</option>
                        <option value="alpha">Alpha</option>
                    </select>
                </div>
            </div>

            <div class="space-y-1">
                <label class="text-sm font-semibold text-slate-600">Keterangan</label>
                <textarea name="keterangan" rows="2" placeholder="Catatan tambahan (opsional)" class="w-full rounded-lg border border-slate-200 p-2.5 text-sm text-slate-800 outline-none transition-all focus:border-sky-600 focus:ring-1 focus:ring-sky-600"></textarea>
            </div>

            <div class="flex justify-end pt-2">
                <button type="submit" class="rounded-lg bg-sky-700 px-8 py-3 text-sm font-bold text-white shadow-md transition-all hover:bg-sky-800 active:scale-95">
                    Simpan Absensi
                </button>
            </div>
        </form>
    </section>

    {{-- Tabel Data Absensi --}}
    <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="flex items-center justify-between border-b border-slate-200 p-5">
            <h3 class="text-[14px] font-semibold uppercase tracking-wider text-slate-600">Data Kehadiran</h3>
            <span class="rounded-lg bg-slate-100 px-3 py-1 text-xs font-bold text-slate-700">{{ number_format($absensi->total()) }} data</span>
        </div>

        <div class="border-b border-slate-200 bg-slate-50 px-5 py-4">
            <form action="{{ route('admin.absensi-karyawan.index') }}" method="GET" class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                <div class="relative w-full lg:max-w-xs">
                    <span class="material-symbols-outlined pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-[20px] text-slate-400">search</span>
                    <input type="search" name="search" value="{{ request('search') }}" placeholder="Cari nama karyawan..." class="w-full rounded-lg border border-slate-200 bg-white py-2.5 pl-10 pr-4 text-sm text-slate-700 outline-none focus:border-sky-600 focus:ring-1 focus:ring-sky-600">
                </div>
                <div class="flex flex-col gap-2 sm:flex-row">
                    <input type="date" name="tanggal" value="{{ $tanggal }}" class="rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm font-semibold text-slate-600 outline-none focus:border-sky-600 focus:ring-1 focus:ring-sky-600">
                    <button type="submit" class="flex items-center justify-center gap-2 rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-slate-800">
                        <span class="material-symbols-outlined text-[19px]">filter_alt</span>
                        Terapkan
                    </button>
                    @if(request('search') || request('tanggal') !== $tanggal)
                        <a href="{{ route('admin.absensi-karyawan.index') }}" class="flex items-center justify-center rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-600 transition hover:bg-slate-100">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-slate-50 text-xs text-slate-500">
                    <tr>
                        <th class="px-6 py-3 font-semibold">Nama Karyawan</th>
                        <th class="px-6 py-3 font-semibold">Tanggal</th>
                        <th class="px-6 py-3 font-semibold">Jam Masuk</th>
                        <th class="px-6 py-3 font-semibold">Jam Pulang</th>
                        <th class="px-6 py-3 text-center font-semibold">Status</th>
                        <th class="px-6 py-3 text-right font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($absensi as $item)
                        @php
                            $statusMap = [
                                'hadir' => ['label' => 'Hadir', 'bg' => '#dcfce7', 'text' => '#166534'],
                                'terlambat' => ['label' => 'Terlambat', 'bg' => '#fee2e2', 'text' => '#991b1b'],
                                'izin' => ['label' => 'Izin', 'bg' => '#fef9c3', 'text' => '#854d0e'],
                                'sakit' => ['label' => 'Sakit', 'bg' => '#e0f2fe', 'text' => '#075985'],
                                'alpha' => ['label' => 'Alpha', 'bg' => '#f1f5f9', 'text' => '#475569'],
                            ];
                            $badge = $statusMap[$item->status] ?? $statusMap['alpha'];
                        @endphp
                        <tr class="transition-colors hover:bg-slate-50/50">
                            <td class="px-6 py-4">
                                <p class="text-sm font-semibold text-slate-800">{{ $item->karyawan->nama_karyawan ?? '-' }}</p>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-700">{{ \Illuminate\Support\Carbon::parse($item->tanggal)->translatedFormat('d M Y') }}</td>
                            <td class="px-6 py-4 text-sm text-slate-600">{{ $item->jam_masuk ?? '--:--' }}</td>
                            <td class="px-6 py-4 text-sm text-slate-600">{{ $item->jam_pulang ?? '--:--' }}</td>
                            <td class="flex justify-center px-6 py-4">
                                <span class="rounded-full px-3 py-1 text-xs font-bold" style="background-color: {{ $badge['bg'] }}; color: {{ $badge['text'] }};">
                                    {{ $badge['label'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('admin.absensi-karyawan.show', $item) }}" class="rounded-lg border border-sky-200 bg-sky-50 px-2.5 py-1.5 text-xs font-bold text-sky-700 transition hover:bg-sky-100">
                                        Detail
                                    </a>
                                    <a href="{{ route('admin.absensi-karyawan.edit', $item) }}" class="rounded-lg border border-amber-200 bg-amber-50 px-2.5 py-1.5 text-xs font-bold text-amber-700 transition hover:bg-amber-100">
                                        Edit
                                    </a>
                                    <form action="{{ route('admin.absensi-karyawan.destroy', $item) }}" method="POST" onsubmit="return confirm('Hapus data absensi ini?')" class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded-lg border border-rose-200 bg-rose-50 px-2.5 py-1.5 text-xs font-bold text-rose-700 transition hover:bg-rose-100">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center">
                                <p class="font-bold text-slate-600">Belum ada data absensi untuk tanggal ini.</p>
                                <p class="mt-1 text-sm text-slate-400">Gunakan form di atas untuk menambahkan data.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($absensi->hasPages())
            <div class="border-t border-slate-100 px-6 py-4">
                {{ $absensi->links() }}
            </div>
        @endif
    </section>
</div>
@endsection