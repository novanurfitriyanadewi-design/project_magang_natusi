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

    <header class="flex flex-col md:flex-row md:items-end justify-between gap-3 rounded-2xl border border-sky-800 p-6 text-white shadow-md md:p-7" style="background: linear-gradient(135deg, #075177 0%, #006191 55%, #0a7ab5 100%);">
        <div>
            <span class="inline-flex items-center gap-2 rounded-full bg-sky-900/40 px-3 py-1 text-[11px] font-bold uppercase tracking-wider text-white ring-1 ring-sky-200/40">
                Pembayaran Gaji
            </span>
            <h2 class="mt-2 text-2xl font-bold text-white">Slip Gaji</h2>
            <p class="text-sm text-sky-100">Ringkasan periode {{ $periodeFilter }}</p>
        </div>

        <div class="flex gap-3">
            <form method="GET" action="{{ route('admin-karyawan.pembayaran-karyawan.index') }}" class="flex gap-3">
                <select name="status" onchange="this.form.submit()"
                    class="rounded-lg border border-sky-300 bg-[#075177] px-4 py-2 text-sm font-semibold text-white shadow-sm focus:border-white focus:ring-2 focus:ring-white/40"
                    style="background-color: #075177; color: #ffffff;">
                    <option value="" @selected(! $statusFilter)>Semua Status</option>
                    <option value="terbayar" @selected($statusFilter === 'terbayar')>Terbayar</option>
                    <option value="belum_terbayar" @selected($statusFilter === 'belum_terbayar')>Belum Terbayar</option>
                </select>
            </form>

            <a href="{{ route('admin-karyawan.laporan.gaji') }}"
                class="inline-flex items-center gap-2 rounded-lg border border-sky-300 bg-[#075177] px-4 py-2 text-sm font-semibold text-white transition-colors hover:bg-[#063f5d]"
                style="background-color: #075177; color: #ffffff;">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none"><path d="M9 17v-6m4 6V7m4 10v-3M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                Laporan Gaji
            </a>

            <button type="button" @click="openCreate()"
                class="inline-flex items-center gap-1.5 rounded-lg px-4 py-2 text-sm font-semibold text-white shadow transition-colors hover:brightness-110"
                style="background-color: #004b70;">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none"><path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                Bayar Gaji
            </button>
        </div>
    </header>

    @if(session('success'))
        <div class="bg-green-100 text-green-700 px-4 py-3 rounded-lg text-sm">{{ session('success') }}</div>
    @endif

    <section class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        <div class="relative overflow-hidden rounded-2xl bg-[#087eb8] p-5 text-white shadow-md transition-all hover:-translate-y-0.5 hover:shadow-lg" style="background: linear-gradient(135deg, #075177 0%, #087eb8 100%);">
            <div class="absolute -right-6 -bottom-8 h-28 w-28 rounded-full border border-cyan-200/20"></div>
            <div class="relative flex items-start justify-between gap-3">
                <div>
                    <span class="inline-block rounded-full bg-sky-950/35 px-2.5 py-0.5 text-[10px] font-extrabold uppercase tracking-wider">Total Karyawan</span>
                    <p class="mt-2.5 text-2xl font-black leading-tight">{{ $totalKaryawan }}</p>
                </div>
                <div class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-sky-950/35">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
            </div>
        </div>

        <div class="relative overflow-hidden rounded-2xl bg-[#159b72] p-5 text-white shadow-md transition-all hover:-translate-y-0.5 hover:shadow-lg" style="background: linear-gradient(135deg, #12805f 0%, #159b72 100%);">
            <div class="absolute -right-6 -bottom-8 h-28 w-28 rounded-full border border-emerald-100/20"></div>
            <div class="relative flex items-start justify-between gap-3">
                <div>
                    <span class="inline-block rounded-full bg-emerald-950/30 px-2.5 py-0.5 text-[10px] font-extrabold uppercase tracking-wider">Sudah Dibayar</span>
                    <p class="mt-2.5 text-2xl font-black leading-tight">{{ $sudahDibayar }}</p>
                    <p class="mt-1 text-[11px] font-medium text-emerald-100">Rp {{ number_format($totalNominalLunas, 0, ',', '.') }}</p>
                </div>
                <div class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-emerald-950/30">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </div>

        <div class="relative overflow-hidden rounded-2xl bg-[#d94a45] p-5 text-white shadow-md transition-all hover:-translate-y-0.5 hover:shadow-lg" style="background: linear-gradient(135deg, #c83f47 0%, #d94a45 100%);">
            <div class="absolute -right-6 -bottom-8 h-28 w-28 rounded-full border border-rose-100/20"></div>
            <div class="relative flex items-start justify-between gap-3">
                <div>
                    <span class="inline-block rounded-full bg-rose-950/30 px-2.5 py-0.5 text-[10px] font-extrabold uppercase tracking-wider">Belum Dibayar</span>
                    <p class="mt-2.5 text-2xl font-black leading-tight">{{ $belumDibayar }}</p>
                    <p class="mt-1 text-[11px] font-medium text-rose-100">Perlu diproses HRD</p>
                </div>
                <div class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-rose-950/30">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
            </div>
        </div>

        <div class="relative overflow-hidden rounded-2xl bg-[#6950b8] p-5 text-white shadow-md transition-all hover:-translate-y-0.5 hover:shadow-lg" style="background: linear-gradient(135deg, #51409b 0%, #6950b8 100%);">
            <div class="absolute -right-6 -bottom-8 h-28 w-28 rounded-full border border-violet-100/20"></div>
            <div class="relative flex items-start justify-between gap-3">
                <div>
                    <span class="inline-block rounded-full bg-violet-950/30 px-2.5 py-0.5 text-[10px] font-extrabold uppercase tracking-wider">Total Nominal Lunas</span>
                    <p class="mt-2.5 text-xl sm:text-2xl font-black leading-tight whitespace-nowrap">Rp {{ number_format($totalNominalLunas, 0, ',', '.') }}</p>
                    <p class="mt-1 text-[11px] font-medium text-violet-100">Tergabung periode {{ $periodeFilter }}</p>
                </div>
                <div class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-violet-950/30">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m9-6a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="flex flex-wrap items-center justify-between gap-3 px-6 py-4 border-b border-slate-100">
            <div class="flex items-center gap-2">
                <span class="grid h-8 w-8 place-items-center rounded-lg bg-sky-50 text-[#006191]">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none"><path d="M4 6h16M4 12h16M4 18h10" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                </span>
                <div>
                    <h3 class="text-lg font-semibold text-slate-800 leading-tight">Detail Slip Gaji</h3>
                    <p class="text-xs text-slate-400">Periode {{ $periodeFilter }}</p>
                </div>
            </div>
            <a href="{{ route('admin-karyawan.laporan.gaji', ['tahun' => substr($periodeFilter, 0, 4), 'bulan' => substr($periodeFilter, 5, 2)]) }}"
                class="inline-flex items-center gap-1.5 rounded-full bg-sky-50 px-4 py-1.5 text-xs font-bold text-[#006191] ring-1 ring-sky-100 hover:bg-sky-100 transition-colors">
                Lihat Laporan Periode Ini
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[760px] text-left">
                <thead style="background-color: #075177; color: #ffffff;">
                    <tr class="text-[11px] uppercase tracking-wider text-white" style="background-color: #075177; color: #ffffff;">
                        <th class="px-6 py-3.5 font-extrabold">Nama Karyawan</th>
                        <th class="px-6 py-3.5 font-extrabold">Nominal</th>
                        <th class="px-6 py-3.5 font-extrabold">Status</th>
                        <th class="px-6 py-3.5 font-extrabold">Tanggal Bayar</th>
                        <th class="px-6 py-3.5 font-extrabold">Keterangan</th>
                        <th class="px-6 py-3.5 font-extrabold">Bukti Transfer</th>
                        <th class="px-6 py-3.5 font-extrabold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($riwayat as $item)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-3">{{ $item->karyawan->nama_karyawan ?? '-' }}</td>
                            <td class="px-6 py-3 font-semibold">Rp {{ number_format($item->nominal, 0, ',', '.') }}</td>
                            <td class="px-6 py-3">
                                @php
                                    $badge = $item->status === 'terbayar'
                                        ? 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200'
                                        : 'bg-red-50 text-red-600 ring-1 ring-red-200';
                                    $labelStatus = $item->status === 'terbayar' ? 'Terbayar' : 'Belum Terbayar';
                                @endphp
                                <span class="{{ $badge }} inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-[11px] font-bold uppercase tracking-wide">
                                    <span class="h-1.5 w-1.5 rounded-full {{ $item->status === 'terbayar' ? 'bg-emerald-500' : 'bg-red-500' }}"></span>
                                    {{ $labelStatus }}
                                </span>
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