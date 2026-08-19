@extends('layouts.portal')
@section('title', 'Laporan Pembayaran - CV Natusi')
@section('content')

<div class="space-y-6">
    <header class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
        <div>
            <h1 class="headline text-2xl font-extrabold text-slate-950 md:text-3xl">Laporan Pembayaran</h1>
            <p class="mt-1 text-sm text-slate-500">Ringkasan pembayaran QRIS peserta magang.</p>
        </div>
    </header>

    <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-2xl border border-emerald-100 bg-white p-5 shadow-sm">
            <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Lunas</p>
            <p class="mt-2 text-2xl font-extrabold text-emerald-700">Rp {{ number_format($stats['total_pendapatan'] ?? 0, 0, ',', '.') }}</p>
        </div>
        <div class="rounded-2xl border border-amber-100 bg-white p-5 shadow-sm">
            <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Menunggu Verifikasi</p>
            <p class="mt-2 text-2xl font-extrabold text-amber-700">{{ number_format($stats['jumlah_tertunda'] ?? 0) }} data</p>
        </div>
        <div class="rounded-2xl border border-sky-100 bg-white p-5 shadow-sm">
            <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Nominal Menunggu</p>
            <p class="mt-2 text-2xl font-extrabold text-sky-700">Rp {{ number_format($stats['tagihan_tertunda'] ?? 0, 0, ',', '.') }}</p>
        </div>
        <div class="rounded-2xl border border-blue-100 bg-white p-5 shadow-sm">
            <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Rata-rata Pembayaran</p>
            <p class="mt-2 text-2xl font-extrabold text-blue-700">Rp {{ number_format($stats['rata_rata'] ?? 0, 0, ',', '.') }}</p>
        </div>
    </section>

    <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
        <form method="GET" action="{{ route('admin-peserta.laporan.pembayaran') }}" class="grid gap-3 md:grid-cols-4">
            <input type="search" name="search" value="{{ $search }}" placeholder="Cari nama peserta..." class="rounded-xl border-slate-300 text-sm focus:border-sky-500 focus:ring-sky-500 md:col-span-2">
            <select name="status_filter" class="rounded-xl border-slate-300 text-sm focus:border-sky-500 focus:ring-sky-500">
                <option value="">Semua Status</option>
                <option value="menunggu" @selected($statusFilter === 'menunggu')>Menunggu</option>
                <option value="lunas" @selected($statusFilter === 'lunas')>Lunas</option>
                <option value="ditolak" @selected($statusFilter === 'ditolak')>Ditolak</option>
            </select>
            <select name="year" class="rounded-xl border-slate-300 text-sm focus:border-sky-500 focus:ring-sky-500">
                <option value="0" @selected((int) $year === 0)>Semua Tahun</option>
                @foreach($availableYears as $th)
                    <option value="{{ $th }}" @selected((int) $year === (int) $th)>{{ $th }}</option>
                @endforeach
            </select>
            <div class="flex justify-end gap-2 md:col-span-4">
                <a href="{{ route('admin-peserta.laporan.pembayaran') }}" class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50">Reset</a>
                <button type="submit" class="rounded-xl bg-sky-600 px-5 py-2 text-sm font-bold text-white hover:bg-sky-700">Terapkan Filter</button>
            </div>
        </form>
    </section>

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 bg-slate-50 px-6 py-4">
            <h2 class="text-lg font-bold text-slate-900">Detail Transaksi</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[900px] border-collapse text-left">
                <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-6 py-3">Kode</th>
                        <th class="px-6 py-3">Nama Peserta</th>
                        <th class="px-6 py-3">Tanggal</th>
                        <th class="px-6 py-3">Jumlah</th>
                        <th class="px-6 py-3">Metode</th>
                        <th class="px-6 py-3">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($transaksi as $t)
                        @php
                            $statusLabel = match($t->status) {
                                'lunas' => 'Lunas',
                                'ditolak' => 'Ditolak',
                                default => 'Menunggu',
                            };
                            $badge = match($t->status) {
                                'lunas' => 'bg-emerald-100 text-emerald-700',
                                'ditolak' => 'bg-rose-100 text-rose-700',
                                default => 'bg-amber-100 text-amber-700',
                            };
                        @endphp
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4 font-semibold text-sky-700">PAY-{{ str_pad((string) $t->id_pembayaran, 5, '0', STR_PAD_LEFT) }}</td>
                            <td class="px-6 py-4">
                                <p class="font-semibold text-slate-800">{{ $t->peserta?->user?->nama ?? $t->peserta?->permintaan?->nama_pemohon ?? '-' }}</p>
                            </td>
                            <td class="px-6 py-4 text-slate-600">{{ $t->tgl_bayar?->translatedFormat('d M Y, H:i') ?? '-' }}</td>
                            <td class="px-6 py-4 font-semibold text-slate-800">Rp {{ number_format($t->nominal ?? 0, 0, ',', '.') }}</td>
                            <td class="px-6 py-4"><span class="rounded-full bg-sky-50 px-3 py-1 text-xs font-bold text-sky-700">QRIS</span></td>
                            <td class="px-6 py-4"><span class="rounded-full px-3 py-1 text-xs font-bold {{ $badge }}">{{ $statusLabel }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-6 py-12 text-center text-sm text-slate-400">Belum ada transaksi pada filter ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($transaksi->hasPages())
            <div class="border-t border-slate-200 px-6 py-4">{{ $transaksi->links() }}</div>
        @endif
    </section>
</div>
@endsection
