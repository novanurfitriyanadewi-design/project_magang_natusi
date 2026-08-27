@extends('layouts.portal')

@section('title', 'Laporan Gaji Karyawan')

@section('content')

@php
    $periodeLabel = $tahun === 'all'
        ? 'Semua Periode'
        : ($bulan !== ''
            ? \Carbon\Carbon::createFromDate(
                (int) $tahun,
                (int) $bulan,
                1
            )->translatedFormat('F Y')
            : 'Tahun ' . $tahun);

    $persenTerbayar = $stats['total_slip'] > 0
        ? round(($stats['slip_terbayar'] / $stats['total_slip']) * 100)
        : 0;
@endphp

<div class="min-h-full bg-slate-100 p-4 sm:p-6">

    <div class="space-y-5">

        {{-- =========================================================
             HERO HEADER
        ========================================================== --}}
        <header
            class="relative overflow-hidden rounded-2xl bg-[#006191] p-6 text-white shadow-md sm:p-8">

            <div class="absolute -right-16 -top-16 h-52 w-52 rounded-full bg-[#004B70]"></div>

            <div class="absolute -bottom-16 right-32 h-40 w-40 rounded-full bg-[#087EB8]"></div>

            <div class="relative flex flex-col justify-between gap-5 md:flex-row md:items-center">

                <div>

                    <span
                        class="inline-flex items-center gap-2 rounded-full border border-[#087EB8] bg-[#004B70] px-3 py-1 text-[10px] font-bold uppercase tracking-wider text-white">

                        <svg
                            class="h-3.5 w-3.5"
                            viewBox="0 0 24 24"
                            fill="none">

                            <path
                                d="M9 17v-6m4 6V7m4 10v-3M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                            />

                        </svg>

                        Laporan

                    </span>

                    <h1 class="mt-3 text-2xl font-extrabold tracking-tight text-white sm:text-3xl">
                        Laporan Gaji Karyawan
                    </h1>

                    <p class="mt-1 text-sm font-medium text-sky-100">
                        Rekapitulasi pembayaran gaji berdasarkan periode bulan dan tahun.
                    </p>

                </div>


                <div
                    class="shrink-0 rounded-xl border border-[#087EB8] bg-[#004B70] px-5 py-3">

                    <p class="text-[10px] font-bold uppercase tracking-wider text-sky-200">
                        Periode Aktif
                    </p>

                    <p class="mt-1 text-lg font-black leading-tight text-white">
                        {{ $periodeLabel }}
                    </p>

                </div>

            </div>

        </header>


        {{-- =========================================================
             FILTER PERIODE
             CARD SOLID BIRU MUDA - TANPA PUTIH
        ========================================================== --}}
        <form
            method="GET"
            action="{{ route('admin-karyawan.laporan.gaji') }}"
            class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">

            <div
                class="mb-5 flex items-center gap-3 border-b border-slate-200 pb-4">

                <span
                    class="grid h-9 w-9 place-items-center rounded-lg border border-slate-200 bg-white text-[#006191]">

                    <svg
                        class="h-4 w-4"
                        viewBox="0 0 24 24"
                        fill="none">

                        <path
                            d="M3 4h18M6.5 4v13m11-13v8m-15 4l5 3 5-6 6 7"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        />

                    </svg>

                </span>

                <div>

                    <h2 class="text-base font-extrabold text-slate-800">
                        Filter Periode
                    </h2>

                    <p class="mt-1 text-xs text-slate-600">
                        Cari laporan berdasarkan tahun, bulan, dan status
                    </p>

                </div>

            </div>


            <div class="grid grid-cols-1 items-end gap-4 sm:grid-cols-2 lg:grid-cols-4">

                {{-- TAHUN --}}
                <div>

                    <label
                        for="f-tahun"
                        class="mb-1.5 block text-[11px] font-bold uppercase tracking-wider text-slate-600">

                        Tahun

                    </label>

                    <select
                        id="f-tahun"
                        name="tahun"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm font-semibold text-slate-800 outline-none transition focus:border-[#006191] focus:bg-white focus:ring-2 focus:ring-[#006191]/20">

                        <option
                            value="all"
                            @selected($tahun === 'all')>

                            Semua Tahun

                        </option>

                        @foreach ($tahunList as $t)

                            <option
                                value="{{ $t }}"
                                @selected((string) $t === (string) $tahun)>

                                {{ $t }}

                            </option>

                        @endforeach

                    </select>

                </div>


                {{-- BULAN --}}
                <div>

                    <label
                        for="f-bulan"
                        class="mb-1.5 block text-[11px] font-bold uppercase tracking-wider text-slate-600">

                        Bulan

                    </label>

                    <select
                        id="f-bulan"
                        name="bulan"
                        @disabled($tahun === 'all')
                        class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm font-semibold outline-none transition focus:border-[#006191] focus:ring-2 focus:ring-[#006191]/20
                        {{ $tahun === 'all'
                            ? 'cursor-not-allowed bg-slate-200 text-slate-400'
                            : 'bg-slate-50 text-slate-800' }}">

                        <option value="">
                            Semua Bulan
                        </option>

                        @foreach ([
                            '01' => 'Januari',
                            '02' => 'Februari',
                            '03' => 'Maret',
                            '04' => 'April',
                            '05' => 'Mei',
                            '06' => 'Juni',
                            '07' => 'Juli',
                            '08' => 'Agustus',
                            '09' => 'September',
                            '10' => 'Oktober',
                            '11' => 'November',
                            '12' => 'Desember'
                        ] as $no => $nama)

                            <option
                                value="{{ $no }}"
                                @selected($bulan === $no)>

                                {{ $nama }}

                            </option>

                        @endforeach

                    </select>

                </div>


                {{-- STATUS --}}
                <div>

                    <label
                        for="f-status"
                        class="mb-1.5 block text-[11px] font-bold uppercase tracking-wider text-slate-600">

                        Status

                    </label>

                    <select
                        id="f-status"
                        name="status"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm font-semibold text-slate-800 outline-none transition focus:border-[#006191] focus:bg-white focus:ring-2 focus:ring-[#006191]/20">

                        <option value="">
                            Semua Status
                        </option>

                        <option
                            value="terbayar"
                            @selected($status === 'terbayar')>

                            Terbayar

                        </option>

                        <option
                            value="belum_terbayar"
                            @selected($status === 'belum_terbayar')>

                            Belum Terbayar

                        </option>

                    </select>

                </div>


                {{-- BUTTON --}}
                <div class="flex gap-2">

                    <button
                        type="submit"
                        class="inline-flex flex-1 items-center justify-center gap-2 rounded-xl bg-[#006191] px-5 py-2.5 text-sm font-extrabold text-white shadow-sm transition hover:bg-[#004B70]">

                        <svg
                            class="h-4 w-4"
                            viewBox="0 0 24 24"
                            fill="none">

                            <circle
                                cx="11"
                                cy="11"
                                r="7"
                                stroke="currentColor"
                                stroke-width="2"
                            />

                            <path
                                d="M20 20l-3.5-3.5"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                            />

                        </svg>

                        Cari

                    </button>


                    <a
                        href="{{ route('admin-karyawan.laporan.gaji') }}"
                        title="Reset filter"
                        class="grid place-items-center rounded-xl border border-slate-200 bg-slate-50 px-4 text-slate-600 transition hover:bg-slate-100">

                        <svg
                            class="h-4 w-4"
                            viewBox="0 0 24 24"
                            fill="none">

                            <path
                                d="M4 4v5h5M20 20v-5h-5M19.5 9A8 8 0 006 6L4 9m16 6l-2 3a8 8 0 01-13.5-3"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />

                        </svg>

                    </a>

                </div>

            </div>

        </form>


        {{-- =========================================================
             CARD STATISTIK
             SEMUA CARD SOLID - TANPA GRADASI PUTIH
        ========================================================== --}}
        <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">


            {{-- =====================================================
                 CARD 1 - TOTAL GAJI TERBAYAR
            ====================================================== --}}
            <div
                class="relative overflow-hidden rounded-2xl border border-emerald-800 bg-emerald-600 p-5 text-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">

                {{-- Dekorasi hijau, BUKAN PUTIH --}}
                <div
                    class="absolute -bottom-8 -right-6 h-28 w-28 rounded-full bg-emerald-700">
                </div>

                <div
                    class="absolute -right-5 -top-5 h-16 w-16 rounded-full bg-emerald-400">
                </div>


                <div class="relative flex items-start justify-between gap-3">

                    <div>

                        <span
                            class="inline-block rounded-full bg-emerald-700 px-2.5 py-1 text-[10px] font-extrabold uppercase tracking-wider text-white">

                            Total Gaji Terbayar

                        </span>

                        <p
                            class="mt-2.5 text-xl font-black tracking-tight sm:text-2xl">

                            Rp {{ number_format($stats['total_gaji'], 0, ',', '.') }}

                        </p>

                        <p class="mt-1 text-[11px] font-medium text-emerald-100">

                            {{ $periodeLabel }}

                        </p>

                    </div>


                    <div
                        class="grid h-11 w-11 shrink-0 place-items-center rounded-xl border border-emerald-500 bg-emerald-700">

                        <svg
                            class="h-5 w-5"
                            viewBox="0 0 24 24"
                            fill="none">

                            <path
                                d="M12 6V4m0 16v-2m6-6h2M4 12h2m10.24-4.24l1.42-1.42M6.34 17.66l1.42-1.42m0-8.48L6.34 6.34m11.32 11.32l-1.42-1.42M12 9a3 3 0 100 6 3 3 0 000-6z"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                            />

                        </svg>

                    </div>

                </div>

            </div>


            {{-- =====================================================
                 CARD 2 - BELUM DIBAYAR
                 SOLID MERAH - TIDAK ADA PUTIH
            ====================================================== --}}
            <div
                class="relative overflow-hidden rounded-2xl border border-rose-800 bg-rose-500 p-5 text-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">

                {{-- Dekorasi merah tua --}}
                <div
                    class="absolute -bottom-8 -right-6 h-28 w-28 rounded-full bg-rose-600">
                </div>

                <div
                    class="absolute -right-5 -top-5 h-16 w-16 rounded-full bg-rose-400">
                </div>


                <div class="relative flex items-start justify-between gap-3">

                    <div>

                        <span
                            class="inline-block rounded-full bg-rose-600 px-2.5 py-1 text-[10px] font-extrabold uppercase tracking-wider text-white">

                            Belum Dibayar

                        </span>

                        <p
                            class="mt-2.5 text-xl font-black tracking-tight sm:text-2xl">

                            Rp {{ number_format($stats['total_belum'], 0, ',', '.') }}

                        </p>

                        <p class="mt-1 text-[11px] font-medium text-rose-100">

                            Menunggu pembayaran

                        </p>

                    </div>


                    <div
                        class="grid h-11 w-11 shrink-0 place-items-center rounded-xl border border-rose-400 bg-rose-600">

                        <svg
                            class="h-5 w-5"
                            viewBox="0 0 24 24"
                            fill="none">

                            <path
                                d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />

                        </svg>

                    </div>

                </div>

            </div>


            {{-- =====================================================
                 CARD 3 - JUMLAH SLIP GAJI
            ====================================================== --}}
            <div
                class="relative overflow-hidden rounded-2xl border border-blue-800 bg-blue-600 p-5 text-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">

                {{-- Dekorasi biru --}}
                <div
                    class="absolute -bottom-8 -right-6 h-28 w-28 rounded-full bg-blue-700">
                </div>

                <div
                    class="absolute -right-5 -top-5 h-16 w-16 rounded-full bg-blue-400">
                </div>


                <div class="relative flex items-start justify-between gap-3">

                    <div>

                        <span
                            class="inline-block rounded-full bg-blue-700 px-2.5 py-1 text-[10px] font-extrabold uppercase tracking-wider text-white">

                            Jumlah Slip Gaji

                        </span>

                        <p
                            class="mt-2.5 text-xl font-black tracking-tight sm:text-2xl">

                            {{ $stats['total_slip'] }}

                            <span class="text-sm font-bold text-blue-100">
                                slip
                            </span>

                        </p>

                        <p class="mt-1 text-[11px] font-medium text-blue-100">

                            Total data periode ini

                        </p>

                    </div>


                    <div
                        class="grid h-11 w-11 shrink-0 place-items-center rounded-xl border border-blue-500 bg-blue-700">

                        <svg
                            class="h-5 w-5"
                            viewBox="0 0 24 24"
                            fill="none">

                            <path
                                d="M7 3h8a1 1 0 01.7.3l4 4a1 1 0 01.3.7v13a1 1 0 01-1 1H7a1 1 0 01-1-1V4a1 1 0 011-1z"
                                stroke="currentColor"
                                stroke-width="2"
                            />

                            <path
                                d="M14 3v5h5M9 13h6m-6 4h6"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                            />

                        </svg>

                    </div>

                </div>

            </div>


            {{-- =====================================================
                 CARD 4 - SLIP TERBAYAR
            ====================================================== --}}
            <div
                class="relative overflow-hidden rounded-2xl p-5 text-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-md"
                style="background-color:#7C3AED; border:1px solid #5B21B6;">

                {{-- Dekorasi ungu --}}
                <div
                    class="absolute -bottom-8 -right-6 h-28 w-28 rounded-full"
                    style="background-color:#6D28D9;">
                </div>

                <div
                    class="absolute -right-5 -top-5 h-16 w-16 rounded-full"
                    style="background-color:#A78BFA;">
                </div>


                <div class="relative flex items-start justify-between gap-3">

                    <div>

                        <span
                            class="inline-block rounded-full px-2.5 py-1 text-[10px] font-extrabold uppercase tracking-wider text-white"
                            style="background-color:#6D28D9;">

                            Slip Terbayar

                        </span>


                        <p
                            class="mt-2.5 text-xl font-black tracking-tight sm:text-2xl">

                            {{ $stats['slip_terbayar'] }}

                            <span class="text-sm font-bold" style="color:#EDE9FE;">

                                dari {{ $stats['total_slip'] }}

                            </span>

                        </p>


                        <div
                            class="mt-2 h-1.5 w-full max-w-[150px] overflow-hidden rounded-full"
                            style="background-color:#6D28D9;">

                            <div
                                class="h-full rounded-full"
                                style="width: {{ $persenTerbayar }}%; background-color:#EDE9FE;">
                            </div>

                        </div>


                        <p
                            class="mt-1 text-[11px] font-medium" style="color:#EDE9FE;">

                            {{ $persenTerbayar }}% terbayar

                        </p>

                    </div>


                    <div
                        class="grid h-11 w-11 shrink-0 place-items-center rounded-xl"
                        style="background-color:#6D28D9; border:1px solid #8B5CF6;">

                        <svg
                            class="h-5 w-5"
                            viewBox="0 0 24 24"
                            fill="none">

                            <path
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />

                        </svg>

                    </div>

                </div>

            </div>

        </section>


        {{-- =========================================================
             DETAIL SLIP GAJI
             CARD SOLID BIRU MUDA - TANPA PUTIH
        ========================================================== --}}
        <section
            class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

            {{-- HEADER TABEL --}}
            <div
                class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 bg-slate-100 px-5 py-4 sm:px-6">

                <div class="flex items-center gap-3">

                    <span
                        class="grid h-9 w-9 place-items-center rounded-lg border border-slate-200 bg-slate-100 text-[#006191]">

                        <svg
                            class="h-4 w-4"
                            viewBox="0 0 24 24"
                            fill="none">

                            <path
                                d="M7 3h8a1 1 0 01.7.3l4 4a1 1 0 01.3.7v13a1 1 0 01-1 1H7a1 1 0 01-1-1V4a1 1 0 011-1z"
                                stroke="currentColor"
                                stroke-width="2"
                            />

                            <path
                                d="M14 3v5h5M9 13h6m-6 4h6"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                            />

                        </svg>

                    </span>


                    <div>

                        <h3 class="font-extrabold leading-tight text-slate-800">
                            Detail Slip Gaji
                        </h3>

                        <p class="text-xs text-slate-600">
                            Periode: {{ $periodeLabel }}
                        </p>

                    </div>

                </div>


                <span
                    class="rounded-full border border-slate-200 bg-slate-100 px-3.5 py-1 text-xs font-bold text-[#006191]">

                    {{ $data->count() }} data

                </span>

            </div>


            {{-- TABLE --}}
            <div class="overflow-x-auto">

                <table class="w-full min-w-[950px] text-left">

                    {{-- HEADER KOLOM --}}
                    <thead>

                        <tr
                            class="border-b border-slate-200 text-[11px] uppercase tracking-wider text-slate-600">

                            <th class="bg-slate-100 px-6 py-3.5 font-bold">
                                Nama Karyawan
                            </th>

                            <th class="bg-slate-200 px-6 py-3.5 font-bold">
                                Periode
                            </th>

                            <th class="bg-emerald-200 px-6 py-3.5 font-bold">
                                Nominal
                            </th>

                            <th class="bg-blue-200 px-6 py-3.5 font-bold">
                                Status
                            </th>

                            <th class="bg-violet-200 px-6 py-3.5 font-bold">
                                Tanggal Bayar
                            </th>

                            <th class="bg-amber-200 px-6 py-3.5 font-bold">
                                Keterangan
                            </th>

                            <th class="bg-slate-100 px-6 py-3.5 font-bold">
                                Bukti
                            </th>

                        </tr>

                    </thead>


                    {{-- BODY --}}
                    <tbody class="divide-y divide-slate-200">

                        @forelse($data as $item)

                            @php
                                $terbayar = $item->status === 'terbayar';

                                $namaKaryawan = $item->karyawan->nama_karyawan ?? '-';

                                $inisial = mb_strtoupper(
                                    mb_substr(
                                        collect(
                                            preg_split(
                                                '/\s+/',
                                                trim($namaKaryawan)
                                            )
                                        )->first() ?? '-',
                                        0,
                                        1
                                    )
                                );
                            @endphp


                            <tr
                                class="transition-colors hover:bg-slate-50">


                                {{-- NAMA --}}
                                <td class="bg-white px-6 py-3.5">

                                    <div class="flex items-center gap-3">

                                        <span
                                            class="grid h-9 w-9 shrink-0 place-items-center rounded-full border border-slate-200 bg-slate-100 text-xs font-extrabold text-[#006191]">

                                            {{ $inisial }}

                                        </span>

                                        <span class="font-bold text-slate-800">

                                            {{ $namaKaryawan }}

                                        </span>

                                    </div>

                                </td>


                                {{-- PERIODE --}}
                                <td
                                    class="bg-slate-100 px-6 py-3.5 text-sm text-slate-600">

                                    {{ \Carbon\Carbon::createFromFormat('Y-m', $item->periode)->translatedFormat('F Y') }}

                                </td>


                                {{-- NOMINAL --}}
                                <td
                                    class="bg-emerald-100 px-6 py-3.5 font-extrabold text-slate-900 whitespace-nowrap">

                                    Rp {{ number_format($item->nominal, 0, ',', '.') }}

                                </td>


                                {{-- STATUS --}}
                                <td
                                    class="bg-blue-100 px-6 py-3.5">

                                    <span
                                        class="inline-flex items-center gap-1.5 rounded-full border px-3 py-1 text-[11px] font-bold uppercase tracking-wide
                                        {{ $terbayar
                                            ? 'border-emerald-300 bg-emerald-100 text-emerald-700'
                                            : 'border-rose-300 bg-rose-100 text-rose-700' }}">

                                        <span
                                            class="h-1.5 w-1.5 rounded-full
                                            {{ $terbayar
                                                ? 'bg-emerald-500'
                                                : 'bg-rose-500' }}">
                                        </span>

                                        {{ $terbayar ? 'Terbayar' : 'Belum Terbayar' }}

                                    </span>

                                </td>


                                {{-- TANGGAL BAYAR --}}
                                <td
                                    class="bg-violet-100 px-6 py-3.5 text-sm text-slate-600 whitespace-nowrap">

                                    {{ $item->tanggal_bayar ?? '-' }}

                                </td>


                                {{-- KETERANGAN --}}
                                <td
                                    class="max-w-[220px] truncate bg-amber-100 px-6 py-3.5 text-sm text-slate-600">

                                    {{ $item->keterangan ?? '-' }}

                                </td>


                                {{-- BUKTI --}}
                                <td
                                    class="bg-white px-6 py-3.5">

                                    @if($item->bukti_transfer)

                                        <a
                                            href="{{ asset('storage/'.$item->bukti_transfer) }}"
                                            target="_blank"
                                            class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-slate-100 px-3 py-1.5 text-xs font-bold text-[#006191] transition hover:border-slate-300 hover:bg-slate-100">

                                            <svg
                                                class="h-3.5 w-3.5"
                                                viewBox="0 0 24 24"
                                                fill="none">

                                                <path
                                                    d="M15 3h6v6M21 3l-9 9m-2-3H6a2 2 0 00-2 2v7a2 2 0 002 2h7a2 2 0 002-2v-6"
                                                    stroke="currentColor"
                                                    stroke-width="1.8"
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                />

                                            </svg>

                                            Lihat

                                        </a>

                                    @else

                                        <span class="text-sm text-slate-400">
                                            &mdash;
                                        </span>

                                    @endif

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td
                                    colspan="7"
                                    class="px-6 py-14 text-center">

                                    <div
                                        class="mx-auto mb-3 grid h-12 w-12 place-items-center rounded-full bg-slate-100 text-[#006191]">

                                        <svg
                                            class="h-6 w-6"
                                            viewBox="0 0 24 24"
                                            fill="none">

                                            <path
                                                d="M7 3h8a1 1 0 01.7.3l4 4a1 1 0 01.3.7v13a1 1 0 01-1 1H7a1 1 0 01-1-1V4a1 1 0 011-1z"
                                                stroke="currentColor"
                                                stroke-width="1.6"
                                            />

                                        </svg>

                                    </div>

                                    <p class="text-sm font-bold text-slate-700">
                                        Tidak ada data slip gaji
                                    </p>

                                    <p class="mt-1 text-xs text-slate-500">
                                        Coba ubah filter periode atau status di atas.
                                    </p>

                                </td>

                            </tr>

                        @endforelse

                    </tbody>


                    {{-- FOOTER --}}
                    @if($data->isNotEmpty())

                        <tfoot>

                            <tr
                                class="border-t border-slate-200 bg-slate-100">

                                <td
                                    colspan="2"
                                    class="px-6 py-4 text-xs font-extrabold uppercase tracking-wider text-slate-600">

                                    Total Terbayar

                                </td>

                                <td
                                    class="whitespace-nowrap px-6 py-4 text-base font-black text-emerald-700">

                                    Rp
                                    {{ number_format(
                                        $data
                                            ->where('status', 'terbayar')
                                            ->sum('nominal'),
                                        0,
                                        ',',
                                        '.'
                                    ) }}

                                </td>

                                <td
                                    colspan="4"
                                    class="px-6 py-4 text-right text-xs font-medium text-slate-600">

                                    {{ $periodeLabel }}
                                    &mdash;
                                    khusus gaji status terbayar

                                </td>

                            </tr>

                        </tfoot>

                    @endif

                </table>

            </div>

        </section>

    </div>

</div>

@endsection