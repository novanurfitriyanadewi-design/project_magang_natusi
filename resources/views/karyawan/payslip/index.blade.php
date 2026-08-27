@extends('layouts.portal')

@section('title', 'Slip Gaji')

@section('content')
<div class="max-w-[1200px] mx-auto w-full p-4 sm:p-6 space-y-6">

    {{-- ===== Header ===== --}}
    <section class="relative overflow-hidden rounded-2xl border border-sky-100 bg-gradient-to-r from-sky-500 via-sky-600 to-cyan-500 px-6 py-7 sm:px-8 sm:py-8 shadow-sm">
        <div class="pointer-events-none absolute -right-10 -top-10 h-40 w-40 rounded-full border-2 border-cyan-200/20"></div>
        <div class="pointer-events-none absolute bottom-0 right-1/4 h-24 w-24 rounded-full bg-cyan-300/20 blur-2xl"></div>

        <div class="relative">
            <span class="inline-flex items-center gap-2 rounded-full bg-white/15 px-3 py-1 text-[10px] font-bold uppercase tracking-[0.16em] text-white ring-1 ring-white/20">
                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none">
                    <path d="M7 4h7l4 4v12H7z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                </svg>
                Keuangan
            </span>

            <h1 class="mt-3 text-2xl font-extrabold tracking-tight text-white sm:text-3xl">
                Slip Gaji
            </h1>

            <p class="mt-1 text-sm text-sky-50">
                Riwayat pembayaran gaji Anda di CV Natusi.
            </p>
        </div>
    </section>


    {{-- ===== Ringkasan Tanpa Card ===== --}}
    <section class="overflow-hidden rounded-2xl border border-sky-100 bg-sky-50 shadow-sm">
        <div class="grid divide-y divide-sky-100 sm:grid-cols-2 sm:divide-x sm:divide-y-0 lg:grid-cols-4">

            {{-- Total Gaji --}}
            <div class="bg-sky-50 px-5 py-5 sm:px-6">
                <div class="flex items-start gap-3">
                    <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-sky-50 text-sky-600">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none">
                            <circle cx="12" cy="12" r="8" stroke="currentColor" stroke-width="1.8"/>
                            <path d="M12 8v8M9.5 10.5c0-1 1.1-1.7 2.5-1.7s2.5.7 2.5 1.7-1 1.5-2.5 1.8-2.5.8-2.5 1.8 1.1 1.7 2.5 1.7 2.5-.7 2.5-1.7" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                        </svg>
                    </span>

                    <div class="min-w-0">
                        <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-slate-400">
                            Total Gaji Diterima
                        </p>

                        <p class="mt-1 truncate text-xl font-black text-slate-900">
                            Rp {{ number_format($totalGaji, 0, ',', '.') }}
                        </p>

                        <p class="mt-1 text-[11px] text-slate-400">
                            {{ request('tahun') ? 'Akumulasi tahun ' . request('tahun') : 'Seluruh periode' }}
                        </p>
                    </div>
                </div>
            </div>


            {{-- Slip Terbayar --}}
            <div class="bg-sky-50 px-5 py-5 sm:px-6">
                <div class="flex items-start gap-3">
                    <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-emerald-50 text-emerald-600">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none">
                            <path d="M5 13l4 4L19 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>

                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-slate-400">
                            Slip Terbayar
                        </p>

                        <p class="mt-1 text-2xl font-black text-slate-900">
                            {{ $jumlahSlipTerbayar }}
                            <span class="text-sm font-bold text-slate-400">slip</span>
                        </p>

                        <p class="mt-1 text-[11px] text-slate-400">
                            Pembayaran dari HRD
                        </p>
                    </div>
                </div>
            </div>


            {{-- Menunggu Pembayaran --}}
            <div class="bg-sky-50 px-5 py-5 sm:px-6">
                <div class="flex items-start gap-3">
                    <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-amber-50 text-amber-600">
                        <span class="material-symbols-outlined">schedule</span>
                    </span>

                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-slate-400">
                            Menunggu Pembayaran
                        </p>

                        <p class="mt-1 text-2xl font-black text-slate-900">
                            {{ $jumlahSlipMenunggu }}
                            <span class="text-sm font-bold text-slate-400">slip</span>
                        </p>

                        <p class="mt-1 text-[11px] text-slate-400">
                            Diproses oleh HRD
                        </p>
                    </div>
                </div>
            </div>


            {{-- Gaji Terakhir --}}
            <div class="bg-sky-50 px-5 py-5 sm:px-6">
                <div class="flex items-start gap-3">
                    <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-violet-50 text-violet-600">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none">
                            <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.7"/>
                            <path d="M12 7v5l3 2" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                        </svg>
                    </span>

                    <div class="min-w-0">
                        <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-slate-400">
                            Gaji Terakhir
                        </p>

                        <p class="mt-1 truncate text-xl font-black text-slate-900">
                            {{ $slipTerakhir ? 'Rp ' . number_format($slipTerakhir->nominal, 0, ',', '.') : '-' }}
                        </p>

                        <p class="mt-1 text-[11px] text-slate-400">
                            {{ $slipTerakhir?->periode_label ? 'Periode ' . $slipTerakhir->periode_label : 'Belum ada data' }}
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </section>


    {{-- ===== Informasi Kosong ===== --}}
    @if ($slipGaji->isEmpty())
        <section class="flex items-start gap-4 rounded-xl border border-sky-100 bg-sky-50 p-5">
            <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-white text-sky-600 shadow-sm">
                <span class="material-symbols-outlined">info</span>
            </span>

            <div>
                <h2 class="text-sm font-bold text-sky-900">
                    Belum ada slip gaji
                </h2>

                <p class="mt-1 text-xs leading-5 text-sky-800">
                    Slip gaji akan muncul otomatis setelah HRD membuat data pembayaran untuk Anda.
                </p>
            </div>
        </section>
    @endif


    {{-- ===== Filter Tahun ===== --}}
    @if ($tahunList->isNotEmpty())
        <form method="GET" class="flex flex-wrap items-end gap-4 border-b border-slate-200 pb-5">

            <div class="mr-auto flex items-center gap-3">
                <span class="grid h-10 w-10 place-items-center rounded-xl bg-sky-50 text-sky-600">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none">
                        <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.8"/>
                        <path d="M12 7v5l3 2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                    </svg>
                </span>

                <div>
                    <h3 class="text-sm font-bold text-slate-900">
                        Riwayat Gaji
                    </h3>

                    <p class="text-[11px] text-slate-400">
                        Saring berdasarkan tahun pembayaran
                    </p>
                </div>
            </div>

            <div>
                <label for="f-tahun" class="mb-1.5 block text-[10px] font-bold uppercase tracking-wide text-slate-400">
                    Tahun
                </label>

                <select
                    id="f-tahun"
                    name="tahun"
                    class="rounded-lg border-slate-300 text-sm shadow-sm focus:border-sky-500 focus:ring-sky-500"
                >
                    <option value="">Semua Tahun</option>

                    @foreach ($tahunList as $tahun)
                        <option value="{{ $tahun }}" @selected(request('tahun') == $tahun)>
                            {{ $tahun }}
                        </option>
                    @endforeach
                </select>
            </div>

            <button
                type="submit"
                class="inline-flex items-center gap-1.5 rounded-lg bg-sky-600 px-4 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-sky-700"
            >
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none">
                    <circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="1.8"/>
                    <path d="M20 20l-3.5-3.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                </svg>

                Terapkan
            </button>

            @if (request('tahun'))
                <a
                    href="{{ route('karyawan.payslip.index') }}"
                    class="grid place-items-center rounded-lg border border-slate-200 px-4 py-2.5 text-slate-500 transition hover:bg-slate-50 hover:text-slate-700"
                    title="Reset filter"
                >
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none">
                        <path d="M4 4v5h5M20 20v-5h-5M19.5 9A8 8 0 006 6L4 9m16 6l-2 3a8 8 0 01-13.5-3"
                            stroke="currentColor"
                            stroke-width="1.8"
                            stroke-linecap="round"
                            stroke-linejoin="round"/>
                    </svg>
                </a>
            @endif

        </form>
    @endif


    {{-- ===== Grid Slip Gaji ===== --}}
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">

        @forelse ($slipGaji as $slip)

            @php($meta = $slip->statusMeta())
            @php($terbayar = $slip->status === 'terbayar')

            <div class="group relative overflow-hidden rounded-xl border border-slate-200 bg-white transition hover:-translate-y-0.5 hover:shadow-md">

                <div class="flex items-start justify-between gap-3 border-b border-slate-100 px-5 py-4">

                    <div class="min-w-0">
                        <p class="text-[10px] font-bold uppercase tracking-wide text-slate-400">
                            Periode
                        </p>

                        <p class="mt-1 truncate text-base font-extrabold text-slate-900">
                            {{ $slip->periode_label }}
                        </p>
                    </div>

                    <span class="inline-flex shrink-0 items-center gap-1.5 rounded-full px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide
                        {{ $terbayar
                            ? 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-100'
                            : 'bg-amber-50 text-amber-700 ring-1 ring-amber-100'
                        }}">

                        <span class="h-1.5 w-1.5 rounded-full {{ $terbayar ? 'bg-emerald-500' : 'bg-amber-500' }}"></span>

                        {{ $meta['label'] }}

                    </span>

                </div>


                <div class="p-5">

                    <div class="border-l-4 {{ $terbayar ? 'border-emerald-400' : 'border-amber-400' }} bg-slate-50 px-4 py-3">

                        <p class="text-[10px] font-bold uppercase tracking-wide text-slate-400">
                            Nominal
                        </p>

                        <p class="mt-1 text-lg font-black {{ $terbayar ? 'text-emerald-700' : 'text-slate-700' }}">
                            Rp {{ number_format($slip->nominal, 0, ',', '.') }}
                        </p>

                    </div>


                    @if ($slip->tanggal_bayar || $slip->keterangan)

                        <div class="mt-4 space-y-2">

                            @if ($slip->tanggal_bayar)

                                <p class="flex items-center gap-2 text-xs text-slate-500">

                                    <svg class="h-4 w-4 shrink-0 text-sky-500" viewBox="0 0 24 24" fill="none">
                                        <path
                                            d="M8 7V3m8 4V3M3 11h18M5 5h14a2 2 0 012 2v12a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2z"
                                            stroke="currentColor"
                                            stroke-width="1.7"
                                            stroke-linecap="round"
                                        />
                                    </svg>

                                    Dibayar pada {{ $slip->tanggal_bayar->translatedFormat('d M Y') }}

                                </p>

                            @endif


                            @if ($slip->keterangan)

                                <p class="flex items-start gap-2 text-xs text-slate-500">

                                    <svg class="mt-0.5 h-4 w-4 shrink-0 text-slate-400" viewBox="0 0 24 24" fill="none">
                                        <path
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                            stroke="currentColor"
                                            stroke-width="1.7"
                                        />
                                    </svg>

                                    <span class="min-w-0">
                                        {{ $slip->keterangan }}
                                    </span>

                                </p>

                            @endif

                        </div>

                    @endif


                    @if ($slip->bukti_transfer)

                        <a
                            href="{{ asset('storage/' . $slip->bukti_transfer) }}"
                            target="_blank"
                            rel="noopener"
                            class="mt-5 flex items-center justify-center gap-2 rounded-lg bg-sky-500 py-2.5 text-xs font-bold text-white transition hover:bg-sky-600"
                        >

                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none">
                                <path
                                    d="M15 3h6v6M21 3l-9 9m-2-3H6a2 2 0 00-2 2v7a2 2 0 002 2h7a2 2 0 002-2v-6"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                />
                            </svg>

                            Lihat Bukti Transfer

                        </a>

                    @endif

                </div>

            </div>

        @empty

            <div class="col-span-full py-14 text-center">

                <div class="mx-auto mb-4 grid h-14 w-14 place-items-center rounded-full bg-sky-50">

                    <svg class="h-7 w-7 text-sky-400" viewBox="0 0 24 24" fill="none">
                        <path
                            d="M7 4h7l4 4v12H7z"
                            stroke="currentColor"
                            stroke-width="1.6"
                            stroke-linejoin="round"
                        />
                    </svg>

                </div>

                <p class="text-sm font-extrabold text-slate-800">
                    Belum Ada Slip Gaji
                </p>

                <p class="mt-1 text-xs text-slate-500">
                    Slip gaji akan muncul di sini setelah pembayaran pertama diproses HRD.
                </p>

            </div>

        @endforelse

    </div>


    {{-- ===== Pagination ===== --}}
    @if ($slipGaji instanceof \Illuminate\Pagination\LengthAwarePaginator && $slipGaji->hasPages())

        <div class="border-t border-slate-200 pt-5">
            {{ $slipGaji->links() }}
        </div>

    @endif

</div>
@endsection