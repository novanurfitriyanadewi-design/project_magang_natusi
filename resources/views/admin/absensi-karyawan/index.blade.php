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

    {{-- Form Tambah Absensi Manual --}}
    <section class="overflow-hidden rounded-3xl border border-white/80 bg-white/90 shadow-[0_18px_45px_rgba(15,52,94,0.08)] backdrop-blur">
        <details>
            <summary class="flex cursor-pointer list-none items-center gap-3 border-b border-sky-100 bg-gradient-to-r from-sky-50 to-blue-50 px-5 py-5 sm:px-6">
                <span class="grid h-11 w-11 place-items-center rounded-2xl bg-white text-sky-600 shadow-sm ring-1 ring-sky-100">
                    <span class="material-symbols-outlined text-[23px]">edit_calendar</span>
                </span>
                <div>
                    <h2 class="text-lg font-extrabold tracking-tight text-slate-950">Tambah Absensi Manual</h2>
                    <p class="mt-0.5 text-sm text-slate-500">Klik untuk membuka form input absensi karyawan.</p>
                </div>
            </summary>

            <form action="{{ route('admin.absensi-karyawan.store') }}" method="POST" class="space-y-4 px-5 py-5 sm:px-6">
                @csrf
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div class="space-y-1">
                        <label class="text-xs font-bold uppercase tracking-wide text-slate-500">Nama Karyawan</label>
                        <select name="id_karyawan" required class="w-full rounded-xl border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 shadow-sm focus:border-sky-400 focus:ring-sky-200">
                            <option value="">Pilih karyawan</option>
                            @foreach($karyawanList as $karyawan)
                                <option value="{{ $karyawan->id_karyawan }}">{{ $karyawan->nama_karyawan }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-bold uppercase tracking-wide text-slate-500">Tanggal</label>
                        <input type="date" name="tanggal" required value="{{ $tanggal }}" class="w-full rounded-xl border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 shadow-sm focus:border-sky-400 focus:ring-sky-200">
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                    <div class="space-y-1">
                        <label class="text-xs font-bold uppercase tracking-wide text-slate-500">Jam Masuk</label>
                        <input type="time" name="jam_masuk" class="w-full rounded-xl border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 shadow-sm focus:border-sky-400 focus:ring-sky-200">
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-bold uppercase tracking-wide text-slate-500">Jam Pulang</label>
                        <input type="time" name="jam_pulang" class="w-full rounded-xl border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 shadow-sm focus:border-sky-400 focus:ring-sky-200">
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-bold uppercase tracking-wide text-slate-500">Status</label>
                        <select name="status" required class="w-full rounded-xl border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 shadow-sm focus:border-sky-400 focus:ring-sky-200">
                            <option value="hadir">Hadir</option>
                            <option value="izin">Izin</option>
                            <option value="sakit">Sakit</option>
                            <option value="alpha">Alpha</option>
                        </select>
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-bold uppercase tracking-wide text-slate-500">Keterangan</label>
                    <textarea name="keterangan" rows="2" placeholder="Catatan tambahan (opsional)" class="w-full rounded-xl border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 shadow-sm focus:border-sky-400 focus:ring-sky-200"></textarea>
                </div>

                <div class="flex justify-end pt-1">
                    <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-sky-500 to-blue-600 px-6 py-2.5 text-sm font-extrabold text-white shadow-[0_8px_20px_rgba(14,165,233,0.24)] transition hover:-translate-y-0.5">
                        <span class="material-symbols-outlined text-[18px]">add_circle</span>
                        Simpan Absensi
                    </button>
                </div>
            </form>
        </details>
    </section>

    {{-- Tabel Data Absensi --}}
    <section class="overflow-hidden rounded-3xl border border-white/80 bg-white/90 shadow-[0_18px_45px_rgba(15,52,94,0.08)] backdrop-blur">
        <header class="flex flex-col gap-3 border-b border-slate-100 bg-white px-5 py-5 sm:flex-row sm:items-center sm:justify-between sm:px-6">
            <div>
                <h2 class="text-xl font-extrabold tracking-tight text-slate-950">Data Kehadiran</h2>
                <p class="mt-1 text-sm text-slate-500">Menampilkan data absensi pada tanggal terpilih.</p>
            </div>
            <span class="w-fit rounded-xl bg-slate-100 px-4 py-2 text-xs font-extrabold text-slate-700 shadow-sm ring-1 ring-slate-200">
                {{ number_format($absensi->total()) }} data
            </span>
        </header>

        <div class="border-b border-slate-200 bg-slate-50/70 px-5 py-4 sm:px-6">
            <form action="{{ route('admin.absensi-karyawan.index') }}" method="GET" class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                <div class="relative w-full lg:max-w-xs">
                    <span class="material-symbols-outlined pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-[20px] text-slate-400">search</span>
                    <input type="search" name="search" value="{{ request('search') }}" placeholder="Cari nama karyawan..." class="w-full rounded-xl border-slate-200 bg-white py-2.5 pl-10 pr-4 text-sm text-slate-700 shadow-sm focus:border-sky-400 focus:ring-sky-200">
                </div>

                <div class="flex flex-col gap-2 sm:flex-row">
                    <input type="date" name="tanggal" value="{{ $tanggal }}" class="rounded-xl border-slate-200 bg-white px-3 py-2.5 text-sm font-semibold text-slate-600 shadow-sm focus:border-sky-400 focus:ring-sky-200">
                    <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-extrabold text-white shadow-sm transition hover:bg-slate-800">
                        <span class="material-symbols-outlined text-[19px]">filter_alt</span>
                        Terapkan
                    </button>
                    @if(request('search') || request('tanggal') !== $tanggal)
                        <a href="{{ route('admin.absensi-karyawan.index') }}" class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-600 transition hover:bg-slate-100">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-[900px] w-full border-collapse text-left">
                <thead>
                    <tr class="border-b border-slate-200 bg-sky-50/70">
                        <th class="px-6 py-4 text-[11px] font-extrabold uppercase tracking-[0.12em] text-slate-500">Nama Karyawan</th>
                        <th class="px-5 py-4 text-[11px] font-extrabold uppercase tracking-[0.12em] text-slate-500">Tanggal</th>
                        <th class="px-5 py-4 text-[11px] font-extrabold uppercase tracking-[0.12em] text-slate-500">Jam Masuk</th>
                        <th class="px-5 py-4 text-[11px] font-extrabold uppercase tracking-[0.12em] text-slate-500">Jam Pulang</th>
                        <th class="px-5 py-4 text-[11px] font-extrabold uppercase tracking-[0.12em] text-slate-500">Status</th>
                        <th class="px-6 py-4 text-right text-[11px] font-extrabold uppercase tracking-[0.12em] text-slate-500">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($absensi as $item)
                        @php
                            $statusMap = [
                                'hadir' => ['label' => 'Hadir', 'class' => 'border-emerald-200 bg-emerald-50 text-emerald-700', 'dot' => 'bg-emerald-500'],
                                'izin' => ['label' => 'Izin', 'class' => 'border-amber-200 bg-amber-50 text-amber-700', 'dot' => 'bg-amber-500'],
                                'sakit' => ['label' => 'Sakit', 'class' => 'border-sky-200 bg-sky-50 text-sky-700', 'dot' => 'bg-sky-500'],
                                'alpha' => ['label' => 'Alpha', 'class' => 'border-rose-200 bg-rose-50 text-rose-700', 'dot' => 'bg-rose-500'],
                            ];
                            $badge = $statusMap[$item->status] ?? $statusMap['hadir'];
                        @endphp
                        <tr class="transition hover:bg-sky-50/45">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="grid h-10 w-10 shrink-0 place-items-center rounded-full bg-gradient-to-br from-sky-100 to-blue-100 text-xs font-black text-sky-700 ring-1 ring-sky-200">
                                        {{ mb_strtoupper(mb_substr($item->karyawan->nama_karyawan ?? '-', 0, 1)) }}
                                    </div>
                                    <p class="text-sm font-extrabold text-slate-900">{{ $item->karyawan->nama_karyawan ?? '-' }}</p>
                                </div>
                            </td>
                            <td class="px-5 py-4 text-sm font-semibold text-slate-700">{{ \Illuminate\Support\Carbon::parse($item->tanggal)->translatedFormat('d M Y') }}</td>
                            <td class="px-5 py-4 text-sm text-slate-600">{{ $item->jam_masuk ?? '--:--' }}</td>
                            <td class="px-5 py-4 text-sm text-slate-600">{{ $item->jam_pulang ?? '--:--' }}</td>
                            <td class="px-5 py-4">
                                <span class="inline-flex items-center gap-1.5 rounded-full border px-3 py-1 text-xs font-extrabold {{ $badge['class'] }}">
                                    <span class="h-1.5 w-1.5 rounded-full {{ $badge['dot'] }}"></span>
                                    {{ $badge['label'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <form action="{{ route('admin.absensi-karyawan.destroy', $item) }}" method="POST" onsubmit="return confirm('Hapus data absensi ini?')" class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center gap-1.5 rounded-xl border border-rose-200 bg-rose-50 px-3 py-2 text-xs font-extrabold text-rose-700 transition hover:bg-rose-100">
                                        <span class="material-symbols-outlined text-[16px]">delete</span>
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center">
                                <div class="mx-auto grid h-14 w-14 place-items-center rounded-2xl bg-slate-100 text-slate-400">
                                    <span class="material-symbols-outlined text-[30px]">event_busy</span>
                                </div>
                                <p class="mt-4 font-extrabold text-slate-700">Belum ada data absensi untuk tanggal ini.</p>
                                <p class="mt-1 text-sm text-slate-500">Gunakan form di atas untuk menambahkan data absensi.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($absensi->hasPages())
            <footer class="border-t border-slate-100 bg-white px-6 py-4">
                {{ $absensi->links() }}
            </footer>
        @endif
    </section>
</div>
@endsection
