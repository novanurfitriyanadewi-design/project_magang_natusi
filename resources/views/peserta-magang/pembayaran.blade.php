@extends('layouts.portal')

@section('title', 'Pembayaran')

@section('content')

<section class="mb-6">
    <h1 class="mt-5 text-2xl font-bold text-slate-900 md:text-3xl mb-1">Pembayaran</h1>
    <p class="text-sm text-slate-500">Kelola dan pantau status pembayaran iuran magang Anda.</p>
</section>

@if (session('success'))
    <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
        {{ session('success') }}
    </div>
@endif
@if (session('error'))
    <div class="mb-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
        {{ session('error') }}
    </div>
@endif

<div class="grid grid-cols-1 gap-4 mb-6 md:grid-cols-3">

    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-blue-700 to-blue-800 p-5 text-white shadow-lg shadow-blue-200/40 transition hover:-translate-y-1 hover:shadow-xl">
        <div class="flex items-start justify-between">
            <span class="text-xs font-medium uppercase tracking-wider text-blue-200">Status Pembayaran</span>
            <div class="grid h-12 w-12 place-items-center rounded-xl bg-white/20 text-white backdrop-blur-sm">
                <span class="material-symbols-outlined">payments</span>
            </div>
        </div>
        @if ($pembayaranTerkini)
            @php
                $statusLabel = match ($pembayaranTerkini->status) {
                    'lunas' => 'Lunas',
                    'ditolak' => 'Ditolak',
                    default => 'Menunggu Verifikasi',
                };
            @endphp
            <p class="mt-2 text-2xl font-bold">Rp {{ number_format($pembayaranTerkini->nominal, 0, ',', '.') }}</p>
            <div class="mt-2">
                <span class="rounded bg-white/20 px-2 py-1 text-[10px] font-bold uppercase">{{ $statusLabel }}</span>
            </div>
            <p class="mt-2 text-sm text-blue-200">Tanggal bayar: {{ $pembayaranTerkini->tgl_bayar?->format('d M Y') ?? '-' }}</p>
        @else
            <p class="mt-2 text-2xl font-bold">Belum Ada Pembayaran</p>
            <p class="mt-2 text-sm text-blue-200">Silakan unggah bukti transfer di bawah.</p>
        @endif
        <div class="absolute -bottom-6 -right-6 h-20 w-20 rounded-full bg-white/5"></div>
    </div>

    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-blue-500 to-blue-600 p-5 text-white shadow-lg shadow-blue-200/40 transition hover:-translate-y-1 hover:shadow-xl">
        <div class="flex items-start justify-between">
            <span class="text-xs font-medium uppercase tracking-wider text-blue-100">Nominal Wajib Bayar</span>
            <div class="grid h-12 w-12 place-items-center rounded-xl bg-white/20 text-white backdrop-blur-sm">
                <span class="material-symbols-outlined">receipt_long</span>
            </div>
        </div>
        @if ($nominalAktif)
            <p class="mt-2 text-2xl font-bold">Rp {{ number_format($nominalAktif->jumlah_nominal, 0, ',', '.') }}</p>
            <p class="mt-2 text-sm text-blue-100">Sesuai ketentuan CV Natusi</p>
        @else
            <p class="mt-2 text-lg font-semibold text-blue-100">Belum diatur admin</p>
        @endif
        <div class="absolute -bottom-6 -right-6 h-20 w-20 rounded-full bg-white/5"></div>
    </div>

    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-indigo-500 to-indigo-600 p-5 text-white shadow-lg shadow-indigo-200/40 transition hover:-translate-y-1 hover:shadow-xl">
        <span class="material-symbols-outlined text-[90px] absolute -right-3 -bottom-3 text-white opacity-10">account_balance</span>
        <span class="relative z-10 text-xs font-medium uppercase tracking-wider text-indigo-100">Rekening Perusahaan</span>
        <div class="relative z-10 mt-3 space-y-2">
            @forelse ($banks as $bank)
                <div>
                    <p class="text-sm font-bold">{{ $bank->nama_bank }} — {{ $bank->no_rekening }}</p>
                    <p class="text-xs text-indigo-100">a.n. {{ $bank->nama_pemilik }}</p>
                </div>
            @empty
                <p class="text-sm text-indigo-100">Belum ada rekening terdaftar.</p>
            @endforelse
        </div>
        <div class="absolute -bottom-6 -right-6 h-20 w-20 rounded-full bg-white/5"></div>
    </div>
</div>

<div class="mb-6 rounded-2xl border border-slate-200 bg-white shadow-sm">
    <div class="border-b border-slate-100 p-5">
        <h3 class="text-lg font-semibold text-slate-900">Unggah Bukti Pembayaran</h3>
        <p class="text-sm text-slate-500">Isi formulir di bawah setelah melakukan transfer.</p>
    </div>
    <form method="POST" action="{{ route('peserta-magang.pembayaran.store') }}" enctype="multipart/form-data" class="p-6 grid grid-cols-1 lg:grid-cols-2 gap-6">
        @csrf

        <div class="space-y-4">
            <div>
                <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Bank Tujuan Transfer</label>
                <select name="id_bank" class="w-full rounded-xl border-slate-200 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100" required>
                    <option value="">Pilih rekening tujuan</option>
                    @foreach ($banks as $bank)
                        <option value="{{ $bank->id_bank }}" @selected(old('id_bank') == $bank->id_bank)>
                            {{ $bank->nama_bank }} — {{ $bank->no_rekening }}
                        </option>
                    @endforeach
                </select>
                @error('id_bank')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

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
            <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Bukti Transfer (JPG, PNG, atau PDF, maks 5MB)</label>
            <div class="flex-1 border-2 border-dashed border-slate-300 rounded-2xl flex flex-col items-center justify-center p-8 bg-slate-50 hover:border-blue-400 hover:bg-slate-100 transition-colors">
                <span class="material-symbols-outlined text-blue-600 text-4xl mb-2">cloud_upload</span>
                <input type="file" name="bukti_transfer" accept=".jpg,.jpeg,.png,.pdf" class="text-sm" required>
            </div>
            @error('bukti_transfer')
                <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
            @enderror

            <button type="submit" class="mt-4 w-full bg-blue-600 text-white font-semibold py-3 rounded-xl shadow-lg shadow-blue-200/40 hover:bg-blue-700 transition-colors flex items-center justify-center gap-2">
                Kirim Laporan Pembayaran
                <span class="material-symbols-outlined text-[20px]">send</span>
            </button>
        </div>
    </form>
</div>

<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="p-5 border-b border-slate-100">
        <h3 class="text-lg font-semibold text-slate-900">Riwayat Pembayaran</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200">
                    <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wide">Tanggal Bayar</th>
                    <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wide">Bank</th>
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
                        <td class="px-6 py-4 text-sm text-slate-700">{{ $item->bank?->nama_bank ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm font-semibold text-slate-900">Rp {{ number_format($item->nominal, 0, ',', '.') }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 {{ $statusClass }} rounded text-[10px] font-bold uppercase">{{ $statusLabel }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ Storage::url($item->bukti_transfer) }}" target="_blank" class="text-blue-600 text-sm font-semibold hover:underline">Lihat</a>
                        </td>
                    </tr>
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
