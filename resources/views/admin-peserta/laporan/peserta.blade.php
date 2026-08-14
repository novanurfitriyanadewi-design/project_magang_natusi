@extends('layouts.portal')
@section('content')

<div
    class="max-w-7xl mx-auto p-6 space-y-6"
    x-data="{
        showAddModal: false,
        showEditModal: false,
        editItem: {},
        baseUrl: '{{ url('admin/laporan-peserta') }}'
    }"
    x-cloak>

    @if(session('success'))
        <div class="bg-green-100 border border-green-300 text-green-700 px-4 py-3 rounded">
            {{ session('success') }}
        </div>
    @endif

    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Laporan Peserta Magang</h1>
            <p class="text-gray-500 mt-1">Analisis data peserta magang berdasarkan instansi dan periode.</p>
        </div>
        <button
            @click="showAddModal = true"
            class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg transition">
            + Tambah Peserta
        </button>
    </div>

    <div class="bg-white shadow rounded-xl p-5">
        <form method="GET" class="flex flex-wrap gap-3 items-center">
            <input
                type="text"
                name="search"
                value="{{ $search }}"
                placeholder="Cari nama peserta / sekolah / jurusan..."
                class="flex-1 min-w-[220px] border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">

            <select name="status_filter" class="border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                <option value="overall" @selected($statusFilter === 'overall')>Overall</option>
                <option value="active" @selected($statusFilter === 'active')>Active</option>
                <option value="non-active" @selected($statusFilter === 'non-active')>Non-Active</option>
            </select>

            <select name="year" class="border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                @foreach($availableYears as $th)
                    <option value="{{ $th }}" @selected((int) $year === (int) $th)>{{ $th }}</option>
                @endforeach
            </select>

            <button class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg transition">
                Terapkan
            </button>
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b bg-gray-50">
            <h4 class="text-lg font-semibold text-gray-800">Daftar Rincian Peserta</h4>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b">
                    <tr class="text-sm text-gray-600">
                        <th class="px-6 py-4 text-left">Nama</th>
                        <th class="px-6 py-4 text-left">Institusi</th>
                        <th class="px-6 py-4 text-left">Periode</th>
                        <th class="px-6 py-4 text-center">Status</th>
                        <th class="px-6 py-4 text-center">Durasi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200">
                @forelse($peserta as $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="font-semibold text-gray-800">{{ $item->user->nama ?? '-' }}</div>
                            <div class="text-sm text-gray-500">{{ $item->user->email ?? '-' }}</div>
                        </td>

                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-800">{{ $item->permintaan->nama_sekolah ?? '-' }}</div>
                            <div class="text-sm text-gray-500">{{ $item->permintaan->jurusan ?? '-' }}</div>
                        </td>

                        <td class="px-6 py-4 text-gray-700">
                            {{ $item->tgl_mulai ? $item->tgl_mulai->format('d M Y') : '-' }}
                            <br>
                            <small class="text-gray-500">s/d {{ $item->tgl_selesai ? $item->tgl_selesai->format('d M Y') : '-' }}</small>
                        </td>

                        <td class="px-6 py-4 text-center">
                            @if($item->status == 'aktif')
                                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">Aktif</span>
                            @elseif($item->status == 'selesai')
                                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">Selesai</span>
                            @else
                                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">Dibatalkan</span>
                            @endif
                        </td>

                        <td class="px-6 py-4 text-center text-gray-700">
                            {{ $item->durasi_magang ?? '-' }}
                        </td>

                        <td class="px-6 py-4">
                            <div class="flex justify-center gap-2">
                                <button
                                    @click="showEditModal = true; editItem = {
                                        id: {{ $item->id_peserta }},
                                        user_id: {{ $item->user_id }},
                                        permintaan_id: {{ $item->permintaan_id ?? 'null' }},
                                        alamat: @js($item->alamat),
                                        tingkat_pendidikan: @js($item->tingkat_pendidikan),
                                        kelas: @js($item->kelas),
                                        tgl_mulai: @js(optional($item->tgl_mulai)->format('Y-m-d')),
                                        tgl_selesai: @js(optional($item->tgl_selesai)->format('Y-m-d')),
                                        durasi_magang: @js($item->durasi_magang),
                                        nama_guru: @js($item->nama_guru),
                                        no_hpguru: @js($item->no_hpguru),
                                        status: @js($item->status)
                                    }"
                                    class="px-3 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600 text-sm transition">
                                    Edit
                                </button>

                                <form action="{{ route('admin-peserta.laporan-peserta.destroy', $item->id_peserta) }}"
                                      method="POST"
                                      onsubmit="return confirm('Hapus peserta ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 text-sm transition">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-8 text-center text-gray-500">
                            Belum ada data peserta magang.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t bg-gray-50">
            {{ $peserta->links() }}
        </div>
    </div>
</div>

@endsection