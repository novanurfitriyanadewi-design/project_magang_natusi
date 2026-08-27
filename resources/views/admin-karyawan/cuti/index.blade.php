@extends('layouts.portal')

@section('title', 'Pengajuan Cuti')

@section('content')

<div class="mx-auto max-w-6xl">

    <div class="mb-6 flex items-center gap-3">
        <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-50 text-[#05658f]">
            <span class="material-symbols-outlined">event_busy</span>
        </span>
        <div>
            <h1 class="headline text-2xl font-bold text-slate-900">Pengajuan Cuti</h1>
            <p class="text-sm text-slate-500">Tinjau dan proses pengajuan cuti dari seluruh karyawan.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-5 flex items-center gap-3 rounded-2xl border border-emerald-100 bg-emerald-50 px-5 py-4 text-sm font-semibold text-emerald-700">
            <span class="material-symbols-outlined text-[20px]">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    {{-- STAT CARDS --}}
    <section class="mb-6 space-y-4">
        <div class="flex items-center justify-between rounded-2xl bg-gradient-to-br from-blue-600 to-[#05658f] p-5 text-white shadow-lg">
            <div class="flex items-center gap-3">
                <span class="grid h-11 w-11 place-items-center rounded-xl bg-white/20">
                    <span class="material-symbols-outlined text-[22px]">event_busy</span>
                </span>
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-wider text-blue-100">Total Pengajuan</p>
                    <p class="text-xs text-blue-100">Seluruh pengajuan cuti masuk</p>
                </div>
            </div>
            <p class="text-4xl font-black">{{ $totalPengajuan }}</p>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-amber-500 to-orange-500 p-4 text-white shadow-md">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-amber-100">Menunggu</span>
                    <span class="grid h-9 w-9 place-items-center rounded-lg bg-white/20">
                        <span class="material-symbols-outlined text-[18px]">hourglass_top</span>
                    </span>
                </div>
                <p class="mt-2 text-2xl font-black">{{ $menunggu }}</p>
            </div>

            <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-emerald-500 to-green-600 p-4 text-white shadow-md">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-100">Disetujui</span>
                    <span class="grid h-9 w-9 place-items-center rounded-lg bg-white/20">
                        <span class="material-symbols-outlined text-[18px]">check_circle</span>
                    </span>
                </div>
                <p class="mt-2 text-2xl font-black">{{ $disetujui }}</p>
            </div>

            <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-rose-500 to-red-600 p-4 text-white shadow-md">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-rose-100">Ditolak</span>
                    <span class="grid h-9 w-9 place-items-center rounded-lg bg-white/20">
                        <span class="material-symbols-outlined text-[18px]">cancel</span>
                    </span>
                </div>
                <p class="mt-2 text-2xl font-black">{{ $ditolak }}</p>
            </div>
        </div>
    </section>

    {{-- FILTER --}}
    <form method="GET" class="mb-5 flex flex-wrap items-end gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
        <div class="min-w-[200px] flex-1">
            <label class="mb-1 block text-[10px] font-bold uppercase tracking-wide text-slate-400">Cari</label>
            <input type="text" name="search" value="{{ $search }}" placeholder="Nama atau NIP karyawan..."
                class="w-full rounded-lg border-slate-300 text-sm focus:border-[#05658f] focus:ring-[#05658f]">
        </div>
        <div class="min-w-[160px]">
            <label class="mb-1 block text-[10px] font-bold uppercase tracking-wide text-slate-400">Status</label>
            <select name="status" class="w-full rounded-lg border-slate-300 text-sm focus:border-[#05658f] focus:ring-[#05658f]">
                <option value="" @selected(!$status)>Semua Status</option>
                <option value="pending" @selected($status === 'pending')>Menunggu</option>
                <option value="disetujui" @selected($status === 'disetujui')>Disetujui</option>
                <option value="ditolak" @selected($status === 'ditolak')>Ditolak</option>
            </select>
        </div>
        <button type="submit" class="rounded-lg bg-[#05658f] px-4 py-2 text-sm font-bold text-white hover:bg-[#045575]">Terapkan</button>
        @if ($search || $status)
            <a href="{{ route('admin-karyawan.cuti.index') }}" class="text-sm font-bold text-[#05658f] hover:underline">Reset</a>
        @endif
    </form>

    {{-- TABLE --}}
    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                        <th class="px-5 py-3">Karyawan</th>
                        <th class="px-5 py-3">Jenis</th>
                        <th class="px-5 py-3">Tanggal</th>
                        <th class="px-5 py-3">Alasan</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($cutis as $cuti)
                        @php($meta = $cuti->statusMeta())
                        <tr class="hover:bg-slate-50">
                            <td class="px-5 py-3">
                                <p class="text-sm font-bold text-slate-800">{{ $cuti->karyawan->nama_karyawan ?? '-' }}</p>
                                <p class="text-xs text-slate-400">{{ $cuti->karyawan->nip ?? '-' }}</p>
                            </td>
                            <td class="px-5 py-3 text-sm text-slate-600">{{ $cuti->jenis_label }}</td>
                            <td class="px-5 py-3 text-sm text-slate-600">
                                {{ $cuti->tanggal_mulai->translatedFormat('d M Y') }} — {{ $cuti->tanggal_selesai->translatedFormat('d M Y') }}
                                <span class="block text-xs text-slate-400">{{ $cuti->jumlah_hari }} hari</span>
                            </td>
                            <td class="max-w-xs px-5 py-3 text-sm text-slate-600">
                                <p class="line-clamp-2">{{ $cuti->alasan }}</p>
                                @if ($cuti->bukti_pendukung)
                                    <a href="{{ asset('storage/' . $cuti->bukti_pendukung) }}" target="_blank" class="mt-1 inline-flex items-center gap-1 text-xs font-semibold text-[#05658f] hover:underline">
                                        <span class="material-symbols-outlined text-[13px]">attach_file</span>
                                        Lihat Bukti
                                    </a>
                                @endif
                            </td>
                            <td class="px-5 py-3">
                                <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-[11px] font-semibold {{ $meta['class'] }}">
                                    {{ $meta['label'] }}
                                </span>
                                @if ($cuti->status === 'ditolak' && $cuti->catatan_hrd)
                                    <p class="mt-1 max-w-[150px] text-[10px] text-slate-400">{{ $cuti->catatan_hrd }}</p>
                                @endif
                            </td>
                            <td class="px-5 py-3">
                                <div class="flex justify-center gap-2">
                                    <a href="{{ route('admin-karyawan.cuti.show', $cuti) }}" title="Detail pengajuan" class="rounded-lg p-1.5 text-sky-600 hover:bg-sky-50">
                                        <span class="material-symbols-outlined text-[19px]">visibility</span>
                                    </a>
                                    <a href="{{ route('admin-karyawan.cuti.letter', $cuti) }}" target="_blank" title="Lihat surat" class="rounded-lg p-1.5 text-emerald-600 hover:bg-emerald-50">
                                        <span class="material-symbols-outlined text-[19px]">description</span>
                                    </a>
                                @if ($cuti->status === 'pending')
                                        <form method="POST" action="{{ route('admin-karyawan.cuti.approve', $cuti->id) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="rounded-lg border border-emerald-200 px-3 py-1.5 text-[11px] font-bold text-emerald-700 hover:bg-emerald-600 hover:text-white">
                                                Setujui
                                            </button>
                                        </form>
                                        <button type="button"
                                            onclick="document.getElementById('reject-modal-{{ $cuti->id }}').classList.remove('hidden')"
                                            class="rounded-lg border border-rose-200 px-3 py-1.5 text-[11px] font-bold text-rose-700 hover:bg-rose-600 hover:text-white">
                                            Tolak
                                        </button>
                                @else
                                    <span class="text-xs text-slate-400">Selesai</span>
                                @endif
                                </div>
                            </td>
                        </tr>

                        {{-- Modal Tolak --}}
                        <tr id="reject-modal-{{ $cuti->id }}" class="hidden">
                            <td colspan="6" class="bg-rose-50/60 px-5 py-4">
                                <form method="POST" action="{{ route('admin-karyawan.cuti.reject', $cuti->id) }}" class="flex flex-wrap items-end gap-3">
                                    @csrf
                                    @method('PATCH')
                                    <div class="flex-1">
                                        <label class="text-[10px] font-bold uppercase tracking-wide text-slate-500">Alasan Penolakan</label>
                                        <input type="text" name="catatan_hrd" required placeholder="Jelaskan alasan penolakan..."
                                            class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-xs focus:border-rose-500 focus:ring-rose-500">
                                    </div>
                                    <button type="submit" class="rounded-xl bg-rose-600 px-4 py-2 text-xs font-bold text-white hover:bg-rose-700">
                                        Kirim Penolakan
                                    </button>
                                    <button type="button"
                                        onclick="document.getElementById('reject-modal-{{ $cuti->id }}').classList.add('hidden')"
                                        class="rounded-xl border border-slate-300 px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100">
                                        Batal
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-14 text-center">
                                <p class="font-bold text-slate-700">Belum ada pengajuan cuti</p>
                                <p class="mt-1 text-sm text-slate-500">Coba ubah filter status atau kata kunci pencarian.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($cutis->hasPages())
            <div class="border-t border-slate-100 px-5 py-4">
                {{ $cutis->onEachSide(1)->links() }}
            </div>
        @endif
    </section>

</div>

@endsection