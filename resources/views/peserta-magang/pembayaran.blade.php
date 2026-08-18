@extends('layouts.portal')

@section('title', 'Pembayaran')

@section('content')
    {{-- Header --}}
    <section class="mb-6">
        <h1 class="mt-5 text-2xl font-bold text-slate-900 md:text-3xl mb-1">Pembayaran</h1>
        <p class="text-sm text-slate-500">Kelola dan pantau status pembayaran iuran magang Anda.</p>
    </section>

    {{-- Cara Pembayaran: QRIS --}}
    <div class="mb-6 rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 p-5">
            <h3 class="text-lg font-semibold text-slate-900">Cara Pembayaran</h3>
            <p class="text-sm text-slate-500">Scan kode QR di bawah pakai aplikasi m-banking / e-wallet apa saja, lalu bayar sesuai nominal yang ditentukan.</p>
        </div>

        <div class="flex flex-col items-center p-8">
            @if ($qris?->qris_image)
                <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white p-3 shadow-sm">
                    <img src="{{ asset('storage/'.$qris->qris_image) }}" alt="QR Pembayaran" class="h-[240px] w-[240px] object-contain">
                </div>
                <span class="mt-3 inline-flex items-center gap-1 rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>
                    Scan QR & Bayar
                </span>
                @if ($nominalAktif)
                    <p class="mt-3 text-sm text-slate-500">Nominal pembayaran: <span class="font-bold text-slate-900">Rp {{ number_format($nominalAktif->jumlah_nominal, 0, ',', '.') }}</span></p>
                @endif
                <p class="mt-1 max-w-md text-center text-xs text-slate-400">Scan menggunakan aplikasi mobile banking atau e-wallet yang mendukung QRIS.</p>
            @else
                <svg class="mb-2 h-16 w-16 text-slate-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="8" height="8"/><rect x="14" y="2" width="8" height="8"/><rect x="2" y="14" width="8" height="8"/><rect x="14" y="14" width="8" height="8"/></svg>
                <p class="text-sm text-slate-500">QR pembayaran belum diunggah admin. Hubungi admin untuk informasi pembayaran.</p>
            @endif
        </div>
    </div>

    {{-- Form Unggah Bukti --}}
    <div class="mb-6 rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 p-5">
            <h3 class="text-lg font-semibold text-slate-900">Unggah Bukti Pembayaran</h3>
            <p class="text-sm text-slate-500">Isi formulir di bawah setelah menyelesaikan pembayaran melalui QRIS.</p>
        </div>
        <form method="POST" action="{{ route('peserta-magang.pembayaran.store') }}" enctype="multipart/form-data" class="p-6 grid grid-cols-1 lg:grid-cols-2 gap-6">
            @csrf

            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Tanggal Pembayaran</label>
                    <input type="date" name="tgl_bayar" value="{{ old('tgl_bayar') }}" class="w-full rounded-xl border-slate-200 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100" required>
                    @error('tgl_bayar')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Catatan (opsional)</label>
                    <textarea name="keterangan" rows="3" class="w-full rounded-xl border-slate-200 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100" placeholder="Contoh: pembayaran iuran bulan ini...">{{ old('keterangan') }}</textarea>
                    @error('keterangan')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex flex-col">
                <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Bukti Pembayaran (JPG, PNG, atau PDF, maks 5MB)</label>
                <div class="flex-1 border-2 border-dashed border-slate-300 rounded-2xl flex flex-col items-center justify-center p-8 bg-slate-50 hover:border-blue-400 hover:bg-slate-100 transition-colors">
                    <svg class="h-10 w-10 text-blue-600 mb-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                    <input type="file" name="bukti_transfer" accept=".jpg,.jpeg,.png,.pdf" class="text-sm" required>
                    <p class="mt-2 text-xs text-slate-400">Maksimal 5 MB</p>
                </div>
                @error('bukti_transfer')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror

                <button type="submit" class="mt-4 w-full bg-blue-600 text-white font-semibold py-3 rounded-xl shadow-lg shadow-blue-200/40 hover:bg-blue-700 transition-colors flex items-center justify-center gap-2">
                    Kirim Laporan Pembayaran
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                </button>
            </div>
        </form>
    </div>

    {{-- Riwayat Pembayaran --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-100">
            <h3 class="text-lg font-semibold text-slate-900">Riwayat Pembayaran</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wide">Tanggal Bayar</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wide">Metode</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wide">Jumlah</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wide">Status</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wide">Bukti</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($riwayat as $item)
                        @php
                            $statusLabel = match ($item->status) {
                                'lunas' => 'Lunas',
                                'ditolak' => 'Ditolak',
                                default => 'Menunggu',
                            };
                            $statusClass = match ($item->status) {
                                'lunas' => 'bg-green-100 text-green-700',
                                'ditolak' => 'bg-rose-100 text-rose-700',
                                default => 'bg-amber-100 text-amber-700',
                            };
                        @endphp
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4 text-sm text-slate-700">{{ $item->tgl_bayar?->format('d M Y') ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-slate-700">QRIS</td>
                            <td class="px-6 py-4 text-sm font-semibold text-slate-900">Rp {{ number_format($item->nominal, 0, ',', '.') }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 {{ $statusClass }} rounded text-[10px] font-bold uppercase">{{ $statusLabel }}</span>
                            </td>
                            <td class="px-6 py-4">
                                @if ($item->bukti_transfer)
                                    <a href="{{ Storage::url($item->bukti_transfer) }}" target="_blank" class="text-blue-600 text-sm font-semibold hover:underline">Lihat</a>
                                @else
                                    <span class="text-xs text-slate-400">-</span>
                                @endif
                            </td>
                        </tr>
                        @if ($item->status === 'ditolak' && $item->keterangan)
                            <tr class="bg-rose-50/60">
                                <td colspan="5" class="px-6 py-3">
                                    <div class="flex items-start gap-2">
                                        <svg class="h-4 w-4 text-rose-500 shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="12" x2="12" y2="16"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                                        <p class="text-xs text-rose-700"><span class="font-bold">Catatan Admin:</span> {{ $item->keterangan }}</p>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-sm text-slate-500">Belum ada riwayat pembayaran.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($riwayat->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $riwayat->links() }}
            </div>
        @endif
    </div>
@endsection