@extends('layouts.portal')

@section('title', 'Pengajuan Resign')

@section('content')
<div class="space-y-6">
    <header>
        <h1 class="mt-5 text-2xl font-bold tracking-tight text-slate-950">Pengajuan Resign</h1>
        <p class="mt-1 text-sm text-slate-500">Tinjau dan kelola pengajuan resign karyawan.</p>
    </header>

    @if (session('success'))
        <div class="flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            <span class="material-symbols-outlined text-[20px]">check_circle</span>
            <p>{{ session('success') }}</p>
        </div>
    @endif

    @if ($errors->any())
        <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            <div class="flex items-center gap-2 font-semibold">
                <span class="material-symbols-outlined text-[20px]">error</span>
                Data belum dapat diproses
            </div>
            <ul class="mt-2 list-disc space-y-1 pl-6">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid gap-4 md:grid-cols-4">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm border-l-4 border-l-blue-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-slate-500">Total Pengajuan</p>
                    <p class="mt-1 text-2xl font-bold text-slate-900">{{ $totalPengajuan }}</p>
                </div>
                <span class="grid h-11 w-11 place-items-center rounded-xl bg-blue-100 text-blue-700">
                    <span class="material-symbols-outlined text-[21px]">description</span>
                </span>
            </div>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-slate-500">Menunggu Persetujuan</p>
                    <p class="mt-1 text-2xl font-bold text-slate-900">{{ $menunggu }}</p>
                </div>
                <span class="grid h-11 w-11 place-items-center rounded-xl bg-slate-100 text-slate-600">
                    <span class="material-symbols-outlined text-[21px]">hourglass_empty</span>
                </span>
            </div>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm border-l-4 border-l-emerald-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-slate-500">Disetujui</p>
                    <p class="mt-1 text-2xl font-bold text-slate-900">{{ $disetujui }}</p>
                </div>
                <span class="grid h-11 w-11 place-items-center rounded-xl bg-emerald-100 text-emerald-700">
                    <span class="material-symbols-outlined text-[21px]">check_circle</span>
                </span>
            </div>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm border-l-4 border-l-red-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-slate-500">Ditolak</p>
                    <p class="mt-1 text-2xl font-bold text-slate-900">{{ $ditolak }}</p>
                </div>
                <span class="grid h-11 w-11 place-items-center rounded-xl bg-red-100 text-red-700">
                    <span class="material-symbols-outlined text-[21px]">cancel</span>
                </span>
            </div>
        </div>
    </div>

    <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <form method="GET" action="{{ route('admin.resign.index') }}" class="flex flex-wrap items-end gap-4">
            <div class="flex-1 min-w-[220px]">
                <label class="mb-1 block text-xs font-semibold text-slate-600">Cari Nama atau NIP Karyawan</label>
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama atau ID karyawan..."
                    class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold text-slate-600">Status</label>
                <select name="status" class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
                    <option value="" {{ $status === '' ? 'selected' : '' }}>Semua Status</option>
                    <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Menunggu</option>
                    <option value="disetujui" {{ $status === 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                    <option value="ditolak" {{ $status === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                </select>
            </div>
            <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-blue-700 px-5 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-blue-800">
                <span class="material-symbols-outlined text-[18px]">filter_alt</span>
                Filter
            </button>
            <a href="{{ route('admin.resign.index') }}" class="text-sm font-semibold text-blue-700 hover:underline">Reset</a>
        </form>
    </section>

    <section class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-xl shadow-slate-200/70">
        <div class="overflow-x-auto">
            <table class="min-w-[1000px] w-full border-collapse text-left text-sm">
                <thead>
                    <tr class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                        <th class="px-6 py-4 font-semibold">No</th>
                        <th class="px-6 py-4 font-semibold">Karyawan</th>
                        <th class="px-6 py-4 font-semibold">ID Karyawan</th>
                        <th class="px-6 py-4 font-semibold">Tanggal Efektif</th>
                        <th class="px-6 py-4 font-semibold">Alasan</th>
                        <th class="px-6 py-4 font-semibold text-center">Status</th>
                        <th class="px-6 py-4 font-semibold text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($pengajuan as $index => $item)
                        @php
                            $labelStatus = ['pending' => 'Menunggu', 'disetujui' => 'Disetujui', 'ditolak' => 'Ditolak'];
                            $warnaStatus = [
                                'pending' => 'bg-sky-50 text-sky-700',
                                'disetujui' => 'bg-emerald-50 text-emerald-700',
                                'ditolak' => 'bg-red-50 text-red-700',
                            ];
                        @endphp
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="px-6 py-4 text-slate-500">{{ $pengajuan->firstItem() + $index }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <span class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-blue-100 text-xs font-bold text-blue-700">
                                        {{ strtoupper(substr($item->karyawan->nama_karyawan ?? '-', 0, 1)) }}
                                    </span>
                                    <div>
                                        <p class="font-semibold text-slate-800">{{ $item->karyawan->nama_karyawan ?? '-' }}</p>
                                        <p class="text-xs text-slate-400">{{ $item->karyawan->jabatan ?? '-' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-slate-600">{{ $item->karyawan->nip ?? '-' }}</td>
                            <td class="px-6 py-4 text-slate-600">{{ \Carbon\Carbon::parse($item->tanggal_efektif)->translatedFormat('d M Y') }}</td>
                            <td class="px-6 py-4 text-slate-600 max-w-[240px] truncate" title="{{ $item->alasan }}">{{ $item->alasan }}</td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold {{ $warnaStatus[$item->status] ?? 'bg-slate-100 text-slate-600' }}">
                                    {{ $labelStatus[$item->status] ?? ucfirst($item->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap items-center justify-center gap-2">
                                    @if ($item->status === 'pending')
                                        <form action="{{ route('admin.resign.setujui', $item) }}" method="POST" onsubmit="return confirm('Setujui pengajuan resign ini?');">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="inline-flex items-center gap-1 rounded-lg border border-emerald-200 px-3 py-1.5 text-xs font-bold text-emerald-700 hover:bg-emerald-50">
                                                Setujui
                                            </button>
                                        </form>
                                        <button type="button" onclick="document.getElementById('tolak-modal-{{ $item->id }}').classList.remove('hidden')"
                                            class="inline-flex items-center gap-1 rounded-lg border border-red-200 px-3 py-1.5 text-xs font-bold text-red-600 hover:bg-red-50">
                                            Tolak
                                        </button>

                                        <div id="tolak-modal-{{ $item->id }}" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/40 px-4">
                                            <div class="w-full max-w-sm rounded-2xl bg-white p-6 shadow-xl">
                                                <h3 class="text-sm font-bold text-slate-900">Tolak Pengajuan Resign</h3>
                                                <p class="mt-1 text-xs text-slate-500">Berikan catatan alasan penolakan.</p>
                                                <form action="{{ route('admin.resign.tolak', $item) }}" method="POST" class="mt-4 space-y-3">
                                                    @csrf
                                                    @method('PATCH')
                                                    <textarea name="catatan_hrd" rows="3" required placeholder="Catatan HRD..."
                                                        class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"></textarea>
                                                    <div class="flex justify-end gap-2">
                                                        <button type="button" onclick="document.getElementById('tolak-modal-{{ $item->id }}').classList.add('hidden')"
                                                            class="rounded-lg px-4 py-2 text-xs font-semibold text-slate-500 hover:bg-slate-100">Batal</button>
                                                        <button type="submit" class="rounded-lg bg-red-600 px-4 py-2 text-xs font-bold text-white hover:bg-red-700">Tolak</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-xs text-slate-400">{{ $item->catatan_hrd ?: '-' }}</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center text-sm text-slate-400">Belum ada pengajuan resign.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($pengajuan->hasPages())
            <div class="border-t border-slate-100 px-6 py-4">
                {{ $pengajuan->links() }}
            </div>
        @endif
    </section>
</div>
@endsection
