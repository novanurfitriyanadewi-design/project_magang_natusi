@extends('layouts.portal')

@section('title', 'Laporan Pembayaran - CV Natusi')

@section('content')
<div class="p-6">

    {{-- Header --}}
    <header class="mb-6 flex flex-col md:flex-row md:items-end justify-between gap-3">
        <div>
            <h2 class="text-2xl font-bold text-[#006191] tracking-tight">Laporan Pembayaran</h2>
            <p class="text-sm text-slate-500">Ringkasan transaksi keuangan administrasi magang.</p>
        </div>

        <form method="GET" class="flex flex-wrap items-center gap-3">
            <select name="year" onchange="this.form.submit()" class="appearance-none bg-white border border-slate-200 rounded-lg px-4 py-2 text-sm font-medium focus:ring-2 focus:ring-[#006191] outline-none">
                @foreach ($availableYears as $th)
                    <option value="{{ $th }}" @selected($th == $year)>Tahun {{ $th }}</option>
                @endforeach
            </select>

            <select name="status_filter" onchange="this.form.submit()" class="appearance-none bg-white border border-slate-200 rounded-lg px-4 py-2 text-sm font-medium focus:ring-2 focus:ring-[#006191] outline-none">
                <option value="" @selected(! $statusFilter)>Semua Status</option>
                <option value="berhasil" @selected($statusFilter === 'berhasil')>Berhasil</option>
                <option value="menunggu" @selected($statusFilter === 'menunggu')>Menunggu</option>
                <option value="dibatalkan" @selected($statusFilter === 'dibatalkan')>Dibatalkan</option>
            </select>

            <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama / kode transaksi"
                   class="bg-white border border-slate-200 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-[#006191] outline-none">

            <button type="submit" class="bg-[#006191] text-white px-4 py-2 rounded-lg text-sm font-semibold">Filter</button>
        </form>
    </header>

    {{-- Statistik --}}
    <section class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white p-4 rounded-xl border border-slate-200 border-l-4 border-l-[#006191] shadow-sm">
            <span class="text-slate-500 text-xs font-bold tracking-wide">TOTAL PENDAPATAN</span>
            <div class="text-xl font-bold mt-1">Rp {{ number_format($stats['total_pendapatan'], 0, ',', '.') }}</div>
            <div class="text-xs text-slate-500 mt-1">{{ $stats['jumlah_berhasil'] }} transaksi berhasil</div>
        </div>

        <div class="bg-white p-4 rounded-xl border border-slate-200 border-l-4 border-l-[#bb0014] shadow-sm">
            <span class="text-slate-500 text-xs font-bold tracking-wide">TAGIHAN TERTUNDA</span>
            <div class="text-xl font-bold mt-1">Rp {{ number_format($stats['tagihan_tertunda'], 0, ',', '.') }}</div>
            <div class="text-xs text-slate-500 mt-1">{{ $stats['jumlah_tertunda'] }} transaksi</div>
        </div>

        <div class="bg-white p-4 rounded-xl border border-slate-200 border-l-4 border-l-[#4d5d70] shadow-sm">
            <span class="text-slate-500 text-xs font-bold tracking-wide">RATA-RATA / INTERN</span>
            <div class="text-xl font-bold mt-1">Rp {{ number_format($stats['rata_rata'], 0, ',', '.') }}</div>
            <div class="text-xs text-slate-500 mt-1">Berdasarkan {{ $stats['jumlah_berhasil'] }} transaksi berhasil</div>
        </div>
    </section>

    {{-- Grafik --}}
    <section class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
        <div class="lg:col-span-2 bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
            <h3 class="text-lg font-semibold text-[#006191] mb-4">Tren Pendapatan Bulanan {{ $year }}</h3>
            <div class="h-56 w-full flex items-end justify-between gap-2 px-2">
                @php
                    $bulanLabel = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
                @endphp
                @foreach ($monthly as $bulan => $total)
                    @php $h = $total > 0 ? max(6, ($total / $chartMax) * 100) : 4; @endphp
                    <div class="flex-1 flex flex-col justify-end items-center gap-1">
                        <div class="w-full bg-[#006191]/15 rounded-t relative" style="height: 100%;">
                            <div class="absolute bottom-0 left-0 w-full bg-[#006191] rounded-t" style="height: {{ $h }}%;" title="Rp {{ number_format($total,0,',','.') }}"></div>
                        </div>
                        <span class="text-[10px] text-slate-500">{{ $bulanLabel[$bulan - 1] }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex flex-col">
            <h3 class="text-lg font-semibold text-[#006191] mb-4">Metode Pembayaran</h3>
            <div class="flex-1 flex items-center justify-center">
                <div class="w-36 h-36 rounded-full flex items-center justify-center" style="background: conic-gradient({{ $conicGradient }});">
                    <div class="w-20 h-20 bg-white rounded-full flex flex-col items-center justify-center">
                        <span class="text-[10px] text-slate-500">Total</span>
                        <span class="text-sm font-bold">{{ $stats['jumlah_berhasil'] }}</span>
                    </div>
                </div>
            </div>
            <div class="mt-4 space-y-1">
                @forelse ($metodePersen as $metode => $persen)
                    <div class="flex items-center justify-between text-xs">
                        <span>{{ ucfirst($metode) }}</span>
                        <span class="font-semibold">{{ $persen }}%</span>
                    </div>
                @empty
                    <p class="text-xs text-slate-400">Belum ada data.</p>
                @endforelse
            </div>
        </div>
    </section>

    {{-- Tabel transaksi --}}
    <section class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200">
            <h3 class="text-lg font-semibold">Detail Transaksi</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                        <th class="px-6 py-3">Kode</th>
                        <th class="px-6 py-3">Nama Intern</th>
                        <th class="px-6 py-3">Tanggal</th>
                        <th class="px-6 py-3">Jumlah</th>
                        <th class="px-6 py-3">Metode</th>
                        <th class="px-6 py-3">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($transaksi as $t)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-3 font-semibold text-[#006191]">{{ $t->kode_transaksi ?? '#'.$t->id }}</td>
                            <td class="px-6 py-3">{{ $t->pesertaMagang->user->nama ?? '-' }}</td>
                            <td class="px-6 py-3 text-slate-500">{{ $t->created_at->format('d M Y') }}</td>
                            <td class="px-6 py-3 font-semibold">Rp {{ number_format($t->jumlah, 0, ',', '.') }}</td>
                            <td class="px-6 py-3">
                                <span class="bg-slate-100 px-2 py-1 rounded text-xs font-medium">{{ ucfirst($t->metode) }}</span>
                            </td>
                            <td class="px-6 py-3">
                                @php
                                    $badge = match ($t->status) {
                                        'berhasil' => 'bg-green-100 text-green-700',
                                        'menunggu' => 'bg-yellow-100 text-yellow-700',
                                        default => 'bg-red-100 text-red-700',
                                    };
                                @endphp
                                <span class="{{ $badge }} px-3 py-1 rounded-full text-[11px] font-bold">{{ ucfirst($t->status) }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-6 py-8 text-center text-slate-400">Belum ada transaksi.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-slate-200">
            {{ $transaksi->links() }}
        </div>
    </section>

</div>
@endsection