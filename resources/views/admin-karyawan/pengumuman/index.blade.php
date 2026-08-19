@extends('layouts.portal')

@push('styles')
<style>
    .admin-announcement-create {
        background-color: #05658f;
        color: #ffffff;
        cursor: pointer;
        opacity: 1;
        position: relative;
        z-index: 1;
    }

    .admin-announcement-create:hover {
        background-color: #045575;
    }
</style>
@endpush

@section('content')
<div class="p-6">

    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">
                Pengumuman
            </h1>
            <p class="mt-1 text-sm text-slate-500">
                Kelola pengumuman untuk karyawan.
            </p>
        </div>

        <a
            href="{{ route('admin-karyawan.pengumuman.create') }}"
            class="admin-announcement-create inline-flex items-center justify-center gap-2 rounded-xl bg-[#05658f] px-5 py-3 text-sm font-bold text-white shadow-md transition-all duration-200 hover:-translate-y-0.5 hover:bg-[#045575] hover:shadow-lg"
        >
            <span class="material-symbols-outlined text-[20px]">add</span>
            Tambah Pengumuman
        </a>
    </div>

    @if (session('success'))
        <div class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
            {{ session('success') }}
        </div>
    @endif

    @if ($pengumuman->count())
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">

                    <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                        <tr>
                            <th class="px-6 py-4">No</th>
                            <th class="px-6 py-4">Judul</th>
                            <th class="px-6 py-4">Kategori</th>

                            {{-- TAMBAHAN --}}
                            <th class="px-6 py-4">Tujuan</th>

                            <th class="px-6 py-4">Dibuat Oleh</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4">Tanggal</th>
                            <th class="px-6 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">

                        @foreach ($pengumuman as $item)

                            <tr class="hover:bg-slate-50">

                                {{-- NO --}}
                                <td class="px-6 py-4 text-slate-600">
                                    {{ $pengumuman->firstItem() + $loop->index }}
                                </td>

                                {{-- JUDUL --}}
                                <td class="px-6 py-4">
                                    <div class="font-semibold text-slate-800">
                                        {{ $item->judul }}
                                    </div>

                                    <div class="mt-1 max-w-md truncate text-xs text-slate-400">
                                        {{ $item->isi }}
                                    </div>
                                </td>

                                {{-- KATEGORI --}}
                                <td class="px-6 py-4">

                                    @php
                                        $kategoriClass = match ($item->kategori) {
                                            'penting' => 'bg-red-50 text-red-600',
                                            'acara' => 'bg-purple-50 text-purple-600',
                                            default => 'bg-blue-50 text-blue-600',
                                        };
                                    @endphp

                                    <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $kategoriClass }}">
                                        {{ ucfirst($item->kategori) }}
                                    </span>

                                </td>

                                {{-- TUJUAN / PENERIMA --}}
                                <td class="px-6 py-4">

                                    @if ($item->penerima->count() > 0)

                                        <div class="space-y-2">

                                            @foreach ($item->penerima as $penerima)

                                                @if ($penerima->karyawan)

                                                    <div>
                                                        <div class="font-semibold text-slate-700">
                                                            {{ $penerima->karyawan->nama_karyawan }}
                                                        </div>

                                                        @if ($penerima->karyawan->nip)
                                                            <div class="text-xs text-slate-400">
                                                                NIP: {{ $penerima->karyawan->nip }}
                                                            </div>
                                                        @endif
                                                    </div>

                                                @endif

                                            @endforeach

                                        </div>

                                    @else

                                        <span class="inline-flex rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-600">
                                            Semua Karyawan
                                        </span>

                                    @endif

                                </td>

                                {{-- DIBUAT OLEH --}}
                                <td class="px-6 py-4 text-slate-600">
                                    {{ $item->pembuat?->name ?? $item->pembuat?->nama ?? '-' }}
                                </td>

                                {{-- STATUS --}}
                                <td class="px-6 py-4">

                                    @if ($item->aktif)

                                        <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-600">
                                            Aktif
                                        </span>

                                    @else

                                        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-500">
                                            Tidak Aktif
                                        </span>

                                    @endif

                                </td>

                                {{-- TANGGAL --}}
                                <td class="px-6 py-4 text-slate-500">
                                    {{ $item->created_at?->format('d/m/Y') }}
                                </td>

                                {{-- AKSI --}}
                                <td class="px-6 py-4">

                                    <div class="flex items-center justify-center gap-2">

                                        <a
                                            href="{{ route('admin-karyawan.pengumuman.edit', $item) }}"
                                            class="rounded-lg bg-amber-50 px-3 py-2 text-xs font-semibold text-amber-600 hover:bg-amber-100"
                                        >
                                            Edit
                                        </a>

                                        <form
                                            action="{{ route('admin-karyawan.pengumuman.destroy', $item) }}"
                                            method="POST"
                                            onsubmit="return confirm('Yakin ingin menghapus pengumuman ini?')"
                                        >

                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                class="rounded-lg bg-red-50 px-3 py-2 text-xs font-semibold text-red-600 hover:bg-red-100"
                                            >
                                                Hapus
                                            </button>

                                        </form>

                                    </div>

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>
            </div>

            <div class="border-t border-slate-100 px-6 py-4">
                {{ $pengumuman->links() }}
            </div>

        </div>

    @else

        <div class="rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-16 text-center">

            <div class="text-4xl">📢</div>

            <h3 class="mt-4 text-lg font-semibold text-slate-700">
                Belum Ada Pengumuman
            </h3>

            <p class="mt-1 text-sm text-slate-400">
                Silakan tambahkan pengumuman baru.
            </p>

            <a
                href="{{ route('admin-karyawan.pengumuman.create') }}"
                class="admin-announcement-create mt-5 inline-flex items-center justify-center gap-2 rounded-xl bg-[#05658f] px-5 py-3 text-sm font-bold text-white shadow-md transition-all duration-200 hover:-translate-y-0.5 hover:bg-[#045575] hover:shadow-lg"
            >
                <span class="material-symbols-outlined text-[20px]">add</span>
                Tambah Pengumuman
            </a>

        </div>

    @endif

</div>
@endsection