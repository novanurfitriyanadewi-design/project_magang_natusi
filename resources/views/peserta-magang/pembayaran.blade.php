@extends('layouts.portal')

@section('title', 'Pembayaran')

@section('content')

    <section class="mb-6">
        <h1 class="headline text-2xl md:text-3xl font-bold text-slate-900 mb-1">Pembayaran</h1>
        <p class="text-sm text-slate-500">Kelola dan pantau status pembayaran iuran magang Anda.</p>
    </section>

    @if (session('success'))
        <div class="mb-4 rounded-lg bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-3">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="mb-4 rounded-lg bg-rose-50 border border-rose-200 text-rose-700 text-sm px-4 py-3">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-6">

        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm relative overflow-hidden">
            <div class="absolute top-0 left-0 w-1 h-full bg-blue-800"></div>
            <div class="flex justify-between items-start mb-4">
                <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Status Pembayaran</span>
                <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center text-blue-800">
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
                    $statusClass = match ($pembayaranTerkini->status) {
                        'lunas' => 'bg-green-100 text-green-700',
                        'ditolak' => 'bg-rose-100 text-rose-700',
                        default => 'bg-amber-100 text-amber-700',
                    };
                @endphp
                <span class="text-2xl font-bold text-slate-900">Rp {{ number_format($pembayaranTerkini->nominal, 0, ',', '.') }}</span>
                <div class="mt-2">
                    <span class="px-2 py-1 {{ $statusClass }} rounded text-[10px] font-bold uppercase">{{ $statusLabel }}</span>
                </div>
                <p class="text-sm text-slate-500 mt-2">Tanggal bayar: {{ $pembayaranTerkini->tgl_bayar?->format('d M Y') ?? '-' }}</p>
            @else
                <span class="text-2xl font-bold text-slate-900">Belum Ada Pembayaran</span>
                <p class="text-sm text-slate-500 mt-2">Silakan unggah bukti transfer di bawah.</p>
            @endif
        </div>

        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm relative overflow-hidden">
            <div class="absolute top-0 left-0 w-1 h-full bg-blue-600"></div>
            <div class="flex justify-between items-start mb-4">
                <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Nominal Wajib Bayar</span>
                <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center text-blue-600">
                    <span class="material-symbols-outlined">receipt_long</span>
                </div>
            </div>
            @if ($nominalAktif)
                <span class="text-2xl font-bold text-slate-900">Rp {{ number_format($nominalAktif->jumlah_nominal, 0, ',', '.') }}</span>
                <p class="text-sm text-slate-500 mt-2">Sesuai ketentuan CV Natusi</p>
            @else
                <span class="text-lg font-semibold text-slate-400">Belum diatur admin</span>
            @endif
        </div>

        <div class="bg-blue-800 text-white p-5 rounded-xl relative overflow-hidden">
            <span class="material-symbols-outlined text-[90px] absolute -right-3 -bottom-3 text-blue-600 opacity-30">account_balance</span>
            <span class="text-[11px] font-semibold text-blue-100 uppercase tracking-wider relative z-10">Rekening Perusahaan</span>
            <div class="mt-3 space-y-2 relative z-10">
                @forelse ($banks as $bank)
                    <div>
                        <p class="text-sm font-bold">{{ $bank->nama_bank }} — {{ $bank->no_rekening }}</p>
                        <p class="text-xs text-blue-100">a.n. {{ $bank->nama_pemilik }}</p>
                    </div>
                @empty
                    <p class="text-sm text-blue-100">Belum ada rekening terdaftar.</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm mb-6">
        <div class="p-5 border-b border-slate-200 bg-slate-50">
            <h3 class="headline text-lg font-semibold text-slate-900">Unggah Bukti Pembayaran</h3>
            <p class="text-sm text-slate-500">Isi formulir di bawah setelah melakukan transfer.</p>
        </div>
        <form method="POST" action="{{ route('peserta-magang.pembayaran.store') }}" enctype="multipart/form-data" class="p-6 grid grid-cols-1 lg:grid-cols-2 gap-6">
            @csrf

            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Bank Tujuan Transfer</label>
                    <select name="id_bank" class="w-full rounded-lg border-slate-300 text-sm focus:border-blue-600 focus:ring-blue-600" required>
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
                    <input type="date" name="tgl_bayar" value="{{ old('tgl_bayar') }}" class="w-full rounded-lg border-slate-300 text-sm focus:border-blue-600 focus:ring-blue-600" required>
                    @error('tgl_bayar')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Catatan (opsional)</label>
                    <textarea name="keterangan" rows="3" class="w-full rounded-lg border-slate-300 text-sm focus:border-blue-600 focus:ring-blue-600" placeholder="Contoh: pembayaran iuran bulan ini...">{{ old('keterangan') }}</textarea>
                    @error('keterangan')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex flex-col">
                <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Bukti Transfer (JPG, PNG, atau PDF, maks 5MB)</label>
                <div class="flex-1 border-2 border-dashed border-slate-300 rounded-xl flex flex-col items-center justify-center p-8 bg-slate-50 hover:bg-slate-100 transition-colors">
                    <span class="material-symbols-outlined text-blue-600 text-4xl mb-2">cloud_upload</span>
                    <input type="file" name="bukti_transfer" accept=".jpg,.jpeg,.png,.pdf" class="text-sm" required>
                </div>
                @error('bukti_transfer')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror

                <button type="submit" class="mt-4 w-full bg-blue-600 text-white font-semibold py-3 rounded-lg hover:bg-blue-700 transition-colors flex items-center justify-center gap-2">
                    Kirim Laporan Pembayaran
                    <span class="material-symbols-outlined text-[20px]">send</span>
                </button>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-200 bg-slate-50">
            <h3 class="headline text-lg font-semibold text-slate-900">Riwayat Pembayaran</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50">
                        <th class="px-6 py-3 text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Tanggal Bayar</th>
                        <th class="px-6 py-3 text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Bank</th>
                        <th class="px-6 py-3 text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Jumlah</th>
                        <th class="px-6 py-3 text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Bukti</th>
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
