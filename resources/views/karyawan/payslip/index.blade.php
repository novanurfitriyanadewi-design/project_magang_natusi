@extends('layouts.portal')

@section('title', 'Slip Gaji')

@section('content')
<div class="max-w-[1440px] mx-auto w-full p-4 sm:p-6 space-y-6">

    {{-- ===== Header ===== --}}
    <section class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-teal-700 via-teal-600 to-emerald-600 p-6 sm:p-8 text-white shadow-lg">
        <div class="absolute -right-10 -top-14 h-52 w-52 rounded-full bg-white/10"></div>
        <div class="absolute right-24 top-20 h-28 w-28 rounded-full bg-white/10"></div>
        <div class="relative">
            <span class="inline-flex items-center gap-2 rounded-full bg-white/15 px-3 py-1 text-[11px] font-bold uppercase tracking-wider backdrop-blur-sm ring-1 ring-white/25">
                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none"><path d="M7 4h7l4 4v12H7z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/></svg>
                Keuangan
            </span>
            <h1 class="mt-2 text-2xl sm:text-3xl font-extrabold tracking-tight">Slip Gaji</h1>
            <p class="mt-1 text-sm text-teal-50">Riwayat pembayaran gaji Anda di CV Natusi.</p>
        </div>
    </section>

    @if ($tahunList->isNotEmpty())
        {{-- ===== Filter Tahun ===== --}}
        <form method="GET" class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 flex flex-wrap items-end gap-4">
            <div class="flex items-center gap-2 pb-2.5 mr-auto">
                <span class="grid h-8 w-8 place-items-center rounded-lg bg-teal-50 text-teal-700">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.8"/><path d="M12 7v5l3 2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                </span>
                <h3 class="font-bold text-slate-800">Riwayat Gaji</h3>
            </div>
            <div>
                <label for="f-tahun" class="mb-1.5 block text-[11px] font-bold uppercase tracking-wide text-slate-500">Tahun</label>
                <select id="f-tahun" name="tahun"
                    class="rounded-lg border-slate-300 text-sm shadow-sm focus:border-teal-500 focus:ring-teal-500">
                    <option value="">Semua Tahun</option>
                    @foreach ($tahunList as $tahun)
                        <option value="{{ $tahun }}" @selected(request('tahun') == $tahun)>{{ $tahun }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg bg-teal-700 px-4 py-2.5 text-sm font-bold text-white shadow hover:bg-teal-800 transition-colors">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none"><circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="1.8"/><path d="M20 20l-3.5-3.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                Terapkan
            </button>
            @if (request('tahun'))
                <a href="{{ route('karyawan.payslip.index') }}"
                    class="grid place-items-center rounded-lg border border-slate-300 px-4 py-2.5 text-slate-500 hover:bg-slate-50 hover:text-slate-700 transition-colors" title="Reset filter">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none"><path d="M4 4v5h5M20 20v-5h-5M19.5 9A8 8 0 006 6L4 9m16 6l-2 3a8 8 0 01-13.5-3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </a>
            @endif
        </form>
    @endif

    @if ($jumlahSlipTerbayar > 0)
        {{-- ===== Ringkasan Total Gaji ===== --}}
        <section class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">

            {{-- Total Gaji Diterima --}}
            <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-emerald-500 via-green-500 to-teal-600 p-5 text-white shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all">
                <div class="absolute -right-6 -bottom-8 h-28 w-28 rounded-full bg-white/10"></div>
                <div class="relative flex items-start justify-between gap-3">
                    <div>
                        <span class="inline-block rounded-full bg-white/20 px-2.5 py-0.5 text-[10px] font-extrabold uppercase tracking-wider backdrop-blur-sm">Total Gaji Diterima</span>
                        <p class="mt-2.5 text-xl sm:text-2xl font-black leading-tight tracking-tight">Rp {{ number_format($totalGaji, 0, ',', '.') }}</p>
                        <p class="mt-1 text-[11px] font-medium text-emerald-100">
                            {{ request('tahun') ? 'Akumulasi tahun ' . request('tahun') : 'Seluruh periode' }}
                        </p>
                    </div>
                    <div class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-white/25 backdrop-blur-sm">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none"><path d="M12 6V4m0 16v-2m6-6h2M4 12h2m10.24-4.24l1.42-1.42M6.34 17.66l1.42-1.42m0-8.48L6.34 6.34m11.32 11.32l-1.42-1.42M12 9a3 3 0 100 6 3 3 0 000-6z" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
                    </div>
                </div>
            </div>

            {{-- Slip Terbayar --}}
            <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-blue-600 via-sky-600 to-cyan-600 p-5 text-white shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all">
                <div class="absolute -right-6 -bottom-8 h-28 w-28 rounded-full bg-white/10"></div>
                <div class="relative flex items-start justify-between gap-3">
                    <div>
                        <span class="inline-block rounded-full bg-white/20 px-2.5 py-0.5 text-[10px] font-extrabold uppercase tracking-wider backdrop-blur-sm">Slip Terbayar</span>
                        <p class="mt-2.5 text-2xl font-black leading-tight">{{ $jumlahSlipTerbayar }} <span class="text-sm font-bold text-blue-100">slip</span></p>
                        <p class="mt-1 text-[11px] font-medium text-blue-100">Pembayaran dari HRD</p>
                    </div>
                    <div class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-white/25 backdrop-blur-sm">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none"><path d="M7 3h8a1 1 0 01.7.3l4 4a1 1 0 01.3.7v13a1 1 0 01-1 1H7a1 1 0 01-1-1V4a1 1 0 011-1z" stroke="currentColor" stroke-width="1.7"/><path d="M14 3v5h5M9 13h6m-6 4h6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
                    </div>
                </div>
            </div>

            {{-- Gaji Terakhir --}}
            <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-violet-600 via-purple-600 to-fuchsia-600 p-5 text-white shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all sm:col-span-2 lg:col-span-1">
                <div class="absolute -right-6 -bottom-8 h-28 w-28 rounded-full bg-white/10"></div>
                <div class="relative flex items-start justify-between gap-3">
                    <div>
                        <span class="inline-block rounded-full bg-white/20 px-2.5 py-0.5 text-[10px] font-extrabold uppercase tracking-wider backdrop-blur-sm">Gaji Terakhir</span>
                        <p class="mt-2.5 text-xl sm:text-2xl font-black leading-tight tracking-tight whitespace-nowrap">
                            {{ $slipTerakhir ? 'Rp ' . number_format($slipTerakhir->nominal, 0, ',', '.') : '-' }}
                        </p>
                        @if ($slipTerakhir?->periode_label)
                            <p class="mt-1 text-[11px] font-medium text-violet-100">Periode {{ $slipTerakhir->periode_label }}</p>
                        @endif
                    </div>
                    <div class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-white/25 backdrop-blur-sm">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.7"/><path d="M12 7v5l3 2" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
                    </div>
                </div>
            </div>
        </section>
    @endif

    {{-- ===== Grid Slip Gaji ===== --}}
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @forelse ($slipGaji as $slip)
            @php($meta = $slip->statusMeta())
            @php($terbayar = $slip->status === 'terbayar')
            <div class="group relative overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-[0_12px_32px_rgba(15,23,42,0.06)] transition-all hover:-translate-y-1 hover:shadow-xl">
                {{-- Strip warna atas --}}
                <div class="h-1.5 w-full {{ $terbayar ? 'bg-gradient-to-r from-emerald-500 to-teal-500' : 'bg-gradient-to-r from-orange-400 to-red-500' }}"></div>

                <div class="p-5">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-[10px] font-bold uppercase tracking-wide text-slate-400">Periode</p>
                            <p class="mt-1 text-lg font-extrabold text-slate-900">{{ $slip->periode_label }}</p>
                        </div>
                        <span class="inline-flex shrink-0 items-center gap-1.5 rounded-full px-3 py-1 text-[10px] font-extrabold uppercase tracking-wide
                            {{ $terbayar ? 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200' : 'bg-orange-50 text-orange-600 ring-1 ring-orange-200' }}">
                            <span class="h-1.5 w-1.5 rounded-full {{ $terbayar ? 'bg-emerald-500' : 'bg-orange-400' }}"></span>
                            {{ $meta['label'] }}
                        </span>
                    </div>

                    <div class="mt-4 rounded-xl bg-gradient-to-br from-slate-50 to-sky-50 px-4 py-3 ring-1 ring-slate-100">
                        <p class="text-[10px] font-bold uppercase tracking-wide text-slate-400">Nominal</p>
                        <p class="mt-1 text-xl font-black {{ $terbayar ? 'text-emerald-700' : 'text-slate-700' }}">
                            Rp {{ number_format($slip->nominal, 0, ',', '.') }}
                        </p>
                    </div>

                    <div class="mt-3 space-y-1">
                        @if ($slip->tanggal_bayar)
                            <p class="flex items-center gap-1.5 text-xs text-slate-500">
                                <svg class="h-3.5 w-3.5 text-teal-600" viewBox="0 0 24 24" fill="none"><path d="M8 7V3m8 4V3M3 11h18M5 5h14a2 2 0 012 2v12a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2z" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
                                Dibayar pada {{ $slip->tanggal_bayar->translatedFormat('d M Y') }}
                            </p>
                        @endif
                        @if ($slip->keterangan)
                            <p class="flex items-start gap-1.5 text-xs text-slate-500">
                                <svg class="mt-0.5 h-3.5 w-3.5 shrink-0 text-slate-400" viewBox="0 0 24 24" fill="none"><path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
                                {{ $slip->keterangan }}
                            </p>
                        @endif
                    </div>

                    @if ($slip->bukti_transfer)
                        <a
                            href="{{ asset('storage/' . $slip->bukti_transfer) }}"
                            target="_blank"
                            rel="noopener"
                            class="mt-4 flex items-center justify-center gap-2 rounded-lg bg-teal-700 py-2.5 text-xs font-extrabold text-white shadow-sm transition hover:bg-teal-800"
                        >
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none"><path d="M15 3h6v6M21 3l-9 9m-2-3H6a2 2 0 00-2 2v7a2 2 0 002 2h7a2 2 0 002-2v-6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            Lihat Bukti Transfer
                        </a>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full rounded-2xl border border-dashed border-slate-200 bg-white p-12 text-center shadow-sm">
                <div class="mx-auto mb-4 grid h-16 w-16 place-items-center rounded-full bg-gradient-to-br from-slate-50 to-sky-50 ring-1 ring-slate-100">
                    <svg class="h-8 w-8 text-slate-300" viewBox="0 0 24 24" fill="none"><path d="M7 4h7l4 4v12H7z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/></svg>
                </div>
                <p class="text-sm font-extrabold text-slate-800">Belum Ada Slip Gaji</p>
                <p class="mt-1 text-xs text-slate-500">Slip gaji akan muncul di sini setelah pembayaran pertama diproses HRD.</p>
            </div>
        @endforelse
    </div>

    @if ($slipGaji instanceof \Illuminate\Pagination\LengthAwarePaginator && $slipGaji->hasPages())
        <div class="rounded-2xl border border-slate-100 bg-white px-5 py-4 shadow-sm">
            {{ $slipGaji->links() }}
        </div>
    @endif
</div>
@endsection
