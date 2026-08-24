@extends('layouts.portal')

@section('title', 'Peserta Magang')

@section('content')
<div class="mt-5">
    <div class="mb-5">
        <h1 class="text-2xl font-extrabold text-slate-950">Peserta Magang</h1>
        <p class="text-sm text-slate-500">Daftar peserta yang sudah diterima. Setiap anggota kelompok tampil sebagai peserta tersendiri.</p>
    </div>

    <div class="overflow-hidden rounded-3xl border bg-white shadow">
        <div class="border-b px-6 py-4">
            Menampilkan <strong>{{ $peserta->firstItem() ?? 0 }}-{{ $peserta->lastItem() ?? 0 }}</strong>
            dari <strong>{{ $peserta->total() }}</strong> peserta
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-5 py-3">Nama</th>
                        <th class="px-5 py-3">Email</th>
                        <th class="px-5 py-3">Kelompok</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($peserta as $item)
                    <tr class="border-t">
                        <td class="px-5 py-3">{{ $item->user?->nama ?? '-' }}</td>
                        <td class="px-5 py-3">{{ $item->user?->email ?? '-' }}</td>
                        <td class="px-5 py-3">{{ $item->permintaan?->nama_sekolah ?? '-' }}</td>
                        <td class="px-5 py-3">
                            {{ ucfirst($item->status) }}
                        </td>
                        <td class="px-5 py-3">
                            <a href="{{ route('admin-peserta.peserta.show',$item->id_peserta) }}" class="text-blue-600 font-bold">Detail</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-5 py-5 text-center">Belum ada peserta diterima.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4">
            {{ $peserta->links() }}
        </div>
    </div>
</div>
@endsection
