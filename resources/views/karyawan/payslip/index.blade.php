@extends('layouts.portal')

@section('title', 'Slip Gaji')

@section('content')
<div class="max-w-[1440px] mx-auto w-full">

    <section class="mb-6 flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-950">Slip Gaji</h1>
            <p class="mt-1 text-sm text-slate-500">Riwayat pembayaran gaji Anda di CV Natusi.</p>
        </div>
    </section>

    @if ($tahunList->isNotEmpty())
        <form method="GET" class="mb-5 flex flex-wrap items-end gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            <div>
                <label class="mb-1 block text-[10px] font-bold uppercase tracking-wide text-slate-400">Tahun</label>
                <select name="tahun" class="rounded-lg border-slate-300 text-sm focus:border-teal-500 focus:ring-teal-500">
                    <option value="">Semua Tahun</option>
                    @foreach ($tahunList as $tahun)
                        <option value="{{ $tahun }}" @selected(request('tahun') == $tahun)>{{ $tahun }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="rounded-lg bg-teal-700 px-4 py-2 text-sm font-bold text-white hover:bg-teal-800">Terapkan</button>
            @if (request('tahun'))
                <a href="{{ route('karyawan.payslip.index') }}" class="text-sm font-bold text-teal-700 hover:underline">Reset</a>
            @endif
        </form>
    @endif

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @forelse ($slipGaji as $slip)
            @php($meta = $slip->statusMeta())
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-[0_12px_32px_rgba(15,23,42,0.06)]">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-wide text-slate-400">Periode</p>
                        <p class="mt-1 text-lg font-extrabold text-slate-900">{{ $slip->periode_label }}</p>
                    </div>
                    <span class="rounded-full px-3 py-1 text-[10px] font-extrabold uppercase tracking-wide {{ $meta['class'] }}">
                        {{ $meta['label'] }}
                    </span>
                </div>

                <div class="mt-4 rounded-xl bg-slate-50 px-4 py-3">
                    <p class="text-[10px] font-bold uppercase tracking-wide text-slate-400">Nominal</p>
                    <p class="mt-1 text-xl font-extrabold text-emerald-700">Rp {{ number_format($slip->nominal, 0, ',', '.') }}</p>
                </div>

                @if ($slip->tanggal_bayar)
                    <p class="mt-3 text-xs text-slate-500">
                        Dibayar pada {{ $slip->tanggal_bayar->translatedFormat('d M Y') }}
                    </p>
                @endif

                @if ($slip->keterangan)
                    <p class="mt-2 text-xs text-slate-500">{{ $slip->keterangan }}</p>
                @endif

                @if ($slip->bukti_transfer)
                    <a
                        href="{{ asset('storage/' . $slip->bukti_transfer) }}"
                        target="_blank"
                        rel="noopener"
                        class="mt-4 flex items-center justify-center gap-2 rounded-lg border border-teal-600 py-2.5 text-xs font-extrabold text-teal-700 transition hover:bg-teal-50"
                    >
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none"><path d="M7 4h7l4 4v12H7z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/></svg>
                        Lihat Bukti Transfer
                    </a>
                @endif
            </div>
        @empty
            <div class="col-span-full rounded-2xl border border-dashed border-slate-200 bg-white p-12 text-center">
                <div class="mx-auto mb-4 grid h-16 w-16 place-items-center rounded-full bg-slate-50">
                    <svg class="h-8 w-8 text-slate-300" viewBox="0 0 24 24" fill="none"><path d="M7 4h7l4 4v12H7z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/></svg>
                </div>
                <p class="text-sm font-extrabold text-slate-800">Belum Ada Slip Gaji</p>
                <p class="mt-1 text-xs text-slate-500">Slip gaji akan muncul di sini setelah pembayaran pertama diproses HRD.</p>
            </div>
        @endforelse
    </div>

    @if ($slipGaji instanceof \Illuminate\Pagination\LengthAwarePaginator && $slipGaji->hasPages())
        <div class="mt-6">
            {{ $slipGaji->links() }}
        </div>
    @endif
</div>
@endsection

