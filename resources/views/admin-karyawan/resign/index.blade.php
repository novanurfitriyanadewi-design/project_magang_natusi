@extends('layouts.portal')

@section('title', 'Pengajuan Resign')

@section('content')
<div class="space-y-6">
    <div>
        <span class="inline-flex items-center gap-1.5 rounded-full bg-sky-100/80 px-3 py-1 text-xs font-bold tracking-wider text-sky-700 uppercase">
            <span class="h-1.5 w-1.5 rounded-full bg-sky-600"></span> MANAJEMEN PERSONIL
        </span>
        <h1 class="mt-2 text-3xl font-extrabold tracking-tight text-slate-900">Pengajuan Resign</h1>
        <p class="mt-1 text-sm text-slate-500">Tinjau dan kelola seluruh pengajuan resign karyawan secara efisien.</p>
    </div>

    @if (session('success'))
        <div class="flex items-center gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm text-emerald-800 shadow-sm">
            <span class="material-symbols-outlined text-2xl text-emerald-600">check_circle</span>
            <p class="font-medium">{{ session('success') }}</p>
        </div>
    @endif

    @if ($errors->any())
        <div class="rounded-2xl border border-red-200 bg-red-50 p-5 text-sm text-red-700 shadow-sm">
            <div class="flex items-center gap-2 font-bold text-red-800">
                <span class="material-symbols-outlined text-xl">error</span>
                Data belum dapat diproses
            </div>
            <ul class="mt-2 list-disc space-y-1 pl-6 text-xs font-medium">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-indigo-600 to-blue-700 p-6 text-white shadow-lg shadow-indigo-200">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold tracking-wider uppercase text-indigo-100">TOTAL PENGAJUAN</span>
                <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-white/20 text-white backdrop-blur-md">
                    <span class="material-symbols-outlined text-2xl">description</span>
                </span>
            </div>
            <p class="mt-4 text-4xl font-black tracking-tight">{{ $totalPengajuan }}</p>
            <p class="mt-1 text-xs font-medium text-indigo-100">Seluruh pengajuan masuk</p>
            <div class="absolute -right-6 -bottom-6 h-28 w-28 rounded-full bg-white/10 pointer-events-none"></div>
        </div>

        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-amber-500 to-orange-600 p-6 text-white shadow-lg shadow-amber-200">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold tracking-wider uppercase text-amber-100">MENUNGGU</span>
                <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-white/20 text-white backdrop-blur-md">
                    <span class="material-symbols-outlined text-2xl">hourglass_empty</span>
                </span>
            </div>
            <p class="mt-4 text-4xl font-black tracking-tight">{{ $menunggu }}</p>
            <p class="mt-1 text-xs font-medium text-amber-100">Perlu ditinjau HRD</p>
            <div class="absolute -right-6 -bottom-6 h-28 w-28 rounded-full bg-white/10 pointer-events-none"></div>
        </div>

        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-teal-500 to-emerald-600 p-6 text-white shadow-lg shadow-teal-200">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold tracking-wider uppercase text-teal-100">DISETUJUI</span>
                <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-white/20 text-white backdrop-blur-md">
                    <span class="material-symbols-outlined text-2xl">check_circle</span>
                </span>
            </div>
            <p class="mt-4 text-4xl font-black tracking-tight">{{ $disetujui }}</p>
            <p class="mt-1 text-xs font-medium text-teal-100">Telah disetujui</p>
            <div class="absolute -right-6 -bottom-6 h-28 w-28 rounded-full bg-white/10 pointer-events-none"></div>
        </div>

        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-rose-500 to-red-600 p-6 text-white shadow-lg shadow-rose-200">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold tracking-wider uppercase text-rose-100">DITOLAK</span>
                <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-white/20 text-white backdrop-blur-md">
                    <span class="material-symbols-outlined text-2xl">cancel</span>
                </span>
            </div>
            <p class="mt-4 text-4xl font-black tracking-tight">{{ $ditolak }}</p>
            <p class="mt-1 text-xs font-medium text-rose-100">Pengajuan ditolak</p>
            <div class="absolute -right-6 -bottom-6 h-28 w-28 rounded-full bg-white/10 pointer-events-none"></div>
        </div>
    </div>

    <section class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm">
        <form method="GET" action="{{ route('admin-karyawan.resign.index') }}" class="flex flex-wrap items-end gap-4">
            <div class="flex-1 min-w-[240px]">
                <label class="mb-1.5 block text-[11px] font-extrabold uppercase tracking-wider text-slate-400">CARI</label>
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama atau NIP..."
                    class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-sm font-medium text-slate-700 transition placeholder:text-slate-400 focus:border-sky-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-100">
            </div>

            <div class="min-w-[180px]">
                <label class="mb-1.5 block text-[11px] font-extrabold uppercase tracking-wider text-slate-400">STATUS</label>
                <select name="status" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-sm font-medium text-slate-700 transition focus:border-sky-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-100">
                    <option value="" {{ $status === '' ? 'selected' : '' }}>Semua Status</option>
                    <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Menunggu</option>
                    <option value="disetujui" {{ $status === 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                    <option value="ditolak" {{ $status === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                </select>
            </div>

            <div class="flex items-center gap-3">
                <button type="submit" class="rounded-xl bg-sky-600 px-6 py-2.5 text-sm font-bold text-white shadow-md shadow-sky-200 transition hover:bg-sky-700 active:scale-95">
                    Terapkan
                </button>
                <a href="{{ route('admin-karyawan.resign.index') }}" class="text-sm font-bold text-sky-600 transition hover:text-sky-800 hover:underline">
                    Reset Filter
                </a>
            </div>
        </form>
    </section>

    <section class="overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full border-collapse text-left text-sm">
                <thead>
                    <tr class="bg-sky-50/50 text-[11px] font-extrabold uppercase tracking-wider text-slate-500 border-b border-slate-100">
                        <th class="px-6 py-4">NO</th>
                        <th class="px-6 py-4">NAMA KARYAWAN</th>
                        <th class="px-6 py-4">NIP</th>
                        <th class="px-6 py-4">TGL EFEKTIF</th>
                        <th class="px-6 py-4">ALASAN</th>
                        <th class="px-6 py-4 text-center">DOKUMEN</th>
                        <th class="px-6 py-4 text-center">STATUS</th>
                        <th class="px-6 py-4 text-center">AKSI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($resigns as $index => $item)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="px-6 py-4 font-semibold text-slate-400">{{ $resigns->firstItem() + $index }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-sky-100 text-sm font-bold text-sky-700">
                                        {{ strtoupper(substr($item->karyawan->nama_karyawan ?? '-', 0, 1)) }}
                                    </span>
                                    <div>
                                        <p class="font-bold text-slate-800">{{ $item->karyawan->nama_karyawan ?? '-' }}</p>
                                        <p class="text-xs text-slate-400 font-medium">{{ $item->karyawan->jabatan ?? '-' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 font-medium text-slate-600">{{ $item->karyawan->nip ?? '-' }}</td>
                            <td class="px-6 py-4 font-medium text-slate-600">
                                {{ $item->tanggal_efektif ? \Carbon\Carbon::parse($item->tanggal_efektif)->translatedFormat('d M Y') : '-' }}
                            </td>
                            <td class="px-6 py-4 font-medium text-slate-600 max-w-[220px] truncate" title="{{ $item->alasan }}">
                                {{ $item->alasan }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if ($item->surat_resign_path)
                                    @php
                                        $ext = strtolower(pathinfo($item->surat_resign_original_name ?? $item->surat_resign_path, PATHINFO_EXTENSION));
                                    @endphp
                                    <a href="{{ route('admin-karyawan.resign.download', $item) }}"
                                        title="{{ $item->surat_resign_original_name }}"
                                        class="mx-auto inline-flex max-w-[180px] items-center gap-2 text-xs font-semibold text-slate-600 transition hover:text-sky-600">
                                        <span class="material-symbols-outlined shrink-0 text-[20px] {{ $ext === 'pdf' ? 'text-red-500' : 'text-blue-500' }}">
                                            {{ $ext === 'pdf' ? 'picture_as_pdf' : 'description' }}
                                        </span>
                                        <span class="truncate">{{ Str::limit($item->surat_resign_original_name, 22) }}</span>
                                    </a>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if ($item->status === 'pending')
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 px-3 py-1 text-xs font-bold text-amber-600">
                                        <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span> Menunggu
                                    </span>
                                @elseif ($item->status === 'disetujui')
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-teal-50 px-3 py-1 text-xs font-bold text-teal-600">
                                        <span class="h-1.5 w-1.5 rounded-full bg-teal-500"></span> Disetujui
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-rose-50 px-3 py-1 text-xs font-bold text-rose-600">
                                        <span class="h-1.5 w-1.5 rounded-full bg-rose-500"></span> Ditolak
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('admin-karyawan.resign.show', $item) }}" title="Detail pengajuan" class="flex h-8 w-8 items-center justify-center rounded-lg bg-sky-50 text-sky-600 transition hover:bg-sky-100">
                                        <span class="material-symbols-outlined text-lg">visibility</span>
                                    </a>
                                    @if ($item->status === 'pending')
                                        <form action="{{ route('admin-karyawan.resign.approve', $item) }}" method="POST" onsubmit="return confirm('Setujui pengajuan resign ini?');">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" title="Setujui" class="flex h-8 w-8 items-center justify-center rounded-lg bg-teal-50 text-teal-600 hover:bg-teal-100 transition">
                                                <span class="material-symbols-outlined text-lg">check_circle</span>
                                            </button>
                                        </form>

                                        <button type="button" title="Tolak" onclick="document.getElementById('tolak-modal-{{ $item->id }}').classList.remove('hidden')"
                                            class="flex h-8 w-8 items-center justify-center rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 transition">
                                            <span class="material-symbols-outlined text-lg">cancel</span>
                                        </button>

                                        <div id="tolak-modal-{{ $item->id }}" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/40 p-4 backdrop-blur-xs">
                                            <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl">
                                                <h3 class="text-lg font-extrabold text-slate-900">Tolak Pengajuan Resign</h3>
                                                <p class="mt-1 text-xs font-medium text-slate-500">Berikan catatan alasan penolakan HRD untuk karyawan ini.</p>

                                                <form action="{{ route('admin-karyawan.resign.reject', $item) }}" method="POST" class="mt-4 space-y-4">
                                                    @csrf
                                                    @method('PATCH')
                                                    <textarea name="catatan_hrd" rows="3" required placeholder="Tuliskan catatan HRD..."
                                                        class="w-full rounded-xl border border-slate-200 bg-slate-50 p-3 text-sm font-medium text-slate-800 focus:border-sky-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-100"></textarea>
                                                    <div class="flex justify-end gap-2">
                                                        <button type="button" onclick="document.getElementById('tolak-modal-{{ $item->id }}').classList.add('hidden')"
                                                            class="rounded-xl px-4 py-2 text-xs font-bold text-slate-500 hover:bg-slate-100 transition">Batal</button>
                                                        <button type="submit" class="rounded-xl bg-rose-600 px-4 py-2 text-xs font-bold text-white shadow-md shadow-rose-200 hover:bg-rose-700 transition">Tolak Pengajuan</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-xs font-medium text-slate-400" title="{{ $item->catatan_hrd }}">
                                            {{ $item->catatan_hrd ? Str::limit($item->catatan_hrd, 20) : '-' }}
                                        </span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-sm font-medium text-slate-400">
                                Belum ada pengajuan resign.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($resigns->hasPages())
            <div class="border-t border-slate-100 px-6 py-4">
                {{ $resigns->links() }}
            </div>
        @endif
    </section>
</div>
@endsection
