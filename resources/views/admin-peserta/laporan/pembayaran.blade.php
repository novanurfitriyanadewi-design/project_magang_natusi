@extends('layouts.portal')
@section('title', 'Laporan Pembayaran - CV Natusi')
@section('content')

<div class="p-6">
    <header class="mb-6 flex flex-col md:flex-row md:items-end justify-between gap-3">
        <div>
            <h2 class="text-2xl font-bold text-[#006191] tracking-tight">Laporan Pembayaran</h2>
            <p class="text-sm text-slate-500">Ringkasan transaksi keuangan administrasi magang.</p>
        </div>
    </header>

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