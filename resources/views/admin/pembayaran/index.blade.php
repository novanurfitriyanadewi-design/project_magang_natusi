@extends('layouts.portal')

@section('title', 'Data Pembayaran')

@section('content')
<div class="space-y-6">
    <header>
        <h1 class="mt-5 text-2xl font-bold tracking-tight text-slate-950">Data Pembayaran</h1>
        <p class="mt-1 text-sm text-slate-500">Pantau dan kelola transaksi keuangan peserta magang.</p>
    </header>

    @if (session('success'))
        <div class="flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            <span class="material-symbols-outlined text-[20px]">check_circle</span>
            <p>{{ session('success') }}</p>
        </div>
    @endif

    <div class="grid gap-4 md:grid-cols-2">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm border-l-4 border-l-blue-700">
            <p class="text-xs font-bold uppercase tracking-wide text-slate-400">Total Pembayaran Diterima</p>
            <p class="mt-2 text-3xl font-extrabold text-blue-800">Rp {{ number_format($totalDiterima, 0, ',', '.') }}</p>
            <p class="mt-1 text-xs text-slate-400">Performa 30 hari terakhir</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm border-l-4 border-l-red-600">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wide text-slate-400">Belum Diterima</p>
                    <p class="mt-2 text-3xl font-extrabold text-red-600">Rp {{ number_format($totalBelumDiterima, 0, ',', '.') }}</p>
                </div>
                <span class="rounded-full bg-red-50 px-2 py-1 text-xs font-semibold text-red-600">{{ $countBelumDiterima }} transaksi</span>
            </div>
            <p class="mt-1 text-xs text-slate-400">Perlu ditindaklanjuti segera</p>
        </div>
    </div>

    <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <form method="GET" action="{{ route('admin.pembayaran.index') }}" class="flex flex-wrap items-end gap-4">
            <div class="flex-1 min-w-[200px]">
                <label class="mb-1 block text-xs font-semibold text-slate-600">Cari Nama Peserta</label>
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama peserta..."
                    class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold text-slate-600">Status</label>
                <select name="status" class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
                    <option value="" {{ $status === '' ? 'selected' : '' }}>Semua Status</option>
                    <option value="menunggu" {{ $status === 'menunggu' ? 'selected' : '' }}>Menunggu</option>
                    <option value="lunas" {{ $status === 'lunas' ? 'selected' : '' }}>Lunas</option>
                    <option value="ditolak" {{ $status === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                </select>
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold text-slate-600">Dari Tanggal</label>
                <input type="date" name="dari_tanggal" value="{{ $dariTgl }}" class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold text-slate-600">Sampai Tanggal</label>
                <input type="date" name="sampai_tanggal" value="{{ $sampaiTgl }}" class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
            </div>
            <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-blue-700 px-5 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-blue-800">
                <span class="material-symbols-outlined text-[18px]">filter_alt</span>
                Filter
            </button>
            <a href="{{ route('admin.pembayaran.index') }}" class="text-sm font-semibold text-blue-700 hover:underline">Reset Filter</a>
        </form>
    </section>

    <section class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-xl shadow-slate-200/70">
        <div class="overflow-x-auto">
            <table class="min-w-[1000px] w-full border-collapse text-left text-sm">
                <thead>
                    <tr class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                        <th class="px-6 py-4 font-semibold">ID Transaksi</th>
                        <th class="px-6 py-4 font-semibold">Nama Peserta</th>
                        <th class="px-6 py-4 font-semibold">Tanggal</th>
                        <th class="px-6 py-4 font-semibold">Jumlah</th>
                        <th class="px-6 py-4 font-semibold">Status</th>
                        <th class="px-6 py-4 font-semibold text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($pembayarans as $bayar)
                        @php
                            $labelStatus = ['menunggu' => 'MENUNGGU', 'lunas' => 'SUCCESS', 'ditolak' => 'DITOLAK'];
                            $warnaStatus = [
                                'menunggu' => 'bg-amber-50 text-amber-700',
                                'lunas' => 'bg-emerald-50 text-emerald-700',
                                'ditolak' => 'bg-red-50 text-red-700',
                            ];
                        @endphp
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="px-6 py-4 font-bold text-blue-700">#TXN-{{ str_pad($bayar->id_pembayaran, 4, '0', STR_PAD_LEFT) }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <span class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-blue-100 text-xs font-bold text-blue-700">
                                        {{ strtoupper(substr($bayar->peserta->user->nama ?? '-', 0, 1)) }}
                                    </span>
                                    <span class="font-semibold text-slate-800">{{ $bayar->peserta->user->nama ?? '-' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-slate-600">{{ $bayar->tgl_bayar ? \Carbon\Carbon::parse($bayar->tgl_bayar)->translatedFormat('d M Y') : '-' }}</td>
                            <td class="px-6 py-4 font-bold text-slate-900">Rp {{ number_format($bayar->nominal, 0, ',', '.') }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-1 rounded-full px-3 py-1 text-xs font-bold {{ $warnaStatus[$bayar->status] ?? 'bg-slate-100 text-slate-600' }}">
                                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                                    {{ $labelStatus[$bayar->status] ?? strtoupper($bayar->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap items-center justify-center gap-2">
                                    @if ($bayar->bukti_transfer)
                                        <a href="{{ asset('storage/' . $bayar->bukti_transfer) }}" target="_blank"
                                            class="inline-flex items-center gap-1 rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-600 hover:bg-slate-50">
                                            <span class="material-symbols-outlined text-[16px]">visibility</span>
                                            Lihat Bukti
                                        </a>
                                    @endif

                                    @if ($bayar->status === 'menunggu')
                                        <form action="{{ route('admin.pembayaran.terima', $bayar) }}" method="POST" onsubmit="return confirm('Konfirmasi pembayaran ini lunas?');">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="inline-flex items-center gap-1 rounded-lg border border-emerald-200 px-3 py-1.5 text-xs font-bold text-emerald-700 hover:bg-emerald-50">
                                                Terima
                                            </button>
                                        </form>

                                        <button type="button" onclick="document.getElementById('tolak-modal-{{ $bayar->id_pembayaran }}').classList.remove('hidden')"
                                            class="inline-flex items-center gap-1 rounded-lg border border-red-200 px-3 py-1.5 text-xs font-bold text-red-600 hover:bg-red-50">
                                            Tolak
                                        </button>

                                        <div id="tolak-modal-{{ $bayar->id_pembayaran }}" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/40 px-4">
                                            <div class="w-full max-w-sm rounded-2xl bg-white p-6 shadow-xl">
                                                <h3 class="text-sm font-bold text-slate-900">Tolak Pembayaran</h3>
                                                <p class="mt-1 text-xs text-slate-500">Berikan alasan penolakan untuk transaksi ini.</p>
                                                <form action="{{ route('admin.pembayaran.tolak', $bayar) }}" method="POST" class="mt-4 space-y-3">
                                                    @csrf
                                                    @method('PATCH')
                                                    <textarea name="keterangan" rows="3" required placeholder="Alasan penolakan..."
                                                        class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"></textarea>
                                                    <div class="flex justify-end gap-2">
                                                        <button type="button" onclick="document.getElementById('tolak-modal-{{ $bayar->id_pembayaran }}').classList.add('hidden')"
                                                            class="rounded-lg px-4 py-2 text-xs font-semibold text-slate-500 hover:bg-slate-100">Batal</button>
                                                        <button type="submit" class="rounded-lg bg-red-600 px-4 py-2 text-xs font-bold text-white hover:bg-red-700">Tolak Pembayaran</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-sm text-slate-400">Tidak ada data pembayaran untuk filter ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($pembayarans->hasPages())
            <div class="border-t border-slate-100 px-6 py-4">
                {{ $pembayarans->links() }}
            </div>
        @endif
    </section>
</div>
@endsection
