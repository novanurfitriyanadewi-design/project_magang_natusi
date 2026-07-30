@extends('layouts.admin-karyawan')

@section('title', 'Tambah Absensi Karyawan')

@section('content')
<div class="mx-auto max-w-3xl space-y-6">
    <section class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <a href="{{ route('admin-karyawan.absensi-karyawan.index') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-sky-600 hover:text-sky-800 transition">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M19 12H5m0 0 6 6m-6-6 6-6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Kembali
            </a>
            <h1 class="mt-5 text-2xl font-extrabold tracking-tight text-slate-950 sm:text-3xl">
                Tambah Absensi Karyawan
            </h1>
            <p class="mt-1 text-sm text-slate-500">
                Input data kehadiran, izin, sakit, atau alpha karyawan.
            </p>
        </div>
    </section>

    @if ($errors->any())
        <div class="flex items-start gap-3 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-700 shadow-sm">
            <span class="material-symbols-outlined text-[21px]">error</span>
            <ul class="list-inside list-disc">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm" style="border-left: 4px solid #006191;">
        <div class="mb-6 flex items-center gap-3">
            <div class="rounded-lg bg-sky-100 p-2 text-sky-700">
                <span class="material-symbols-outlined">edit_calendar</span>
            </div>
            <div>
                <h3 class="text-lg font-bold text-slate-900">Form Absensi Karyawan</h3>
                <p class="text-xs text-slate-500">Lengkapi data absensi di bawah ini</p>
            </div>
        </div>

        <form action="{{ route('admin-karyawan.absensi-karyawan.store') }}" method="POST" class="space-y-4">
            @csrf

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div class="space-y-1">
                    <label class="text-sm font-semibold text-slate-600">Nama Karyawan <span class="text-rose-500">*</span></label>
                    <select name="id_karyawan" required class="w-full rounded-lg border border-slate-200 p-2.5 text-sm text-slate-800 outline-none transition-all focus:border-sky-600 focus:ring-1 focus:ring-sky-600">
                        <option value="">Pilih karyawan</option>
                        @foreach($karyawanList as $karyawan)
                            <option value="{{ $karyawan->id_karyawan }}" @selected(old('id_karyawan') == $karyawan->id_karyawan)>
                                {{ $karyawan->nama_karyawan }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="space-y-1">
                    <label class="text-sm font-semibold text-slate-600">Tanggal <span class="text-rose-500">*</span></label>
                    <input type="date" name="tanggal" required value="{{ old('tanggal', now()->toDateString()) }}" class="w-full rounded-lg border border-slate-200 p-2.5 text-sm text-slate-800 outline-none transition-all focus:border-sky-600 focus:ring-1 focus:ring-sky-600">
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                <div class="space-y-1">
                    <label class="text-sm font-semibold text-slate-600">Jam Masuk</label>
                    <input type="time" name="jam_masuk" value="{{ old('jam_masuk') }}" class="w-full rounded-lg border border-slate-200 p-2.5 text-sm text-slate-800 outline-none transition-all focus:border-sky-600 focus:ring-1 focus:ring-sky-600">
                </div>
                <div class="space-y-1">
                    <label class="text-sm font-semibold text-slate-600">Jam Pulang</label>
                    <input type="time" name="jam_pulang" value="{{ old('jam_pulang') }}" class="w-full rounded-lg border border-slate-200 p-2.5 text-sm text-slate-800 outline-none transition-all focus:border-sky-600 focus:ring-1 focus:ring-sky-600">
                </div>
                <div class="space-y-1">
                    <label class="text-sm font-semibold text-slate-600">Status <span class="text-rose-500">*</span></label>
                    <select name="status" required class="w-full rounded-lg border border-slate-200 p-2.5 text-sm text-slate-800 outline-none transition-all focus:border-sky-600 focus:ring-1 focus:ring-sky-600">
                        <option value="hadir" @selected(old('status') == 'hadir')>Hadir</option>
                        <option value="terlambat" @selected(old('status') == 'terlambat')>Terlambat</option>
                        <option value="izin" @selected(old('status') == 'izin')>Izin</option>
                        <option value="sakit" @selected(old('status') == 'sakit')>Sakit</option>
                        <option value="alpha" @selected(old('status') == 'alpha')>Alpha</option>
                    </select>
                </div>
            </div>

            <div class="space-y-1">
                <label class="text-sm font-semibold text-slate-600">Keterangan</label>
                <textarea name="keterangan" rows="3" placeholder="Catatan tambahan (opsional)" class="w-full rounded-lg border border-slate-200 p-2.5 text-sm text-slate-800 outline-none transition-all focus:border-sky-600 focus:ring-1 focus:ring-sky-600">{{ old('keterangan') }}</textarea>
            </div>

            <div class="flex items-center justify-end gap-3 border-t border-slate-100 pt-4">
                <a href="{{ route('admin-karyawan.absensi-karyawan.index') }}" class="rounded-lg border border-slate-200 bg-white px-5 py-2.5 text-sm font-bold text-slate-600 transition hover:bg-slate-100">
                    Batal
                </a>
                <button type="submit" class="rounded-lg bg-gradient-to-r from-sky-600 to-blue-600 px-5 py-2.5 text-sm font-bold text-white shadow-md transition hover:-translate-y-0.5 active:scale-95">
                    Simpan Absensi
                </button>
            </div>
        </form>
    </section>
</div>
@endsection

