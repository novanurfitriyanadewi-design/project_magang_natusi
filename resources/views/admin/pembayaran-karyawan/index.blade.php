@extends('layouts.portal')

@section('title', 'Slip Gaji')

@section('content')
<div class="p-6 space-y-6" x-data="{ showModal: false }">

    <header class="flex flex-col md:flex-row md:items-end justify-between gap-3">
        <div>
            <h2 class="text-2xl font-bold text-[#006191]">Slip Gaji</h2>
            <p class="text-sm text-slate-500">Ringkasan periode {{ $periodeFilter }}</p>
        </div>

        <div class="flex gap-3">
            <form method="GET" class="flex gap-3">
                <select name="status" onchange="this.form.submit()"
                    class="bg-white border border-slate-300 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-[#006191]">
                    <option value="" @selected(! $statusFilter)>Semua Status</option>
                    <option value="terbayar" @selected($statusFilter === 'terbayar')>Terbayar</option>
                    <option value="belum_terbayar" @selected($statusFilter === 'belum_terbayar')>Belum Terbayar</option>
                </select>
            </form>

            <button type="button" @click="showModal = true"
                class="bg-[#006191] text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-[#004b70]">
                + Bayar Gaji
            </button>
        </div>
    </header>

    @if(session('success'))
        <div class="bg-green-100 text-green-700 px-4 py-3 rounded-lg text-sm">{{ session('success') }}</div>
    @endif

    <section class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white p-4 rounded-xl border-l-4 border-l-[#006191] shadow">
            <span class="text-slate-500 text-xs font-bold">TOTAL KARYAWAN</span>
            <div class="text-xl font-bold mt-1">{{ $totalKaryawan }}</div>
        </div>
        <div class="bg-white p-4 rounded-xl border-l-4 border-l-green-600 shadow">
            <span class="text-slate-500 text-xs font-bold">SUDAH DIBAYAR</span>
            <div class="text-xl font-bold mt-1 text-green-700">{{ $sudahDibayar }}</div>
        </div>
        <div class="bg-white p-4 rounded-xl border-l-4 border-l-red-600 shadow">
            <span class="text-slate-500 text-xs font-bold">BELUM DIBAYAR</span>
            <div class="text-xl font-bold mt-1 text-red-700">{{ $belumDibayar }}</div>
        </div>
    </section>

    <section class="bg-white rounded-xl shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200">
            <h3 class="text-lg font-semibold">Detail Slip Gaji</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50 text-xs uppercase text-slate-500">
                        <th class="px-6 py-3">Nama Karyawan</th>
                        <th class="px-6 py-3">Nominal</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Tanggal Bayar</th>
                        <th class="px-6 py-3">Keterangan</th>
                        <th class="px-6 py-3">Bukti Transfer</th>
                        <th class="px-6 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($riwayat as $item)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-3">{{ $item->karyawan->nama_karyawan }}</td>
                            <td class="px-6 py-3 font-semibold">Rp {{ number_format($item->nominal, 0, ',', '.') }}</td>
                            <td class="px-6 py-3">
                                @php
                                    $badge = $item->status === 'terbayar' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700';
                                    $labelStatus = $item->status === 'terbayar' ? 'Terbayar' : 'Belum Terbayar';
                                @endphp
                                <span class="{{ $badge }} px-3 py-1 rounded-full text-xs font-bold">{{ $labelStatus }}</span>
                            </td>
                            <td class="px-6 py-3 text-slate-500">{{ $item->tanggal_bayar ?? '-' }}</td>
                            <td class="px-6 py-3">{{ $item->keterangan ?? '-' }}</td>
                            <td class="px-6 py-3">
                                @if($item->bukti_transfer)
                                    <a href="{{ asset('storage/'.$item->bukti_transfer) }}" target="_blank" class="text-blue-600 hover:underline">Lihat</a>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-6 py-3">
                                <form action="{{ route('admin.pembayaran-karyawan.destroy', $item->id_pembayaran) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button class="bg-red-600 text-white px-3 py-1 rounded text-xs font-semibold" onclick="return confirm('Hapus data ini?')">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-6 py-8 text-center text-slate-400">Belum ada data slip gaji.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <div x-show="showModal" x-cloak class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4" style="display: none;">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-lg p-6" @click.outside="showModal = false">
            <h3 class="text-lg font-semibold mb-4">Bayar Gaji Karyawan</h3>

            <form action="{{ route('admin.pembayaran-karyawan.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-medium mb-1">Karyawan</label>
                    <select name="karyawan_id" required class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm">
                        <option value="">-- Pilih Karyawan --</option>
                        @foreach($karyawan as $k)
                            <option value="{{ $k->id_karyawan }}">{{ $k->nama_karyawan }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium mb-1">Periode</label>
                        <input type="month" name="periode" required value="{{ $periodeFilter }}" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Nominal (Rp)</label>
                        <input type="number" name="nominal" required min="0" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium mb-1">Tanggal Bayar</label>
                        <input type="date" name="tanggal_bayar" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Status</label>
                        <select name="status" required class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm">
                            <option value="belum_terbayar">Belum Terbayar</option>
                            <option value="terbayar">Terbayar</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Keterangan (opsional)</label>
                    <textarea name="keterangan" rows="2" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm"></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Bukti Transfer (opsional)</label>
                    <input type="file" name="bukti_transfer" accept=".jpg,.jpeg,.png,.pdf" class="w-full text-sm">
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" @click="showModal = false" class="px-4 py-2 rounded-lg text-sm font-semibold border border-slate-300">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-lg text-sm font-semibold bg-[#006191] text-white hover:bg-[#004b70]">Simpan</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

