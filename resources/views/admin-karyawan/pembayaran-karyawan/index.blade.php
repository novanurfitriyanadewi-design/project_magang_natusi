@extends('layouts.portal')

@section('title', 'Slip Gaji')

@section('content')
<div
    class="p-6 space-y-6"
    x-data="{
        showModal: false,
        editMode: false,
        editId: null,
        editData: { karyawan_id: '', periode: '', nominal: '', tanggal_bayar: '', status: 'belum_terbayar', keterangan: '' },
        openCreate() {
            this.editMode = false;
            this.editId = null;
            this.editData = { karyawan_id: '', periode: '{{ $periodeFilter }}', nominal: '', tanggal_bayar: '', status: 'belum_terbayar', keterangan: '' };
            this.showModal = true;
        },
        openEdit(item) {
            this.editMode = true;
            this.editId = item.id;
            this.editData = {
                karyawan_id: item.karyawan_id,
                periode: item.periode,
                nominal: item.nominal,
                tanggal_bayar: item.tanggal_bayar ?? '',
                status: item.status,
                keterangan: item.keterangan ?? '',
            };
            this.showModal = true;
        }
    }"
>

    <header class="flex flex-col md:flex-row md:items-end justify-between gap-3">
        <div>
            <h2 class="text-2xl font-bold text-[#006191]">Slip Gaji</h2>
            <p class="text-sm text-slate-500">Ringkasan periode {{ $periodeFilter }}</p>
        </div>

        <div class="flex gap-3">
            <form method="GET" action="{{ route('admin-karyawan.pembayaran-karyawan.index') }}" class="flex gap-3">
                <select name="status" onchange="this.form.submit()"
                    class="bg-white border border-slate-300 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-[#006191]">
                    <option value="" @selected(! $statusFilter)>Semua Status</option>
                    <option value="terbayar" @selected($statusFilter === 'terbayar')>Terbayar</option>
                    <option value="belum_terbayar" @selected($statusFilter === 'belum_terbayar')>Belum Terbayar</option>
                </select>
            </form>

            <button type="button" @click="openCreate()"
                class="bg-[#006191] text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-[#004b70] transition-colors">
                + Bayar Gaji
            </button>
        </div>
    </header>

    @if(session('success'))
        <div class="bg-green-100 text-green-700 px-4 py-3 rounded-lg text-sm">{{ session('success') }}</div>
    @endif

    <section class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-gradient-to-br from-blue-600 to-[#006191] text-white p-4 rounded-xl shadow-md flex items-center justify-between">
            <div class="space-y-0.5">
                <span class="text-blue-100 text-[11px] font-bold uppercase tracking-wider block">Total Karyawan</span>
                <div class="text-2xl font-black leading-tight">{{ $totalKaryawan }}</div>
            </div>
            <div class="bg-white/20 p-2.5 rounded-lg backdrop-blur-sm shrink-0">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </div>
        </div>

        <div class="bg-gradient-to-br from-emerald-500 to-green-600 text-white p-4 rounded-xl shadow-md flex items-center justify-between">
            <div class="space-y-0.5">
                <span class="text-emerald-100 text-[11px] font-bold uppercase tracking-wider block">Sudah Dibayar</span>
                <div class="text-2xl font-black leading-tight">{{ $sudahDibayar }}</div>
            </div>
            <div class="bg-white/20 p-2.5 rounded-lg backdrop-blur-sm shrink-0">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>

        <div class="bg-gradient-to-br from-rose-500 to-red-600 text-white p-4 rounded-xl shadow-md flex items-center justify-between">
            <div class="space-y-0.5">
                <span class="text-rose-100 text-[11px] font-bold uppercase tracking-wider block">Belum Dibayar</span>
                <div class="text-2xl font-black leading-tight">{{ $belumDibayar }}</div>
            </div>
            <div class="bg-white/20 p-2.5 rounded-lg backdrop-blur-sm shrink-0">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
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
                            <td class="px-6 py-3">{{ $item->karyawan->nama_karyawan ?? '-' }}</td>
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
                                <div class="flex gap-2">
                                    <button
                                        type="button"
                                        class="bg-amber-500 text-white px-3 py-1 rounded text-xs font-semibold hover:bg-amber-600"
                                        @click="openEdit({
                                            id: {{ $item->id_pembayaran }},
                                            karyawan_id: {{ $item->karyawan_id }},
                                            periode: @js($item->periode),
                                            nominal: {{ $item->nominal }},
                                            tanggal_bayar: @js($item->tanggal_bayar ? \Illuminate\Support\Carbon::parse($item->tanggal_bayar)->format('Y-m-d') : ''),
                                            status: @js($item->status),
                                            keterangan: @js($item->keterangan),
                                        })"
                                    >
                                        Edit
                                    </button>
                                    <form action="{{ route('admin-karyawan.pembayaran-karyawan.destroy', $item->id_pembayaran) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-red-600 text-white px-3 py-1 rounded text-xs font-semibold hover:bg-red-700" onclick="return confirm('Hapus data ini?')">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-slate-400">Belum ada data slip gaji.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <div x-show="showModal" x-cloak class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4" style="display: none;">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-lg p-6" @click.outside="showModal = false">
            <h3 class="text-lg font-semibold mb-4" x-text="editMode ? 'Edit Slip Gaji' : 'Bayar Gaji Karyawan'"></h3>

            <form
                :action="editMode ? '{{ url('admin-karyawan/pembayaran-karyawan') }}/' + editId : '{{ route('admin-karyawan.pembayaran-karyawan.store') }}'"
                method="POST"
                enctype="multipart/form-data"
                class="space-y-4"
            >
                @csrf
                <template x-if="editMode">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <div>
                    <label class="block text-sm font-medium mb-1">Karyawan</label>
                    <select name="karyawan_id" x-model="editData.karyawan_id" required class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm">
                        <option value="">-- Pilih Karyawan --</option>
                        @foreach($karyawan as $k)
                            <option value="{{ $k->id_karyawan }}">{{ $k->nama_karyawan }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium mb-1">Periode</label>
                        <input type="month" name="periode" x-model="editData.periode" required class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Nominal (Rp)</label>
                        <input type="number" name="nominal" x-model="editData.nominal" required min="0" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium mb-1">Tanggal Bayar</label>
                        <input type="date" name="tanggal_bayar" x-model="editData.tanggal_bayar" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Status</label>
                        <select name="status" x-model="editData.status" required class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm">
                            <option value="belum_terbayar">Belum Terbayar</option>
                            <option value="terbayar">Terbayar</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Keterangan (opsional)</label>
                    <textarea name="keterangan" x-model="editData.keterangan" rows="2" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm"></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Bukti Transfer (opsional, kosongkan jika tidak diubah)</label>
                    <input type="file" name="bukti_transfer" accept=".jpg,.jpeg,.png,.pdf" class="w-full text-sm">
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" @click="showModal = false" class="px-4 py-2 rounded-lg text-sm font-semibold border border-slate-300">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-lg text-sm font-semibold bg-[#006191] text-white hover:bg-[#004b70]" x-text="editMode ? 'Simpan Perubahan' : 'Simpan'"></button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection